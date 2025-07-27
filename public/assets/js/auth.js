/**
 * Authentication Manager - Phase 3.4
 * Handles login, register, forgot password functionality
 */

class AuthManager {
	constructor() {
		this.currentUser = null;
		this.isLoggedIn = false;

		// Load user session if exists
		this.loadUserSession();

		// Initialize event listeners
		this.initializeGlobalListeners();
	}

	/**
	 * Initialize global event listeners
	 */
	initializeGlobalListeners() {
		// Listen for storage changes (multi-tab login/logout)
		window.addEventListener('storage', (e) => {
			if (e.key === 'userSession') {
				this.loadUserSession();
				this.updateAuthUI();
			}
		});
	}

	/**
	 * Initialize login page
	 */
	initializeLogin() {
		const form = document.getElementById('loginForm');
		if (!form) return;

		form.addEventListener('submit', (e) => {
			e.preventDefault();
			this.handleLogin();
		});

		// Real-time validation
		const emailInput = document.getElementById('loginEmail');
		const passwordInput = document.getElementById('loginPassword');

		emailInput.addEventListener('blur', () =>
			this.validateLoginField(emailInput)
		);
		passwordInput.addEventListener('blur', () =>
			this.validateLoginField(passwordInput)
		);

		// Remember me functionality
		this.loadRememberedCredentials();
	}

	/**
	 * Initialize register page
	 */
	initializeRegister() {
		const form = document.getElementById('registerForm');
		if (!form) return;

		form.addEventListener('submit', (e) => {
			e.preventDefault();
			this.handleRegister();
		});

		// Real-time validation
		const inputs = form.querySelectorAll(
			'input[required], select[required]'
		);
		inputs.forEach((input) => {
			input.addEventListener('blur', () =>
				this.validateRegisterField(input)
			);
		});

		// Password strength indicator
		const passwordInput = document.getElementById('registerPassword');
		const confirmPasswordInput = document.getElementById('confirmPassword');

		passwordInput.addEventListener('input', () =>
			this.updatePasswordStrength()
		);
		confirmPasswordInput.addEventListener('input', () =>
			this.validatePasswordMatch()
		);
	}

	/**
	 * Initialize forgot password page
	 */
	initializeForgotPassword() {
		const form = document.getElementById('forgotPasswordForm');
		if (!form) return;

		form.addEventListener('submit', (e) => {
			e.preventDefault();
			this.handleForgotPassword();
		});

		// Email validation
		const emailInput = document.getElementById('forgotEmail');
		emailInput.addEventListener('blur', () =>
			this.validateForgotPasswordField(emailInput)
		);
	}

	/**
	 * Handle login form submission
	 */
	async handleLogin() {
		const form = document.getElementById('loginForm');
		const formData = new FormData(form);
		const loginData = {
			email: formData.get('email'),
			password: formData.get('password'),
			remember: formData.get('remember') === 'on',
		};

		// Validate form
		if (!this.validateLoginForm(loginData)) {
			return;
		}

		try {
			// Show loading
			this.setButtonLoading('loginBtn', true);

			// Simulate API call
			const result = await this.authenticateUser(loginData);

			if (result.success) {
				// Save user session
				this.saveUserSession(result.user, loginData.remember);

				// Show success message
				this.showMessage('Đăng nhập thành công!', 'success');

				// Redirect after short delay
				setTimeout(() => {
					const redirectUrl =
						new URLSearchParams(window.location.search).get(
							'redirect'
						) || '/';
					window.location.href = redirectUrl;
				}, 1500);
			} else {
				throw new Error(result.message || 'Đăng nhập thất bại');
			}
		} catch (error) {
			console.error('Login error:', error);
			this.showMessage(
				error.message || 'Có lỗi xảy ra khi đăng nhập',
				'error'
			);
		} finally {
			this.setButtonLoading('loginBtn', false);
		}
	}

