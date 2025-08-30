/**
 * Unified Cart System - zone Fashion
 * Handles cart operations consistently across all pages
 */

class UnifiedCartManager {
	constructor() {
		this.init();
	}

	init() {
		// Initialize sync tracking
		this.syncInProgress = false;
		this.shouldTriggerCrossTabSync = false;

		// Setup event listeners first
		this.setupEventListeners();

		// Only sync cart if logged in AND if we're not on wishlist page
		// Wishlist page doesn't need cart data immediately
		if (window.isLoggedIn === true || window.isLoggedIn === 'true') {
			const currentPath = window.location.pathname;
			if (!currentPath.includes('/wishlist')) {
				this.syncCartFromServer();
			}
		}
	}

	setupEventListeners() {
		// Prevent infinite loop by tracking sync operations
		this.syncInProgress = false;

		window.addEventListener('storage', (e) => {
			if (
				e.key === 'cart_sync' &&
				(window.isLoggedIn === true || window.isLoggedIn === 'true') &&
				!this.syncInProgress
			) {
				// Only sync from other tabs, not from current tab
				if (
					e.storageArea === localStorage &&
					e.newValue !== e.oldValue
				) {
					this.syncInProgress = true;
					this.syncCartFromServer().finally(() => {
						this.syncInProgress = false;
					});
				}
			}
		});

		document.addEventListener('cartUpdated', () => {
			if (
				window.isLoggedIn === true ||
				(window.isLoggedIn === 'true' && !this.syncInProgress)
			) {
				this.syncInProgress = true;
				this.syncCartFromServer().finally(() => {
					this.syncInProgress = false;
				});
			}
		});
	}

