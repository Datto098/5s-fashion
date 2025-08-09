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
                                        <input type="number" class="quantity-input" value="1" min="1" max="99" id="productQuantity">
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
                                            <button class="btn btn-secondary w-100" disabled style="border-radius: 50px; padding: 15px 30px;">
                                                <i class="fas fa-times me-2"></i>
                                                Hết hàng
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
                            <button class="btn btn-outline-secondary btn-sm active" style="border-radius: 25px; min-width: 45px;">
                                M
                            </button>
                            <button class="btn btn-outline-secondary btn-sm" style="border-radius: 25px; min-width: 45px;">
                                L
                            </button>
                            <button class="btn btn-outline-secondary btn-sm" style="border-radius: 25px; min-width: 45px;">
                                XL
                            </button>
                        </div>
                    </div>

                    <!-- Color variants -->
                    <div class="variant-group">
                        <label class="form-label small fw-semibold">Màu sắc:</label>
                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-outline-secondary btn-sm active" style="border-radius: 25px;">
                                Trắng
                            </button>
                            <button class="btn btn-outline-secondary btn-sm" style="border-radius: 25px;">
                                Đen
                            </button>
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
	 * Show notification
	 */
	showNotification(message, type = 'success') {
		if (typeof showNotification === 'function') {
			showNotification(message, type);
		} else {
			// Fallback alert
			alert(message);
		}
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

		const images = [];

		// Add featured image first
		if (product.featured_image) {
			images.push(product.featured_image);
		}

		// Add additional images from API
		if (product.images && Array.isArray(product.images)) {
			product.images.forEach((img) => {
				const imgPath = img.image_path || img.path || img;
				if (imgPath && imgPath !== product.featured_image) {
					images.push(imgPath);
				}
			});
		}

		// Fallback to product.image if available
		if (images.length === 0 && product.image) {
			images.push(product.image);
		}

		// If still no images, use placeholder
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
									<span class="badge bg-secondary fs-6 px-3 py-2" style="border-radius: 25px;">
										Hết hàng
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
