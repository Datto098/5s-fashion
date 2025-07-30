/**
 * Client Website JavaScript
 * 5S Fashion E-commerce Platform
 */

// Get base URL dynamically
const BASE_URL = (() => {
	const pathParts = window.location.pathname
		.split('/')
		.filter((part) => part);
	// If we're in a subdirectory like /5s-fashion/, use it as base
	if (pathParts.length > 0 && !pathParts[0].includes('.')) {
		return '/' + pathParts[0];
	}
	// Otherwise use root
	return '';
})();

// Global variables
let cart = JSON.parse(localStorage.getItem('cart')) || [];
// Wishlist is now handled via API, no localStorage needed

/**
 * Client Website JavaScript
 * 5S Fashion E-commerce Platform
 */

// Helper function to get proper image URL
function getImageUrl(imagePath) {
	if (!imagePath) {
		return `${BASE_URL}/assets/images/no-image.jpg`;
	}

	// If it's already a full serve-file.php URL, return as is
	if (imagePath.startsWith(`${BASE_URL}/serve-file.php`)) {
		return imagePath;
	}

	// If it's a full HTTP URL, return as is
	if (imagePath.startsWith('http')) {
		return imagePath;
	}

	// Handle API response format: /uploads/products/filename.webp
	if (imagePath.startsWith('/uploads/products/')) {
		// Remove /uploads/ prefix since serve-file.php adds /public/uploads/ automatically
		const fileName = imagePath.replace('/uploads/', '');
		return `${BASE_URL}/serve-file.php?file=${encodeURIComponent(
			fileName
		)}`;
	}

	// Handle format: uploads/products/filename.webp
	if (imagePath.startsWith('uploads/products/')) {
		// Remove uploads/ prefix since serve-file.php adds /public/uploads/ automatically
		const fileName = imagePath.replace('uploads/', '');
		return `${BASE_URL}/serve-file.php?file=${encodeURIComponent(
			fileName
		)}`;
	}

	// For direct products/ path
	if (imagePath.startsWith('products/')) {
		return `${BASE_URL}/serve-file.php?file=${encodeURIComponent(
			imagePath
		)}`;
	}

	// Default fallback
	return `${BASE_URL}/assets/images/no-image.jpg`;
}

// DOM Ready
document.addEventListener('DOMContentLoaded', function () {
	initializeComponents();
	loadCartItemsFromServer(); // Load cart from server
	updateWishlistCounterFromAPI(); // Load wishlist count from API
	updateWishlistButtonsFromAPI(); // Update wishlist button states
	initializeBackToTop();
});

// Initialize all components
function initializeComponents() {
	// Initialize Swiper sliders
	if (document.querySelector('.hero-slider')) {
		initHeroSlider();
	}

	// Initialize product image galleries
	if (document.querySelector('.product-gallery')) {
		initProductGallery();
	}

	// Initialize tooltips
	initTooltips();

	// Initialize lazy loading
	initLazyLoading();
}

// Hero Slider
function initHeroSlider() {
	new Swiper('.hero-slider', {
		loop: true,
		autoplay: {
			delay: 5000,
			disableOnInteraction: false,
		},
		pagination: {
			el: '.swiper-pagination',
			clickable: true,
		},
		navigation: {
			nextEl: '.swiper-button-next',
			prevEl: '.swiper-button-prev',
		},
		effect: 'fade',
		fadeEffect: {
			crossFade: true,
		},
	});
}

// Product Gallery
function initProductGallery() {
	// Product thumbnails slider
	const thumbsSwiper = new Swiper('.product-thumbs', {
		spaceBetween: 10,
		slidesPerView: 4,
		freeMode: true,
		watchSlidesProgress: true,
		breakpoints: {
			768: {
				slidesPerView: 6,
			},
		},
	});

	// Main product slider
	const mainSwiper = new Swiper('.product-gallery', {
		spaceBetween: 10,
		thumbs: {
			swiper: thumbsSwiper,
		},
		zoom: {
			maxRatio: 3,
		},
		pagination: {
			el: '.swiper-pagination',
			clickable: true,
		},
	});
}

// Cart Functions
function toggleCartSidebar() {
	const sidebar = document.getElementById('cartSidebar');
	const overlay = document.getElementById('cartSidebarOverlay');

	sidebar.classList.toggle('show');
	overlay.classList.toggle('show');

	if (sidebar.classList.contains('show')) {
		// Load fresh cart data from server when opening
		loadCartItemsFromServer();
		document.body.style.overflow = 'hidden';
	} else {
		document.body.style.overflow = '';
	}
}

