/**
 * Shopping Cart Manager - Phase 3.3
 * Handles all cart page functionality including item management, calculations, and checkout
 */

class CartManager {
	constructor() {
		this.cart = [];
		this.promoCode = null;
		this.freeShippingThreshold = 500000; // 500k VND
		this.shippingFee = 30000; // 30k VND
		this.discountAmount = 0;

		// Initialize event listeners
		this.initializeEventListeners();
	}

	/**
	 * Initialize event listeners
	 */
	initializeEventListeners() {
		// Listen for cart updates from other pages
		window.addEventListener('cartUpdated', () => {
			this.loadCart();
		});

		// Listen for storage changes (when cart is updated from other tabs)
		window.addEventListener('storage', (e) => {
			if (e.key === 'cart') {
				this.loadCart();
			}
		});

		// Auto-save cart on page unload
		window.addEventListener('beforeunload', () => {
			this.saveCart();
		});
	}

	/**
	 * Load cart from localStorage and display
	 */
	loadCart() {
		try {
			const savedCart = localStorage.getItem('cart');
			this.cart = savedCart ? JSON.parse(savedCart) : [];

			this.displayCart();
			this.updateSummary();
			this.updateFreeShippingProgress();
		} catch (error) {
			console.error('Error loading cart:', error);
			this.cart = [];
			this.displayCart();
		}
	}

	/**
	 * Display cart items
	 */
	displayCart() {
		const cartItemsContainer =
			document.getElementById('cartItemsContainer');
		const emptyCartMessage = document.getElementById('emptyCartMessage');
		const cartItemsList = document.getElementById('cartItemsList');
		const continueShoppingSection = document.getElementById(
			'continueShoppingSection'
		);
		const cartSummary = document.getElementById('cartSummary');

		if (!this.cart || this.cart.length === 0) {
			// Show empty cart
			emptyCartMessage.style.display = 'block';
			cartItemsList.style.display = 'none';
			continueShoppingSection.style.display = 'none';
			cartSummary.style.display = 'none';
			return;
		}

		// Show cart items
		emptyCartMessage.style.display = 'none';
		cartItemsList.style.display = 'block';
		continueShoppingSection.style.display = 'block';
		cartSummary.style.display = 'block';

		// Render cart items
		const template = document.getElementById('cartItemTemplate').innerHTML;
		let cartHTML = '';

		this.cart.forEach((item) => {
			let itemHTML = template
				.replace(/\{\{id\}\}/g, item.id)
				.replace(
					/\{\{image\}\}/g,
					item.image ||
						'/5s-fashion/public/assets/images/placeholder.jpg'
				)
				.replace(/\{\{name\}\}/g, item.name)
				.replace(/\{\{slug\}\}/g, item.slug || '#')
				.replace(/\{\{variant\}\}/g, this.formatVariant(item))
				.replace(/\{\{price\}\}/g, this.formatPrice(item.price))
				.replace(/\{\{quantity\}\}/g, item.quantity)
				.replace(
					/\{\{totalPrice\}\}/g,
					this.formatPrice(item.price * item.quantity)
				);

			cartHTML += itemHTML;
		});

		cartItemsList.innerHTML = cartHTML;

		// Update page title with cart count
		document.title = `Giỏ hàng (${this.getTotalItems()}) - 5S Fashion`;
	}

	/**
	 * Update cart summary
	 */
	updateSummary() {
		const subtotal = this.getSubtotal();
		const shipping = this.getShippingFee();
		const total = this.getTotal();

		// Update summary display
		document.getElementById('subtotal').textContent =
			this.formatPrice(subtotal);
		document.getElementById('shippingFee').textContent =
			this.formatPrice(shipping);
		document.getElementById('totalAmount').textContent =
			this.formatPrice(total);

		// Update discount display
		const discountRow = document.getElementById('discountRow');
		if (this.discountAmount > 0) {
			discountRow.style.display = 'flex';
			document.getElementById('discountAmount').textContent =
				'-' + this.formatPrice(this.discountAmount);
		} else {
			discountRow.style.display = 'none';
		}

		// Update checkout button state
		const checkoutBtn = document.getElementById('checkoutBtn');
		if (this.cart.length === 0) {
			checkoutBtn.disabled = true;
			checkoutBtn.innerHTML =
				'<i class="fas fa-shopping-cart me-2"></i>Giỏ hàng trống';
		} else {
			checkoutBtn.disabled = false;
			checkoutBtn.innerHTML =
				'<i class="fas fa-credit-card me-2"></i>Tiến hành thanh toán';
		}
	}

