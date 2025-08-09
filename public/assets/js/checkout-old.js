/**
 * Checkout Manager - Phase 3.3
 * Handles checkout process including form validation, order processing, and payment
 */

class CheckoutManager {
	constructor() {
		this.cart = [];
		this.orderData = {};
		this.promoCode = null;
		this.discountAmount = 0;
		this.shippingFee = 30000; // Default shipping fee

		this.initializeEventListeners();
	}

	/**
	 * Initialize event listeners
	 */
	initializeEventListeners() {
		// Shipping method change
		document
			.querySelectorAll('input[name="shippingMethod"]')
			.forEach((input) => {
				input.addEventListener('change', () => {
					this.updateShippingFee();
				});
			});

		// Payment method change
		document
			.querySelectorAll('input[name="paymentMethod"]')
			.forEach((input) => {
				input.addEventListener('change', () => {
					this.updatePaymentMethod();
				});
			});

		// Form validation on input
		const requiredFields = document.querySelectorAll(
			'input[required], select[required]'
		);
		requiredFields.forEach((field) => {
			field.addEventListener('blur', () => {
				this.validateField(field);
			});
		});

		// Initialize real-time validation
		this.initializeFormValidation();

		// Check payment status from URL (for payment gateway returns)
		this.checkPaymentStatusFromURL();
	}

	/**
	 * Load order from cart
	 */
	loadOrder() {
		try {
			// Debug: log server cart data
			console.log('Server cart data:', window.serverCartData);

			// Use server cart data if available, fallback to localStorage
			if (
				window.serverCartData &&
				window.serverCartData.items.length > 0
			) {
				console.log(
					'Using server cart data:',
					window.serverCartData.items
				);
				this.cart = window.serverCartData.items;
			} else {
				// Fallback to localStorage for backward compatibility
				console.log('Falling back to localStorage cart');
				const savedCart = localStorage.getItem('cart');
				this.cart = savedCart ? JSON.parse(savedCart) : [];
				console.log('localStorage cart:', this.cart);
			}

			console.log('Final cart data:', this.cart);
			console.log('Cart length:', this.cart.length);

			if (this.cart.length === 0) {
				console.log('Cart is empty, showing error message');
				this.showMessage(
					'Giỏ hàng trống. Vui lòng thêm sản phẩm trước khi thanh toán.',
					'error'
				);
				setTimeout(() => {
					window.location.href = '/5s-fashion/cart';
				}, 2000);
				return;
			}

			// Load saved promo code
			const savedPromo = localStorage.getItem('promoCode');
			if (savedPromo) {
				const promoData = JSON.parse(savedPromo);
				this.promoCode = promoData.code;
				this.discountAmount = promoData.discount;
				document.getElementById('checkoutPromoCode').value =
					promoData.code;
			}

			this.displayOrderItems();
			this.updateOrderSummary();
		} catch (error) {
			console.error('Error loading order:', error);
			this.showMessage('Có lỗi xảy ra khi tải đơn hàng', 'error');
		}
	}

