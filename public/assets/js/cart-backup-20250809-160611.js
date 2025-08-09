/**
 * Cart Manager - Database Only
 * Xử lý giỏ hàng hoàn toàn qua database
 */
class CartManager {
	constructor() {
		this.baseUrl = '/5s-fashion';
		this.init();
	}

	init() {
		this.updateCartCounter();
		this.bindEvents();
	}

	/**
	 * Bind các events
	 */
	bindEvents() {
		// Add to cart buttons
		document.addEventListener('click', (e) => {
			if (e.target.matches('.add-to-cart-btn, .add-to-cart-btn *')) {
				e.preventDefault();
				const btn = e.target.closest('.add-to-cart-btn');
				this.addToCart(btn);
			}
		});

		// Cart quantity update
		document.addEventListener('change', (e) => {
			if (e.target.matches('.cart-quantity-input')) {
				this.updateCartQuantity(e.target);
			}
		});

		// Remove cart item
		document.addEventListener('click', (e) => {
			if (e.target.matches('.remove-cart-item, .remove-cart-item *')) {
				e.preventDefault();
				const btn = e.target.closest('.remove-cart-item');
				this.removeCartItem(btn);
			}
		});

		// Clear cart
		document.addEventListener('click', (e) => {
			if (e.target.matches('.clear-cart-btn')) {
				e.preventDefault();
				this.clearCart();
			}
		});
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
	async updateCartQuantity(input) {
		try {
			const cartId = input.dataset.cartId;
			const quantity = parseInt(input.value);

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
	async removeCartItem(btn) {
		try {
			const cartId = btn.dataset.cartId;

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
				btn.closest('.cart-item')?.remove();
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
