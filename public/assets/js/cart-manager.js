/**
 * zone Fashion Cart Manager - Unified System
 * Handles all cart operations consistently across pages
 * Following UI Guidelines and RULE.md standards
 */

class CartManager {
	constructor() {
		this.baseUrl = '/zone-fashion/ajax';
		this.syncInProgress = false;
		this.init();
	}

	init() {
		console.log('🛒 CartManager initialized');
		this.setupEventListeners();

		// Only sync if user is logged in
		if (this.isUserLoggedIn()) {
			this.syncCartFromServer();
		}
	}

	/**
	 * Check if user is logged in
	 */
	isUserLoggedIn() {
		// Check global JavaScript variable first
		if (typeof window.isLoggedIn !== 'undefined') {
			return window.isLoggedIn === true || window.isLoggedIn === 'true';
		}

		// Check body attribute as fallback
		const bodyLoggedIn = document.body.getAttribute('data-logged-in');
		return bodyLoggedIn === 'true';
	}

	/**
	 * Setup event listeners
	 */
	setupEventListeners() {
		// Cart button clicks (delegation)
		document.addEventListener('click', (e) => {
			// Add to cart buttons
			if (
				e.target.matches('[data-action="add-to-cart"]') ||
				e.target.closest('[data-action="add-to-cart"]')
			) {
				e.preventDefault();
				const button = e.target.matches('[data-action="add-to-cart"]')
					? e.target
					: e.target.closest('[data-action="add-to-cart"]');

				const productId = button.getAttribute('data-product-id');
				const quantity = parseInt(
					button.getAttribute('data-quantity') || 1
				);

				if (productId) {
					this.addToCart(productId, quantity);
				}
			}

			// Remove from cart buttons
			if (
				e.target.matches('[data-action="remove-from-cart"]') ||
				e.target.closest('[data-action="remove-from-cart"]') ||
				e.target.matches('.remove-cart-item') ||
				e.target.closest('.remove-cart-item')
			) {
				console.log('Remove button clicked!', e.target);
				e.preventDefault();
				const button = e.target.matches(
					'[data-action="remove-from-cart"]'
				)
					? e.target
					: e.target.closest('[data-action="remove-from-cart"]');

				const removeButton = e.target.matches('.remove-cart-item')
					? e.target
					: e.target.closest('.remove-cart-item');

				let productId = null;
				let cartId = null;

				if (button) {
					productId = button.getAttribute('data-product-id');
					console.log('Found button with product ID:', productId);
				} else if (removeButton) {
					cartId = removeButton.getAttribute('data-cart-id');
					productId = removeButton.getAttribute('data-product-id');
					console.log(
						'Found remove button with cart ID:',
						cartId,
						'product ID:',
						productId
					);
				}

				if (productId) {
					console.log(
						'Calling removeFromCart with product ID:',
						productId
					);
					this.removeFromCart(productId);
				} else if (cartId) {
					console.log('Calling removeCartItem with cart ID:', cartId);
					this.removeCartItem(cartId);
				} else {
					console.log('No product ID or cart ID found!');
				}
			}

			// Cart quantity updates
			if (e.target.matches('[data-action="update-quantity"]')) {
				const productId = e.target.getAttribute('data-product-id');
				const quantity = parseInt(e.target.value);

				if (productId && quantity >= 0) {
					this.updateQuantity(productId, quantity);
				}
			}
		});

		// Storage event for cross-tab sync
		window.addEventListener('storage', (e) => {
			if (
				e.key === 'cart_sync' &&
				this.isUserLoggedIn() &&
				!this.syncInProgress
			) {
				this.syncCartFromServer();
			}
		});
	}

