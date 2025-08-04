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
	}

	/**
	 * Load order from cart
	 */
	loadOrder() {
		try {
			// Load cart from localStorage
			const savedCart = localStorage.getItem('cart');
			this.cart = savedCart ? JSON.parse(savedCart) : [];

			if (this.cart.length === 0) {
				this.showMessage(
					'Giỏ hàng trống. Vui lòng thêm sản phẩm trước khi thanh toán.',
					'error'
				);
				setTimeout(() => {
					window.location.href = '/cart';
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
			let itemHTML = template
				.replace(
					/\{\{image\}\}/g,
					item.image ||
						'/5s-fashion/public/assets/images/placeholder.jpg'
				)
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
		let isValid = true;
		let message = '';

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
		}

		// Specific field validations
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
		const form = document.getElementById('checkoutForm');
		const requiredFields = form.querySelectorAll(
			'input[required], select[required]'
		);
		let isValid = true;

		requiredFields.forEach((field) => {
			if (!this.validateField(field)) {
				isValid = false;
			}
		});

		return isValid;
	}

	/**
	 * Place order
	 */
	async placeOrder() {
		// Validate form
		if (!this.validateForm()) {
			this.showMessage(
				'Vui lòng điền đầy đủ thông tin bắt buộc',
				'error'
			);
			return;
		}

		// Validate address selection
		if (window.addressManager) {
			const selectedAddress = window.addressManager.getSelectedAddress();
			if (!selectedAddress) {
				this.showMessage('Vui lòng chọn địa chỉ giao hàng', 'error');
				return;
			}
		}

		try {
			// Show loading
			this.showLoadingOverlay('Đang xử lý đơn hàng...');

			// Collect order data
			this.orderData = this.collectOrderData();
			
			if (!this.orderData) {
				this.hideLoadingOverlay();
				return;
			}

			// Save customer info for future use
			localStorage.setItem(
				'customerInfo',
				JSON.stringify({
					fullName: this.orderData.customer.name,
					phone: this.orderData.customer.phone,
					email: this.orderData.customer.email,
				})
			);

			// Process order based on payment method
			const result = await this.processOrder();

			if (result.success) {
				// Clear cart
				localStorage.removeItem('cart');
				localStorage.removeItem('promoCode');

				// Redirect to success page
				window.location.href = `/order-success?orderCode=${result.orderCode}`;
			} else {
				throw new Error(result.message || 'Có lỗi xảy ra khi đặt hàng');
			}
		} catch (error) {
			console.error('Error placing order:', error);
			this.showMessage(
				error.message || 'Có lỗi xảy ra khi đặt hàng',
				'error'
			);
		} finally {
			this.hideLoadingOverlay();
		}
	}

	/**
	 * Collect order data from form
	 */
	collectOrderData() {
		// Get shipping method
		const shippingMethod = document.querySelector('input[name="shippingMethod"]:checked')?.value || 'standard';
		
		// Get payment method
		const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked')?.value || 'cod';
		
		// Get order notes
		const orderNotes = document.getElementById('orderNotes')?.value.trim() || '';

		// Get address from address manager
		const selectedAddress = window.addressManager?.getSelectedAddress();
		if (!selectedAddress) {
			this.showMessage('Vui lòng chọn địa chỉ giao hàng', 'error');
			return null;
		}

		return {
			items: this.cart,
			customer: {
				name: selectedAddress.name,
				phone: selectedAddress.phone,
				email: '', // Can be added later if needed
				address: selectedAddress.address,
				note: selectedAddress.note || ''
			},
			shipping: {
				method: shippingMethod,
				fee: this.getShippingFee()
			},
			payment: {
				method: paymentMethod
			},
			orderNotes: orderNotes,
			subtotal: this.getSubtotal(),
			total: this.getTotal(),
			discount: this.discountAmount,
			promoCode: this.promoCode,
			orderDate: new Date().toISOString()
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
