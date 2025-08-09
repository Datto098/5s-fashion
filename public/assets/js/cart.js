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
		this.baseUrl = '/5s-fashion/ajax'; // Add base URL for API calls
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

	/**
	 * Load cart data from server
	 */
	async loadCartData() {
		try {
			const response = await fetch(`${this.baseUrl}/cart/list`);
			const result = await response.json();

			if (result.success) {
				this.cartItems = result.data || [];
				this.updateCartDisplay();
			}
		} catch (error) {
			console.error('Load cart data error:', error);
		}
	}

	/**
	 * Update cart display
	 */
	updateCartDisplay() {
		// Update cart counter
		this.updateCartCounter(this.cartItems.length);

		// Update cart totals
		this.calculateTotals();
	}

	/**
	 * Calculate cart totals
	 */
	calculateTotals() {
		this.subtotal = this.cartItems.reduce((total, item) => {
			return total + item.price * item.quantity;
		}, 0);

		this.total = this.subtotal + this.shippingFee - this.discount;
		this.updateCartTotal(this.total);
	}

	/**
	 * Proceed to checkout
	 */
	proceedToCheckout(e) {
		e.preventDefault();

		// Check if cart is empty
		if (this.cartItems.length === 0) {
			this.showError(
				'Giỏ hàng trống! Vui lòng thêm sản phẩm để tiếp tục.'
			);
			return;
		}

		// Redirect to checkout page
		window.location.href = '/5s-fashion/checkout';
	}

	/**
	 * Apply promo code
	 */
	async applyPromoCode() {
		const promoInput = document.getElementById('promo-code');
		const promoCode = promoInput?.value?.trim();

		if (!promoCode) {
			this.showError('Vui lòng nhập mã khuyến mãi');
			return;
		}

		try {
			const response = await fetch(`${this.baseUrl}/coupon/apply`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify({
					coupon_code: promoCode,
				}),
			});

			const result = await response.json();

			if (result.success) {
				this.discount = result.discount || 0;
				this.showSuccess(
					result.message || 'Áp dụng mã khuyến mãi thành công!'
				);
				this.calculateTotals();
			} else {
				throw new Error(result.message || 'Mã khuyến mãi không hợp lệ');
			}
		} catch (error) {
			console.error('Apply promo code error:', error);
			this.showError(error.message);
		}
	}

	/**
	 * Thêm sản phẩm vào giỏ hàng
	 */
	async addToCart(btn) {
		// Prevent double click
		if (btn.classList.contains('loading')) return;

		const originalText = btn.textContent;

		try {
			btn.classList.add('loading');
			btn.textContent = 'Đang thêm...';

			// Get product data
			const productId =
				btn.dataset.productId ||
				btn.closest('[data-product-id]')?.dataset.productId;
			const variantId =
				btn.dataset.variantId || this.getSelectedVariantId();
			const quantity = parseInt(
				btn.dataset.quantity ||
					document.querySelector('.quantity-input')?.value ||
					1
			);

			if (!productId) {
				throw new Error('Không tìm thấy thông tin sản phẩm');
			}

			// Prepare data
			const data = {
				product_id: parseInt(productId),
				quantity: quantity,
				variant_id: variantId ? parseInt(variantId) : null,
			};

			// Call API
			const response = await fetch(`${this.baseUrl}/cart/add`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify(data),
			});

			const result = await response.json();

			if (result.success) {
				this.showSuccess(result.message || 'Đã thêm vào giỏ hàng!');
				this.updateCartCounter(result.cart_count);
				this.animateAddToCart(btn);
			} else {
				throw new Error(result.message || 'Có lỗi xảy ra');
			}
		} catch (error) {
			console.error('Add to cart error:', error);
			this.showError(error.message);
		} finally {
			btn.classList.remove('loading');
			btn.textContent = originalText;
		}
	}

	/**
	 * Cập nhật số lượng sản phẩm trong giỏ hàng
	 */
	async updateCartQuantity(element, action = 'change') {
		try {
			let cartId, quantity;

			if (action === 'increase' || action === 'decrease') {
				// Button was clicked
				const input = element.parentNode.querySelector(
					'.cart-quantity-input'
				);
				cartId = input.dataset.cartId;
				quantity =
					parseInt(input.value) + (action === 'increase' ? 1 : -1);
				if (quantity < 1) quantity = 1;
				input.value = quantity;
			} else {
				// Input was changed directly
				cartId = element.dataset.cartId;
				quantity = parseInt(element.value);
			}

			if (!cartId) {
				throw new Error('Không tìm thấy ID giỏ hàng');
			}

			if (quantity < 0) {
				input.value = 1;
				return;
			}

			const response = await fetch(`${this.baseUrl}/cart/update`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify({
					cart_id: parseInt(cartId),
					quantity: quantity,
				}),
			});

			const result = await response.json();

			if (result.success) {
				if (quantity === 0) {
					// Remove item row if quantity is 0
					input.closest('.cart-item')?.remove();
					this.showSuccess('Đã xóa sản phẩm khỏi giỏ hàng');
				}

				this.updateCartCounter(result.cart_count);
				this.updateCartTotal(result.cart_total);
			} else {
				throw new Error(result.message || 'Có lỗi xảy ra');
			}
		} catch (error) {
			console.error('Update cart error:', error);
			this.showError(error.message);
		}
	}

	/**
	 * Xóa sản phẩm khỏi giỏ hàng
	 */
	async removeCartItem(cartIdOrBtn) {
		try {
			let cartId, btn;

			if (typeof cartIdOrBtn === 'string') {
				// Called with cartId directly
				cartId = cartIdOrBtn;
				btn = document.querySelector(`[data-cart-id="${cartId}"]`);
			} else {
				// Called with button element
				btn = cartIdOrBtn;
				cartId = btn.dataset.cartId;
			}

			if (!cartId) {
				throw new Error('Không tìm thấy ID giỏ hàng');
			}

			if (!confirm('Bạn có chắc muốn xóa sản phẩm này?')) {
				return;
			}

			const response = await fetch(`${this.baseUrl}/cart/remove`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify({
					cart_id: parseInt(cartId),
				}),
			});

			const result = await response.json();

			if (result.success) {
				// Remove item from DOM
				if (btn) {
					btn.closest('.cart-item')?.remove();
				} else {
					// Find and remove by cart ID
					const itemElement = document.querySelector(
						`[data-item-id="${cartId}"], .cart-item:has([data-cart-id="${cartId}"])`
					);
					itemElement?.remove();
				}

				this.updateCartCounter(result.cart_count);
				this.updateCartTotal(result.cart_total);
				this.showSuccess(result.message);

				// Check if cart is empty
				if (result.cart_count === 0) {
					this.showEmptyCart();
				}
			} else {
				throw new Error(result.message || 'Có lỗi xảy ra');
			}
		} catch (error) {
			console.error('Remove cart item error:', error);
			this.showError(error.message);
		}
	}

	/**
	 * Xóa toàn bộ giỏ hàng
	 */
	async clearCart() {
		try {
			if (!confirm('Bạn có chắc muốn xóa toàn bộ giỏ hàng?')) {
				return;
			}

			const response = await fetch(`${this.baseUrl}/cart/clear`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
				},
			});

			const result = await response.json();

			if (result.success) {
				location.reload(); // Reload page to show empty cart
			} else {
				throw new Error(result.message || 'Có lỗi xảy ra');
			}
		} catch (error) {
			console.error('Clear cart error:', error);
			this.showError(error.message);
		}
	}

	/**
	 * Cập nhật số lượng hiển thị trên counter
	 */
	async updateCartCounter(count = null) {
		try {
			if (count === null) {
				const response = await fetch(`${this.baseUrl}/cart/count`);
				const result = await response.json();
				count = result.success ? result.count : 0;
			}

			// Update all cart counters
			document.querySelectorAll('.cart-count').forEach((el) => {
				el.textContent = count;
				el.style.display = count > 0 ? 'inline' : 'none';
			});
		} catch (error) {
			console.error('Update cart counter error:', error);
		}
	}

	/**
	 * Cập nhật tổng giá trị giỏ hàng
	 */
	updateCartTotal(total) {
		document.querySelectorAll('.cart-total').forEach((el) => {
			el.textContent = this.formatCurrency(total);
		});
	}

	/**
	 * Get selected variant ID (for product detail page)
	 */
	getSelectedVariantId() {
		const colorInput = document.querySelector(
			'input[name="variant_color"]:checked'
		);
		const sizeInput = document.querySelector(
			'input[name="variant_size"]:checked'
		);

		if (colorInput && sizeInput) {
			return colorInput.dataset.variantId || sizeInput.dataset.variantId;
		}

		return null;
	}

	/**
	 * Show empty cart message
	 */
	showEmptyCart() {
		const cartContainer = document.querySelector('.cart-items-container');
		if (cartContainer) {
			cartContainer.innerHTML = `
                <div class="empty-cart text-center py-5">
                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                    <h5>Giỏ hàng trống</h5>
                    <p>Hãy thêm sản phẩm vào giỏ hàng để tiếp tục mua sắm</p>
                    <a href="${this.baseUrl}/" class="btn btn-primary">Tiếp tục mua sắm</a>
                </div>
            `;
		}
	}

	/**
	 * Animation for add to cart button
	 */
	animateAddToCart(btn) {
		btn.classList.add('added');
		setTimeout(() => {
			btn.classList.remove('added');
		}, 1000);
	}

	/**
	 * Format currency
	 */
	formatCurrency(amount) {
		return new Intl.NumberFormat('vi-VN', {
			style: 'currency',
			currency: 'VND',
		}).format(amount);
	}

	/**
	 * Show success message
	 */
	showSuccess(message) {
		if (window.showSuccess) {
			window.showSuccess(message);
		} else {
			this.showToast(message, 'success');
		}
	}

	/**
	 * Show error message
	 */
	showError(message) {
		if (window.showError) {
			window.showError(message);
		} else {
			this.showToast(message, 'error');
		}
	}

	/**
	 * Show toast notification
	 */
	showToast(message, type = 'info') {
		// Create toast element
		const toast = document.createElement('div');
		toast.className = `cart-toast cart-toast-${type}`;
		toast.innerHTML = `
            <div class="cart-toast-content">
                <i class="fas ${
					type === 'success'
						? 'fa-check-circle'
						: 'fa-exclamation-triangle'
				}"></i>
                <span>${message}</span>
            </div>
        `;

		// Add to page
		document.body.appendChild(toast);

		// Show with animation
		setTimeout(() => toast.classList.add('show'), 100);

		// Remove after 3 seconds
		setTimeout(() => {
			toast.classList.remove('show');
			setTimeout(() => toast.remove(), 300);
		}, 3000);
	}
}

