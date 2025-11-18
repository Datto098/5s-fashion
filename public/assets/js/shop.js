/**
 * Shop JavaScript
 * zone Fashion E-commerce Platform
 */

class ShopManager {
	constructor() {
		this.currentFilters = {
			search: '',
			categories: [],
			brands: [],
			priceMin: null,
			priceMax: null,
			sort: 'newest',
			featured: null,
			sale: null,
		};

		this.currentView = 'grid';
		this.isLoading = false;
		this.priceTimeout = null;

		this.init();
	}

	init() {
		this.bindEvents();
		this.initializePriceRange();
		this.loadUrlParams();
		this.updateProductCount();
	}

	bindEvents() {
		// Search functionality
		const searchInput = document.getElementById('product-search');
		if (searchInput) {
			let searchTimeout;
			searchInput.addEventListener('input', (e) => {
				clearTimeout(searchTimeout);
				searchTimeout = setTimeout(() => {
					this.handleSearch(e.target.value);
				}, 500);
			});
		}

		// Filter group toggles
		document.querySelectorAll('.filter-title').forEach(title => {
			title.addEventListener('click', (e) => {
				this.toggleFilterGroup(e.currentTarget);
			});
		});

		// Filter checkboxes
		document
			.querySelectorAll('input[name="category"], input[name="brand"]')
			.forEach((checkbox) => {
				checkbox.addEventListener('change', () =>
					this.handleFilterChange()
				);
			});

		// Price range
		document
			.getElementById('price-slider')
			?.addEventListener('input', (e) => this.handlePriceSlider(e));
		document
			.getElementById('price-slider')
			?.addEventListener('change', (e) => this.handlePriceChange());

		// Price input fields
		document
			.getElementById('min-price')
			?.addEventListener('input', () => this.handlePriceChange());
		document
			.getElementById('max-price')
			?.addEventListener('input', () => this.handlePriceChange());
		document
			.getElementById('min-price')
			?.addEventListener('change', () => this.handlePriceChange());
		document
			.getElementById('max-price')
			?.addEventListener('change', () => this.handlePriceChange());

		// View toggle
		document.querySelectorAll('.view-btn').forEach((btn) => {
			btn.addEventListener('click', (e) =>
				this.toggleView(e.target.closest('.view-btn').dataset.view)
			);
		});

		// Sort dropdown
		document
			.getElementById('sort-select')
			?.addEventListener('change', (e) =>
				this.handleSort(e.target.value)
			);

		// Clear filters
		document
			.getElementById('clear-filters')
			?.addEventListener('click', () => this.clearAllFilters());

		// Reset search
		document
			.getElementById('reset-search')
			?.addEventListener('click', () => this.resetSearch());

		// Mobile filter toggle
		this.setupMobileFilters();
	}

	setupMobileFilters() {
		// Create mobile filter toggle button
		const toolbar = document.querySelector('.shop-toolbar');
		if (toolbar && window.innerWidth <= 991) {
			const filterToggle = document.createElement('button');
			filterToggle.className = 'filter-toggle';
			filterToggle.innerHTML = '<i class="fas fa-filter me-2"></i>Bộ lọc';
			filterToggle.addEventListener('click', () =>
				this.toggleMobileFilters()
			);

			toolbar.insertBefore(filterToggle, toolbar.firstChild);
		}

		// Add close button to sidebar
		const sidebar = document.querySelector('.shop-sidebar');
		if (sidebar) {
			const closeBtn = document.createElement('button');
			closeBtn.className = 'sidebar-close d-lg-none';
			closeBtn.innerHTML = '<i class="fas fa-times"></i>';
			closeBtn.addEventListener('click', () => this.closeMobileFilters());

			sidebar.insertBefore(closeBtn, sidebar.firstChild);
		}
	}

	toggleMobileFilters() {
		const sidebar = document.querySelector('.shop-sidebar');
		if (sidebar) {
			sidebar.classList.add('show');
			document.body.style.overflow = 'hidden';
		}
	}

	closeMobileFilters() {
		const sidebar = document.querySelector('.shop-sidebar');
		if (sidebar) {
			sidebar.classList.remove('show');
			document.body.style.overflow = '';
		}
	}