	/**
	 * Handle register form submission
	 */
	async handleRegister() {
		const form = document.getElementById('registerForm');
		const formData = new FormData(form);
		const registerData = {
			firstName: formData.get('firstName'),
			lastName: formData.get('lastName'),
			email: formData.get('email'),
			phone: formData.get('phone'),
			password: formData.get('password'),
			confirmPassword: formData.get('confirmPassword'),
			birthDate: formData.get('birthDate'),
			gender: formData.get('gender'),
			agreeTerms: formData.get('agreeTerms') === 'on',
			subscribeNewsletter: formData.get('subscribeNewsletter') === 'on',
		};

		// Validate form
		if (!this.validateRegisterForm(registerData)) {
			return;
		}

		try {
			// Show loading
			this.setButtonLoading('registerBtn', true);

			// Simulate API call
			const result = await this.createUser(registerData);

			if (result.success) {
				// Save user session
				this.saveUserSession(result.user, false);

				// Show success message
				this.showMessage(
					'Đăng ký thành công! Chào mừng bạn đến với 5S Fashion!',
					'success'
				);

				// Redirect after short delay
				setTimeout(() => {
					window.location.href = '/welcome';
				}, 2000);
			} else {
				throw new Error(result.message || 'Đăng ký thất bại');
			}
		} catch (error) {
			console.error('Register error:', error);
			this.showMessage(
				error.message || 'Có lỗi xảy ra khi đăng ký',
				'error'
			);
		} finally {
			this.setButtonLoading('registerBtn', false);
		}
	}

	/**
	 * Handle forgot password form submission
	 */
	async handleForgotPassword() {
		const form = document.getElementById('forgotPasswordForm');
		const email = form.email.value;

		// Validate email
		if (!this.isValidEmail(email)) {
			this.showFieldError('forgotEmail', 'Email không hợp lệ');
			return;
		}

		try {
			// Show loading
			this.setButtonLoading('forgotPasswordBtn', true);

			// Simulate API call
			const result = await this.sendPasswordReset(email);

			if (result.success) {
				// Hide form and show success message
				form.style.display = 'none';
				document.getElementById('successMessage').style.display =
					'block';
			} else {
				throw new Error(
					result.message || 'Không thể gửi email khôi phục'
				);
			}
		} catch (error) {
			console.error('Forgot password error:', error);
			this.showMessage(
				error.message || 'Có lỗi xảy ra khi gửi email',
				'error'
			);
		} finally {
			this.setButtonLoading('forgotPasswordBtn', false);
		}
	}

	/**
	 * Authenticate user (mock API)
	 */
	async authenticateUser(credentials) {
		// Simulate API delay
		await new Promise((resolve) => setTimeout(resolve, 1500));

		// Mock users database
		const mockUsers = {
			'customer@demo.com': {
				id: 1,
				email: 'customer@demo.com',
				password: 'password123',
				firstName: 'Nguyễn',
				lastName: 'Văn A',
				role: 'customer',
				avatar: '/5s-fashion/public/assets/images/avatar-customer.jpg',
			},
			'admin@demo.com': {
				id: 2,
				email: 'admin@demo.com',
				password: 'admin123',
				firstName: 'Admin',
				lastName: 'System',
				role: 'admin',
				avatar: '/5s-fashion/public/assets/images/avatar-admin.jpg',
			},
		};

		const user = mockUsers[credentials.email];

		if (!user || user.password !== credentials.password) {
			return {
				success: false,
				message: 'Email hoặc mật khẩu không chính xác',
			};
		}

		// Remove password from response
		const { password, ...userInfo } = user;

		return {
			success: true,
			user: userInfo,
			token: 'mock-jwt-token-' + Date.now(),
		};
	}

	/**
	 * Create new user (mock API)
	 */
	async createUser(userData) {
		// Simulate API delay
		await new Promise((resolve) => setTimeout(resolve, 2000));

		// Check if email already exists (mock)
		const existingEmails = ['existing@test.com', 'admin@demo.com'];
		if (existingEmails.includes(userData.email)) {
			return {
				success: false,
				message: 'Email đã được sử dụng, vui lòng chọn email khác',
			};
		}

		// Create new user
		const newUser = {
			id: Date.now(),
			email: userData.email,
			firstName: userData.firstName,
			lastName: userData.lastName,
			phone: userData.phone,
			birthDate: userData.birthDate,
			gender: userData.gender,
			role: 'customer',
			avatar: '/5s-fashion/public/assets/images/avatar-default.jpg',
			createdAt: new Date().toISOString(),
		};

		return {
			success: true,
			user: newUser,
			token: 'mock-jwt-token-' + Date.now(),
		};
	}

	/**
	 * Send password reset email (mock API)
	 */
	async sendPasswordReset(email) {
		// Simulate API delay
		await new Promise((resolve) => setTimeout(resolve, 1000));

		// Mock successful response
		return {
			success: true,
			message: 'Email khôi phục đã được gửi',
		};
	}

	/**
	 * Save user session
	 */
	saveUserSession(user, remember = false) {
		this.currentUser = user;
		this.isLoggedIn = true;

		// Save to localStorage/sessionStorage
		const storage = remember ? localStorage : sessionStorage;
		storage.setItem(
			'userSession',
			JSON.stringify({
				user: user,
				token: 'mock-token-' + Date.now(),
				loginTime: Date.now(),
			})
		);

		// Save credentials if remember me is checked
		if (remember) {
			localStorage.setItem('rememberedEmail', user.email);
		}

		// Update UI
		this.updateAuthUI();

		// Trigger login event
		window.dispatchEvent(new CustomEvent('userLoggedIn', { detail: user }));
	}