	/**
	 * Update free shipping progress
	 */
	updateFreeShippingProgress() {
		const subtotal = this.getSubtotal();
		const remaining = Math.max(0, this.freeShippingThreshold - subtotal);
		const progress = Math.min(
			100,
			(subtotal / this.freeShippingThreshold) * 100
		);

		const progressBar = document.getElementById('freeShippingProgress');
		const remainingAmount = document.getElementById('remainingAmount');
		const freeShippingText = document.getElementById('freeShippingText');

		progressBar.style.width = progress + '%';

		if (remaining > 0) {
			remainingAmount.textContent = this.formatPrice(remaining);
			freeShippingText.innerHTML = `Mua thêm <strong>${this.formatPrice(
				remaining
			)}</strong> để được <strong>miễn phí vận chuyển</strong>`;
		} else {
			freeShippingText.innerHTML =
				'<i class="fas fa-check-circle me-1"></i><strong>Bạn được miễn phí vận chuyển!</strong>';
		}
	}

	/**
	 * Update item quantity
	 */
	updateQuantity(itemId, change) {
		const item = this.cart.find((item) => item.id == itemId);
		if (!item) return;

		const newQuantity = item.quantity + change;

		if (newQuantity <= 0) {
			this.removeItem(itemId);
			return;
		}

		if (newQuantity > 99) {
			this.showMessage('Số lượng tối đa là 99', 'error');
			return;
		}

		item.quantity = newQuantity;
		this.saveCart();
		this.displayCart();
		this.updateSummary();
		this.updateFreeShippingProgress();

		// Trigger cart update event
		window.dispatchEvent(new CustomEvent('cartUpdated'));

		this.showMessage('Đã cập nhật số lượng', 'success');
	}

	/**
	 * Set specific quantity
	 */
	setQuantity(itemId, quantity) {
		const item = this.cart.find((item) => item.id == itemId);
		if (!item) return;

		const newQuantity = parseInt(quantity);

		if (newQuantity <= 0) {
			this.removeItem(itemId);
			return;
		}

		if (newQuantity > 99) {
			this.showMessage('Số lượng tối đa là 99', 'error');
			return;
		}

		item.quantity = newQuantity;
		this.saveCart();
		this.displayCart();
		this.updateSummary();
		this.updateFreeShippingProgress();

		// Trigger cart update event
		window.dispatchEvent(new CustomEvent('cartUpdated'));
	}

	/**
	 * Remove item from cart
	 */
	removeItem(itemId) {
		// Show confirmation
		if (!confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')) {
			return;
		}

		this.cart = this.cart.filter((item) => item.id != itemId);
		this.saveCart();
		this.displayCart();
		this.updateSummary();
		this.updateFreeShippingProgress();

		// Trigger cart update event
		window.dispatchEvent(new CustomEvent('cartUpdated'));

		this.showMessage('Đã xóa sản phẩm khỏi giỏ hàng', 'success');
	}

	/**
	 * Clear entire cart
	 */
	clearCart() {
		if (this.cart.length === 0) {
			this.showMessage('Giỏ hàng đã trống', 'error');
			return;
		}

		if (!confirm('Bạn có chắc muốn xóa tất cả sản phẩm trong giỏ hàng?')) {
			return;
		}

		this.cart = [];
		this.promoCode = null;
		this.discountAmount = 0;
		localStorage.removeItem('cart');
		localStorage.removeItem('promoCode');

		this.displayCart();
		this.updateSummary();
		this.updateFreeShippingProgress();

		// Trigger cart update event
		window.dispatchEvent(new CustomEvent('cartUpdated'));

		this.showMessage('Đã xóa tất cả sản phẩm', 'success');
	}

	/**
	 * Apply promo code
	 */
	async applyPromoCode() {
		const promoCodeInput = document.getElementById('promoCodeInput');
		const code = promoCodeInput.value.trim().toUpperCase();

		if (!code) {
			this.showMessage('Vui lòng nhập mã giảm giá', 'error');
			return;
		}

		if (this.cart.length === 0) {
			this.showMessage(
				'Giỏ hàng trống, không thể áp dụng mã giảm giá',
				'error'
			);
			return;
		}

		try {
			// Show loading
			const button = promoCodeInput.nextElementSibling;
			const originalText = button.textContent;
			button.innerHTML =
				'<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
			button.disabled = true;

			// Simulate API call (replace with actual API call)
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

				promoCodeInput.value = '';
				this.updateSummary();
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

			// Restore button
			const button = promoCodeInput.nextElementSibling;
			button.textContent = 'Áp dụng';
			button.disabled = false;
		}
	}

	/**
	 * Validate promo code (simulate API call)
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
	 * Proceed to checkout
	 */
	proceedToCheckout() {
		if (this.cart.length === 0) {
			this.showMessage('Giỏ hàng trống', 'error');
			return;
		}

		// Save current cart state
		this.saveCart();

		// Redirect to checkout page
		window.location.href = '/checkout';
	}