	initializePriceRange() {
		const slider = document.getElementById('price-slider');
		const minInput = document.getElementById('min-price');
		const maxInput = document.getElementById('max-price');
		const displayElement = document.getElementById('current-price-display');

		if (slider && minInput && maxInput) {
			// Set initial values
			minInput.value = 0;
			maxInput.value = 2500000;
			slider.value = 2500000;

			// Update the initial price display
			if (displayElement) {
				displayElement.textContent = this.formatPrice(slider.value);
			}
		}
	}

	handlePriceSlider(e) {
		const value = parseInt(e.target.value);
		const maxInput = document.getElementById('max-price');
		const displayElement = document.getElementById('current-price-display');

		if (maxInput) {
			// Update max price input with slider value
			maxInput.value = value;

			// Update the price display
			if (displayElement) {
				displayElement.textContent = this.formatPrice(value);
			}

			// Apply filter immediately
			this.handlePriceChange();
		}
	}

	handlePriceChange() {
		const minPriceInput = document.getElementById('min-price');
		const maxPriceInput = document.getElementById('max-price');
		const slider = document.getElementById('price-slider');
		const displayElement = document.getElementById('current-price-display');

		const minPrice = minPriceInput?.value;
		const maxPrice = maxPriceInput?.value;

		// Update current filters
		this.currentFilters.priceMin = minPrice && parseInt(minPrice) > 0 ? parseInt(minPrice) : null;
		this.currentFilters.priceMax = maxPrice && parseInt(maxPrice) > 0 ? parseInt(maxPrice) : null;

		// Update slider position if max price changed
		if (slider && maxPrice && parseInt(maxPrice) > 0) {
			slider.value = parseInt(maxPrice);
			// Update display
			if (displayElement) {
				displayElement.textContent = this.formatPrice(parseInt(maxPrice));
			}
		} else if (displayElement && (!maxPrice || parseInt(maxPrice) === 0)) {
			// Reset to default if empty
			if (slider) slider.value = 2500000;
			displayElement.textContent = this.formatPrice(2500000);
		}

		console.log('Price filter updated:', this.currentFilters.priceMin, 'to', this.currentFilters.priceMax);

		// Apply filters with debounce
		clearTimeout(this.priceTimeout);
		this.priceTimeout = setTimeout(() => {
			this.applyFilters();
		}, 500);
	}

	toggleFilterGroup(titleElement) {
		const filterGroup = titleElement.closest('.filter-group');
		const content = filterGroup.querySelector('.filter-content');
		const toggleIcon = titleElement.querySelector('.toggle-icon');
		
		if (!content || !toggleIcon) return;
		
		const isCollapsed = content.classList.contains('collapsed');
		
		if (isCollapsed) {
			// Expand
			content.classList.remove('collapsed');
			toggleIcon.classList.remove('collapsed');
			toggleIcon.style.transform = 'rotate(0deg)';
		} else {
			// Collapse
			content.classList.add('collapsed');
			toggleIcon.classList.add('collapsed');
			toggleIcon.style.transform = 'rotate(-90deg)';
		}
	}

	updatePriceDisplay() {
		const slider = document.getElementById('price-slider');
		const maxPrice = slider ? slider.value : 5000000;

		const labels = document.querySelector('.price-labels');
		if (labels) {
			labels.innerHTML = `
                <span>${this.formatPrice(0)}</span>
                <span>${this.formatPrice(5000000)}</span>
            `;
		}

		const displayElement = document.getElementById('current-price-display');
		if (displayElement) {
			displayElement.textContent = this.formatPrice(maxPrice);
		}
	}

	handleSearch(query) {
		this.currentFilters.search = query.toLowerCase();
		this.applyFilters();
	}

	handleFilterChange() {
		// Update category filters
		this.currentFilters.categories = Array.from(
			document.querySelectorAll('input[name="category"]:checked')
		)
			.map((input) => input.value)
			.filter((value) => value !== 'all');

		// Update brand filters
		this.currentFilters.brands = Array.from(
			document.querySelectorAll('input[name="brand"]:checked')
		).map((input) => input.value);

		this.applyFilters();
	}

