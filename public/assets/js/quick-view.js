/**
 * Quick View Modal System
 * Following UI.md design standards
 */

class QuickViewModal {
	constructor() {
		console.log('QuickViewModal constructor called');
		this.modal = null;
		this.modalContent = null;
		this.currentProductId = null;

		this.init();
	}

	init() {
		console.log('QuickViewModal init called');
		// Initialize modal elements
		this.modal = document.getElementById('quickViewModal');
		this.modalContent = document.getElementById('quickViewContent');

		console.log('Modal elements:', {
			modal: this.modal,
			modalContent: this.modalContent,
		});

		if (!this.modal || !this.modalContent) {
			console.warn('Quick view modal elements not found');
			return;
		}

		// Initialize Bootstrap modal
		this.bsModal = new bootstrap.Modal(this.modal);
		console.log('Bootstrap modal initialized:', this.bsModal);

		// Bind events
		this.bindEvents();
		console.log('QuickViewModal initialization complete');
	}

	bindEvents() {
		// Clean up when modal is hidden
		this.modal.addEventListener('hidden.bs.modal', () => {
			this.cleanup();
		});

		// Delegate clicks inside modal to handle variant selection and update quantity max
		this.modalContent.addEventListener('click', (e) => {
			const btn = e.target.closest('.btn');
			if (!btn) return;
			// If clicked button is a variant (size or color)
			if (btn.classList.contains('btn-outline-secondary') && btn.hasAttribute('data-stock')) {
				// Remove active from siblings
				btn.parentElement.querySelectorAll('.btn-outline-secondary').forEach(b => b.classList.remove('active'));
				btn.classList.add('active');
				// Update productQuantity max from button data-stock
				const stock = parseInt(btn.getAttribute('data-stock')) || 0;
				const qtyInput = this.modalContent.querySelector('#productQuantity');
				if (qtyInput) {
					qtyInput.setAttribute('max', String(stock || 99));
					// If current value exceeds max, clamp and trigger change
					if (parseInt(qtyInput.value) > (stock || 99)) {
						qtyInput.value = stock || 99;
						qtyInput.dispatchEvent(new Event('change'));
					}
				}

						// After selecting a variant button, update the action button availability
						this.updateActionButtonsAvailability();
			}
		});
	}


