/**
 * Cart Page JavaScript - Following UI Guidelines
 * Handles cart functionality, quantity updates, and checkout
 */

// Cart Page Manager
class CartPageManager {
	constructor() {
		this.cartItems = [];
		this.subtotal = 0;
		this.shippingFee = 0;
		this.discount = 0;
		this.total = 0;
		this.init();
	}

	init() {
		console.log('Cart page loaded, initializing...');
		this.bindEvents();
		this.loadCartData();
	}

	bindEvents() {
		// Initialize checkout button with retry mechanism
		this.initCheckoutButton();

		// Initialize remove buttons
		this.initRemoveButtons();

		// Initialize quantity controls
		this.initQuantityControls();

		// Initialize promo code
		this.initPromoCode();
	}

	initCheckoutButton() {
		const initButton = () => {
			const checkoutBtn = document.getElementById('checkout-btn');
			if (checkoutBtn) {
				checkoutBtn.removeEventListener(
					'click',
					this.proceedToCheckout.bind(this)
				);
				checkoutBtn.addEventListener(
					'click',
					this.proceedToCheckout.bind(this)
				);
				console.log('Checkout button listener attached');
			} else {
				// Retry if button not found yet
				setTimeout(initButton, 100);
			}
		};
		initButton();
	}

	initRemoveButtons() {
		const removeButtons = document.querySelectorAll('.remove-cart-item');
		removeButtons.forEach((button) => {
			button.addEventListener('click', (e) => {
				const cartId = e.currentTarget.getAttribute('data-cart-id');
				if (cartId) {
					this.removeCartItem(cartId);
				}
			});
		});
		console.log('Remove buttons listeners attached:', removeButtons.length);
	}

	initQuantityControls() {
		// Quantity buttons
		document.querySelectorAll('.quantity-btn').forEach((button) => {
			button.addEventListener('click', (e) => {
				const action = e.currentTarget.onclick
					? e.currentTarget.onclick.toString().includes('increase')
						? 'increase'
						: 'decrease'
					: 'change';
				this.updateCartQuantity(e.currentTarget, action);
			});
		});

		// Quantity inputs
		document.querySelectorAll('.cart-quantity-input').forEach((input) => {
			input.addEventListener('change', (e) => {
				this.updateCartQuantity(e.currentTarget, 'change');
			});
		});
	}

	initPromoCode() {
		const promoBtn = document.querySelector('.promo-btn');
		const promoInput = document.getElementById('promo-code');

		if (promoBtn) {
			promoBtn.addEventListener('click', this.applyPromoCode.bind(this));
		}

		if (promoInput) {
			promoInput.addEventListener('keypress', (e) => {
				if (e.key === 'Enter') {
					this.applyPromoCode();
				}
			});
		}
	}

	loadCartData() {
		// Load initial cart data from DOM
		const cartItems = document.querySelectorAll('.cart-item');
		this.cartItems = Array.from(cartItems).map((item) => {
			const quantityInput = item.querySelector('.cart-quantity-input');
			const priceElement = item.querySelector('.product-price');
			const totalElement = item.querySelector('.item-total');

			return {
				id: quantityInput ? quantityInput.dataset.cartId : null,
				quantity: quantityInput ? parseInt(quantityInput.value) : 1,
				price: this.parsePrice(
					priceElement ? priceElement.textContent : '0'
				),
				total: this.parsePrice(
					totalElement ? totalElement.textContent : '0'
				),
			};
		});

		this.updateSummary();
	}

	parsePrice(priceString) {
		// Parse Vietnamese price format: "123.456 ₫" -> 123456
		return parseInt(priceString.replace(/[^\d]/g, '')) || 0;
	}

	formatPrice(price) {
		return new Intl.NumberFormat('vi-VN').format(price) + ' ₫';
	}

	updateCartQuantity(element, action) {
		const cartItem = element.closest('.cart-item');
		const quantityInput = cartItem.querySelector('.cart-quantity-input');
		const cartId = quantityInput.dataset.cartId;
		let quantity = parseInt(quantityInput.value);

		// Calculate new quantity
		if (action === 'increase') {
			quantity++;
		} else if (action === 'decrease') {
			quantity = Math.max(1, quantity - 1);
		} else if (action === 'change') {
			quantity = Math.max(1, parseInt(element.value) || 1);
		}

		// Update input value
		quantityInput.value = quantity;

		// Send update to server
		this.updateQuantityOnServer(cartId, quantity);
	}