	handleSort(sortValue) {
		this.currentFilters.sort = sortValue;
		this.applyFilters();
	}

	applyFilters() {
		if (this.isLoading) return;

		this.showLoading();

		// Simulate API delay
		setTimeout(() => {
			this.filterProducts();
			this.hideLoading();
			this.updateProductCount();
			this.updateUrl();
		}, 300);
	}

	filterProducts() {
		const products = document.querySelectorAll('.product-item');
		let visibleCount = 0;

		products.forEach((product) => {
			let isVisible = true;

			// Search filter
			if (this.currentFilters.search) {
				const productName = product.dataset.name || '';
				if (!productName.includes(this.currentFilters.search)) {
					isVisible = false;
				}
			}

            // Category filter - handle multiple categories with IDs
            if (this.currentFilters.categories.length > 0) {
                const productCategory = product.dataset.category;
                if (!this.currentFilters.categories.includes(productCategory)) {
                    isVisible = false;
                }
            }			// Brand filter
			if (this.currentFilters.brands.length > 0) {
				const productBrand = product.dataset.brand;
				if (!this.currentFilters.brands.includes(productBrand)) {
					isVisible = false;
				}
			}

			// Price filter
			if (this.currentFilters.priceMin || this.currentFilters.priceMax) {
				const productPrice = parseInt(product.dataset.price || 0);

				if (
					this.currentFilters.priceMin &&
					productPrice < this.currentFilters.priceMin
				) {
					isVisible = false;
				}

				if (
					this.currentFilters.priceMax &&
					productPrice > this.currentFilters.priceMax
				) {
					isVisible = false;
				}
			}

			// Rating filter removed

			// Show/hide product
			if (isVisible) {
				product.style.display = 'block';
				product.style.opacity = '1';
				visibleCount++;
			} else {
				product.style.display = 'none';
				product.style.opacity = '0';
			}
		});

		// Sort visible products
		this.sortProducts();

		// Show/hide no products message
		this.toggleNoProductsMessage(visibleCount === 0);

		return visibleCount;
	}

	sortProducts() {
		const container = document.getElementById('products-row');
		const products = Array.from(
			document.querySelectorAll(
				'.product-item[style*="display: block"], .product-item:not([style*="display: none"])'
			)
		);

		products.sort((a, b) => {
			switch (this.currentFilters.sort) {
				case 'price-asc':
					return (
						parseInt(a.dataset.price || 0) -
						parseInt(b.dataset.price || 0)
					);

				case 'price-desc':
					return (
						parseInt(b.dataset.price || 0) -
						parseInt(a.dataset.price || 0)
					);

				case 'name-asc':
					return (a.dataset.name || '').localeCompare(
						b.dataset.name || ''
					);

				case 'name-desc':
					return (b.dataset.name || '').localeCompare(
						a.dataset.name || ''
					);

				case 'rating':
					return (
						parseFloat(b.dataset.rating || 0) -
						parseFloat(a.dataset.rating || 0)
					);

				case 'oldest':
					return 1; // Keep original order for oldest

				case 'newest':
				default:
					return -1; // Reverse order for newest
			}
		});

		// Reappend sorted products
		products.forEach((product) => {
			container.appendChild(product);
		});
	}

	toggleView(view) {
		this.currentView = view;

		// Update button states
		document.querySelectorAll('.view-btn').forEach((btn) => {
			btn.classList.toggle('active', btn.dataset.view === view);
		});

		// Update container class
		const container = document.getElementById('products-grid');
		if (container) {
			container.classList.toggle('list-view', view === 'list');
		}

		// Save preference
		localStorage.setItem('shop-view', view);
	}

	clearAllFilters() {
		// Reset form elements
		document
			.querySelectorAll('input[type="checkbox"], input[type="radio"]')
			.forEach((input) => {
				input.checked = false;
			});

		// Check "all categories" if exists
		const allCategories = document.querySelector(
			'input[name="category"][value="all"]'
		);
		if (allCategories) {
			allCategories.checked = true;
		}

		// Reset price inputs
		document.getElementById('min-price').value = '';
		document.getElementById('max-price').value = '';
		document.getElementById('price-slider').value = 2500000;

		// Reset search
		document.getElementById('product-search').value = '';

		// Reset sort
		document.getElementById('sort-select').value = 'newest';

		// Preserve featured/sale
		const featured = this.currentFilters.featured;
		const sale = this.currentFilters.sale;
		this.currentFilters = {
			search: '',
			categories: [],
			brands: [],
			priceMin: null,
			priceMax: null,
			sort: 'newest',
			featured: featured,
			sale: sale,
		};

		this.applyFilters();
	}