	/**
	 * Update availability of primary action buttons in quick view (add to cart)
	 * Disable add-to-cart when no size available or selected size has zero stock
	 */
	updateActionButtonsAvailability() {
		try {
			// Find primary add-to-cart button in modal (support multiple possible classes/selectors)
			const addBtnSelectors = [
				'.btn-primary-action',
				'.quickview-add-to-cart',
				"button[onclick*='addToCartFromQuickView']",
				"button[onclick*='addToCartAndClose']",
			];
			let addBtn = null;
			for (const sel of addBtnSelectors) {
				addBtn = this.modalContent.querySelector(sel);
				if (addBtn) break;
			}
			// Also update any product-page buttons (outside modal)
			const globalAddBtns = Array.from(document.querySelectorAll('.add-to-cart, #add-to-cart-btn, .add-to-cart-btn'));
			const globalBuyBtns = Array.from(document.querySelectorAll('.buy-now, .btn-buy-now'));
			if (!addBtn && globalAddBtns.length === 0) return;

			// Check size buttons inside modal (buttons with data-stock attribute)
			const sizeButtons = Array.from(this.modalContent.querySelectorAll('[data-stock]'))
				.filter(el => el.closest('.variant-group') && el.closest('.variant-group').querySelector('.form-label') && el.closest('.variant-group').querySelector('.form-label').textContent.toLowerCase().includes('kích'));

			// If no size buttons found, fallback to checking any data-stock buttons
			const stockButtons = sizeButtons.length ? sizeButtons : Array.from(this.modalContent.querySelectorAll('[data-stock]'));

			// Prefer the active selection (if any)
			const activeButton = stockButtons.find(b => b.classList.contains('active'));
			const activeStock = activeButton ? parseInt(activeButton.getAttribute('data-stock')) : null;

			const applyDisabled = (el, disabled, outOfStockLabel = false) => {
				if (!el) return;
				if (disabled) {
					el.disabled = true;
					el.setAttribute('aria-disabled', 'true');
					if (outOfStockLabel) el.innerHTML = '<i class="fas fa-times me-2"></i>Hết Hàng';
				} else {
					el.disabled = false;
					el.removeAttribute('aria-disabled');
					if (!outOfStockLabel) el.innerHTML = '<i class="fas fa-shopping-cart me-2"></i>Thêm vào giỏ hàng';
				}
			};

			if (activeButton) {
				// If an active selection exists, require its stock > 0
				if (activeStock > 0) {
					applyDisabled(addBtn, false);
					globalAddBtns.forEach(b => applyDisabled(b, false));
					globalBuyBtns.forEach(b => applyDisabled(b, false, false));
				} else {
					applyDisabled(addBtn, true, true);
					globalAddBtns.forEach(b => applyDisabled(b, true, true));
					globalBuyBtns.forEach(b => applyDisabled(b, true, true));
				}
			} else {
				// No specific active selection — enable if any variant has stock
				const anyAvailable = stockButtons.some(b => parseInt(b.getAttribute('data-stock')) > 0);
				if (anyAvailable) {
					applyDisabled(addBtn, false);
					globalAddBtns.forEach(b => applyDisabled(b, false));
					globalBuyBtns.forEach(b => applyDisabled(b, false));
				} else {
					applyDisabled(addBtn, true, true);
					globalAddBtns.forEach(b => applyDisabled(b, true, true));
					globalBuyBtns.forEach(b => applyDisabled(b, true, true));
				}
			}
		} catch (err) {
			console.error('updateActionButtonsAvailability error', err);
		}
	}
	/**
	 * Show product in quick view modal
	 */
	async show(productId) {
		if (!productId) {
			console.error('Product ID is required');
			return;
		}

		this.currentProductId = productId;

		// Show loading state
		this.showLoading();

		// Show modal
		this.bsModal.show();

		try {
			// Fetch product data
			const response = await fetch(
				`/5s-fashion/ajax/getProductForQuickView?id=${productId}`
			);
			const data = await response.json();
			console.log('API Response:', data);

			if (data.success) {
				const productData = data.data || data.product;
				console.log('Product data to render:', productData);
				this.renderProduct(productData);
				// Ensure action button availability reflects rendered variants/stock
				setTimeout(() => this.updateActionButtonsAvailability(), 0);
			} else {
				this.showError(
					data.message || 'Không thể tải thông tin sản phẩm'
				);
			}
		} catch (error) {
			console.error('Error loading product:', error);
			this.showError('Lỗi kết nối. Vui lòng thử lại sau.');
		}
	}

	/**
	 * Show loading state
	 */
	showLoading() {
		this.modalContent.innerHTML = `
            <div class="d-flex justify-content-center align-items-center" style="min-height: 300px;">
                <div class="text-center">
                    <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                    <p class="text-muted">Đang tải thông tin sản phẩm...</p>
                </div>
            </div>
        `;

		// After rendering the HTML, initialize variant selection and quantity limits
		try {
			this.initVariantSelection(product);
		} catch (err) {
			console.error('initVariantSelection error', err);
		}
	}

	/**
	 * Show error state
	 */
	showError(message) {
		this.modalContent.innerHTML = `
            <div class="d-flex justify-content-center align-items-center" style="min-height: 300px;">
                <div class="text-center">
                    <div class="text-danger mb-3">
                        <i class="fas fa-exclamation-circle" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="text-danger mb-3">Lỗi tải sản phẩm</h5>
                    <p class="text-muted">${message}</p>
                    <button class="btn-outline-primary-action" onclick="quickViewModal.show(${this.currentProductId})">
                        <i class="fas fa-refresh me-2"></i>
                        Thử lại
                    </button>
                </div>
            </div>
        `;
	}