	/**
	 * Load recommended products
	 */
	async loadRecommendedProducts() {
		try {
			// Get recommended products based on cart items
			const recommendedContainer = document.getElementById(
				'recommendedProducts'
			);

			// Mock recommended products (replace with actual API call)
			const recommended = await this.fetchRecommendedProducts();

			let html = '';
			recommended.forEach((product) => {
				html += `
                    <a href="/product/${product.slug}" class="recommended-item">
                        <img src="${product.image}" alt="${product.name}">
                        <div class="recommended-item-info">
                            <div class="recommended-item-name">${
								product.name
							}</div>
                            <div class="recommended-item-price">${this.formatPrice(
								product.price
							)}</div>
                        </div>
                    </a>
                `;
			});

			recommendedContainer.innerHTML = html;
		} catch (error) {
			console.error('Error loading recommended products:', error);
		}
	}

	/**
	 * Fetch recommended products (mock function)
	 */
	async fetchRecommendedProducts() {
		// Simulate API delay
		await new Promise((resolve) => setTimeout(resolve, 500));

		// Mock data
		return [
			{
				id: 1,
				name: 'Áo thun nam basic trắng',
				slug: 'ao-thun-nam-basic-trang',
				price: 199000,
				image: '/5s-fashion/public/assets/images/products/ao-thun-1.jpg',
			},
			{
				id: 2,
				name: 'Quần jean nữ skinny',
				slug: 'quan-jean-nu-skinny',
				price: 399000,
				image: '/5s-fashion/public/assets/images/products/quan-jean-1.jpg',
			},
			{
				id: 3,
				name: 'Áo sơ mi nam công sở',
				slug: 'ao-so-mi-nam-cong-so',
				price: 299000,
				image: '/5s-fashion/public/assets/images/products/ao-so-mi-1.jpg',
			},
		];
	}

	/**
	 * Load recently viewed products
	 */
	loadRecentlyViewed() {
		try {
			const recentlyViewed = JSON.parse(
				localStorage.getItem('recentlyViewed') || '[]'
			);

			if (recentlyViewed.length === 0) {
				document.getElementById('recentlyViewedSection').style.display =
					'none';
				return;
			}

			document.getElementById('recentlyViewedSection').style.display =
				'block';

			const container = document.getElementById('recentlyViewedProducts');
			let html = '';

			// Show only first 4 items
			const items = recentlyViewed.slice(0, 4);

			items.forEach((product) => {
				html += `
                    <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                        <div class="product-card">
                            <div class="product-image">
                                <img src="${product.image}" alt="${
					product.name
				}">
                            </div>
                            <div class="product-info">
                                <h6 class="product-name">
                                    <a href="/product/${
										product.slug
									}" class="text-decoration-none">${
					product.name
				}</a>
                                </h6>
                                <div class="product-price">${this.formatPrice(
									product.price
								)}</div>
                            </div>
                        </div>
                    </div>
                `;
			});

			container.innerHTML = html;
		} catch (error) {
			console.error('Error loading recently viewed:', error);
			document.getElementById('recentlyViewedSection').style.display =
				'none';
		}
	}

	/**
	 * Save cart to localStorage
	 */
	saveCart() {
		localStorage.setItem('cart', JSON.stringify(this.cart));
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
	 * Calculate shipping fee
	 */
	getShippingFee() {
		const subtotal = this.getSubtotal();
		return subtotal >= this.freeShippingThreshold ? 0 : this.shippingFee;
	}

	/**
	 * Calculate total
	 */
	getTotal() {
		return this.getSubtotal() + this.getShippingFee() - this.discountAmount;
	}

	/**
	 * Get total items count
	 */
	getTotalItems() {
		return this.cart.reduce((total, item) => total + item.quantity, 0);
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
	 * Show message to user
	 */
	showMessage(message, type = 'info') {
		// Create message element
		const messageEl = document.createElement('div');
		messageEl.className = `alert alert-custom alert-${
			type === 'error' ? 'error' : 'success'
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

		// Auto remove after 3 seconds
		setTimeout(() => {
			messageEl.style.animation = 'slideOutRight 0.3s ease';
			setTimeout(() => {
				if (messageEl.parentNode) {
					messageEl.parentNode.removeChild(messageEl);
				}
			}, 300);
		}, 3000);
	}
}

// Global functions for template usage
window.updateQuantity = function (itemId, change) {
	if (window.cartManager) {
		window.cartManager.updateQuantity(itemId, change);
	}
};

window.setQuantity = function (itemId, quantity) {
	if (window.cartManager) {
		window.cartManager.setQuantity(itemId, quantity);
	}
};

window.removeItem = function (itemId) {
	if (window.cartManager) {
		window.cartManager.removeItem(itemId);
	}
};

window.clearCart = function () {
	if (window.cartManager) {
		window.cartManager.clearCart();
	}
};

window.applyPromoCode = function () {
	if (window.cartManager) {
		window.cartManager.applyPromoCode();
	}
};

window.proceedToCheckout = function () {
	if (window.cartManager) {
		window.cartManager.proceedToCheckout();
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