	resetSearch() {
		this.clearAllFilters();
	}

	showLoading() {
		this.isLoading = true;
		const loadingState = document.querySelector('.loading-state');
		const productsContainer = document.getElementById('products-grid');

		if (loadingState && productsContainer) {
			loadingState.classList.remove('d-none');
			productsContainer.style.opacity = '0.5';
		}
	}

	hideLoading() {
		this.isLoading = false;
		const loadingState = document.querySelector('.loading-state');
		const productsContainer = document.getElementById('products-grid');

		if (loadingState && productsContainer) {
			loadingState.classList.add('d-none');
			productsContainer.style.opacity = '1';
		}
	}

	toggleNoProductsMessage(show) {
		const noProducts = document.querySelector('.no-products');
		const productsGrid = document.getElementById('products-row');

		if (noProducts && productsGrid) {
			if (show) {
				noProducts.style.display = 'block';
				productsGrid.style.display = 'none';
			} else {
				noProducts.style.display = 'none';
				productsGrid.style.display = 'flex';
			}
		}
	}

	updateProductCount() {
		const visibleProducts = document.querySelectorAll(
			'.product-item[style*="display: block"], .product-item:not([style*="display: none"])'
		).length;
		const totalProducts = document.querySelectorAll('.product-item').length;

		const showingCount = document.getElementById('showing-count');
		const totalCount = document.getElementById('total-count');

		if (showingCount && totalCount) {
			showingCount.textContent = `1-${visibleProducts}`;
			totalCount.textContent = visibleProducts;
		}
	}

	updateUrl() {
		const params = new URLSearchParams();

		// Always set featured and sale if present in currentFilters
		if (this.currentFilters.featured) {
			params.set('featured', this.currentFilters.featured);
		}
		if (this.currentFilters.sale) {
			params.set('sale', this.currentFilters.sale);
		}

		if (this.currentFilters.search) {
			params.set('search', this.currentFilters.search);
		}

        if (this.currentFilters.categories.length > 0) {
            // Sử dụng categories parameter với ID của danh mục
            params.set('categories', this.currentFilters.categories.join(','));
        }		if (this.currentFilters.brands.length > 0) {
			params.set('brands', this.currentFilters.brands.join(','));
		}

		if (this.currentFilters.priceMin) {
			params.set('price_min', this.currentFilters.priceMin);
		}

		if (this.currentFilters.priceMax) {
			params.set('price_max', this.currentFilters.priceMax);
		}

		if (this.currentFilters.sort !== 'newest') {
			params.set('sort', this.currentFilters.sort);
		}

		const newUrl = params.toString()
			? `${window.location.pathname}?${params.toString()}`
			: window.location.pathname;
		history.replaceState(null, '', newUrl);
	}

