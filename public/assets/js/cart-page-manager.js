/**
 * Cart Page Manager - For cart page specific functionality
 * Works with the global CartManager for consistency
 * Following UI Guidelines and RULE.md standards
 */

class CartPageManager {
	constructor() {
		this.items = [];
		this.subtotal = 0;
		this.shippingFee = 0;
		this.discount = 0;
		this.total = 0;
		this.init();
	}

	init() {
		console.log('🛒 Cart Page Manager initialized');
		this.setupEventListeners();
		this.loadCartData();
	}

	setupEventListeners() {
		// Quantity update buttons
		document.addEventListener('click', (e) => {
			if (e.target.matches('.quantity-btn')) {
				e.preventDefault();
				const action = e.target.getAttribute('data-action');
				const input =
					e.target.parentElement.querySelector('.quantity-input');
				const productId = input.getAttribute('data-product-id');

				let currentQty = parseInt(input.value) || 1;
				let newQty =
					action === 'increase'
						? currentQty + 1
						: Math.max(1, currentQty - 1);

				input.value = newQty;
				this.updateQuantity(productId, newQty);
			}
		});

		// Quantity input changes
		document.addEventListener('change', (e) => {
			if (e.target.matches('.quantity-input')) {
				const productId = e.target.getAttribute('data-product-id');
				const quantity = Math.max(1, parseInt(e.target.value) || 1);
				e.target.value = quantity;
				this.updateQuantity(productId, quantity);
			}
		});

		// Remove item buttons
		document.addEventListener('click', (e) => {
			if (
				e.target.matches('.remove-item-btn') ||
				e.target.closest('.remove-item-btn')
			) {
				e.preventDefault();
				const button = e.target.matches('.remove-item-btn')
					? e.target
					: e.target.closest('.remove-item-btn');

				const productId = button.getAttribute('data-product-id');
				if (productId) {
					this.removeItem(productId);
				}
			}
		});

		// Checkout button
		const checkoutBtn = document.getElementById('checkout-btn');
		if (checkoutBtn) {
			checkoutBtn.addEventListener('click', (e) => {
				e.preventDefault();
				this.proceedToCheckout();
			});
		}

		// Promo code
		const promoBtn = document.querySelector('.apply-promo-btn');
		const promoInput = document.getElementById('promo-code');

		if (promoBtn) {
			promoBtn.addEventListener('click', () => this.applyPromoCode());
		}

		if (promoInput) {
			promoInput.addEventListener('keypress', (e) => {
				if (e.key === 'Enter') {
					this.applyPromoCode();
				}
			});
		}
	}

	async loadCartData() {
		this.showLoading(true);

		try {
			// Use global cart manager if available
			if (window.cartManager) {
				const items = await window.cartManager.getCartItems();
				this.updateDisplay(items);
			} else {
				// Fallback to direct API call
				const response = await fetch('/zone-fashion/ajax/cart/items');
				const data = await response.json();

				if (data.success) {
					this.updateDisplay(data.cart_items || []);
				}
			}
		} catch (error) {
			console.error('Load cart data error:', error);
			this.showAlert('Có lỗi khi tải giỏ hàng', 'error');
		} finally {
			this.showLoading(false);
		}
	}

	updateDisplay(items) {
		this.items = items;

		const cartItemsContainer = document.querySelector(
			'.cart-items-container'
		);
		const emptyCartContainer = document.querySelector(
			'.empty-cart-container'
		);

		if (!items.length) {
			if (cartItemsContainer) cartItemsContainer.style.display = 'none';
			if (emptyCartContainer) emptyCartContainer.style.display = 'block';
			return;
		}

		if (cartItemsContainer) cartItemsContainer.style.display = 'block';
		if (emptyCartContainer) emptyCartContainer.style.display = 'none';

		// Update cart items
		const cartBody = document.querySelector('.cart-items-body');
		if (cartBody) {
			cartBody.innerHTML = this.renderCartItems(items);
		}

		// Calculate and update totals
		this.calculateTotals();
		this.updateSummary();
	}

	renderCartItems(items) {
		return items
			.map(
				(item) => `
            <div class="cart-item border-bottom py-3" data-product-id="${
				item.product_id
			}">
                <div class="row align-items-center">
                    <div class="col-2">
                        <img src="${
							item.image ||
							'/zone-fashion/public/assets/images/placeholder.jpg'
						}"
                             alt="${item.name}" class="img-fluid rounded">
                    </div>
                    <div class="col-4">
                        <h6 class="mb-1">${item.name}</h6>
                        ${
							item.variant
								? `<small class="text-muted">${item.variant}</small>`
								: ''
						}
                        <div class="fw-bold text-primary">${this.formatPrice(
							item.price
						)}</div>
                    </div>
                    <div class="col-3">
                        <div class="quantity-controls d-flex align-items-center">
                            <button class="btn btn-sm btn-outline-secondary quantity-btn" data-action="decrease">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" class="form-control form-control-sm quantity-input mx-2 text-center"
                                   style="width: 60px;" value="${
										item.quantity
									}" min="1"
                                   data-product-id="${item.product_id}">
                            <button class="btn btn-sm btn-outline-secondary quantity-btn" data-action="increase">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="fw-bold">${this.formatPrice(
							item.price * item.quantity
						)}</div>
                    </div>
                    <div class="col-1">
                        <button class="btn btn-sm btn-outline-danger remove-item-btn"
                                data-product-id="${item.product_id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `
			)
			.join('');
	}