	/**
	 * Add item to cart
	 */
	async addToCart(productId, quantity, variant = null) {
		console.log('CartManager.addToCart called with:', {
			productId: productId,
			quantity: quantity,
			variant: variant,
		});

		if (!this.isUserLoggedIn()) {
			this.showAlert(
				'Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng ',
				'warning'
			);
			return false;
		}
// hết hàng
		if () {
			this.showAlert('Sản phẩm đã hết hàng', 'error');
			return false;
		}

		if (window.cartOperationInProgress) {
			console.log('Cart operation in progress, ignoring...');
			return false;
		}

		window.cartOperationInProgress = true;
		this.showLoading(true);

		try {
			const requestData = {
				product_id: parseInt(productId),
				quantity: parseInt(quantity),
			};

			console.log('Sending to server:', requestData);

			// Add variant data if provided
			if (variant) {
				// prefer full variant object if provided
				requestData.variant_id = variant.id || variant.variant_id || null;
				requestData.variant_color = variant.color || variant.variant_color || null;
				requestData.variant_size = variant.size || variant.variant_size || null;
				// include variant sku and full object for server-side use
				requestData.variant_sku = variant.sku || variant.variant_sku || null;
				requestData.variant = variant.full || variant;
				// include client-side price derived from variant to help server validation
				requestData.price = variant.price || variant.sale_price || null;
			}

			const response = await fetch(`${this.baseUrl}/cart/add`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify(requestData),
			});

			const data = await response.json();

			if (data.success) {
				this.showAlert('Đã thêm sản phẩm vào giỏ hàng', 'success');
				await this.syncCartFromServer();
				this.triggerCrossTabSync();
				return true;
			} else {
				this.showAlert(
					data.message || 'Có lỗi xảy ra khi thêm vào giỏ hàng',
					'error'
				);
				return false;
			}
		} catch (error) {
			console.error('Add to cart error:', error);
			this.showAlert('Có lỗi xảy ra. Vui lòng thử lại.', 'error');
			return false;
		} finally {
			this.showLoading(false);
			window.cartOperationInProgress = false;
		}
	}

	/**
	 * Update item quantity
	 */
	async updateQuantity(productId, quantity) {
		console.log('CartManager.updateQuantity called with:', {
			productId: productId,
			quantity: quantity,
		});

		if (!this.isUserLoggedIn()) {
			this.showAlert('Vui lòng đăng nhập', 'warning');
			return false;
		}

		try {
			const response = await fetch(`${this.baseUrl}/cart/update`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify({
					cart_key: parseInt(productId), // Use productId as cart_key for cart updates
					quantity: parseInt(quantity),
				}),
			});

			console.log('Cart update response:', response);
			const data = await response.json();
			console.log('Cart update data:', data);

			if (data.success) {
				await this.syncCartFromServer();
				this.triggerCrossTabSync();
				return true;
			} else {
				this.showAlert(
					data.message || 'Có lỗi khi cập nhật số lượng',
					'error'
				);
				return false;
			}
		} catch (error) {
			console.error('Update quantity error:', error);
			this.showAlert('Có lỗi xảy ra. Vui lòng thử lại.', 'error');
			return false;
		}
	}

	/**
	 * Remove item from cart
	 */
	async removeFromCart(productId) {
		if (!this.isUserLoggedIn()) {
			return false;
		}

		try {
			const response = await fetch(`${this.baseUrl}/cart/remove`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify({
					product_id: parseInt(productId),
				}),
			});

			const data = await response.json();

			if (data.success) {
				this.showAlert('Đã xóa sản phẩm khỏi giỏ hàng', 'success');
				await this.syncCartFromServer();
				this.triggerCrossTabSync();
				return true;
			} else {
				this.showAlert(
					data.message || 'Có lỗi khi xóa sản phẩm',
					'error'
				);
				return false;
			}
		} catch (error) {
			console.error('Remove from cart error:', error);
			this.showAlert('Có lỗi xảy ra. Vui lòng thử lại.', 'error');
			return false;
		}
	}

	/**
	 * Remove cart item by cart ID
	 */
	async removeCartItem(cartId) {
		if (!this.isUserLoggedIn()) {
			return false;
		}

		try {
			const response = await fetch(`${this.baseUrl}/cart/remove`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify({
					cart_key: cartId,
				}),
			});

			const data = await response.json();

			if (data.success) {
				this.showAlert('Đã xóa sản phẩm khỏi giỏ hàng', 'success');
				// Reload page to refresh cart display
				setTimeout(() => {
					window.location.reload();
				}, 500);
				return true;
			} else {
				this.showAlert(
					data.message || 'Có lỗi khi xóa sản phẩm',
					'error'
				);
				return false;
			}
		} catch (error) {
			console.error('Remove cart item error:', error);
			this.showAlert('Có lỗi xảy ra. Vui lòng thử lại.', 'error');
			return false;
		}
	}

	/**
	 * Get cart items from server
	 */
	async getCartItems() {
		if (!this.isUserLoggedIn()) {
			return [];
		}

		try {
			const response = await fetch(`${this.baseUrl}/cart/items`, {
				method: 'GET',
				headers: {
					'Content-Type': 'application/json',
				},
			});

			const data = await response.json();
			console.log('Cart API response:', data);

			if (data.success && Array.isArray(data.items)) {
				return data.items;
			} else if (data.success && Array.isArray(data.cart_items)) {
				return data.cart_items;
			} else {
				console.warn('Invalid cart data:', data);
				return [];
			}
		} catch (error) {
			console.error('Get cart items error:', error);
			return [];
		}
	}

	/**
	 * Sync cart from server and update UI
	 */
	async syncCartFromServer() {
		if (this.syncInProgress || !this.isUserLoggedIn()) {
			return;
		}

		this.syncInProgress = true;

		try {
			const cartItems = await this.getCartItems();

			// Update cart counter
			this.updateCartCounter(cartItems.length);

			// Update cart sidebar if exists
			this.updateCartSidebar(cartItems);

			// Update cart page if we're on cart page
			if (window.location.pathname.includes('/cart')) {
				this.updateCartPage(cartItems);
			}
		} catch (error) {
			console.error('Sync cart error:', error);
		} finally {
			this.syncInProgress = false;
		}
	}

	/**
	 * Update cart counter in header
	 */
	updateCartCounter(count) {
		const counters = document.querySelectorAll('#cart-count, .cart-count');
		counters.forEach((counter) => {
			counter.textContent = count || 0;
			counter.style.display = count > 0 ? 'inline' : 'none';
		});
	}

	/**
	 * Update cart sidebar
	 */
	updateCartSidebar(items) {
		const cartItemsContainer = document.getElementById('cart-items');
		if (!cartItemsContainer) return;

		if (!items.length) {
			cartItemsContainer.innerHTML = `
                <div class="empty-cart text-center py-4">
                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Giỏ hàng trống</p>
                    <small class="text-muted">Thêm sản phẩm để bắt đầu mua sắm</small>
                </div>
            `;
			return;
		}

		const itemsHTML = items
			.map(
				(item) => `
            <div class="cart-item border-bottom pb-3 mb-3">
                <div class="row align-items-center">
                    <div class="col-3">
                        <img src="${
							item.image ||
							'/zone-fashion/public/assets/images/placeholder.jpg'
						}"
                             alt="${item.name}" class="img-fluid rounded">
                    </div>
                    <div class="col-6">
                        <h6 class="mb-1">${item.name}</h6>
                        <small class="text-muted">Số lượng: ${
							item.quantity
						}</small>
                        <div class="fw-bold text-primary">${this.formatPrice(
							item.price
						)}</div>
                    </div>
                    <div class="col-3 text-end">
                        <button class="btn btn-sm btn-outline-danger"
                                data-action="remove-from-cart"
                                data-product-id="${item.product_id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `
			)
			.join('');

		cartItemsContainer.innerHTML = itemsHTML;

		// Update cart total
		const total = items.reduce(
			(sum, item) => sum + item.price * item.quantity,
			0
		);
		const cartTotal = document.getElementById('cart-total');
		if (cartTotal) {
			cartTotal.innerHTML = `
                <div class="d-flex justify-content-between mb-2">
                    <span>Tổng cộng:</span>
                    <strong class="text-primary">${this.formatPrice(
						total
					)}</strong>
                </div>
                <div class="d-grid">
                    <a href="/zone-fashion/cart" class="btn btn-outline-primary btn-sm mb-2">Xem giỏ hàng</a>
                    <a href="/zone-fashion/checkout" class="btn btn-primary btn-sm">Thanh toán</a>
                </div>
            `;
		}
	}

	/**
	 * Update cart page (for cart.php)
	 */
	updateCartPage(items) {
		// This will be implemented by cart page specific code
		if (
			window.cartPageManager &&
			typeof window.cartPageManager.updateDisplay === 'function'
		) {
			window.cartPageManager.updateDisplay(items);
		}
	}

	/**
	 * Trigger cross-tab sync
	 */
	triggerCrossTabSync() {
		localStorage.setItem('cart_sync', Date.now().toString());
	}

	/**
	 * Show loading state
	 */
	showLoading(show) {
		const elements = document.querySelectorAll(
			'[data-action="add-to-cart"]'
		);
		elements.forEach((el) => {
			if (show) {
				el.disabled = true;
				el.classList.add('loading');
			} else {
				el.disabled = false;
				el.classList.remove('loading');
			}
		});
	}

	/**
	 * Show alert message
	 */
	showAlert(message, type = 'info') {
		if (typeof window.showAlert === 'function') {
			window.showAlert(message, type);
		} else if (typeof showNotification === 'function') {
			showNotification(message, type);
		} else {
			console.log(`${type.toUpperCase()}: ${message}`);
		}
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
}