	loadUrlParams() {
		const params = new URLSearchParams(window.location.search);

		// Load featured/sale
		const featured = params.get('featured');
		if (featured) {
			this.currentFilters.featured = featured;
		}
		const sale = params.get('sale');
		if (sale) {
			this.currentFilters.sale = sale;
		}

		// Load search
		const search = params.get('search');
		if (search) {
			document.getElementById('product-search').value = search;
			this.currentFilters.search = search;
		}

		// Load featured parameter (already loaded above, just log if present)
		if (featured) {
			console.log('Featured parameter found:', featured);
		}

        // Load categories parameter
        const categories = params.get('categories');
        if (categories) {
            this.currentFilters.categories = categories.split(',');
            this.currentFilters.categories.forEach((categoryId) => {
                const checkbox = document.querySelector(
                    `input[name="category"][value="${categoryId}"]`
                );
                if (checkbox) checkbox.checked = true;
            });
            console.log('Categories parameter found:', categories);
        }		// Load brands
		const brands = params.get('brands');
		if (brands) {
			this.currentFilters.brands = brands.split(',');
			this.currentFilters.brands.forEach((brandId) => {
				const checkbox = document.querySelector(
					`input[name="brand"][value="${brandId}"]`
				);
				if (checkbox) checkbox.checked = true;
			});
		}

		// Load price range
		const priceMin = params.get('price_min');
		const priceMax = params.get('price_max');
		if (priceMin) {
			document.getElementById('min-price').value = priceMin;
			this.currentFilters.priceMin = parseInt(priceMin);
		}
		if (priceMax) {
			document.getElementById('max-price').value = priceMax;
			this.currentFilters.priceMax = parseInt(priceMax);
		}

		// Load sort
		const sort = params.get('sort');
		if (sort) {
			document.getElementById('sort-select').value = sort;
			this.currentFilters.sort = sort;
		}

		// Load saved view preference
		const savedView = localStorage.getItem('shop-view');
		if (savedView) {
			this.toggleView(savedView);
		}

		// Apply initial filters
		this.applyFilters();
	}

	formatPrice(price) {
		return new Intl.NumberFormat('vi-VN', {
			style: 'currency',
			currency: 'VND',
		}).format(price);
	}
}

// Enhanced product interactions
class ProductInteractions {
	constructor() {
		this.init();
	}

	init() {
		this.bindProductEvents();
		this.setupQuickView();
		this.setupImageHover();
	}

	bindProductEvents() {
		document.addEventListener('click', (e) => {
			// Quick view
			if (
				e.target.matches('[onclick*="quickView"]') ||
				e.target.closest('[onclick*="quickView"]')
			) {
				e.preventDefault();
				const btn = e.target.matches('[onclick*="quickView"]')
					? e.target
					: e.target.closest('[onclick*="quickView"]');
				const productId = btn.getAttribute('onclick').match(/\d+/)[0];
				this.showQuickView(productId);
			}

			// Add to wishlist
			if (
				e.target.matches('[onclick*="toggleWishlist"]') ||
				e.target.closest('[onclick*="toggleWishlist"]')
			) {
				e.preventDefault();
				const btn = e.target.matches('[onclick*="toggleWishlist"]')
					? e.target
					: e.target.closest('[onclick*="toggleWishlist"]');
				const productId = btn.getAttribute('onclick').match(/\d+/)[0];
				this.toggleWishlist(productId, btn);
			}

			// Add to cart
			if (
				e.target.matches('[onclick*="addToCart"]') ||
				e.target.closest('[onclick*="addToCart"]')
			) {
				e.preventDefault();
				e.stopPropagation(); // Prevent event bubbling
				const btn = e.target.matches('[onclick*="addToCart"]')
					? e.target
					: e.target.closest('[onclick*="addToCart"]');
				const productId = btn.getAttribute('onclick').match(/\d+/)[0];

				// Use global addToCart function instead of class method
				if (typeof window.addToCart === 'function') {
					window.addToCart(productId, 1, null);
				} else {
					this.addToCart(productId, btn);
				}
			}
		});
	}

	setupImageHover() {
		document.querySelectorAll('.product-card').forEach((card) => {
			const image = card.querySelector('.product-image img');
			if (image && image.dataset.hover) {
				const originalSrc = image.src;
				const hoverSrc = image.dataset.hover;

				card.addEventListener('mouseenter', () => {
					image.style.transition = 'opacity 0.3s ease';
					image.style.opacity = '0';
					setTimeout(() => {
						image.src = hoverSrc;
						image.style.opacity = '1';
					}, 150);
				});

				card.addEventListener('mouseleave', () => {
					image.style.opacity = '0';
					setTimeout(() => {
						image.src = originalSrc;
						image.style.opacity = '1';
					}, 150);
				});
			}
		});
	}