// CSS for toast notifications
const toastCSS = `
.cart-toast {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    background: #fff;
    border-radius: 6px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    padding: 12px 16px;
    transform: translateX(100%);
    transition: transform 0.3s ease;
}

.cart-toast.show {
    transform: translateX(0);
}

.cart-toast-success {
    border-left: 4px solid #28a745;
}

.cart-toast-error {
    border-left: 4px solid #dc3545;
}

.cart-toast-content {
    display: flex;
    align-items: center;
    gap: 8px;
}

.cart-toast-success i {
    color: #28a745;
}

.cart-toast-error i {
    color: #dc3545;
}

.add-to-cart-btn.loading {
    opacity: 0.7;
    pointer-events: none;
}

.add-to-cart-btn.added {
    background-color: #28a745 !important;
    border-color: #28a745 !important;
}
`;

// Add CSS to page
const style = document.createElement('style');
style.textContent = toastCSS;
document.head.appendChild(style);

// Initialize cart page when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
	if (typeof window.cartPageManager === 'undefined') {
		window.cartPageManager = new CartPageManager();
	}
});

// Also initialize immediately if DOM is already loaded
if (document.readyState === 'loading') {
	// DOM is still loading, wait for DOMContentLoaded
} else {
	// DOM is already loaded, initialize immediately
	if (typeof window.cartPageManager === 'undefined') {
		window.cartPageManager = new CartPageManager();
	}
}