	/**
	 * Render product content - Following UI.md Standards
	 */
	renderProduct(product) {
		// Validate product data
		if (!product) {
			console.error('Product data is undefined');
			this.showError('Không có thông tin sản phẩm');
			return;
		}

		console.log('Rendering product:', product);

		const salePrice =
			product.sale_price && product.sale_price > 0
				? product.sale_price
				: null;
		const originalPrice = product.price;
		const discountPercent = salePrice
			? Math.round(((originalPrice - salePrice) / originalPrice) * 100)
			: 0;

		// Format image URL
		let imageUrl = '/5s-fashion/public/assets/images/no-image.jpg';
		if (product.featured_image) {
			let imagePath = product.featured_image;
			if (imagePath.startsWith('/uploads/')) {
				imagePath = imagePath.substring(9);
			} else if (imagePath.startsWith('uploads/')) {
				imagePath = imagePath.substring(8);
			} else {
				imagePath = imagePath.replace(/^\/+/, '');
			}
			imageUrl = `/5s-fashion/serve-file.php?file=${encodeURIComponent(
				imagePath
			)}`;
		}

		this.modalContent.innerHTML = `
            <div class="container-fluid p-4">
                <div class="row g-4">
                    <!-- Product Images Carousel -->
                    <div class="col-md-6">
                        ${this.renderImageCarousel(product)}
                    </div>

                    <!-- Product Details -->
                    <div class="col-md-6">
                        <div class="product-details h-100 d-flex flex-column">
                            <!-- Category -->
                            ${
								product.category_name
									? `
                                <div class="mb-2">
                                    <span class="text-muted small text-uppercase">
                                        <i class="fas fa-tag me-1"></i>
                                        ${this.escapeHtml(
											product.category_name
										)}
                                    </span>
                                </div>
                            `
									: ''
							}

                            <!-- Product Name -->
                            <h3 class="product-name fw-bold mb-3" style="color: var(--dark-color, #343a40); line-height: 1.3;">
                                ${this.escapeHtml(product.name)}
                            </h3>

                            <!-- Rating -->
                            ${
								product.rating > 0
									? `
                                <div class="product-rating mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="stars me-2">
                                            ${this.renderStars(product.rating)}
                                        </div>
                                        <span class="text-muted small">
                                            (${
												product.review_count || 0
											} đánh giá)
                                        </span>
                                    </div>
                                </div>
                            `
									: ''
							}

                            <!-- Price -->
                            <div class="product-price mb-4">
                                ${
									salePrice
										? `
                                    <div class="d-flex align-items-baseline gap-3">
                                        <span class="current-price fw-bold text-danger" style="font-size: 2rem;">
                                            ${this.formatCurrency(salePrice)}
                                        </span>
                                        <span class="original-price text-muted text-decoration-line-through" style="font-size: 1.2rem;">
                                            ${this.formatCurrency(
												originalPrice
											)}
                                        </span>
                                    </div>
                                `
										: `
                                    <span class="current-price fw-bold" style="font-size: 2rem; color: var(--primary-color, #007bff);">
                                        ${this.formatCurrency(originalPrice)}
                                    </span>
                                `
								}
                            </div>

                            <!-- Description -->
                            ${
								product.description
									? `
                                <div class="product-description mb-4">
                                    <h6 class="fw-semibold mb-2">Mô tả sản phẩm:</h6>
                                    <p class="text-muted" style="line-height: 1.6;">
                                        ${this.truncateDescription(
											product.description,
											200
										)}
                                    </p>
                                </div>
                            `
									: ''
							}

                            <!-- Variants (if available) -->
							${this.renderVariants(product.variants)}

                            <!-- Quantity Selector -->
                            ${
								product.in_stock
									? `
								<div class="mb-4">
									<label class="quantity-label">Số lượng:</label>
									<div class="quantity-selector">
										<button type="button" class="quantity-btn" onclick="window.quickViewModal.decreaseQuantity()">−</button>
										<input type="number" class="quantity-input" value="1" min="1" max="${product.stock || 99}" id="productQuantity">
										<button type="button" class="quantity-btn" onclick="window.quickViewModal.increaseQuantity()">+</button>
									</div>
								</div>
                            `
									: ''
							}

                            <!-- Actions -->
                            <div class="product-actions mt-auto pt-4">
                                <div class="row g-3">
                                    <div class="col-12">
                                        ${
											product.in_stock
												? `
                                            <button class="btn-primary-action w-100" onclick="window.quickViewModal.addToCartAndClose(${product.id})">
                                                <i class="fas fa-shopping-cart me-2"></i>
                                                Thêm vào giỏ hàng
                                            </button>
                                        `
												: `
											<button class="btn btn-secondary w-100" disabled aria-disabled="true" style="border-radius: 50px; padding: 15px 30px;">
												<i class="fas fa-times me-2"></i>
												Hết Hàng
											</button>
                                        `
										}
                                    </div>
                                    <div class="col-6">
                                        <button class="btn-outline-primary-action w-100" onclick="window.quickViewModal.addToWishlist(${
											product.id
										})">
                                            <i class="far fa-heart me-2"></i>
                                            Yêu thích
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <a href="/5s-fashion/product/${
											product.id
										}" class="btn-outline-secondary-action w-100 text-decoration-none">
                                            <i class="fas fa-eye me-2"></i>
                                            Xem chi tiết
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
	}

	/**
	 * Render star rating
	 */
	renderStars(rating) {
		let stars = '';
		const fullStars = Math.floor(rating);
		const hasHalfStar = rating - fullStars >= 0.5;
		const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);

		// Full stars
		for (let i = 0; i < fullStars; i++) {
			stars += '<i class="fas fa-star text-warning"></i>';
		}

		// Half star
		if (hasHalfStar) {
			stars += '<i class="fas fa-star-half-alt text-warning"></i>';
		}

		// Empty stars
		for (let i = 0; i < emptyStars; i++) {
			stars += '<i class="far fa-star text-warning"></i>';
		}

		return stars;
	}

	/**
	 * Render product variants (if any)
	 */
	renderVariants(variants) {
		if (!variants || variants.length === 0) {
			return '';
		}

		return `
            <div class="product-variants mb-4">
                <h6 class="fw-semibold mb-3">Tùy chọn:</h6>
                <div class="variants-container">
                    <!-- Size variants -->
                    <div class="variant-group mb-3">
                        <label class="form-label small fw-semibold">Kích thước:</label>
							<div class="d-flex flex-wrap gap-2">
									${variants
								.slice(0, 10)
								.map((v) => {
									const stockVal = parseInt(v.stock_quantity || v.stock || 0) || 0;
									const disabledAttr = stockVal <= 0 ? 'disabled aria-disabled="true"' : '';
									const extraClass = stockVal <= 0 ? ' unavailable disabled' : '';
									return `
									<button class="btn btn-outline-secondary btn-sm ${extraClass}" data-variant-id="${v.id || ''}" data-stock="${stockVal}" ${disabledAttr} style="border-radius: 25px; min-width: 45px;">
										${v.size || 'One Size'}
									</button>
								`
								}).join('')}
						</div>
                    </div>

                    <!-- Color variants -->
                    <div class="variant-group">
                        <label class="form-label small fw-semibold">Màu sắc:</label>
							<div class="d-flex flex-wrap gap-2">
							${variants
								.slice(0, 10)
								.map((v) => `
									<button class="btn btn-outline-secondary btn-sm" data-variant-id="${v.id || ''}" data-stock="${v.stock_quantity || v.stock || 0}" data-color="${v.color || ''}" style="border-radius: 25px;">
										${v.color || 'Default'}
									</button>
								`)
								.join('')}
						</div>
                    </div>
                </div>
            </div>
        `;
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
	 * Truncate description
	 */
	truncateDescription(text, maxLength) {
		if (!text || text.length <= maxLength) {
			return text;
		}
		return text.substring(0, maxLength) + '...';
	}

	/**
	 * Escape HTML
	 */
	escapeHtml(text) {
		const div = document.createElement('div');
		div.textContent = text;
		return div.innerHTML;
	}

	/**
	 * Add to cart and close modal
	 */
	addToCartAndClose(productId) {
		const quantity = this.getCurrentQuantity();

		// Use global cart manager
		if (typeof unifiedCartManager !== 'undefined') {
			unifiedCartManager.addToCart(productId, quantity);
		} else if (typeof addToCart === 'function') {
			addToCart(productId, quantity);
		} else {
			console.warn('Cart manager not available');
			this.showNotification(
				'Chức năng giỏ hàng đang được cập nhật',
				'warning'
			);
		}

		// Close modal
		this.bsModal.hide();
	}

	/**
	 * Add to wishlist
	 */
	addToWishlist(productId) {
		// Use global wishlist manager
		if (typeof unifiedWishlistManager !== 'undefined') {
			unifiedWishlistManager.add(productId);
		} else {
			console.warn('Wishlist manager not available');
			this.showNotification(
				'Chức năng yêu thích đang được cập nhật',
				'warning'
			);
		}
	}

	/**
	 * Show notification - sử dụng hệ thống thống nhất
	 */
	showNotification(message, type = 'success') {
		// Sử dụng hệ thống notification thống nhất
		if (
			window.notifications &&
			typeof window.notifications.show === 'function'
		) {
			window.notifications.show(message, type);
			return;
		}

		// Fallback: sử dụng global function
		if (typeof showNotification === 'function') {
			showNotification(message, type);
			return;
		}

		// Final fallback: console log thay vì alert
		console.log(`${type.toUpperCase()}: ${message}`);
	}

	/**
	 * Increase quantity
	 */
	increaseQuantity() {
		console.log('increaseQuantity called');
		const input =
			document.getElementById('productQuantity') ||
			document.getElementById('quantityInput');
		if (input) {
			const currentValue = parseInt(input.value) || 1;
			const maxValue = parseInt(input.max) || 99;
			if (currentValue < maxValue) {
				input.value = currentValue + 1;
				// Trigger change event for any listeners
				input.dispatchEvent(new Event('change'));
			}
		}
	}

	/**
	 * Decrease quantity
	 */
	decreaseQuantity() {
		console.log('decreaseQuantity called');
		const input =
			document.getElementById('productQuantity') ||
			document.getElementById('quantityInput');
		if (input) {
			const currentValue = parseInt(input.value) || 1;
			const minValue = parseInt(input.min) || 1;
			if (currentValue > minValue) {
				input.value = currentValue - 1;
				// Trigger change event for any listeners
				input.dispatchEvent(new Event('change'));
			}
		}
	}

	/**
	 * Get current quantity
	 */
	getCurrentQuantity() {
		const input =
			document.getElementById('productQuantity') ||
			document.getElementById('quantityInput');
		return input ? parseInt(input.value) || 1 : 1;
	}

	/**
	 * Render image carousel for product
	 */
	renderImageCarousel(product) {
		console.log('renderImageCarousel called with product:', product);

		// Validate product data
		if (!product) {
			console.error('Product is undefined in renderImageCarousel');
			return `<div class="text-center p-4">
				<img src="/public/assets/images/no-image.jpg" alt="No image" class="img-fluid" style="height: 400px; object-fit: cover;">
			</div>`;
		}

		// Collect images and dedupe while preserving order.
		const images = [];
		const seen = new Set();

		function normalize(p) {
			if (!p) return '';
			// strip leading slashes for consistent comparisons
			return String(p).replace(/^\/+/, '').trim();
		}

		// Helper to push unique image
		function pushUnique(p) {
			const n = normalize(p);
			if (!n) return;
			if (seen.has(n)) return;
			seen.add(n);
			images.push(p);
		}

		// Ensure featured image is first (if present)
		if (product.featured_image) {
			pushUnique(product.featured_image);
		}

		// Add additional images from API (preserve order)
		if (product.images && Array.isArray(product.images)) {
			product.images.forEach((img) => {
				const imgPath = img.image_path || img.path || img;
				pushUnique(imgPath);
			});
		}

		// Fallback to product.image if nothing collected yet
		if (images.length === 0 && product.image) {
			pushUnique(product.image);
		}

		// Final fallback to placeholder
		if (images.length === 0) {
			images.push('/public/assets/images/placeholder.jpg');
		}

		console.log('Images for carousel:', images);

		const discountPercent = this.calculateDiscountPercent(
			product.price,
			product.sale_price
		);
		const carouselId = 'productCarousel_' + Date.now();

		return `
			<div id="${carouselId}" class="carousel slide product-image-carousel" data-bs-ride="carousel">
				${
					images.length > 1
						? `
				<div class="carousel-indicators">
					${images
						.map(
							(_, index) => `
						<button type="button" data-bs-target="#${carouselId}" data-bs-slide-to="${index}" ${
								index === 0
									? 'class="active" aria-current="true"'
									: ''
							} aria-label="Slide ${index + 1}"></button>
					`
						)
						.join('')}
				</div>
				`
						: ''
				}

				<div class="carousel-inner">
					${images
						.map(
							(image, index) => `
						<div class="carousel-item ${index === 0 ? 'active' : ''}">
							<img src="${this.getImageUrl(image)}" alt="${this.escapeHtml(
								product.name
							)}" class="d-block w-100">

							${
								index === 0 && discountPercent > 0
									? `
								<div class="position-absolute top-0 start-0 m-3">
									<span class="badge bg-danger fs-6 px-3 py-2" style="border-radius: 25px;">
										-${discountPercent}%
									</span>
								</div>
							`
									: ''
							}

							${
								index === 0 && !product.in_stock
									? `
								<div class="position-absolute top-0 end-0 m-3">
									<span class="badge bg-secondary fs-6 px-3 py-2" style="border-radius: 25px;" aria-hidden="false">
										Hết Hàng
									</span>
								</div>
							`
									: ''
							}
						</div>
					`
						)
						.join('')}
				</div>

				${
					images.length > 1
						? `
				<button class="carousel-control-prev" type="button" data-bs-target="#${carouselId}" data-bs-slide="prev">
					<span class="carousel-control-prev-icon" aria-hidden="true"></span>
					<span class="visually-hidden">Previous</span>
				</button>
				<button class="carousel-control-next" type="button" data-bs-target="#${carouselId}" data-bs-slide="next">
					<span class="carousel-control-next-icon" aria-hidden="true"></span>
					<span class="visually-hidden">Next</span>
				</button>
				`
						: ''
				}
			</div>
		`;
	}

	/**
	 * Initialize variant buttons and quantity max based on first available variant
	 */
	initVariantSelection(product) {
		if (!product || !product.variants || product.variants.length === 0) return;

		// Debug: log entry and product/variants summary
		console.debug('initVariantSelection called', {
			productId: product.id,
			variants: (product.variants || []).map(v => ({ id: v.id, stock: v.stock_quantity || v.stock }))
		});

		// Find first in-stock variant or fallback to first
		const initial = product.variants.find(v => (parseInt(v.stock_quantity || v.stock) || 0) > 0) || product.variants[0];
		if (!initial) return;

		// Do NOT mutate DOM active classes here (avoid overriding user's clicks).
		// Instead, store the selected variant internally so other handlers can read it.
		selectedVariant = {
			id: initial.id,
			size: initial.size,
			price: parseFloat(initial.price) || 0,
			color: initial.color,
			stock_quantity: parseInt(initial.stock_quantity || initial.stock || 0)
		};

		// Set quantity input max to variant stock or product stock
		let stock = parseInt(initial.stock_quantity || initial.stock);
		if (isNaN(stock) || stock === 0) {
			const pstock = parseInt(product.stock);
			stock = !isNaN(pstock) && pstock > 0 ? pstock : 0;
		}
		if (!stock) stock = 0;
		const qtyInput = this.modalContent.querySelector('#productQuantity');
		const plusBtn = this.modalContent.querySelector('.quantity-selector .quantity-btn:last-of-type');
		const minusBtn = this.modalContent.querySelector('.quantity-selector .quantity-btn:first-of-type');
		if (qtyInput) {
			// ensure min is 1
			qtyInput.setAttribute('min', '1');
			// set max (use 99 as fallback if product intentionally unlimited)
			if (stock > 0) {
				qtyInput.setAttribute('max', String(stock));
			} else {
				qtyInput.setAttribute('max', String(product.stock || 99));
			}

			// clamp current value
			const cur = parseInt(qtyInput.value) || 1;
			if (stock > 0 && cur > stock) {
				qtyInput.value = stock;
				qtyInput.dispatchEvent(new Event('change'));
			} else if (cur < 1) {
				qtyInput.value = 1;
			}

			// enable/disable controls based on stock
			const controlsEnabled = stock > 0;
			qtyInput.disabled = !controlsEnabled ? true : false;
			if (plusBtn) {
				// Force-enable if max > current value
				try {
					const maxAttr = parseInt(qtyInput.getAttribute('max')) || parseInt(qtyInput.max) || 0;
					plusBtn.disabled = !(maxAttr > 0);
				} catch (e) {
					plusBtn.disabled = !controlsEnabled ? true : false;
				}
			}
			if (minusBtn) minusBtn.disabled = !controlsEnabled ? true : false;
		}

		// Also ensure Add to Cart button state matches stock
		const addBtn = this.modalContent.querySelector('.btn-primary-action') || this.modalContent.querySelector('.btn-primary');
		if (addBtn) {
			if (stock > 0) {
				addBtn.disabled = false;
			} else {
				addBtn.disabled = true;
			}
		}
	}

	/**
	 * Clean up modal content
	 */
	cleanup() {
		this.currentProductId = null;
		this.showLoading();
	}
}

// Initialize quick view modal when DOM is ready
let quickViewModal;
document.addEventListener('DOMContentLoaded', function () {
	console.log('Initializing QuickViewModal...');
	quickViewModal = new QuickViewModal();
	console.log('QuickViewModal initialized:', quickViewModal);

	// Expose globally for easy access
	window.quickViewModal = quickViewModal;
	console.log('QuickViewModal exposed globally');

	// Global quantity change function for compatibility
	window.changeQuantity = function (delta) {
		const input =
			document.getElementById('quantityInput') ||
			document.getElementById('productQuantity');
		if (input) {
			const currentValue = parseInt(input.value) || 1;
			const newValue = currentValue + delta;
			const minValue = parseInt(input.min) || 1;
			const maxValue = parseInt(input.max) || 99;

			if (newValue >= minValue && newValue <= maxValue) {
				input.value = newValue;
				input.dispatchEvent(new Event('change'));
			}
		}
	};
});

/**
 * Global quick view function - called from product cards
 */
function quickView(productId) {
	if (quickViewModal) {
		quickViewModal.show(productId);
	} else {
		console.error('Quick view modal not initialized');
	}
}

// Expose globally
window.quickView = quickView;