	/**
	 * Load user session
	 */
	loadUserSession() {
		try {
			// Try localStorage first, then sessionStorage
			let sessionData =
				localStorage.getItem('userSession') ||
				sessionStorage.getItem('userSession');

			if (sessionData) {
				const session = JSON.parse(sessionData);
				this.currentUser = session.user;
				this.isLoggedIn = true;
			} else {
				this.currentUser = null;
				this.isLoggedIn = false;
			}
		} catch (error) {
			console.error('Error loading user session:', error);
			this.currentUser = null;
			this.isLoggedIn = false;
		}
	}

	/**
	 * Load remembered credentials
	 */
	loadRememberedCredentials() {
		const rememberedEmail = localStorage.getItem('rememberedEmail');
		if (rememberedEmail) {
			const emailInput = document.getElementById('loginEmail');
			const rememberCheckbox = document.getElementById('rememberMe');

			if (emailInput) emailInput.value = rememberedEmail;
			if (rememberCheckbox) rememberCheckbox.checked = true;
		}
	}

	/**
	 * Logout user
	 */
	logout() {
		// Clear session data
		localStorage.removeItem('userSession');
		sessionStorage.removeItem('userSession');

		// Reset state
		this.currentUser = null;
		this.isLoggedIn = false;

		// Update UI
		this.updateAuthUI();

		// Trigger logout event
		window.dispatchEvent(new CustomEvent('userLoggedOut'));

		// Redirect to home
		window.location.href = '/';
	}

	/**
	 * Update authentication UI
	 */
	updateAuthUI() {
		// This would update header navigation, user menu, etc.
		// Implementation depends on your header structure
		console.log(
			'Auth UI updated:',
			this.isLoggedIn ? this.currentUser : 'Not logged in'
		);
	}

	/**
	 * Validate login form
	 */
	validateLoginForm(data) {
		let isValid = true;

		// Email validation
		if (!data.email.trim()) {
			this.showFieldError(
				'loginEmail',
				'Vui lòng nhập email hoặc số điện thoại'
			);
			isValid = false;
		}

		// Password validation
		if (!data.password.trim()) {
			this.showFieldError('loginPassword', 'Vui lòng nhập mật khẩu');
			isValid = false;
		}

		return isValid;
	}

	/**
	 * Validate register form
	 */
	validateRegisterForm(data) {
		let isValid = true;

		// First name validation
		if (!data.firstName.trim()) {
			this.showFieldError('firstName', 'Vui lòng nhập họ');
			isValid = false;
		}

		// Last name validation
		if (!data.lastName.trim()) {
			this.showFieldError('lastName', 'Vui lòng nhập tên');
			isValid = false;
		}

		// Email validation
		if (!data.email.trim()) {
			this.showFieldError('registerEmail', 'Vui lòng nhập email');
			isValid = false;
		} else if (!this.isValidEmail(data.email)) {
			this.showFieldError('registerEmail', 'Email không hợp lệ');
			isValid = false;
		}

		// Phone validation
		if (!data.phone.trim()) {
			this.showFieldError('registerPhone', 'Vui lòng nhập số điện thoại');
			isValid = false;
		} else if (!this.isValidPhone(data.phone)) {
			this.showFieldError('registerPhone', 'Số điện thoại không hợp lệ');
			isValid = false;
		}

		// Password validation
		if (!data.password.trim()) {
			this.showFieldError('registerPassword', 'Vui lòng nhập mật khẩu');
			isValid = false;
		} else if (data.password.length < 8) {
			this.showFieldError(
				'registerPassword',
				'Mật khẩu phải có ít nhất 8 ký tự'
			);
			isValid = false;
		}

		// Confirm password validation
		if (data.password !== data.confirmPassword) {
			this.showFieldError(
				'confirmPassword',
				'Mật khẩu xác nhận không khớp'
			);
			isValid = false;
		}

		// Terms agreement validation
		if (!data.agreeTerms) {
			this.showFieldError(
				'agreeTerms',
				'Vui lòng đồng ý với điều khoản sử dụng'
			);
			isValid = false;
		}

		return isValid;
	}