	setupQuickView() {
		// Create quick view modal if it doesn't exist
		if (!document.getElementById('quickViewModal')) {
			const modal = document.createElement('div');
			modal.className = 'modal fade';
			modal.id = 'quickViewModal';
			modal.innerHTML = `
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Xem nhanh sản phẩm</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="quick-view-image">
                                        <img src="" alt="" class="img-fluid">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="quick-view-info">
                                        <h4 class="product-title"></h4>
                                        <div class="product-rating mb-2"></div>
                                        <div class="product-price mb-3"></div>
                                        <div class="product-description mb-3"></div>
                                        <div class="product-actions">
                                            <button class="btn btn-primary me-2">
                                                <i class="fas fa-shopping-cart me-2"></i>Thêm vào giỏ
                                            </button>
                                            <button class="btn btn-outline-secondary">
                                                <i class="fas fa-heart me-2"></i>Yêu thích
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
			document.body.appendChild(modal);
		}
	}

	showQuickView(productId) {
		// Find product data
		const productCard = document.querySelector(
			`[data-product-id="${productId}"]`
		);
		if (!productCard) return;

		const modal = document.getElementById('quickViewModal');
		const image = modal.querySelector('.quick-view-image img');
		const title = modal.querySelector('.product-title');
		const rating = modal.querySelector('.product-rating');
		const price = modal.querySelector('.product-price');
		const description = modal.querySelector('.product-description');

		// Populate modal with product data
		const productImage = productCard.querySelector('.product-image img');
		const productTitle = productCard.querySelector('.product-title');
		const productPrice = productCard.querySelector('.product-price');
		const productRating = productCard.querySelector('.product-rating');

		if (productImage) image.src = productImage.src;
		if (productTitle) title.textContent = productTitle.textContent;
		if (productPrice) price.innerHTML = productPrice.innerHTML;
		if (productRating) rating.innerHTML = productRating.innerHTML;

		description.textContent =
			'Sản phẩm chất lượng cao, thiết kế hiện đại và phù hợp với nhiều phong cách khác nhau.';

		// Show modal
		const bsModal = new bootstrap.Modal(modal);
		bsModal.show();
	}

	toggleWishlist(productId, button) {
		const icon = button.querySelector('i');
		const isInWishlist = icon.classList.contains('fas');

		// Toggle icon
		if (isInWishlist) {
			icon.classList.remove('fas');
			icon.classList.add('far');
			showToast('Đã xóa khỏi danh sách yêu thích', 'info');
		} else {
			icon.classList.remove('far');
			icon.classList.add('fas');
			showToast('Đã thêm vào danh sách yêu thích', 'success');
		}

		// Add animation
		button.style.transform = 'scale(0.8)';
		setTimeout(() => {
			button.style.transform = 'scale(1)';
		}, 150);
	}

	addToCart(productId, button) {
		const originalText = button.innerHTML;

		// Show loading state
		button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
		button.disabled = true;

		// Simulate API call
		setTimeout(() => {
			// Reset button
			button.innerHTML = originalText;
			button.disabled = false;

			// Show success
			showToast('Đã thêm sản phẩm vào giỏ hàng', 'success');

			// Update cart counter
			updateCartCounter();

			// Add bounce animation
			button.style.transform = 'scale(1.1)';
			setTimeout(() => {
				button.style.transform = 'scale(1)';
			}, 200);
		}, 1000);
	}
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
	const shopManager = new ShopManager();
	const productInteractions = new ProductInteractions();

	// Handle window resize for mobile filters
	window.addEventListener('resize', () => {
		if (window.innerWidth > 991) {
			const sidebar = document.querySelector('.shop-sidebar');
			if (sidebar) {
				sidebar.classList.remove('show');
				document.body.style.overflow = '';
			}
		}
	});
});

// Utility functions
function showToast(message, type = 'info') {
	// Check if showToast function exists in client.js
	if (typeof parent.showToast === 'function') {
		parent.showToast(message, type);
	} else if (
		typeof window.parent !== 'undefined' &&
		typeof window.parent.showToast === 'function'
	) {
		window.parent.showToast(message, type);
	} else {
		// Fallback to simple alert
		alert(message);
	}
}

function updateCartCounter() {
	// ALWAYS use unified cart manager - no fallbacks to prevent conflicts
	console.log(
		'🛍️ Shop: updateCartCounter called - doing nothing to prevent conflicts'
	);
	return; // Let unified manager handle everything automatically
}