function closeCartSidebar() {
	const sidebar = document.getElementById('cartSidebar');
	const overlay = document.getElementById('cartSidebarOverlay');

	sidebar.classList.remove('show');
	overlay.classList.remove('show');
	document.body.style.overflow = '';
}

function viewCart() {
	window.location.href = `${BASE_URL}/cart`;
}

function checkout() {
	// Check if cart has items
	if (cart.length === 0) {
		showToast('Giỏ hàng trống', 'warning');
		return;
	}
	window.location.href = `${BASE_URL}/checkout`;
}

function addToCart(productId, quantity = 1, variant = null) {
	// Prevent double execution
	if (window.addToCartInProgress) {
		console.log('AddToCart already in progress, skipping...');
		return;
	}

	window.addToCartInProgress = true;

	// Show loading state
	showLoading();

	// Make API call to add product to cart
	fetch(`${BASE_URL}/ajax/cart/add`, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
		},
		body: JSON.stringify({
			product_id: productId,
			quantity: quantity,
			variant: variant,
		}),
	})
		.then((response) => response.json())
		.then((data) => {
			hideLoading();
			window.addToCartInProgress = false; // Reset flag

			if (data.success) {
				// Show success message
				showToast('Đã thêm sản phẩm vào giỏ hàng!', 'success');

				// Update cart counter from server response
				updateCartCounter(data.cart_count);

				// Reload cart items from server
				loadCartItemsFromServer();

				// Add animation effect (if event target exists)
				if (event && event.target) {
					animateAddToCart(event.target);
				}
			} else {
				showToast(data.message || 'Có lỗi xảy ra!', 'error');
			}
		})
		.catch((error) => {
			hideLoading();
			window.addToCartInProgress = false; // Reset flag on error
			console.error('Error:', error);
			showToast('Có lỗi xảy ra khi thêm vào giỏ hàng!', 'error');
		});
}

function removeFromCart(key) {
	// Validate key
	if (key < 0 || key >= cart.length) {
		console.error('Invalid cart key:', key);
		return;
	}

	const item = cart[key];
	const productId = item.product_id || item.id; // Support both formats
	const variant = item.variant;

	// Send AJAX request to server to remove item from cart
	fetch(`${BASE_URL}/ajax/cart/remove`, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
		},
		body: JSON.stringify({
			product_id: productId,
			variant: variant,
		}),
	})
		.then((response) => response.json())
		.then((data) => {
			if (data.success) {
				// Remove from local cart array
				cart = cart.filter((item, index) => index != key);
				localStorage.setItem('cart', JSON.stringify(cart));

				// Reload cart from server to sync
				loadCartItemsFromServer();

				showToast('Đã xóa sản phẩm khỏi giỏ hàng!', 'info');
			} else {
				console.error('Error removing from cart:', data.message);
				showToast(
					'Lỗi: ' + (data.message || 'Không thể xóa sản phẩm'),
					'error'
				);
			}
		})
		.catch((error) => {
			console.error('Error:', error);
			showToast('Lỗi: Không thể kết nối server', 'error');
		});
}

function updateCartQuantity(key, quantity) {
	if (quantity <= 0) {
		removeFromCart(key);
		return;
	}

	// Validate key
	if (key < 0 || key >= cart.length) {
		console.error('Invalid cart key:', key);
		return;
	}

	const item = cart[key];
	const productId = item.product_id || item.id; // Support both formats
	const variant = item.variant;

	// Send AJAX request to server to update quantity
	fetch(`${BASE_URL}/ajax/cart/update`, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
		},
		body: JSON.stringify({
			product_id: productId,
			variant: variant,
			quantity: parseInt(quantity),
		}),
	})
		.then((response) => response.json())
		.then((data) => {
			if (data.success) {
				// Update local cart array
				if (cart[key]) {
					cart[key].quantity = parseInt(quantity);
					localStorage.setItem('cart', JSON.stringify(cart));
				}

				// Reload cart from server to sync
				loadCartItemsFromServer();
			} else {
				console.error('Error updating cart:', data.message);
				showToast(
					'Lỗi: ' + (data.message || 'Không thể cập nhật số lượng'),
					'error'
				);
			}
		})
		.catch((error) => {
			console.error('Error:', error);
			showToast('Lỗi: Không thể kết nối server', 'error');
		});
}

