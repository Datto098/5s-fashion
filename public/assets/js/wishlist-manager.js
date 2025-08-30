/**
 * zone Fashion Wishlist Manager - Unified System
 * Handles all wishlist operations consistently across pages
 * Following UI Guidelines and RULE.md standards
 */

class WishlistManager {
	constructor() {
		this.baseUrl = '/zone-fashion/ajax';
		this.syncInProgress = false;
		this.init();
	}

	init() {
		console.log('❤️ WishlistManager initialized');
		this.setupEventListeners();

		// Only sync if user is logged in
		if (this.isUserLoggedIn()) {
			setTimeout(() => {
				this.syncWishlistFromServer();
			}, 1000); // Delay to avoid conflicts with cart
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
		// Wishlist button clicks (delegation)
		document.addEventListener('click', (e) => {
			// Toggle wishlist buttons
			if (
				e.target.matches('[data-action="toggle-wishlist"]') ||
				e.target.closest('[data-action="toggle-wishlist"]')
			) {
				e.preventDefault();
				const button = e.target.matches(
					'[data-action="toggle-wishlist"]'
				)
					? e.target
					: e.target.closest('[data-action="toggle-wishlist"]');

				const productId = button.getAttribute('data-product-id');

				if (productId) {
					this.toggleWishlist(productId, button);
				}
			}

			// Legacy onclick support for existing buttons
			if (
				e.target.matches('[onclick*="toggleWishlist"]') ||
				e.target.closest('[onclick*="toggleWishlist"]')
			) {
				e.preventDefault();
				e.stopPropagation();

				const button = e.target.matches('[onclick*="toggleWishlist"]')
					? e.target
					: e.target.closest('[onclick*="toggleWishlist"]');

				const onclick = button.getAttribute('onclick');
				const match = onclick.match(/toggleWishlist\((\d+)\)/);

				if (match) {
					const productId = match[1];
					this.toggleWishlist(productId, button);
				}
			}
		});

		// Storage event for cross-tab sync
		window.addEventListener('storage', (e) => {
			if (
				e.key === 'wishlist_sync' &&
				this.isUserLoggedIn() &&
				!this.syncInProgress
			) {
				setTimeout(() => {
					this.syncWishlistFromServer();
				}, 500);
			}
		});
	}

	/**
	 * Toggle product in wishlist
	 */
	async toggleWishlist(productId, button = null) {
		if (!this.isUserLoggedIn()) {
			this.showAlert('Vui lòng đăng nhập để sử dụng wishlist', 'warning');
			return false;
		}

		if (window.wishlistOperationInProgress) {
			console.log('Wishlist operation in progress, ignoring...');
			return false;
		}

		window.wishlistOperationInProgress = true;

		try {
			// Check current state from button
			const icon = button ? button.querySelector('i') : null;
			const isCurrentlyInWishlist = icon
				? icon.classList.contains('fas')
				: false;

			// Update button state immediately for better UX
			if (icon) {
				if (isCurrentlyInWishlist) {
					icon.classList.remove('fas', 'text-danger');
					icon.classList.add('far');
					button.classList.remove('text-danger');
				} else {
					icon.classList.remove('far');
					icon.classList.add('fas', 'text-danger');
					button.classList.add('text-danger');
				}
			}

			const response = await fetch(`${this.baseUrl}/wishlist/toggle`, {
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
				const action =
					data.action ||
					(isCurrentlyInWishlist ? 'removed' : 'added');

				if (action === 'added') {
					this.showAlert(
						'Đã thêm vào danh sách yêu thích',
						'success'
					);
				} else {
					this.showAlert('Đã xóa khỏi danh sách yêu thích', 'info');
				}

				// Sync all buttons
				await this.syncWishlistFromServer();
				this.triggerCrossTabSync();

				return true;
			} else {
				// Revert button state on error
				if (icon) {
					if (isCurrentlyInWishlist) {
						icon.classList.remove('far');
						icon.classList.add('fas', 'text-danger');
						button.classList.add('text-danger');
					} else {
						icon.classList.remove('fas', 'text-danger');
						icon.classList.add('far');
						button.classList.remove('text-danger');
					}
				}

				this.showAlert(data.message || 'Có lỗi xảy ra', 'error');
				return false;
			}
		} catch (error) {
			console.error('Toggle wishlist error:', error);

			// Revert button state on error
			if (button && icon) {
				if (isCurrentlyInWishlist) {
					icon.classList.remove('far');
					icon.classList.add('fas', 'text-danger');
					button.classList.add('text-danger');
				} else {
					icon.classList.remove('fas', 'text-danger');
					icon.classList.add('far');
					button.classList.remove('text-danger');
				}
			}

			this.showAlert('Có lỗi xảy ra. Vui lòng thử lại.', 'error');
			return false;
		} finally {
			window.wishlistOperationInProgress = false;
		}
	}

	/**
	 * Get wishlist items from server
	 */
	async getWishlistItems() {
		if (!this.isUserLoggedIn()) {
			return [];
		}

		try {
			const response = await fetch(`${this.baseUrl}/wishlist/list`, {
				method: 'GET',
				headers: {
					'Content-Type': 'application/json',
				},
			});

			if (!response.ok) {
				throw new Error(
					`HTTP ${response.status}: ${response.statusText}`
				);
			}

			const responseText = await response.text();

			// Check if response is valid JSON
			if (
				!responseText.trim().startsWith('{') &&
				!responseText.trim().startsWith('[')
			) {
				console.error(
					'Invalid JSON response:',
					responseText.substring(0, 200)
				);
				return [];
			}

			const data = JSON.parse(responseText);

			if (data.success && Array.isArray(data.data)) {
				return data.data.map((item) =>
					parseInt(item.product_id || item.id)
				);
			}

			console.warn('Invalid wishlist data:', data);
			return [];
		} catch (error) {
			console.error('Get wishlist items error:', error);
			return [];
		}
	}

	/**
	 * Sync wishlist from server and update UI
	 */
	async syncWishlistFromServer() {
		if (this.syncInProgress) {
			return;
		}

		this.syncInProgress = true;

		try {
			if (!this.isUserLoggedIn()) {
				// Clear all wishlist buttons for non-logged users
				this.syncWishlistButtons([]);
				this.updateWishlistCounter(0);
				return;
			}

			const wishlistProductIds = await this.getWishlistItems();

			// Update all wishlist buttons
			this.syncWishlistButtons(wishlistProductIds);

			// Update wishlist counter
			this.updateWishlistCounter(wishlistProductIds.length);
		} catch (error) {
			console.error('Sync wishlist error:', error);
		} finally {
			this.syncInProgress = false;
		}
	}

	/**
	 * Sync all wishlist button states
	 */
	syncWishlistButtons(wishlistProductIds = []) {
		// Find all wishlist buttons
		const buttons = document.querySelectorAll(
			'[data-action="toggle-wishlist"], [onclick*="toggleWishlist"]'
		);

		buttons.forEach((button) => {
			let productId = button.getAttribute('data-product-id');

			// Handle legacy onclick buttons
			if (!productId) {
				const onclick = button.getAttribute('onclick');
				if (onclick) {
					const match = onclick.match(/toggleWishlist\((\d+)\)/);
					if (match) {
						productId = match[1];
					}
				}
			}

			if (productId) {
				const icon = button.querySelector('i');
				if (icon) {
					const isInWishlist = wishlistProductIds.includes(
						parseInt(productId)
					);

					if (isInWishlist) {
						icon.classList.remove('far');
						icon.classList.add('fas', 'text-danger');
						button.classList.add('text-danger');
					} else {
						icon.classList.remove('fas', 'text-danger');
						icon.classList.add('far');
						button.classList.remove('text-danger');
					}
				}
			}
		});
	}

	/**
	 * Update wishlist counter in header
	 */
	updateWishlistCounter(count) {
		const counters = document.querySelectorAll(
			'#wishlist-count, .wishlist-count'
		);
		counters.forEach((counter) => {
			counter.textContent = count || 0;
			counter.style.display = count > 0 ? 'inline' : 'none';
		});
	}

	/**
	 * Trigger cross-tab sync
	 */
	triggerCrossTabSync() {
		localStorage.setItem('wishlist_sync', Date.now().toString());
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
}

// Initialize wishlist manager when DOM is ready
let wishlistManager;

document.addEventListener('DOMContentLoaded', function () {
	wishlistManager = new WishlistManager();

	// Make globally available
	window.wishlistManager = wishlistManager;

	// Backward compatibility functions
	window.toggleWishlist = function (productId) {
		return wishlistManager.toggleWishlist(productId);
	};

	window.addToWishlist = function (productId) {
		return wishlistManager.toggleWishlist(productId);
	};

	window.removeFromWishlist = function (productId) {
		return wishlistManager.toggleWishlist(productId);
	};

	window.updateWishlistCounterFromAPI = function () {
		return wishlistManager.syncWishlistFromServer();
	};

	window.updateWishlistButtonsFromAPI = function () {
		return wishlistManager.syncWishlistFromServer();
	};
});