	/**
	 * Validate individual login field
	 */
	validateLoginField(field) {
		const value = field.value.trim();
		let isValid = true;
		let message = '';

		switch (field.id) {
			case 'loginEmail':
				if (!value) {
					isValid = false;
					message = 'Vui lòng nhập email hoặc số điện thoại';
				}
				break;
			case 'loginPassword':
				if (!value) {
					isValid = false;
					message = 'Vui lòng nhập mật khẩu';
				}
				break;
		}

		if (isValid) {
			this.clearFieldError(field.id);
		} else {
			this.showFieldError(field.id, message);
		}

		return isValid;
	}

	/**
	 * Validate individual register field
	 */
	validateRegisterField(field) {
		const value = field.value.trim();
		let isValid = true;
		let message = '';

		switch (field.id) {
			case 'firstName':
				if (!value) {
					isValid = false;
					message = 'Vui lòng nhập họ';
				}
				break;
			case 'lastName':
				if (!value) {
					isValid = false;
					message = 'Vui lòng nhập tên';
				}
				break;
			case 'registerEmail':
				if (!value) {
					isValid = false;
					message = 'Vui lòng nhập email';
				} else if (!this.isValidEmail(value)) {
					isValid = false;
					message = 'Email không hợp lệ';
				}
				break;
			case 'registerPhone':
				if (!value) {
					isValid = false;
					message = 'Vui lòng nhập số điện thoại';
				} else if (!this.isValidPhone(value)) {
					isValid = false;
					message = 'Số điện thoại không hợp lệ';
				}
				break;
			case 'registerPassword':
				if (!value) {
					isValid = false;
					message = 'Vui lòng nhập mật khẩu';
				} else if (value.length < 8) {
					isValid = false;
					message = 'Mật khẩu phải có ít nhất 8 ký tự';
				}
				this.updatePasswordStrength();
				break;
			case 'confirmPassword':
				const password =
					document.getElementById('registerPassword').value;
				if (value && value !== password) {
					isValid = false;
					message = 'Mật khẩu xác nhận không khớp';
				}
				break;
			case 'agreeTerms':
				if (!field.checked) {
					isValid = false;
					message = 'Vui lòng đồng ý với điều khoản sử dụng';
				}
				break;
		}

		if (isValid) {
			this.clearFieldError(field.id);
		} else {
			this.showFieldError(field.id, message);
		}

		return isValid;
	}

	/**
	 * Validate forgot password field
	 */
	validateForgotPasswordField(field) {
		const value = field.value.trim();
		let isValid = true;
		let message = '';

		if (!value) {
			isValid = false;
			message = 'Vui lòng nhập email';
		} else if (!this.isValidEmail(value)) {
			isValid = false;
			message = 'Email không hợp lệ';
		}

		if (isValid) {
			this.clearFieldError(field.id);
		} else {
			this.showFieldError(field.id, message);
		}

		return isValid;
	}

	/**
	 * Update password strength indicator
	 */
	updatePasswordStrength() {
		const passwordInput = document.getElementById('registerPassword');
		const strengthElement = document.getElementById('passwordStrength');

		if (!passwordInput || !strengthElement) return;

		const password = passwordInput.value;
		const strength = this.calculatePasswordStrength(password);

		const strengthFill = strengthElement.querySelector('.strength-fill');
		const strengthText = strengthElement.querySelector('.strength-text');

		// Update strength bar
		strengthFill.className = `strength-fill ${strength.level}`;
		strengthText.textContent = strength.text;
	}

	/**
	 * Calculate password strength
	 */
	calculatePasswordStrength(password) {
		if (password.length === 0) {
			return { level: '', text: 'Mật khẩu phải có ít nhất 8 ký tự' };
		}

		let score = 0;

		// Length check
		if (password.length >= 8) score++;
		if (password.length >= 12) score++;

		// Character variety checks
		if (/[a-z]/.test(password)) score++;
		if (/[A-Z]/.test(password)) score++;
		if (/[0-9]/.test(password)) score++;
		if (/[^A-Za-z0-9]/.test(password)) score++;

		if (score <= 2) {
			return { level: 'weak', text: 'Mật khẩu yếu' };
		} else if (score <= 3) {
			return { level: 'fair', text: 'Mật khẩu trung bình' };
		} else if (score <= 4) {
			return { level: 'good', text: 'Mật khẩu tốt' };
		} else {
			return { level: 'strong', text: 'Mật khẩu mạnh' };
		}
	}

	/**
	 * Validate password match
	 */
	validatePasswordMatch() {
		const passwordInput = document.getElementById('registerPassword');
		const confirmInput = document.getElementById('confirmPassword');

		if (!passwordInput || !confirmInput) return;

		const password = passwordInput.value;
		const confirm = confirmInput.value;

		if (confirm && password !== confirm) {
			this.showFieldError(
				'confirmPassword',
				'Mật khẩu xác nhận không khớp'
			);
		} else if (confirm) {
			this.clearFieldError('confirmPassword');
		}
	}