	/**
	 * Display order items
	 */
	displayOrderItems() {
		const container = document.getElementById('orderItems');
		const template = document.getElementById('orderItemTemplate').innerHTML;
		let html = '';

		this.cart.forEach((item) => {
			// Format image URL using serve-file.php
			let imageUrl = '/5s-fashion/public/assets/images/placeholder.jpg';
			if (item.image) {
				// Remove leading slash and uploads/products/ prefix if present
				let imagePath = item.image
					.replace(/^\/+/, '')
					.replace(/^uploads\/products\//, '');
				imageUrl = `/5s-fashion/serve-file.php?file=${encodeURIComponent(
					'products/' + imagePath
				)}`;
			}

			let itemHTML = template
				.replace(/\{\{image\}\}/g, imageUrl)
				.replace(/\{\{name\}\}/g, item.name)
				.replace(/\{\{quantity\}\}/g, item.quantity)
				.replace(/\{\{variant\}\}/g, this.formatVariant(item))
				.replace(
					/\{\{totalPrice\}\}/g,
					this.formatPrice(item.price * item.quantity)
				);

			html += itemHTML;
		});

		container.innerHTML = html;
	}

	/**
	 * Update order summary
	 */
	updateOrderSummary() {
		const subtotal = this.getSubtotal();
		const shipping = this.getShippingFee();
		const total = this.getTotal();

		document.getElementById('orderSubtotal').textContent =
			this.formatPrice(subtotal);
		document.getElementById('orderShipping').textContent =
			this.formatPrice(shipping);
		document.getElementById('orderTotal').textContent =
			this.formatPrice(total);

		// Update discount display
		const discountRow = document.getElementById('orderDiscountRow');
		if (this.discountAmount > 0) {
			discountRow.style.display = 'flex';
			document.getElementById('orderDiscount').textContent =
				'-' + this.formatPrice(this.discountAmount);
		} else {
			discountRow.style.display = 'none';
		}
	}

	/**
	 * Initialize form with saved data
	 */
	initializeForm() {
		// Load saved customer data from localStorage if available
		const savedCustomer = localStorage.getItem('customerInfo');
		if (savedCustomer) {
			const customerData = JSON.parse(savedCustomer);
			Object.keys(customerData).forEach((key) => {
				const field = document.getElementById(key);
				if (field) {
					field.value = customerData[key];
				}
			});
		}
	}

	/**
	 * Update shipping fee based on selected method
	 */
	updateShippingFee() {
		const selectedMethod = document.querySelector(
			'input[name="shippingMethod"]:checked'
		);
		if (!selectedMethod) return;

		switch (selectedMethod.value) {
			case 'standard':
				this.shippingFee = 30000;
				break;
			case 'express':
				this.shippingFee = 50000;
				break;
			default:
				this.shippingFee = 30000;
		}

		this.updateOrderSummary();
	}

	/**
	 * Update payment method
	 */
	updatePaymentMethod() {
		const selectedMethod = document.querySelector(
			'input[name="paymentMethod"]:checked'
		);
		console.log('Payment method changed:', selectedMethod?.value);
	}

	/**
	 * Apply promo code for checkout
	 */
	async applyCheckoutPromoCode() {
		const promoCodeInput = document.getElementById('checkoutPromoCode');
		const code = promoCodeInput.value.trim().toUpperCase();

		if (!code) {
			this.showMessage('Vui lòng nhập mã giảm giá', 'error');
			return;
		}

		try {
			// Show loading
			const button = promoCodeInput.nextElementSibling;
			const originalText = button.textContent;
			button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
			button.disabled = true;

			// Validate promo code
			const response = await this.validatePromoCode(code);

			if (response.valid) {
				this.promoCode = code;
				this.discountAmount = response.discount;
				localStorage.setItem(
					'promoCode',
					JSON.stringify({
						code: code,
						discount: response.discount,
					})
				);

				this.updateOrderSummary();
				this.showMessage(
					`Đã áp dụng mã giảm giá ${code}! Giảm ${this.formatPrice(
						response.discount
					)}`,
					'success'
				);
			} else {
				this.showMessage(
					response.message || 'Mã giảm giá không hợp lệ',
					'error'
				);
			}

			// Restore button
			button.textContent = originalText;
			button.disabled = false;
		} catch (error) {
			console.error('Error applying promo code:', error);
			this.showMessage('Có lỗi xảy ra khi áp dụng mã giảm giá', 'error');
		}
	}

	/**
	 * Validate promo code
	 */
	async validatePromoCode(code) {
		// Simulate API delay
		await new Promise((resolve) => setTimeout(resolve, 1000));

		// Mock promo codes
		const promoCodes = {
			WELCOME10: { discount: 50000, minOrder: 200000 },
			SALE20: { discount: 100000, minOrder: 300000 },
			FREESHIP: { discount: 30000, minOrder: 150000 },
			VIP30: { discount: 150000, minOrder: 500000 },
		};

		const promo = promoCodes[code];
		if (!promo) {
			return { valid: false, message: 'Mã giảm giá không tồn tại' };
		}

		const subtotal = this.getSubtotal();
		if (subtotal < promo.minOrder) {
			return {
				valid: false,
				message: `Đơn hàng tối thiểu ${this.formatPrice(
					promo.minOrder
				)} để sử dụng mã này`,
			};
		}

		return { valid: true, discount: promo.discount };
	}

	/**
	 * Validate form field
	 */
	validateField(field) {
		const value = field.value.trim();
		const fieldName = field.name || field.id;
		let isValid = true;
		let message = '';

		console.log(`Validating field ${fieldName}:`, {
			value: value,
			required: field.hasAttribute('required'),
			type: field.type,
			visible: field.offsetParent !== null,
		});

		// Remove existing validation classes
		field.classList.remove('is-invalid');
		const existingFeedback =
			field.parentNode.querySelector('.invalid-feedback');
		if (existingFeedback) {
			existingFeedback.remove();
		}

		// Required validation
		if (field.hasAttribute('required') && !value) {
			isValid = false;
			message = 'Trường này là bắt buộc';
			console.log(`Field ${fieldName} failed: required but empty`);
		} else {
			// Specific field validations
			switch (fieldName) {
				case 'customerName':
					if (value.length < 2) {
						isValid = false;
						message = 'Họ tên phải có ít nhất 2 ký tự';
					}
					break;
				case 'customerPhone':
					if (!this.isValidPhone(value)) {
						isValid = false;
						message = 'Số điện thoại không hợp lệ (10-11 số)';
					}
					break;
				case 'customerEmail':
					if (value && !this.isValidEmail(value)) {
						isValid = false;
						message = 'Email không hợp lệ';
					}
					break;
			}

			// Type-specific validations
			switch (field.type) {
				case 'email':
					if (value && !this.isValidEmail(value)) {
						isValid = false;
						message = 'Email không hợp lệ';
					}
					break;
				case 'tel':
					if (value && !this.isValidPhone(value)) {
						isValid = false;
						message = 'Số điện thoại không hợp lệ';
					}
					break;
			}
		}

		// Show validation message
		if (!isValid) {
			field.classList.add('is-invalid');
			const feedback = document.createElement('div');
			feedback.className = 'invalid-feedback';
			feedback.textContent = message;
			field.parentNode.appendChild(feedback);
		}

		return isValid;
	}

	/**
	 * Validate entire form
	 */
	validateForm() {
		console.log('=== FORM VALIDATION DEBUG ===');
		const form = document.getElementById('checkoutForm');
		const requiredFields = form.querySelectorAll(
			'input[required], select[required]'
		);
		let isValid = true;
		let failedFields = [];

		console.log('Total required fields:', requiredFields.length);

		requiredFields.forEach((field) => {
			const fieldValid = this.validateField(field);
			console.log(`Field ${field.id || field.name} (${field.type}):`, {
				value: field.value,
				required: field.required,
				valid: fieldValid,
			});

			if (!fieldValid) {
				isValid = false;
				failedFields.push({
					id: field.id || field.name,
					type: field.type,
					value: field.value,
					placeholder: field.placeholder,
				});
			}
		});

		console.log('Failed fields:', failedFields);

		// Additional validations
		const paymentMethod = document.querySelector(
			'input[name="paymentMethod"]:checked'
		);
		console.log('Payment method selected:', paymentMethod?.value || 'none');
		if (!paymentMethod) {
			this.showMessage('Vui lòng chọn phương thức thanh toán', 'error');
			isValid = false;
		}

		// Check if address is selected or filled
		const addressSelected = document.querySelector(
			'input[name="addressOption"]:checked'
		);
		const newAddress = document.getElementById('newAddress')?.value?.trim();

		console.log('Address validation:', {
			addressSelected: addressSelected?.value || 'none',
			newAddress: newAddress || 'empty',
		});

		// For checkout, address selection is mandatory
		if (!addressSelected && !newAddress) {
			this.showMessage('Vui lòng chọn địa chỉ giao hàng', 'error');
			isValid = false;
		}

		console.log('Form validation result:', isValid);
		console.log('=== END VALIDATION DEBUG ===');

		return isValid;
	}

	/**
	 * Place order
	 */
	async placeOrder() {
		console.log('=== PLACE ORDER DEBUG START ===');

		// Validate form
		console.log('Step 1: Form validation');
		if (!this.validateForm()) {
			console.log('Form validation failed - stopping');
			this.showMessage(
				'Vui lòng điền đầy đủ thông tin bắt buộc',
				'error'
			);
			return;
		}
		console.log('Form validation passed');

		// Validate address selection
		console.log('Step 2: Address manager validation');
		if (window.addressManager) {
			const selectedAddress = window.addressManager.getSelectedAddress();
			console.log('Address manager result:', selectedAddress);
			if (!selectedAddress) {
				console.log('Address manager validation failed - stopping');
				this.showMessage('Vui lòng chọn địa chỉ giao hàng', 'error');
				return;
			}
		} else {
			console.log('No address manager found - skipping');
		}

		try {
			console.log('Step 3: Collecting order data');

			// Show loading
			this.showLoadingOverlay('Đang xử lý đơn hàng...');

			// Collect order data
			this.orderData = this.collectOrderData();
			console.log('Order data collected:', this.orderData);

			if (!this.orderData) {
				console.log('Order data collection failed - stopping');
				this.hideLoadingOverlay();
				return;
			}

			console.log('Step 4: Saving customer info to localStorage');
			// Save customer info for future use
			localStorage.setItem(
				'customerInfo',
				JSON.stringify({
					fullName: this.orderData.customer.name,
					phone: this.orderData.customer.phone,
					email: this.orderData.customer.email,
				})
			);

			console.log('Step 5: Submitting order to server');
			console.log('API endpoint: /5s-fashion/public/order/place');
			console.log('Order data being sent:', this.orderData);
			console.log(
				'Order data JSON:',
				JSON.stringify(this.orderData, null, 2)
			);

			// Submit order to server
			const response = await fetch('/5s-fashion/public/order/place', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-Requested-With': 'XMLHttpRequest',
				},
				body: JSON.stringify(this.orderData),
			});

			console.log('API response status:', response.status);
			console.log(
				'API response headers:',
				Object.fromEntries(response.headers.entries())
			);

			const result = await response.json();
			console.log('API response data:', result);

			// Hide loading
			this.hideLoadingOverlay();

			if (result.success) {
				console.log('Order placed successfully!');
				// Store order info for success page
				localStorage.setItem(
					'lastOrderData',
					JSON.stringify({
						order_code: result.order_code,
						total_amount: this.orderData.total,
						payment_method: this.orderData.payment.method,
					})
				);
				localStorage.setItem('lastOrderId', result.order_id);

				// Handle different payment methods
				if (result.requires_payment && result.payment_url) {
					// Online payment - redirect to payment gateway
					this.showMessage(
						'Đang chuyển hướng đến cổng thanh toán...',
						'success'
					);

					// For online payment, make another request to get payment URL
					const paymentResponse = await fetch(result.payment_url, {
						method: 'POST',
						headers: {
							'Content-Type': 'application/json',
							'X-Requested-With': 'XMLHttpRequest',
						},
						body: JSON.stringify({
							order_id: result.order_id,
						}),
					});

					const paymentResult = await paymentResponse.json();

					if (paymentResult.success && paymentResult.redirect_url) {
						// Clear cart before redirect to payment
						localStorage.removeItem('cart');
						localStorage.removeItem('promoCode');

						// Redirect to payment gateway
						window.location.href = paymentResult.redirect_url;
					} else {
						this.showMessage(
							'Không thể khởi tạo thanh toán. Vui lòng thử lại.',
							'error'
						);
					}
				} else {
					// COD or bank transfer - direct to success page
					if (result.bank_info) {
						// Store bank info for success page
						localStorage.setItem(
							'bankInfo',
							JSON.stringify(result.bank_info)
						);
					}

					// Clear cart
					localStorage.removeItem('cart');
					localStorage.removeItem('promoCode');

					// Show success message
					this.showMessage(
						'Đặt hàng thành công! Đang chuyển hướng...',
						'success'
					);

					// Redirect to success page
					setTimeout(() => {
						window.location.href =
							result.redirect_url ||
							`/5s-fashion/public/order/success?id=${result.order_id}`;
					}, 2000);
				}
			} else {
				this.showMessage(
					result.message || 'Có lỗi xảy ra khi đặt hàng',
					'error'
				);
			}
		} catch (error) {
			console.error('Error placing order:', error);
			this.hideLoadingOverlay();
			this.showMessage(
				error.message || 'Có lỗi xảy ra khi đặt hàng',
				'error'
			);
		}
	}

	/**
	 * Collect order data from form
	 */
	collectOrderData() {
		// Get shipping method
		const shippingMethod =
			document.querySelector('input[name="shippingMethod"]:checked')
				?.value || 'standard';

		// Get payment method
		const paymentMethod =
			document.querySelector('input[name="paymentMethod"]:checked')
				?.value || 'cod';

		// Get order notes
		const orderNotes =
			document.getElementById('orderNotes')?.value.trim() || '';

		// Get address from address manager
		const selectedAddress = window.addressManager?.getSelectedAddress();
		if (!selectedAddress) {
			this.showMessage('Vui lòng chọn địa chỉ giao hàng', 'error');
			return null;
		}

		// Map cart items to order format
		const orderItems = this.cart.map((item) => ({
			product_id: item.product_id || item.cart_key,
			variant_id: item.variant_id || null,
			name: item.name || item.product_name,
			sku: item.sku || item.product_sku || '',
			variant: item.variant,
			quantity: item.quantity,
			price: item.price,
			total: item.price * item.quantity,
		}));

		return {
			items: orderItems,
			customer: {
				name: selectedAddress.name,
				phone: selectedAddress.phone,
				email: '', // Can be added later if needed
			},
			shipping: {
				method: shippingMethod,
				fee: this.getShippingFee(),
				address: selectedAddress.address, // Fix: Move address to shipping
				name: selectedAddress.name,
				phone: selectedAddress.phone,
			},
			payment: {
				method: paymentMethod,
			},
			orderNotes: orderNotes,
			subtotal: this.getSubtotal(),
			total: this.getTotal(),
			discount: this.discountAmount,
			promoCode: this.promoCode,
			orderDate: new Date().toISOString(),
		};
	}

	/**
	 * Process order
	 */
	async processOrder() {
		// Simulate API delay
		await new Promise((resolve) => setTimeout(resolve, 2000));

		// Generate order code
		const orderCode = 'ORD' + Date.now();

		// Mock successful order
		return {
			success: true,
			orderCode: orderCode,
			message: 'Đặt hàng thành công',
		};
	}

	/**
	 * Show loading overlay
	 */
	showLoadingOverlay(message = 'Đang xử lý...') {
		const overlay = document.createElement('div');
		overlay.className = 'loading-overlay';
		overlay.id = 'loadingOverlay';
		overlay.innerHTML = `
            <div class="loading-content">
                <div class="loading-spinner"></div>
                <div class="loading-message">${message}</div>
            </div>
        `;
		document.body.appendChild(overlay);
	}

	/**
	 * Hide loading overlay
	 */
	hideLoadingOverlay() {
		const overlay = document.getElementById('loadingOverlay');
		if (overlay) {
			overlay.remove();
		}
	}

	/**
	 * Calculate subtotal
	 */
	getSubtotal() {
		return this.cart.reduce(
			(total, item) => total + item.price * item.quantity,
			0
		);
	}

	/**
	 * Get shipping fee
	 */
	getShippingFee() {
		const subtotal = this.getSubtotal();
		// Free shipping for orders over 500k
		return subtotal >= 500000 ? 0 : this.shippingFee;
	}

	/**
	 * Calculate total
	 */
	getTotal() {
		return this.getSubtotal() + this.getShippingFee() - this.discountAmount;
	}

	/**
	 * Format variant display
	 */
	formatVariant(item) {
		const parts = [];
		if (item.size) parts.push(`Size: ${item.size}`);
		if (item.color) parts.push(`Màu: ${item.color}`);
		return parts.join(' | ') || 'Mặc định';
	}

	/**
	 * Format price
	 */
	formatPrice(price) {
		return new Intl.NumberFormat('vi-VN', {
			style: 'currency',
			currency: 'VND',
		}).format(price);
	}

	/**
	 * Validate email
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
		messageEl.className = `alert ${
			type === 'error' ? 'error-message' : 'success-message'
		} position-fixed`;
		messageEl.style.cssText = `
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            animation: slideInRight 0.3s ease;
        `;

		messageEl.innerHTML = `
            <i class="fas fa-${
				type === 'success' ? 'check-circle' : 'exclamation-circle'
			} me-2"></i>
            ${message}
        `;

		document.body.appendChild(messageEl);

		// Auto remove after 5 seconds
		setTimeout(() => {
			messageEl.style.animation = 'slideOutRight 0.3s ease';
			setTimeout(() => {
				if (messageEl.parentNode) {
					messageEl.parentNode.removeChild(messageEl);
				}
			}, 300);
		}, 5000);
	}

	/**
	 * Handle payment failure
	 */
	handlePaymentFailure(error) {
		let errorMessage = 'Thanh toán không thành công. ';

		switch (error) {
			case 'payment_failed':
				errorMessage +=
					'Giao dịch bị từ chối. Vui lòng kiểm tra lại thông tin thanh toán.';
				break;
			case 'payment_cancelled':
				errorMessage += 'Bạn đã hủy giao dịch.';
				break;
			case 'payment_timeout':
				errorMessage += 'Giao dịch bị hết thời gian. Vui lòng thử lại.';
				break;
			case 'invalid_signature':
				errorMessage += 'Lỗi bảo mật. Vui lòng thử lại sau.';
				break;
			case 'order_not_found':
				errorMessage += 'Không tìm thấy đơn hàng.';
				break;
			case 'insufficient_balance':
				errorMessage += 'Tài khoản không đủ số dư.';
				break;
			case 'card_declined':
				errorMessage +=
					'Thẻ bị từ chối. Vui lòng kiểm tra thông tin thẻ.';
				break;
			case 'network_error':
				errorMessage +=
					'Lỗi kết nối mạng. Vui lòng kiểm tra kết nối internet.';
				break;
			default:
				errorMessage +=
					'Vui lòng thử lại hoặc chọn phương thức thanh toán khác.';
		}

		this.showMessage(errorMessage, 'error');

		// Re-enable form elements
		this.enableFormElements();

		// Reset loading states
		const placeOrderBtn = document.getElementById('placeOrderBtn');
		if (placeOrderBtn) {
			placeOrderBtn.innerHTML = 'Đặt hàng';
			placeOrderBtn.disabled = false;
		}
	}

	/**
	 * Confirm payment method change
	 */
	confirmPaymentMethodChange(newMethod, oldMethod) {
		if (oldMethod && oldMethod !== newMethod) {
			const methodNames = {
				cod: 'Thanh toán khi nhận hàng',
				vnpay: 'VNPay',
				momo: 'MoMo',
				bank_transfer: 'Chuyển khoản ngân hàng',
			};

			return confirm(
				`Bạn có muốn thay đổi phương thức thanh toán từ "${methodNames[oldMethod]}" sang "${methodNames[newMethod]}" không?`
			);
		}
		return true;
	}

	/**
	 * Enable/disable form elements
	 */
	enableFormElements(enable = true) {
		const formElements = document.querySelectorAll(
			'#checkoutForm input, #checkoutForm select, #checkoutForm button'
		);
		formElements.forEach((element) => {
			element.disabled = !enable;
		});
	}

	/**
	 * Check payment status from URL parameters
	 */
	checkPaymentStatusFromURL() {
		const urlParams = new URLSearchParams(window.location.search);
		const status = urlParams.get('status');
		const error = urlParams.get('error');
		const orderId = urlParams.get('orderId');

		if (status === 'success' && orderId) {
			// Payment successful - redirect to success page
			window.location.href = `/5s-fashion/app/views/client/order/success.php?orderId=${orderId}`;
		} else if (status === 'failed' || error) {
			// Payment failed
			this.handlePaymentFailure(error || 'payment_failed');

			// Clean up URL
			const cleanUrl = window.location.pathname;
			window.history.replaceState({}, document.title, cleanUrl);
		}
	}

	/**
	 * Initialize real-time form validation
	 */
	initializeFormValidation() {
		const fields = document.querySelectorAll(
			'#checkoutForm input, #checkoutForm select, #checkoutForm textarea'
		);

		fields.forEach((field) => {
			// Validate on blur
			field.addEventListener('blur', () => {
				if (field.value.trim() || field.hasAttribute('required')) {
					this.validateField(field);
				}
			});

			// Clear errors on input
			field.addEventListener('input', () => {
				field.classList.remove('is-invalid');
				const errorElement =
					field.parentNode.querySelector('.invalid-feedback');
				if (errorElement) {
					errorElement.remove();
				}
			});
		});

		// Payment method change validation
		document
			.querySelectorAll('input[name="paymentMethod"]')
			.forEach((radio) => {
				radio.addEventListener('change', (e) => {
					const oldMethod = this.orderData?.payment?.method;
					const newMethod = e.target.value;

					if (
						!this.confirmPaymentMethodChange(newMethod, oldMethod)
					) {
						// Revert to old method
						if (oldMethod) {
							const oldRadio = document.querySelector(
								`input[value="${oldMethod}"]`
							);
							if (oldRadio) oldRadio.checked = true;
						}
						e.preventDefault();
						return false;
					}

					this.updatePaymentMethod();
				});
			});
	}
}