// Initialize cart manager when DOM is ready
document.addEventListener('DOMContentLoaded', function () {
	// Only create if not already exists
	if (!window.cartManager) {
		const cartManager = new CartManager();

		// Make globally available
		window.cartManager = cartManager;
		window.unifiedCartManager = cartManager; // For unified system compatibility
	}

	// Backward compatibility functions
	window.addToCart = function (productId, quantity = 1, variant = null) {
		return cartManager.addToCart(productId, quantity, variant);
	};

	window.updateCartQuantity = function (productId, quantity) {
		return cartManager.updateQuantity(productId, quantity);
	};

	window.removeFromCart = function (productId) {
		return cartManager.removeFromCart(productId);
	};

	window.updateCartCounter = function (count = null) {
		if (count !== null) {
			cartManager.updateCartCounter(count);
		} else {
			cartManager.syncCartFromServer();
		}
	};

	// Additional compatibility functions for product detail page
	window.handleAddToCart = function (productId, quantity) {
		// If quantity not provided, read from input or global variable
		if (!quantity || quantity === undefined) {
			// Try global quantity first
			if (window.currentQuantity && window.currentQuantity > 1) {
				quantity = window.currentQuantity;
			} else {
				// Fallback to reading from quantity input
				const quantityInput = document.getElementById('quantity');
				quantity = quantityInput
					? parseInt(quantityInput.value) || 1
					: 1;
			}
		}

		console.log(
			'CartManager handleAddToCart override - using quantity:',
			quantity
		);
		return cartManager.addToCart(productId, quantity);
	};
});