	updateQuantityOnServer(cartKey, newQuantity) {
		if (newQuantity < 1) return;

		// Show loading state
		this.showLoadingState(true);

		fetch('/5s-fashion/ajax/cart/update', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
			},
			body: JSON.stringify({
				cart_key: cartKey,
				quantity: newQuantity,
			}),
		})
			.then((response) => response.json())
			.then((data) => {
				if (data.success) {
					// Reload the page to reflect changes
					window.location.reload();
				} else {
					this.showNotification(
						data.message || 'Có lỗi xảy ra',
						'error'
					);
				}
			})
			.catch((error) => {
				console.error('Error updating quantity:', error);
				this.showNotification(
					'Có lỗi xảy ra khi cập nhật số lượng',
					'error'
				);
			})
			.finally(() => {
				this.showLoadingState(false);
			});
	}

	removeCartItem(cartKey) {
		if (!confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?'))
			return;

		// Show loading state
		this.showLoadingState(true);

		fetch('/5s-fashion/ajax/cart/remove', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
			},
			body: JSON.stringify({
				cart_key: cartKey,
			}),
		})
			.then((response) => response.json())
			.then((data) => {
				if (data.success) {
					// Reload the page to reflect changes
					window.location.reload();
				} else {
					this.showNotification(
						data.message || 'Có lỗi xảy ra',
						'error'
					);
				}
			})
			.catch((error) => {
				console.error('Error removing item:', error);
				this.showNotification(
					'Có lỗi xảy ra khi xóa sản phẩm',
					'error'
				);
			})
			.finally(() => {
				this.showLoadingState(false);
			});
	}

	proceedToCheckout() {
		// Check if cart has items
		const cartItems = document.querySelectorAll('.cart-item');
		if (cartItems.length === 0) {
			this.showNotification(
				'Giỏ hàng trống. Vui lòng thêm sản phẩm trước khi thanh toán.',
				'warning'
			);
			return;
		}

		// Show loading state
		this.showLoadingState(true);

		// Add a small delay for better UX
		setTimeout(() => {
			// Redirect to checkout page
			window.location.href = '/5s-fashion/checkout';
		}, 500);
	}

	applyPromoCode() {
		const promoInput = document.getElementById('promo-code');
		const promoCode = promoInput ? promoInput.value.trim() : '';

		if (!promoCode) {
			this.showNotification('Vui lòng nhập mã giảm giá', 'warning');
			return;
		}

		// Show loading state for promo button
		const promoBtn = document.querySelector('.promo-btn');
		const originalText = promoBtn ? promoBtn.innerHTML : '';
		if (promoBtn) {
			promoBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
			promoBtn.disabled = true;
		}

		// TODO: Implement promo code API
		setTimeout(() => {
			this.showNotification(
				'Tính năng mã giảm giá đang được phát triển',
				'info'
			);

			// Restore button
			if (promoBtn) {
				promoBtn.innerHTML = originalText;
				promoBtn.disabled = false;
			}
		}, 1000);
	}

	updateSummary() {
		// Calculate totals
		this.subtotal = this.cartItems.reduce(
			(sum, item) => sum + item.total,
			0
		);
		this.total = this.subtotal + this.shippingFee - this.discount;

		// Update DOM elements
		const subtotalElement = document.getElementById('subtotal');
		const discountElement = document.getElementById('discount');
		const totalElement = document.getElementById('total');

		if (subtotalElement)
			subtotalElement.textContent = this.formatPrice(this.subtotal);
		if (discountElement)
			discountElement.textContent = this.formatPrice(this.discount);
		if (totalElement)
			totalElement.textContent = this.formatPrice(this.total);
	}

	showLoadingState(show) {
		const checkoutBtn = document.getElementById('checkout-btn');
		const removeButtons = document.querySelectorAll('.remove-cart-item');
		const quantityButtons = document.querySelectorAll('.quantity-btn');

		if (show) {
			if (checkoutBtn) {
				checkoutBtn.disabled = true;
				checkoutBtn.innerHTML =
					'<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...';
			}
			removeButtons.forEach((btn) => (btn.disabled = true));
			quantityButtons.forEach((btn) => (btn.disabled = true));
		} else {
			if (checkoutBtn) {
				checkoutBtn.disabled = false;
				checkoutBtn.innerHTML =
					'<i class="fas fa-credit-card me-2"></i>Thanh toán';
			}
			removeButtons.forEach((btn) => (btn.disabled = false));
			quantityButtons.forEach((btn) => (btn.disabled = false));
		}
	}

	showNotification(message, type = 'info') {
		// Create toast notification following UI guidelines
		const toast = document.createElement('div');
		toast.className = `toast-notification toast-${type}`;

		// Toast content
		const content = document.createElement('div');
		content.className = 'toast-content';

		const icon = document.createElement('i');
		icon.className = this.getNotificationIcon(type);

		const text = document.createElement('span');
		text.textContent = message;

		content.appendChild(icon);
		content.appendChild(text);
		toast.appendChild(content);

		// Add to toast container or body
		const container =
			document.querySelector('.toast-container') || document.body;
		container.appendChild(toast);

		// Show toast with animation
		setTimeout(() => toast.classList.add('show'), 100);

		// Hide toast after 4 seconds
		setTimeout(() => {
			toast.classList.remove('show');
			setTimeout(() => toast.remove(), 300);
		}, 4000);
	}

	getNotificationIcon(type) {
		const icons = {
			success: 'fas fa-check-circle',
			error: 'fas fa-exclamation-circle',
			warning: 'fas fa-exclamation-triangle',
			info: 'fas fa-info-circle',
		};
		return icons[type] || icons.info;
	}
}

// Global functions for backward compatibility
function updateCartQuantity(element, action) {
	if (window.cartPageManager) {
		window.cartPageManager.updateCartQuantity(element, action);
	}
}

function removeCartItem(cartKey) {
	if (window.cartPageManager) {
		window.cartPageManager.removeCartItem(cartKey);
	}
}

function proceedToCheckout() {
	if (window.cartPageManager) {
		window.cartPageManager.proceedToCheckout();
	}
}

function applyPromoCode() {
	if (window.cartPageManager) {
		window.cartPageManager.applyPromoCode();
	}
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function () {
	window.cartPageManager = new CartPageManager();
});