	/**
	 * Add item to cart - unified method for all pages
	 * Requires user authentication
	 */
	async addToCart(productId, quantity = 1, variant = null) {
		console.log('UnifiedCartManager.addToCart called with:', {
			productId,
			quantity,
			variant,
		});

		// Check if user is logged in first
		if (!this.isUserLoggedIn()) {
			this.showLoginPrompt(
				'Bạn cần đăng nhập để thêm sản phẩm vào giỏ hàng'
			);
			return { success: false, error: 'Authentication required' };
		}

		// Validate productId
		if (!productId || productId <= 0) {
			console.error('Invalid productId:', productId);
			this.showToast('ID sản phẩm không hợp lệ!', 'error');
			return { success: false, error: 'Invalid product ID' };
		}

		// Prevent double execution
		if (window.cartOperationInProgress) {
			console.log('Cart operation already in progress');
			return;
		}

		window.cartOperationInProgress = true;

		try {
			// Show loading state
			this.showLoading();

			const requestData = {
				product_id: productId,
				quantity: quantity,
				variant_color: variant?.color || '',
				variant_size: variant?.size || '',
				variant_id: variant?.id || null,
				variant_name: variant?.name || '',
				variant_price: variant?.price || null,
			};

			console.log('Sending request data:', requestData);

			const response = await fetch('/zone-fashion/ajax/cart/add', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify({
					product_id: productId,
					quantity: quantity,
					variant_color: variant?.color || '',
					variant_size: variant?.size || '',
					variant_id: variant?.id || null,
					variant_name: variant?.name || '',
					variant_price: variant?.price || null,
				}),
			});

			const data = await response.json();

			// Handle authentication errors
			if (this.handleApiError(response, data)) {
				return { success: false, error: 'Authentication required' };
			}

			if (data.success) {
				// Success feedback
				this.showToast('Đã thêm sản phẩm vào giỏ hàng!', 'success');

				// Update UI
				this.updateCartCounter(data.cart_count);
				this.syncCartFromServer();

				// Trigger animation if button is available
				if (event && event.target) {
					this.animateAddToCart(event.target);
				}

				// Dispatch custom event
				document.dispatchEvent(
					new CustomEvent('cartUpdated', {
						detail: { action: 'add', productId, quantity, data },
					})
				);

				return { success: true, data };
			} else {
				throw new Error(data.message || 'Có lỗi xảy ra!');
			}
		} catch (error) {
			console.error('Add to cart error:', error);

			// Check if it's an authentication error
			if (error.message && error.message.includes('đăng nhập')) {
				this.showLoginPrompt(error.message);
			} else {
				this.showToast(
					error.message || 'Có lỗi xảy ra khi thêm vào giỏ hàng!',
					'error'
				);
			}
			return { success: false, error: error.message };
		} finally {
			this.hideLoading();
			window.cartOperationInProgress = false;
		}
	}

	/**
	 * Update cart item quantity
	 */
	async updateCartItem(productId, quantity, variant = null) {
		try {
			const response = await fetch('/zone-fashion/ajax/cart/update', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify({
					product_id: productId,
					quantity: quantity,
					variant: variant,
				}),
			});

			const data = await response.json();

			if (data.success) {
				this.updateCartCounter(data.cart_count);
				this.syncCartFromServer();
				this.showToast('Đã cập nhật giỏ hàng!', 'success');
				return { success: true, data };
			} else {
				throw new Error(data.message);
			}
		} catch (error) {
			console.error('Update cart error:', error);
			this.showToast(error.message || 'Có lỗi xảy ra!', 'error');
			return { success: false, error: error.message };
		}
	}

	/**
	 * Remove item from cart
	 */
	async removeFromCart(productId, variant = null) {
		try {
			const response = await fetch('/zone-fashion/ajax/cart/remove', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify({
					product_id: productId,
					variant: variant,
				}),
			});

			const data = await response.json();

			if (data.success) {
				this.updateCartCounter(data.cart_count);
				this.syncCartFromServer();
				this.showToast('Đã xóa sản phẩm khỏi giỏ hàng!', 'info');
				return { success: true, data };
			} else {
				throw new Error(data.message);
			}
		} catch (error) {
			console.error('Remove from cart error:', error);
			this.showToast(error.message || 'Có lỗi xảy ra!', 'error');
			return { success: false, error: error.message };
		}
	}

	/**
	 * Get cart items from server
	 */
	async getCartItems() {
		try {
			const response = await fetch('/zone-fashion/ajax/cart/items', {
				method: 'GET',
				headers: {
					'Content-Type': 'application/json',
				},
			});

			if (!response.ok) {
				throw new Error(`HTTP error! status: ${response.status}`);
			}

			const data = await response.json();

			// Ensure we always return an array
			if (data.success && Array.isArray(data.cart_items)) {
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
		// Prevent infinite loop
		if (this.syncInProgress) {
			return;
		}

		try {
			this.syncInProgress = true;
			const items = await this.getCartItems();

			// Update cart sidebar if exists
			this.updateCartSidebar(items);

			// Update cart counter
			const totalItems = items.reduce(
				(sum, item) => sum + (item.quantity || 0),
				0
			);
			this.updateCartCounter(totalItems);

			// Store in localStorage for quick access
			localStorage.setItem(
				'cart_cache',
				JSON.stringify({
					items: items,
					timestamp: Date.now(),
				})
			);

			// Also update cart_items for compatibility
			localStorage.setItem('cart_items', JSON.stringify(items));

			// Only trigger cross-tab sync when needed, not for current tab
			// This prevents infinite loop
			if (this.shouldTriggerCrossTabSync) {
				localStorage.setItem('cart_sync', Date.now().toString());
				this.shouldTriggerCrossTabSync = false;
			}
		} catch (error) {
			console.error('Sync cart error:', error);
		} finally {
			this.syncInProgress = false;
		}
	}

	/**
	 * Update cart sidebar with items
	 */
	updateCartSidebar(items) {
		const cartItemsContainer = document.getElementById('cart-items');
		const cartTotalElement = document.getElementById('cart-total');

		if (!cartItemsContainer) return;

		// Ensure items is an array
		if (!Array.isArray(items)) {
			console.warn('Items is not an array:', items);
			items = [];
		}

		if (items.length === 0) {
			cartItemsContainer.innerHTML = `
                <div class="empty-cart">
                    <i class="fas fa-shopping-cart"></i>
                    <p>Giỏ hàng trống</p>
                    <small>Thêm sản phẩm để bắt đầu mua sắm</small>
                </div>
            `;
			if (cartTotalElement) cartTotalElement.innerHTML = '';
			return;
		}

		let html = '';
		let total = 0;

		items.forEach((item) => {
			const itemTotal = (item.price || 0) * (item.quantity || 0);
			total += itemTotal;

			// Parse variant for better display
			let variantDisplay = '';
			if (item.variant) {
				try {
					const variantObj = JSON.parse(item.variant);
					const variantTags = Object.entries(variantObj)
						.map(
							([key, value]) =>
								`<span class="variant-tag">${key}: ${value}</span>`
						)
						.join('');

					if (variantTags) {
						variantDisplay = `<div class="cart-item-variant">${variantTags}</div>`;
					}
				} catch (e) {
					if (item.variant) {
						variantDisplay = `<div class="cart-item-variant"><span class="variant-tag">${item.variant}</span></div>`;
					}
				}
			}

			const variantJson = item.variant
				? JSON.stringify(item.variant)
				: '';
			const variantEncoded = btoa(
				unescape(encodeURIComponent(variantJson))
			); // Base64 encode để tránh lỗi syntax

			html += `
                <div class="cart-item" data-product-id="${
					item.product_id
				}" data-variant-encoded="${variantEncoded}">
                    <div class="cart-item-image">
                        <img src="${
							item.product_image ||
							'/assets/images/placeholder.jpg'
						}"
                             alt="${item.product_name || 'Sản phẩm'}"
                             onerror="this.src='/assets/images/placeholder.jpg'">
                    </div>
                    <div class="cart-item-details">
                        <div class="cart-item-name">${
							item.product_name || 'Sản phẩm'
						}</div>
                        ${variantDisplay}
                        <div class="cart-item-price">${this.formatPrice(
							item.price || 0
						)}</div>
                        <div class="cart-item-actions">
                            <div class="quantity-controls">
                                <button class="quantity-btn btn-decrease" data-product-id="${
									item.product_id
								}" data-variant-encoded="${variantEncoded}">-</button>
                                <input type="number" class="quantity-input" value="${
									item.quantity || 0
								}" min="1"
                                       data-product-id="${
											item.product_id
										}" data-variant-encoded="${variantEncoded}" data-current-qty="${
				item.quantity || 0
			}">
                                <button class="quantity-btn btn-increase" data-product-id="${
									item.product_id
								}" data-variant-encoded="${variantEncoded}">+</button>
                            </div>
                            <a href="#" class="remove-item" data-product-id="${
								item.product_id
							}" data-variant-encoded="${variantEncoded}">Xóa</a>
                        </div>
                    </div>
                </div>
            `;
		});

		cartItemsContainer.innerHTML = html;

		// Add event listeners for cart controls
		this.attachCartEventListeners();

		if (cartTotalElement) {
			cartTotalElement.innerHTML = `
				<div class="cart-total">
					<span>Tổng cộng:</span>
					<span>${this.formatPrice(total)}</span>
				</div>
				<div class="cart-actions">
					<a href="/?route=cart" class="btn btn-outline-secondary">Xem giỏ hàng</a>
					<a href="/zone-fashion/checkout" class="btn btn-primary">Thanh toán</a>
				</div>
			`;
		}
	}

	/**
	 * Attach event listeners to cart controls
	 */
	attachCartEventListeners() {
		const cartContainer = document.getElementById('cart-items');
		if (!cartContainer) return;

		// Remove existing listeners
		cartContainer.removeEventListener('click', this.cartClickHandler);
		cartContainer.removeEventListener('change', this.cartChangeHandler);

		// Add new listeners
		this.cartClickHandler = this.handleCartClick.bind(this);
		this.cartChangeHandler = this.handleCartChange.bind(this);

		cartContainer.addEventListener('click', this.cartClickHandler);
		cartContainer.addEventListener('change', this.cartChangeHandler);
	}

	/**
	 * Handle click events in cart
	 */
	handleCartClick(event) {
		const target = event.target;
		const productId = target.dataset.productId;
		const variantEncoded = target.dataset.variantEncoded;

		if (!productId) return;

		// Decode variant
		let variant = null;
		if (variantEncoded) {
			try {
				const variantJson = decodeURIComponent(
					escape(atob(variantEncoded))
				);
				variant = variantJson ? JSON.parse(variantJson) : null;
			} catch (e) {
				console.warn('Failed to decode variant:', e);
			}
		}

		if (target.classList.contains('btn-decrease')) {
			// Decrease quantity
			this.updateCartQuantity(parseInt(productId), -1, variant);
			event.preventDefault();
		} else if (target.classList.contains('btn-increase')) {
			// Increase quantity
			this.updateCartQuantity(parseInt(productId), 1, variant);
			event.preventDefault();
		} else if (target.classList.contains('remove-item')) {
			// Remove item
			this.removeFromCart(parseInt(productId), variant);
			event.preventDefault();
		}
	}

	/**
	 * Handle change events in cart
	 */
	handleCartChange(event) {
		const target = event.target;

		if (target.classList.contains('quantity-input')) {
			const productId = target.dataset.productId;
			const variantEncoded = target.dataset.variantEncoded;
			const currentQty = parseInt(target.dataset.currentQty) || 0;
			const newQty = parseInt(target.value) || 0;

			if (!productId || newQty === currentQty) return;

			// Decode variant
			let variant = null;
			if (variantEncoded) {
				try {
					const variantJson = decodeURIComponent(
						escape(atob(variantEncoded))
					);
					variant = variantJson ? JSON.parse(variantJson) : null;
				} catch (e) {
					console.warn('Failed to decode variant:', e);
				}
			}

			const change = newQty - currentQty;
			this.updateCartQuantity(parseInt(productId), change, variant);
		}
	}

	/**
	 * Helper function for quantity updates
	 */
	async updateCartQuantity(productId, change, variant = null) {
		// Get current quantity first
		const currentQuantity = this.getCurrentItemQuantity(productId, variant);
		const newQuantity = Math.max(0, currentQuantity + change);

		if (newQuantity === 0) {
			return await this.removeFromCart(productId, variant);
		} else {
			return await this.updateCartItem(productId, newQuantity, variant);
		}
	}

	/**
	 * Get current quantity of item
	 */
	getCurrentItemQuantity(productId, variant) {
		const cache = localStorage.getItem('cart_cache');
		if (!cache) return 0;

		try {
			const data = JSON.parse(cache);
			const variantStr = variant ? JSON.stringify(variant) : '';
			const item = data.items.find(
				(i) =>
					i.product_id == productId &&
					(i.variant || '') === variantStr
			);
			return item ? item.quantity : 0;
		} catch (e) {
			return 0;
		}
	}

	/**
	 * Format price for display
	 */
	formatPrice(price) {
		if (!price || price === 0) return '0₫';

		// Convert to number if string
		const numPrice = typeof price === 'string' ? parseFloat(price) : price;

		// Format as Vietnamese currency
		return new Intl.NumberFormat('vi-VN').format(numPrice) + '₫';
	}

	/**
	 * Update cart counter in header - GLOBAL UNIFIED VERSION
	 */
	updateCartCounter(count) {
		// Counters are hidden via CSS - just log the count for debugging
		console.log('🛒 Cart counter would be:', count, '(hidden via CSS)');

		// Also update global window reference for compatibility
		if (typeof window.updateCartCounterGlobal !== 'function') {
			window.updateCartCounterGlobal = () => {
				this.syncCartFromServer();
			};
		}

		// Reset flag after short delay
		setTimeout(() => {
			this.updatingCounter = false;
		}, 100);

		// Reset flag after short delay
		setTimeout(() => {
			this.updatingCounter = false;
		}, 100);
	}

	/**
	 * Show loading state
	 */
	showLoading() {
		// Show global loading indicator if exists
		const loader = document.getElementById('globalLoader');
		if (loader) {
			loader.style.display = 'flex';
		}
	}

	/**
	 * Hide loading state
	 */
	hideLoading() {
		const loader = document.getElementById('globalLoader');
		if (loader) {
			loader.style.display = 'none';
		}
	}

	/**
	 * Show toast notification - sử dụng hệ thống thống nhất với tiêu đề
	 */
	showToast(message, type = 'info', title = '') {
		// Tạo tiêu đề mặc định nếu không có
		if (!title) {
			const defaultTitles = {
				success: 'Giỏ hàng',
				error: 'Lỗi',
				warning: 'Cảnh báo',
				info: 'Thông tin',
			};
			title = defaultTitles[type] || 'Thông báo';
		}

		// Use unified notification system if available
		if (
			window.notifications &&
			typeof window.notifications.show === 'function'
		) {
			window.notifications.show(message, type, title);
			return;
		}

		// Try global notification functions
		if (window.showSuccess && type === 'success') {
			window.showSuccess(message, title);
			return;
		} else if (window.showError && type === 'error') {
			window.showError(message, title);
			return;
		} else if (window.showWarning && type === 'warning') {
			window.showWarning(message, title);
			return;
		} else if (window.showInfo && (type === 'info' || !type)) {
			window.showInfo(message, title);
			return;
		}

		// Fallback toast implementation
		const toast = document.createElement('div');
		toast.className = `toast toast-${type}`;
		toast.textContent = message;
		toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 20px;
            background: ${
				type === 'success'
					? '#28a745'
					: type === 'error'
					? '#dc3545'
					: '#17a2b8'
			};
            color: white;
            border-radius: 4px;
            z-index: 10000;
            opacity: 0;
            transition: opacity 0.3s;
        `;

		document.body.appendChild(toast);

		// Show toast
		setTimeout(() => (toast.style.opacity = '1'), 100);

		// Hide and remove toast
		setTimeout(() => {
			toast.style.opacity = '0';
			setTimeout(() => document.body.removeChild(toast), 300);
		}, 3000);
	}

	/**
	 * Animate add to cart button
	 */
	animateAddToCart(button) {
		if (!button) return;

		const originalText = button.innerHTML;

		// Show success state
		button.innerHTML = '<i class="fas fa-check me-2"></i>Đã thêm!';
		button.disabled = true;
		button.style.transform = 'scale(1.05)';

		// Reset after animation
		setTimeout(() => {
			button.innerHTML = originalText;
			button.disabled = false;
			button.style.transform = 'scale(1)';
		}, 2000);
	}

	/**
	 * Check if user is logged in
	 * @returns {boolean}
	 */
	isUserLoggedIn() {
		// Check global JavaScript variable
		if (typeof window.isLoggedIn !== 'undefined') {
			return window.isLoggedIn === true || window.isLoggedIn === 'true';
		}

		// Check session storage
		const sessionData = sessionStorage.getItem('user_logged_in');
		if (sessionData) {
			return sessionData === 'true';
		}

		// Check local storage as fallback
		const localData = localStorage.getItem('user_logged_in');
		return localData === 'true';
	}

	/**
	 * Show login prompt modal
	 * @param {string} message - Message to display
	 */
	showLoginPrompt(message = 'Bạn cần đăng nhập để tiếp tục') {
		// Create login modal if it doesn't exist
		let modal = document.getElementById('loginPromptModal');
		if (!modal) {
			modal = this.createLoginModal();
		}

		// Update message
		const messageElement = modal.querySelector('.login-prompt-message');
		if (messageElement) {
			messageElement.textContent = message;
		}

		// Show modal using Bootstrap
		if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
			const bsModal = new bootstrap.Modal(modal);
			bsModal.show();
		} else {
			// Fallback for non-Bootstrap environments
			modal.style.display = 'block';
			modal.classList.add('show');
		}
	}

	/**
	 * Create login prompt modal
	 * @returns {HTMLElement}
	 */
	createLoginModal() {
		const modalHtml = `
			<div class="modal fade" id="loginPromptModal" tabindex="-1" aria-labelledby="loginPromptModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered">
					<div class="modal-content">
						<div class="modal-header border-0">
							<h5 class="modal-title text-primary fw-bold" id="loginPromptModalLabel">
								<i class="fas fa-sign-in-alt me-2"></i>
								Yêu cầu đăng nhập
							</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body text-center py-4">
							<div class="login-icon mb-3">
								<i class="fas fa-shopping-cart text-primary" style="font-size: 3rem;"></i>
							</div>
							<p class="login-prompt-message mb-4 text-muted">
								Bạn cần đăng nhập để tiếp tục
							</p>
						</div>
						<div class="modal-footer border-0 justify-content-center">
							<a href="/zone-fashion/login" class="btn btn-primary px-4">
								<i class="fas fa-sign-in-alt me-2"></i>
								Đăng nhập ngay
							</a>
							<button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
								<i class="fas fa-times me-2"></i>
								Hủy bỏ
							</button>
						</div>
					</div>
				</div>
			</div>
		`;

		// Create modal element
		const modalDiv = document.createElement('div');
		modalDiv.innerHTML = modalHtml;
		const modal = modalDiv.firstElementChild;

		// Add to body
		document.body.appendChild(modal);

		return modal;
	}

	// hideToast helper function (kept for compatibility)
	hideToast(toast) {
		if (toast && toast.parentNode) {
			toast.classList.remove('show');
			setTimeout(() => {
				if (toast.parentNode) {
					toast.parentNode.removeChild(toast);
				}
			}, 300);
		}
	}

	/**
	 * Handle API response errors (including authentication errors)
	 */
	handleApiError(response, data) {
		// Handle authentication errors
		if (
			response.status === 401 ||
			data.error_code === 'AUTHENTICATION_REQUIRED'
		) {
			this.showLoginPrompt(
				data.message || 'Bạn cần đăng nhập để tiếp tục'
			);
			return true;
		}

		return false;
	}
}

// Initialize unified cart manager
let unifiedCartManager;

document.addEventListener('DOMContentLoaded', function () {
	unifiedCartManager = new UnifiedCartManager();

	// Make functions globally available for compatibility
	window.addToCart = function (productId, quantity = 1, variant = null) {
		console.log('Global addToCart called with:', {
			productId,
			quantity,
			variant,
			type: typeof productId,
		});

		// Additional validation
		if (
			productId === null ||
			productId === undefined ||
			productId === '' ||
			productId === 0
		) {
			console.error('Invalid productId in global addToCart:', productId);
			if (unifiedCartManager && unifiedCartManager.showToast) {
				unifiedCartManager.showToast(
					'ID sản phẩm không hợp lệ!',
					'error'
				);
			}
			return { success: false, error: 'Invalid product ID' };
		}

		return unifiedCartManager.addToCart(productId, quantity, variant);
	};

	window.updateCartItem = function (productId, quantity, variant = null) {
		return unifiedCartManager.updateCartItem(productId, quantity, variant);
	};

	window.removeFromCart = function (productId, variant = null) {
		return unifiedCartManager.removeFromCart(productId, variant);
	};

	window.loadCartItemsFromServer = function () {
		return unifiedCartManager.syncCartFromServer();
	};

	// Helper function for quantity updates
	window.updateCartQuantity = function (productId, change, variant = null) {
		// Get current quantity first
		const currentQuantity = getCurrentItemQuantity(productId, variant);
		const newQuantity = Math.max(0, currentQuantity + change);

		if (newQuantity === 0) {
			return unifiedCartManager.removeFromCart(productId, variant);
		} else {
			return unifiedCartManager.updateCartItem(
				productId,
				newQuantity,
				variant
			);
		}
	};

	// Helper to get current quantity
	function getCurrentItemQuantity(productId, variant) {
		const cache = localStorage.getItem('cart_cache');
		if (!cache) return 0;

		try {
			const data = JSON.parse(cache);
			const item = data.items.find(
				(i) =>
					i.product_id == productId &&
					(i.variant || '') === (variant || '')
			);
			return item ? item.quantity : 0;
		} catch (e) {
			return 0;
		}
	}

	// Add cart sidebar toggle functions for compatibility
	window.toggleCartSidebar = function () {
		const sidebar = document.getElementById('cartSidebar');
		const overlay = document.getElementById('cartSidebarOverlay');

		if (sidebar) {
			sidebar.classList.toggle('show');
			if (overlay) overlay.classList.toggle('show');

			if (sidebar.classList.contains('show')) {
				unifiedCartManager.syncCartFromServer();
				document.body.style.overflow = 'hidden';
			} else {
				document.body.style.overflow = '';
			}
		}
	};

	window.closeCartSidebar = function () {
		const sidebar = document.getElementById('cartSidebar');
		const overlay = document.getElementById('cartSidebarOverlay');

		if (sidebar) {
			sidebar.classList.remove('show');
			if (overlay) overlay.classList.remove('show');
			document.body.style.overflow = '';
		}
	};
});