	async updateQuantity(productId, quantity) {
		if (window.cartManager) {
			const success = await window.cartManager.updateQuantity(
				productId,
				quantity
			);
			if (success) {
				await this.loadCartData();
			}
		}
	}

	async removeItem(productId) {
		if (window.cartManager) {
			const success = await window.cartManager.removeFromCart(productId);
			if (success) {
				await this.loadCartData();
			}
		}
	}

	calculateTotals() {
		this.subtotal = this.items.reduce(
			(sum, item) => sum + item.price * item.quantity,
			0
		);

		// Calculate shipping (free over 500k)
		this.shippingFee = this.subtotal >= 500000 ? 0 : 30000;

		// Apply discount if any
		// this.discount is set by promo code

		this.total = this.subtotal + this.shippingFee - this.discount;
	}

	updateSummary() {
		const elements = {
			subtotal: document.querySelector('.subtotal-amount'),
			shipping: document.querySelector('.shipping-amount'),
			discount: document.querySelector('.discount-amount'),
			total: document.querySelector('.total-amount'),
		};

		if (elements.subtotal)
			elements.subtotal.textContent = this.formatPrice(this.subtotal);
		if (elements.shipping)
			elements.shipping.textContent = this.formatPrice(this.shippingFee);
		if (elements.discount)
			elements.discount.textContent = this.formatPrice(this.discount);
		if (elements.total)
			elements.total.textContent = this.formatPrice(this.total);

		// Show/hide discount row
		const discountRow = document.querySelector('.discount-row');
		if (discountRow) {
			discountRow.style.display = this.discount > 0 ? 'flex' : 'none';
		}

		// Update shipping message
		const shippingMessage = document.querySelector('.shipping-message');
		if (shippingMessage) {
			if (this.subtotal >= 500000) {
				shippingMessage.innerHTML =
					'<small class="text-success"><i class="fas fa-check"></i> Miễn phí vận chuyển</small>';
			} else {
				const remaining = 500000 - this.subtotal;
				shippingMessage.innerHTML = `<small class="text-muted">Mua thêm ${this.formatPrice(
					remaining
				)} để được miễn phí vận chuyển</small>`;
			}
		}
	}

	async applyPromoCode() {
		const promoInput = document.getElementById('promo-code');
		const promoCode = promoInput?.value.trim();

		if (!promoCode) {
			this.showAlert('Vui lòng nhập mã giảm giá', 'warning');
			return;
		}

		try {
			const response = await fetch('/zone-fashion/ajax/promo/apply', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify({
					promo_code: promoCode,
					order_amount: this.subtotal,
				}),
			});

			const data = await response.json();

			if (data.success) {
				this.discount = data.discount_amount || 0;
				this.calculateTotals();
				this.updateSummary();
				this.showAlert('Áp dụng mã giảm giá thành công', 'success');

				// Disable promo input
				if (promoInput) {
					promoInput.disabled = true;
				}
			} else {
				this.showAlert(
					data.message || 'Mã giảm giá không hợp lệ',
					'error'
				);
			}
		} catch (error) {
			console.error('Apply promo error:', error);
			this.showAlert('Có lỗi khi áp dụng mã giảm giá', 'error');
		}
	}

	proceedToCheckout() {
		if (!this.items.length) {
			this.showAlert('Giỏ hàng trống', 'warning');
			return;
		}

		// Check login status
		if (!window.isLoggedIn) {
			this.showAlert('Vui lòng đăng nhập để tiếp tục', 'warning');
			setTimeout(() => {
				window.location.href =
					'/zone-fashion/login?redirect=' +
					encodeURIComponent('/zone-fashion/checkout');
			}, 1500);
			return;
		}

		// Proceed to checkout
		window.location.href = '/zone-fashion/checkout';
	}

	showLoading(show) {
		const loadingEl = document.querySelector('.cart-loading');
		const contentEl = document.querySelector('.cart-content');

		if (loadingEl && contentEl) {
			loadingEl.style.display = show ? 'block' : 'none';
			contentEl.style.display = show ? 'none' : 'block';
		}
	}

	showAlert(message, type = 'info') {
		if (typeof window.showAlert === 'function') {
			window.showAlert(message, type);
		} else {
			console.log(`${type.toUpperCase()}: ${message}`);
		}
	}

	formatPrice(price) {
		return new Intl.NumberFormat('vi-VN', {
			style: 'currency',
			currency: 'VND',
		}).format(price);
	}
}

// Initialize cart page manager when DOM is ready
document.addEventListener('DOMContentLoaded', function () {
	// Only initialize on cart page
	if (window.location.pathname.includes('/cart')) {
		window.cartPageManager = new CartPageManager();
	}
});