// Global functions
window.applyCheckoutPromoCode = function () {
	if (window.checkoutManager) {
		window.checkoutManager.applyCheckoutPromoCode();
	}
};

window.placeOrder = function () {
	if (window.checkoutManager) {
		window.checkoutManager.placeOrder();
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

/**
 * Checkout Address Manager
 * Handles address management for checkout
 */
class CheckoutAddressManager {
    constructor() {
        this.provinces = [];
        this.districts = [];
        this.wards = [];
        this.map = null;
        this.marker = null;
        this.selectedLocation = null;
    }

    async showAddressModal(addressId = null) {
        const modal = new bootstrap.Modal(document.getElementById('addressModal'));
        
        if (addressId) {
            // Edit mode
            const address = checkoutManager.addresses.find(addr => addr.id === addressId);
            if (address) {
                this.populateAddressForm(address);
            }
        } else {
            // Add new mode
            document.getElementById('addressForm').reset();
        }

        // Load provinces if not loaded
        if (this.provinces.length === 0) {
            await this.loadProvinces();
        }

        modal.show();
    }

    async loadProvinces() {
        try {
            const response = await fetch('https://provinces.open-api.vn/api/p/');
            if (response.ok) {
                const data = await response.json();
                this.provinces = data;
                this.populateProvinceSelect();
            }
        } catch (error) {
            console.error('Error loading provinces:', error);
        }
    }

    populateProvinceSelect() {
        const select = document.querySelector('select[name="province"]');
        if (!select) return;

        select.innerHTML = '<option value="">Chọn tỉnh/thành</option>';
        this.provinces.forEach(province => {
            select.innerHTML += `<option value="${province.code}" data-name="${province.name}">${province.name}</option>`;
        });

        select.addEventListener('change', (e) => this.loadDistricts(e.target.value));
    }

    async loadDistricts(provinceCode) {
        if (!provinceCode) return;

        try {
            const response = await fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`);
            if (response.ok) {
                const data = await response.json();
                this.districts = data.districts || [];
                this.populateDistrictSelect();
            }
        } catch (error) {
            console.error('Error loading districts:', error);
        }
    }

    populateDistrictSelect() {
        const select = document.querySelector('select[name="district"]');
        if (!select) return;

        select.innerHTML = '<option value="">Chọn quận/huyện</option>';
        this.districts.forEach(district => {
            select.innerHTML += `<option value="${district.code}" data-name="${district.name}">${district.name}</option>`;
        });

        select.addEventListener('change', (e) => this.loadWards(e.target.value));
    }

    async loadWards(districtCode) {
        if (!districtCode) return;

        try {
            const response = await fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`);
            if (response.ok) {
                const data = await response.json();
                this.wards = data.wards || [];
                this.populateWardSelect();
            }
        } catch (error) {
            console.error('Error loading wards:', error);
        }
    }

    populateWardSelect() {
        const select = document.querySelector('select[name="ward"]');
        if (!select) return;

        select.innerHTML = '<option value="">Chọn phường/xã</option>';
        this.wards.forEach(ward => {
            select.innerHTML += `<option value="${ward.code}" data-name="${ward.name}">${ward.name}</option>`;
        });
    }

    async saveAddress() {
        const form = document.getElementById('addressForm');
        const formData = new FormData(form);

        // Get selected names (not just codes)
        const provinceSelect = form.querySelector('select[name="province"]');
        const districtSelect = form.querySelector('select[name="district"]');
        const wardSelect = form.querySelector('select[name="ward"]');

        const addressData = {
            full_name: formData.get('full_name'),
            phone: formData.get('phone'),
            province: provinceSelect.options[provinceSelect.selectedIndex]?.dataset.name || '',
            district: districtSelect.options[districtSelect.selectedIndex]?.dataset.name || '',
            ward: wardSelect.options[wardSelect.selectedIndex]?.dataset.name || '',
            address_line: formData.get('address_line'),
            address_type: formData.get('address_type'),
            is_default: formData.get('is_default') ? 1 : 0
        };

        try {
            const response = await fetch('/5s-fashion/order/addAddress', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(addressData)
            });

            if (response.ok) {
                const result = await response.json();
                if (result.success) {
                    // Close modal and reload addresses
                    bootstrap.Modal.getInstance(document.getElementById('addressModal')).hide();
                    await checkoutManager.loadAddresses();
                    checkoutManager.renderCheckoutForm();
                } else {
                    alert(result.message || 'Không thể lưu địa chỉ');
                }
            } else {
                alert('Có lỗi xảy ra khi lưu địa chỉ');
            }
        } catch (error) {
            console.error('Error saving address:', error);
            alert('Có lỗi xảy ra khi lưu địa chỉ');
        }
    }

    async editAddress(addressId) {
        // Implementation for editing existing address
        await this.showAddressModal(addressId);
    }

    async deleteAddress(addressId) {
        if (!confirm('Bạn có chắc chắn muốn xóa địa chỉ này?')) return;

        try {
            const response = await fetch(`/5s-fashion/order/deleteAddress/${addressId}`, {
                method: 'DELETE'
            });

            if (response.ok) {
                const result = await response.json();
                if (result.success) {
                    alert(result.message);
                    await checkoutManager.loadAddresses();
                    checkoutManager.renderCheckoutForm();
                } else {
                    alert(result.message || 'Không thể xóa địa chỉ');
                }
            } else {
                alert('Có lỗi xảy ra khi xóa địa chỉ');
            }
        } catch (error) {
            alert('Có lỗi xảy ra khi xóa địa chỉ');
        }
    }

    async setDefaultAddress(id) {
        try {
            const response = await fetch(`/5s-fashion/order/setDefaultAddress/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ is_default: true })
            });

            if (response.ok) {
                const result = await response.json();
                if (result.success) {
                    alert(result.message);
                    window.location.reload();
                } else {
                    alert(result.message || 'Không thể đặt địa chỉ mặc định');
                }
            } else {
                alert('Có lỗi xảy ra khi đặt địa chỉ mặc định');
            }
        } catch (error) {
            alert('Có lỗi xảy ra khi đặt địa chỉ mặc định');
        }
    }

    populateAddressForm(address) {
        const form = document.getElementById('addressForm');
        if (!form) return;

        form.querySelector('[name="full_name"]').value = address.full_name || '';
        form.querySelector('[name="phone"]').value = address.phone || '';
        form.querySelector('[name="address_line"]').value = address.address_line || '';
        
        // Set address type
        const addressType = form.querySelector(`[name="address_type"][value="${address.address_type}"]`);
        if (addressType) addressType.checked = true;

        // Set default checkbox
        const isDefault = form.querySelector('[name="is_default"]');
        if (isDefault) isDefault.checked = address.is_default == 1;
    }
}