	/**
	 * Show field error
	 */
	showFieldError(fieldId, message) {
		const field = document.getElementById(fieldId);
		if (!field) return;

		field.classList.add('is-invalid');
		field.classList.remove('is-valid');

		// Find or create feedback element
		let feedback = field.parentNode.querySelector('.invalid-feedback');
		if (!feedback) {
			feedback = document.createElement('div');
			feedback.className = 'invalid-feedback';
			field.parentNode.appendChild(feedback);
		}

		feedback.textContent = message;
	}

	/**
	 * Clear field error
	 */
	clearFieldError(fieldId) {
		const field = document.getElementById(fieldId);
		if (!field) return;

		field.classList.remove('is-invalid');
		field.classList.add('is-valid');

		const feedback = field.parentNode.querySelector('.invalid-feedback');
		if (feedback) {
			feedback.textContent = '';
		}
	}

	/**
	 * Set button loading state
	 */
	setButtonLoading(buttonId, isLoading) {
		const button = document.getElementById(buttonId);
		if (!button) return;

		if (isLoading) {
			button.disabled = true;
			const originalText = button.innerHTML;
			button.dataset.originalText = originalText;
			button.innerHTML =
				'<span class="loading-spinner"></span>Đang xử lý...';
		} else {
			button.disabled = false;
			button.innerHTML = button.dataset.originalText || button.innerHTML;
		}
	}

	/**
	 * Validate email format
	 */
	isValidEmail(email) {
		const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
		return emailRegex.test(email);
	}

	/**
	 * Validate phone number
	 */
	isValidPhone(phone) {
		const phoneRegex = /^[0-9]{10,11}$/;
		return phoneRegex.test(phone.replace(/[^0-9]/g, ''));
	}

	/**
	 * Show message to user
	 */
	showMessage(message, type = 'info') {
		// Create message element
		const messageEl = document.createElement('div');
		messageEl.className = `alert alert-${
			type === 'error' ? 'danger' : 'success'
		} position-fixed`;
		messageEl.style.cssText = `
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 350px;
            animation: slideInRight 0.3s ease;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        `;

		messageEl.innerHTML = `
            <i class="fas fa-${
				type === 'success' ? 'check-circle' : 'exclamation-circle'
			} me-2"></i>
            ${message}
        `;

		document.body.appendChild(messageEl);

		// Auto remove after 4 seconds
		setTimeout(() => {
			messageEl.style.animation = 'slideOutRight 0.3s ease';
			setTimeout(() => {
				if (messageEl.parentNode) {
					messageEl.parentNode.removeChild(messageEl);
				}
			}, 300);
		}, 4000);
	}
}

// Global functions
window.togglePassword = function (inputId) {
	const input = document.getElementById(inputId);
	const toggle = input.nextElementSibling;
	const icon = toggle.querySelector('i');

	if (input.type === 'password') {
		input.type = 'text';
		icon.className = 'fas fa-eye-slash';
	} else {
		input.type = 'password';
		icon.className = 'fas fa-eye';
	}
};

window.quickLogin = function (type) {
	const emailInput = document.getElementById('loginEmail');
	const passwordInput = document.getElementById('loginPassword');

	if (type === 'customer') {
		emailInput.value = 'customer@demo.com';
		passwordInput.value = 'password123';
	} else if (type === 'admin') {
		emailInput.value = 'admin@demo.com';
		passwordInput.value = 'admin123';
	}

	// Auto-submit form
	setTimeout(() => {
		document.getElementById('loginForm').dispatchEvent(new Event('submit'));
	}, 500);
};

window.loginWithGoogle = function () {
	alert(
		'Tính năng đăng nhập Google sẽ được triển khai trong phiên bản tiếp theo'
	);
};

window.loginWithFacebook = function () {
	alert(
		'Tính năng đăng nhập Facebook sẽ được triển khai trong phiên bản tiếp theo'
	);
};

window.registerWithGoogle = function () {
	alert(
		'Tính năng đăng ký Google sẽ được triển khai trong phiên bản tiếp theo'
	);
};

window.registerWithFacebook = function () {
	alert(
		'Tính năng đăng ký Facebook sẽ được triển khai trong phiên bản tiếp theo'
	);
};

window.resendEmail = function () {
	const email = document.getElementById('forgotEmail').value;
	if (window.authManager) {
		window.authManager.sendPasswordReset(email);
	}
};

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);
