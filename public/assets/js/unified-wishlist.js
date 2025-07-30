/**
 * Unified Wishlist System - 5S Fashion
 * Handles wishlist operations consistently across all pages
 */

class UnifiedWishlistManager {
	constructor() {
		this.init();
	}

	init() {
		// Check if user is logged in
		this.isLoggedIn =
			document.body.getAttribute('data-logged-in') === 'true';

		if (this.isLoggedIn) {
			// Load wishlist from server
			this.syncWishlistFromServer();
		}

		// Setup event listeners
		this.setupEventListeners();
	}

	setupEventListeners() {
		// Listen for storage changes (for cross-tab sync)
		window.addEventListener('storage', (e) => {
			if (e.key === 'wishlist_sync') {
				this.syncWishlistFromServer();
			}
		});

		// Listen for custom wishlist events
		document.addEventListener('wishlistUpdated', () => {
			this.syncWishlistFromServer();
		});
	}

	/**
	 * Toggle product in wishlist
	 */
	async toggleWishlist(productId) {
		// Check if user is logged in
		if (!this.isLoggedIn) {
			this.showToast(
				'Vui lòng đăng nhập để sử dụng danh sách yêu thích!',
				'warning'
			);
			window.location.href = '/5s-fashion/login';
			return;
		}

		// Prevent double execution
		if (window.wishlistOperationInProgress) {
			console.log('Wishlist operation already in progress');
			return;
		}

		window.wishlistOperationInProgress = true;

		try {
			// Show loading state on button
			const button = document.querySelector(
				`[onclick*="toggleWishlist(${productId})"]`
			);
			const icon = button?.querySelector('i');
			const originalClass = icon?.className;

			if (icon) {
				icon.className = 'fas fa-spinner fa-spin';
			}

			const response = await fetch('/5s-fashion/ajax/wishlist/toggle', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
				body: `product_id=${productId}`,
			});

			const data = await response.json();

			if (data.success) {
				// Update button state
				if (icon) {
					if (data.in_wishlist) {
						icon.className = 'fas fa-heart';
						button.classList.add('text-danger');
						this.showToast(
							'Đã thêm vào danh sách yêu thích!',
							'success'
						);
					} else {
						icon.className = 'far fa-heart';
						button.classList.remove('text-danger');
						this.showToast(
							'Đã xóa khỏi danh sách yêu thích!',
							'info'
						);
					}
				}

				// Update wishlist counter
				this.updateWishlistCounter();

				// Sync all wishlist buttons
				this.syncWishlistButtons();

				// Dispatch custom event
				document.dispatchEvent(
					new CustomEvent('wishlistUpdated', {
						detail: {
							action: data.in_wishlist ? 'add' : 'remove',
							productId,
							data,
						},
					})
				);

				return { success: true, data };
			} else {
				throw new Error(data.message || 'Có lỗi xảy ra!');
			}
		} catch (error) {
			console.error('Toggle wishlist error:', error);
			this.showToast(error.message || 'Có lỗi xảy ra!', 'error');

			// Restore original button state
			const button = document.querySelector(
				`[onclick*="toggleWishlist(${productId})"]`
			);
			const icon = button?.querySelector('i');
			if (icon && originalClass) {
				icon.className = originalClass;
			}

			return { success: false, error: error.message };
		} finally {
			window.wishlistOperationInProgress = false;
		}
	}

	/**
	 * Add product to wishlist
	 */
	async addToWishlist(productId) {
		if (!this.isLoggedIn) {
			this.showToast(
				'Vui lòng đăng nhập để sử dụng danh sách yêu thích!',
				'warning'
			);
			return;
		}

		try {
			const response = await fetch('/5s-fashion/ajax/wishlist/toggle', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
				body: `product_id=${productId}`,
			});

			const data = await response.json();

			if (data.success) {
				const message =
					data.action === 'added'
						? 'Đã thêm vào danh sách yêu thích!'
						: 'Đã xóa khỏi danh sách yêu thích!';
				const type = data.action === 'added' ? 'success' : 'info';

				this.showToast(message, type);
				this.syncWishlistFromServer();
				return { success: true, data };
			} else {
				throw new Error(data.message);
			}
		} catch (error) {
			console.error('Add to wishlist error:', error);
			this.showToast(error.message || 'Có lỗi xảy ra!', 'error');
			return { success: false, error: error.message };
		}
	}

	/**
	 * Remove product from wishlist
	 */
	async removeFromWishlist(productId) {
		if (!this.isLoggedIn) {
			return;
		}

		try {
			const response = await fetch('/5s-fashion/ajax/wishlist/toggle', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
				body: `product_id=${productId}`,
			});

			const data = await response.json();

			if (data.success) {
				const message =
					data.action === 'removed'
						? 'Đã xóa khỏi danh sách yêu thích!'
						: 'Đã thêm vào danh sách yêu thích!';
				const type = data.action === 'removed' ? 'info' : 'success';

				this.showToast(message, type);
				this.syncWishlistFromServer();
				return { success: true, data };
			} else {
				throw new Error(data.message);
			}
		} catch (error) {
			console.error('Remove from wishlist error:', error);
			this.showToast(error.message || 'Có lỗi xảy ra!', 'error');
			return { success: false, error: error.message };
		}
	}

	/**
	 * Get wishlist items from server
	 */
	async getWishlistItems() {
		if (!this.isLoggedIn) {
			return [];
		}

		try {
			const response = await fetch('/5s-fashion/wishlist', {
				method: 'GET',
				headers: {
					'Content-Type': 'text/html',
				},
			});

			const html = await response.text();

			// Parse HTML to extract wishlist product IDs
			const parser = new DOMParser();
			const doc = parser.parseFromString(html, 'text/html');
			const wishlistItems = doc.querySelectorAll('[data-product-id]');

			return Array.from(wishlistItems).map((item) =>
				parseInt(item.getAttribute('data-product-id'))
			);
		} catch (error) {
			console.error('Get wishlist items error:', error);
			return [];
		}
	}

	/**
	 * Sync wishlist from server and update UI
	 */
	async syncWishlistFromServer() {
		if (!this.isLoggedIn) {
			// If not logged in, ensure all buttons show empty state
			this.syncWishlistButtons([]);
			this.updateWishlistCounter(0);
			return;
		}

		try {
			const wishlistProductIds = await this.getWishlistItems();

			// Update all wishlist buttons
			this.syncWishlistButtons(wishlistProductIds);

			// Update wishlist counter
			this.updateWishlistCounter(wishlistProductIds.length);

			// Store in localStorage for quick access
			localStorage.setItem(
				'wishlist_cache',
				JSON.stringify({
					items: wishlistProductIds,
					timestamp: Date.now(),
				})
			);

			// Trigger cross-tab sync
			localStorage.setItem('wishlist_sync', Date.now().toString());
		} catch (error) {
			console.error('Sync wishlist error:', error);
		}
	}

	/**
	 * Sync all wishlist button states
	 */
	syncWishlistButtons(wishlistProductIds = []) {
		document
			.querySelectorAll('[onclick*="toggleWishlist"]')
			.forEach((button) => {
				const productId = parseInt(
					button.getAttribute('onclick').match(/\d+/)[0]
				);
				const icon = button.querySelector('i');

				if (wishlistProductIds.includes(productId)) {
					if (icon) {
						icon.classList.remove('far');
						icon.classList.add('fas');
						button.classList.add('text-danger');
					}
				} else {
					if (icon) {
						icon.classList.remove('fas');
						icon.classList.add('far');
						button.classList.remove('text-danger');
					}
				}
			});
	}

	/**
	 * Update wishlist counter in header
	 */
	updateWishlistCounter(count = null) {
		if (count === null && this.isLoggedIn) {
			// Fetch count from server
			fetch('/5s-fashion/wishlist/count')
				.then((response) => {
					if (!response.ok) {
						throw new Error(
							`HTTP error! status: ${response.status}`
						);
					}
					return response.json();
				})
				.then((data) => {
					this.updateWishlistCounterUI(data.count || 0);
				})
				.catch((error) => {
					console.error('Error getting wishlist count:', error);
					// Fallback: try to get count from cached wishlist items
					const cache = localStorage.getItem('wishlist_cache');
					if (cache) {
						try {
							const data = JSON.parse(cache);
							this.updateWishlistCounterUI(
								data.items ? data.items.length : 0
							);
						} catch (e) {
							this.updateWishlistCounterUI(0);
						}
					} else {
						this.updateWishlistCounterUI(0);
					}
				});
		} else {
			this.updateWishlistCounterUI(count || 0);
		}
	}

	updateWishlistCounterUI(count) {
		const counters = document.querySelectorAll(
			'#wishlist-count, .wishlist-count'
		);
		counters.forEach((counter) => {
			counter.textContent = count;
			counter.style.display = count > 0 ? 'inline' : 'none';
		});
	}

	/**
	 * Show toast notification
	 */
	showToast(message, type = 'info') {
		// Use existing toast function if available
		if (typeof showToast === 'function') {
			showToast(message, type);
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
					: type === 'warning'
					? '#ffc107'
					: '#17a2b8'
			};
            color: ${type === 'warning' ? '#000' : '#fff'};
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
}

// Initialize unified wishlist manager
let unifiedWishlistManager;

document.addEventListener('DOMContentLoaded', function () {
	unifiedWishlistManager = new UnifiedWishlistManager();

	// Make functions globally available for compatibility
	window.toggleWishlist = function (productId) {
		return unifiedWishlistManager.toggleWishlist(productId);
	};

	window.addToWishlist = function (productId) {
		return unifiedWishlistManager.addToWishlist(productId);
	};

	window.removeFromWishlist = function (productId) {
		return unifiedWishlistManager.removeFromWishlist(productId);
	};

	window.updateWishlistCounterFromAPI = function () {
		return unifiedWishlistManager.updateWishlistCounter();
	};

	window.updateWishlistButtonsFromAPI = function () {
		return unifiedWishlistManager.syncWishlistFromServer();
	};
});