function loadCartItems() {
	const cartContainer = document.getElementById('cartItems');
	const cartTotal = document.getElementById('cartTotal');

	if (!cartContainer) return;

	if (cart.length === 0) {
		cartContainer.innerHTML = `
            <div class="empty-cart text-center py-4">
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <p class="text-muted">Giỏ hàng trống</p>
                <a href="${BASE_URL}/shop" class="btn btn-primary btn-sm">Mua Sắm Ngay</a>
            </div>
        `;
		if (cartTotal) cartTotal.textContent = '0₫';
		return;
	}

	let html = '';
	let total = 0;

	cart.forEach((item, index) => {
		const itemTotal = item.price * item.quantity;
		total += itemTotal;

		html += `
            <div class="cart-item mb-3 pb-3 border-bottom">
                <div class="row align-items-center">
                    <div class="col-3">
                        <img src="${getImageUrl(
							item.product_image || item.image
						)}"
                             alt="${
									item.product_name || item.name
								}" class="img-fluid rounded">
                    </div>
                    <div class="col-9">
                        <h6 class="mb-1">${item.product_name || item.name}</h6>
                        ${
							item.variant
								? `<small class="text-muted">${item.variant}</small>`
								: ''
						}
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="quantity-controls">
                                <button class="btn btn-sm btn-outline-secondary"
                                        onclick="updateCartQuantity(${index}, ${
			item.quantity - 1
		})">-</button>
                                <span class="mx-2">${item.quantity}</span>
                                <button class="btn btn-sm btn-outline-secondary"
                                        onclick="updateCartQuantity(${index}, ${
			item.quantity + 1
		})">+</button>
                            </div>
                            <div class="item-price">
                                <strong>${formatCurrency(itemTotal)}</strong>
                                <button class="btn btn-sm btn-link text-danger p-0 ms-2"
                                        onclick="removeFromCart(${index})"
                                        title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
	});

	cartContainer.innerHTML = html;
	if (cartTotal) cartTotal.textContent = formatCurrency(total);
}

function updateCartCounter(count = null) {
	const counter = document.getElementById('cart-count');
	if (counter) {
		const cartCount =
			count !== null
				? count
				: cart.reduce((sum, item) => sum + item.quantity, 0);
		counter.textContent = cartCount;

		if (cartCount > 0) {
			counter.style.display = 'inline';
		} else {
			counter.style.display = 'none';
		}
	}
}

// Load cart items from server session
function loadCartItemsFromServer() {
	fetch(`${BASE_URL}/ajax/cart/items`, {
		method: 'GET',
		headers: {
			'Content-Type': 'application/json',
		},
	})
		.then((response) => response.json())
		.then((data) => {
			if (data.success) {
				// Convert server cart format to client format
				cart = Object.values(data.items).map((item) => ({
					product_id: item.product_id,
					name: item.product_name,
					image: item.product_image,
					price: parseFloat(item.price),
					quantity: parseInt(item.quantity),
					variant: item.variant,
				}));

				// Update localStorage as backup
				localStorage.setItem('cart', JSON.stringify(cart));

				// Update UI
				updateCartCounter(data.cart_count);
				loadCartItems();
			}
		})
		.catch((error) => {
			console.error('Error loading cart:', error);
			// Fallback to localStorage if server fails
			loadCartFromLocalStorage();
		});
}

// Fallback function to load from localStorage
function loadCartFromLocalStorage() {
	const savedCart = localStorage.getItem('cart');
	if (savedCart) {
		cart = JSON.parse(savedCart);
	}
	updateCartCounter();
	loadCartItems();
}

// Wishlist Functions
function toggleWishlist(productId) {
	// Check if user is logged in
	const isLoggedIn = document.body.getAttribute('data-logged-in') === 'true';

	if (!isLoggedIn) {
		showToast(
			'Vui lòng đăng nhập để sử dụng danh sách yêu thích!',
			'warning'
		);
		window.location.href = `${BASE_URL}/login`;
		return;
	}

	// Show loading state
	const button = document.querySelector(
		`[onclick*="toggleWishlist(${productId})"]`
	);
	const icon = button?.querySelector('i');
	const originalClass = icon?.className;

	if (icon) {
		icon.className = 'fas fa-spinner fa-spin';
	}

	// Call API to toggle wishlist
	fetch(`${BASE_URL}/ajax/wishlist/toggle`, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded',
		},
		body: `product_id=${productId}`,
	})
		.then((response) => response.json())
		.then((data) => {
			if (data.success) {
				showToast(data.message, 'success');

				// Update button state
				if (icon) {
					if (data.in_wishlist) {
						icon.className = 'fas fa-heart';
						button.classList.add('text-danger');
					} else {
						icon.className = 'far fa-heart';
						button.classList.remove('text-danger');
					}
				}

				// Update wishlist counter
				updateWishlistCounterFromAPI();
			} else {
				showToast(data.message || 'Có lỗi xảy ra!', 'error');
				// Restore original icon
				if (icon && originalClass) {
					icon.className = originalClass;
				}
			}
		})
		.catch((error) => {
			console.error('Error:', error);
			showToast('Có lỗi xảy ra khi kết nối!', 'error');
			// Restore original icon
			if (icon && originalClass) {
				icon.className = originalClass;
			}
		});
}

// New function to get wishlist count from API
function updateWishlistCounterFromAPI() {
	const isLoggedIn = document.body.getAttribute('data-logged-in') === 'true';

	if (!isLoggedIn) {
		const counter = document.getElementById('wishlist-count');
		if (counter) {
			counter.textContent = '0';
			counter.style.display = 'none';
		}
		return;
	}

	fetch(`${BASE_URL}/wishlist/count`)
		.then((response) => response.json())
		.then((data) => {
			const counter = document.getElementById('wishlist-count');
			if (counter) {
				counter.textContent = data.count || 0;

				if (data.count > 0) {
					counter.style.display = 'inline';
				} else {
					counter.style.display = 'none';
				}
			}
		})
		.catch((error) => {
			console.error('Error getting wishlist count:', error);
		});
}

// Update wishlist button states from database
function updateWishlistButtonsFromAPI() {
	const isLoggedIn = document.body.getAttribute('data-logged-in') === 'true';

	if (!isLoggedIn) {
		// If not logged in, ensure all buttons show empty state
		document
			.querySelectorAll('[onclick*="toggleWishlist"]')
			.forEach((button) => {
				const icon = button.querySelector('i');
				if (icon) {
					icon.classList.remove('fas');
					icon.classList.add('far');
					button.classList.remove('text-danger');
				}
			});
		return;
	}

	// Get current wishlist from API and update button states
	fetch(`${BASE_URL}/wishlist`)
		.then((response) => response.text())
		.then((html) => {
			// Parse the HTML to extract wishlist product IDs
			const parser = new DOMParser();
			const doc = parser.parseFromString(html, 'text/html');
			const wishlistItems = doc.querySelectorAll('[data-product-id]');
			const wishlistProductIds = Array.from(wishlistItems).map((item) =>
				parseInt(item.getAttribute('data-product-id'))
			);

			// Update all wishlist buttons
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
		})
		.catch((error) => {
			console.error('Error updating wishlist buttons:', error);
		});
}

// Quick View
function quickView(productId) {
	// Show loading modal
	showLoading();

	fetch(`${BASE_URL}/ajax/product/data?id=${productId}`)
		.then((response) => {
			if (!response.ok) {
				throw new Error(`HTTP error! status: ${response.status}`);
			}
			return response.json();
		})
		.then((data) => {
			hideLoading();
			if (data.success) {
				showQuickViewModal(data.product);
			} else {
				throw new Error(
					data.message || 'Không thể tải thông tin sản phẩm'
				);
			}
		})
		.catch((error) => {
			hideLoading();
			console.error('Error:', error);
			showToast('Không thể tải thông tin sản phẩm!', 'error');
		});
}

function showQuickViewModal(product) {
	// Create and show quick view modal
	const modal = document.createElement('div');
	modal.className = 'modal fade';
	modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">${product.name}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <img src="${getImageUrl(product.featured_image)}"
                                 alt="${
										product.name
									}" class="product-image img-fluid">
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted">${
								product.category_name || 'Chưa phân loại'
							}</p>
                            <h4>${formatCurrency(
								product.sale_price || product.price
							)}</h4>
                            <p>${product.description || ''}</p>
                            <div class="mt-3">
                                <button class="btn btn-primary me-2" onclick="addToCart(${
									product.id
								})">
                                    <i class="fas fa-shopping-cart me-2"></i>Thêm Vào Giỏ
                                </button>
                                <a href="${BASE_URL}/product/${
		product.slug
	}" class="btn btn-outline-primary">
                                    Xem Chi Tiết
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

	document.body.appendChild(modal);
	const bsModal = new bootstrap.Modal(modal);
	bsModal.show();

	// Remove modal when hidden
	modal.addEventListener('hidden.bs.modal', function () {
		document.body.removeChild(modal);
	});
}

// Utility Functions
function formatCurrency(amount) {
	return new Intl.NumberFormat('vi-VN', {
		style: 'currency',
		currency: 'VND',
	})
		.format(amount)
		.replace('₫', '₫');
}

function showToast(message, type = 'info') {
	// Create toast element
	const toast = document.createElement('div');
	toast.className = `toast align-items-center text-white bg-${
		type === 'error' ? 'danger' : type
	} border-0`;
	toast.setAttribute('role', 'alert');
	toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-${
					type === 'success'
						? 'check-circle'
						: type === 'error'
						? 'exclamation-circle'
						: 'info-circle'
				} me-2"></i>
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;

	// Add to toast container or create one
	let container = document.querySelector('.toast-container');
	if (!container) {
		container = document.createElement('div');
		container.className = 'toast-container position-fixed top-0 end-0 p-3';
		container.style.zIndex = '1070';
		document.body.appendChild(container);
	}

	container.appendChild(toast);

	// Show toast
	const bsToast = new bootstrap.Toast(toast);
	bsToast.show();

	// Remove toast element after it's hidden
	toast.addEventListener('hidden.bs.toast', function () {
		container.removeChild(toast);
	});
}

function showLoading() {
	let loader = document.getElementById('globalLoader');
	if (!loader) {
		loader = document.createElement('div');
		loader.id = 'globalLoader';
		loader.className = 'global-loader';
		loader.innerHTML = `
            <div class="loader-content">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Đang tải...</p>
            </div>
        `;
		document.body.appendChild(loader);
	}
	loader.style.display = 'flex';
}

function hideLoading() {
	const loader = document.getElementById('globalLoader');
	if (loader) {
		loader.style.display = 'none';
	}
}

function animateAddToCart(button) {
	button.classList.add('animate-pulse');
	setTimeout(() => {
		button.classList.remove('animate-pulse');
	}, 600);
}

// Initialize tooltips
function initTooltips() {
	const tooltipTriggerList = [].slice.call(
		document.querySelectorAll('[data-bs-toggle="tooltip"]')
	);
	tooltipTriggerList.map(function (tooltipTriggerEl) {
		return new bootstrap.Tooltip(tooltipTriggerEl);
	});
}

// Initialize lazy loading
function initLazyLoading() {
	if ('IntersectionObserver' in window) {
		const imageObserver = new IntersectionObserver((entries, observer) => {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {
					const img = entry.target;
					img.src = img.dataset.src;
					img.classList.remove('lazy');
					imageObserver.unobserve(img);
				}
			});
		});

		document.querySelectorAll('img[data-src]').forEach((img) => {
			imageObserver.observe(img);
		});
	}
}

// Back to top button
function initializeBackToTop() {
	const backToTopBtn = document.getElementById('backToTop');

	if (backToTopBtn) {
		window.addEventListener('scroll', function () {
			if (window.pageYOffset > 300) {
				backToTopBtn.classList.add('show');
			} else {
				backToTopBtn.classList.remove('show');
			}
		});

		backToTopBtn.addEventListener('click', function () {
			window.scrollTo({
				top: 0,
				behavior: 'smooth',
			});
		});
	}
}

// Search functionality
function initializeSearch() {
	const searchInput = document.querySelector('input[name="q"]');
	if (searchInput) {
		// Add search suggestions
		searchInput.addEventListener(
			'input',
			debounce(function () {
				const query = this.value;
				if (query.length >= 2) {
					fetchSearchSuggestions(query);
				}
			}, 300)
		);
	}
}

function fetchSearchSuggestions(query) {
	fetch(`${BASE_URL}/api/search/suggestions?q=${encodeURIComponent(query)}`)
		.then((response) => response.json())
		.then((data) => {
			if (data.success) {
				showSearchSuggestions(data.suggestions);
			}
		})
		.catch((error) => console.error('Search suggestions error:', error));
}

// Debounce function
function debounce(func, wait) {
	let timeout;
	return function executedFunction(...args) {
		const later = () => {
			clearTimeout(timeout);
			func(...args);
		};
		clearTimeout(timeout);
		timeout = setTimeout(later, wait);
	};
}

// Add custom styles for loading and animations
const customCSS = `
.global-loader {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255,255,255,0.9);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.loader-content {
    text-align: center;
}

.animate-pulse {
    animation: pulse 0.6s ease-in-out;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}
`;

// Add custom CSS to head
const styleSheet = document.createElement('style');
styleSheet.textContent = customCSS;
document.head.appendChild(styleSheet);
