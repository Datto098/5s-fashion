/**
 * Product Detail JavaScript
 * 5S Fashion E-commerce Platform
 */

class ProductDetailManager {
	constructor() {
		this.selectedSize = null;
		this.selectedColor = null;
		this.quantity = 1;
		this.maxQuantity = window.productData?.stock || 10;

		this.init();
	}

	init() {
		this.initializeThumbnailSwiper();
		this.bindEvents();
		this.setDefaultSelections();
		this.updateAddToCartButton();
	}

	initializeThumbnailSwiper() {
		if (document.querySelector('.thumbnail-swiper')) {
			this.thumbnailSwiper = new Swiper('.thumbnail-swiper', {
				slidesPerView: 'auto',
				spaceBetween: 10,
				navigation: {
					nextEl: '.thumbnail-swiper .swiper-button-next',
					prevEl: '.thumbnail-swiper .swiper-button-prev',
				},
				breakpoints: {
					768: {
						slidesPerView: 4,
						spaceBetween: 15,
					},
					992: {
						slidesPerView: 5,
						spaceBetween: 15,
					},
				},
			});
		}
	}

	bindEvents() {
		// Quantity controls
		document
			.getElementById('productQuantity')
			?.addEventListener('change', (e) => {
				this.setQuantity(parseInt(e.target.value));
			});

		// Size selection
		document.querySelectorAll('input[name="size"]').forEach((input) => {
			input.addEventListener('change', (e) => {
				this.selectedSize = e.target.value;
				this.updateAddToCartButton();
			});
		});

		// Color selection
		document.querySelectorAll('input[name="color"]').forEach((input) => {
			input.addEventListener('change', (e) => {
				this.selectedColor = e.target.value;
				this.updateAddToCartButton();
			});
		});

		// Star rating for reviews
		document.querySelectorAll('.star-rating input').forEach((input) => {
			input.addEventListener('change', this.updateStarRating);
		});

		// Review form
		const reviewForm = document.querySelector('.review-form');
		if (reviewForm) {
			reviewForm.addEventListener('submit', this.handleReviewSubmit);
		}

		// Image zoom
		document.addEventListener('keydown', (e) => {
			if (e.key === 'Escape') {
				this.closeImageZoom();
			}
		});
	}

	setDefaultSelections() {
		// Set default size
		const defaultSize = document.querySelector(
			'input[name="size"]:checked'
		);
		if (defaultSize) {
			this.selectedSize = defaultSize.value;
		}

		// Set default color
		const defaultColor = document.querySelector(
			'input[name="color"]:checked'
		);
		if (defaultColor) {
			this.selectedColor = defaultColor.value;
		}

		// Set initial quantity
		const quantityInput = document.getElementById('productQuantity');
		if (quantityInput) {
			this.quantity = parseInt(quantityInput.value);
		}
	}

	changeMainImage(thumbnail) {
		const mainImage = document.getElementById('mainProductImage');
		if (mainImage && thumbnail.src) {
			// Remove active class from all thumbnails
			document.querySelectorAll('.thumbnail').forEach((thumb) => {
				thumb.classList.remove('active');
			});

			// Add active class to clicked thumbnail
			thumbnail.classList.add('active');

			// Change main image with smooth transition
			mainImage.style.opacity = '0';

			setTimeout(() => {
				mainImage.src = thumbnail.src;
				mainImage.style.opacity = '1';
			}, 200);
		}
	}

	changeQuantity(change) {
		const newQuantity = this.quantity + change;

		if (newQuantity >= 1 && newQuantity <= this.maxQuantity) {
			this.setQuantity(newQuantity);
		}
	}

	setQuantity(quantity) {
		if (quantity >= 1 && quantity <= this.maxQuantity) {
			this.quantity = quantity;

			const quantityInput = document.getElementById('productQuantity');
			if (quantityInput) {
				quantityInput.value = quantity;
			}

			this.updateAddToCartButton();
		}
	}

	updateAddToCartButton() {
		const addToCartBtn = document.querySelector('.add-to-cart');
		const buyNowBtn = document.querySelector('.buy-now');

		if (addToCartBtn) {
			const hasRequiredSelections =
				this.selectedSize && this.selectedColor;
			const inStock = this.maxQuantity > 0;

			if (!inStock) {
				addToCartBtn.disabled = true;
				addToCartBtn.innerHTML =
					'<i class="fas fa-times me-2"></i>Hết hàng';
				if (buyNowBtn) buyNowBtn.disabled = true;
			} else if (!hasRequiredSelections) {
				addToCartBtn.disabled = true;
				addToCartBtn.innerHTML =
					'<i class="fas fa-exclamation-triangle me-2"></i>Chọn size & màu';
				if (buyNowBtn) buyNowBtn.disabled = true;
			} else {
				addToCartBtn.disabled = false;
				addToCartBtn.innerHTML =
					'<i class="fas fa-shopping-cart me-2"></i>Thêm vào giỏ';
				if (buyNowBtn) buyNowBtn.disabled = false;
			}
		}
	}

	addToCart(productId) {
		if (!this.selectedSize || !this.selectedColor) {
			showToast('Vui lòng chọn size và màu sắc', 'warning');
			return;
		}

		const addToCartBtn = document.querySelector('.add-to-cart');
		if (addToCartBtn.disabled) return;

		// Show loading state
		addToCartBtn.classList.add('loading');
		addToCartBtn.innerHTML =
			'<i class="fas fa-spinner fa-spin me-2"></i>Đang thêm...';
		addToCartBtn.disabled = true;

		// Create variant string for server
		const variant = `${this.selectedSize} - ${this.selectedColor}`;

		// Use global addToCart function
		if (typeof window.addToCart === 'function') {
			// Call global function with variant info
			window.addToCart(productId, this.quantity, variant);

			// Reset button after short delay
			setTimeout(() => {
				addToCartBtn.classList.remove('loading');
				addToCartBtn.innerHTML =
					'<i class="fas fa-check me-2"></i>Đã thêm vào giỏ';

				setTimeout(() => {
					addToCartBtn.innerHTML =
						'<i class="fas fa-shopping-cart me-2"></i>Thêm vào giỏ';
					addToCartBtn.disabled = false;
				}, 2000);
			}, 500);
		} else {
			// Fallback to original logic if global function not available
			this.addToCartFallback(productId);
		}
	}

	addToCartFallback(productId) {
		const addToCartBtn = document.querySelector('.add-to-cart');

		// Prepare cart item data
		const cartItem = {
			id: productId,
			name: window.productData.name,
			price: window.productData.price,
			image: window.productData.image,
			quantity: this.quantity,
			size: this.selectedSize,
			color: this.selectedColor,
			variant: `${this.selectedSize} - ${this.selectedColor}`,
		};

		// Simulate API call
		setTimeout(() => {
			// Add to cart
			this.addItemToCart(cartItem);

			// Reset button
			addToCartBtn.classList.remove('loading');
			addToCartBtn.innerHTML =
				'<i class="fas fa-check me-2"></i>Đã thêm vào giỏ';

			// Show success message
			showToast(`Đã thêm "${cartItem.name}" vào giỏ hàng`, 'success');

			// Update cart counter
			updateCartCounter();

			// Reset button after 2 seconds
			setTimeout(() => {
				addToCartBtn.innerHTML =
					'<i class="fas fa-shopping-cart me-2"></i>Thêm vào giỏ';
				addToCartBtn.disabled = false;
			}, 2000);
		}, 1000);
	}

	addItemToCart(item) {
		let cartItems = JSON.parse(localStorage.getItem('cart') || '[]');

		// Check if item already exists
		const existingItemIndex = cartItems.findIndex(
			(cartItem) =>
				cartItem.id === item.id &&
				cartItem.size === item.size &&
				cartItem.color === item.color
		);

		if (existingItemIndex !== -1) {
			// Update quantity
			cartItems[existingItemIndex].quantity += item.quantity;
		} else {
			// Add new item
			cartItems.push(item);
		}

		localStorage.setItem('cart', JSON.stringify(cartItems));
	}

	buyNow(productId) {
		if (!this.selectedSize || !this.selectedColor) {
			showToast('Vui lòng chọn size và màu sắc', 'warning');
			return;
		}

		// Add to cart first
		const cartItem = {
			id: productId,
			name: window.productData.name,
			price: window.productData.price,
			image: window.productData.image,
			quantity: this.quantity,
			size: this.selectedSize,
			color: this.selectedColor,
			variant: `${this.selectedSize} - ${this.selectedColor}`,
		};

		this.addItemToCart(cartItem);
		updateCartCounter();

		// Redirect to checkout
		window.location.href = '/checkout';
	}

	toggleWishlist(productId) {
		const wishlistBtn = document.querySelector('.wishlist-btn');
		const heartIcon = wishlistBtn.querySelector('i');

		// Get current wishlist
		let wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
		const isInWishlist = wishlist.includes(productId);

		if (isInWishlist) {
			// Remove from wishlist
			wishlist = wishlist.filter((id) => id !== productId);
			heartIcon.classList.remove('fas');
			heartIcon.classList.add('far');
			wishlistBtn.classList.remove('active');
			showToast('Đã xóa khỏi danh sách yêu thích', 'info');
		} else {
			// Add to wishlist
			wishlist.push(productId);
			heartIcon.classList.remove('far');
			heartIcon.classList.add('fas');
			wishlistBtn.classList.add('active');
			showToast('Đã thêm vào danh sách yêu thích', 'success');
		}

		localStorage.setItem('wishlist', JSON.stringify(wishlist));

		// Animation
		wishlistBtn.style.transform = 'scale(0.8)';
		setTimeout(() => {
			wishlistBtn.style.transform = 'scale(1)';
		}, 150);
	}

	openImageZoom() {
		const mainImage = document.getElementById('mainProductImage');
		const zoomModal = new bootstrap.Modal(
			document.getElementById('imageZoomModal')
		);
		const zoomImage = document.getElementById('zoomImage');

		if (mainImage && zoomImage) {
			zoomImage.src = mainImage.src;
			zoomImage.alt = mainImage.alt;
			zoomModal.show();
		}
	}

	closeImageZoom() {
		const zoomModal = bootstrap.Modal.getInstance(
			document.getElementById('imageZoomModal')
		);
		if (zoomModal) {
			zoomModal.hide();
		}
	}

	updateStarRating() {
		const rating = this.value;
		const labels = document.querySelectorAll('.star-rating label');

		labels.forEach((label, index) => {
			const starValue = 5 - index;
			if (starValue <= rating) {
				label.style.color = '#ffc107';
			} else {
				label.style.color = '#ddd';
			}
		});
	}

	handleReviewSubmit(e) {
		e.preventDefault();

		const formData = new FormData(e.target);
		const rating = formData.get('rating');
		const reviewText = formData.get('reviewText');

		if (!rating) {
			showToast('Vui lòng chọn số sao đánh giá', 'warning');
			return;
		}

		if (!reviewText || reviewText.trim().length < 10) {
			showToast(
				'Vui lòng nhập nội dung đánh giá ít nhất 10 ký tự',
				'warning'
			);
			return;
		}

		const submitBtn = e.target.querySelector('button[type="submit"]');
		const originalText = submitBtn.innerHTML;

		// Show loading
		submitBtn.innerHTML =
			'<i class="fas fa-spinner fa-spin me-2"></i>Đang gửi...';
		submitBtn.disabled = true;

		// Simulate API call
		setTimeout(() => {
			// Reset form
			e.target.reset();
			document.querySelectorAll('.star-rating label').forEach((label) => {
				label.style.color = '#ddd';
			});

			// Reset button
			submitBtn.innerHTML = originalText;
			submitBtn.disabled = false;

			showToast('Cảm ơn bạn đã đánh giá sản phẩm!', 'success');
		}, 1500);
	}
}

// Social sharing functions
function shareOnFacebook() {
	const url = encodeURIComponent(window.location.href);
	const title = encodeURIComponent(window.productData.name);

	window.open(
		`https://www.facebook.com/sharer/sharer.php?u=${url}&quote=${title}`,
		'facebook-share',
		'width=600,height=400'
	);
}

function shareOnTwitter() {
	const url = encodeURIComponent(window.location.href);
	const title = encodeURIComponent(window.productData.name);

	window.open(
		`https://twitter.com/intent/tweet?url=${url}&text=${title}`,
		'twitter-share',
		'width=600,height=400'
	);
}

function shareOnPinterest() {
	const url = encodeURIComponent(window.location.href);
	const title = encodeURIComponent(window.productData.name);
	const image = encodeURIComponent(window.productData.image);

	window.open(
		`https://pinterest.com/pin/create/button/?url=${url}&media=${image}&description=${title}`,
		'pinterest-share',
		'width=600,height=400'
	);
}

function copyProductLink() {
	const url = window.location.href;

	if (navigator.clipboard) {
		navigator.clipboard
			.writeText(url)
			.then(() => {
				showToast('Đã sao chép link sản phẩm', 'success');
			})
			.catch(() => {
				fallbackCopyTextToClipboard(url);
			});
	} else {
		fallbackCopyTextToClipboard(url);
	}
}

function fallbackCopyTextToClipboard(text) {
	const textArea = document.createElement('textarea');
	textArea.value = text;
	textArea.style.top = '0';
	textArea.style.left = '0';
	textArea.style.position = 'fixed';

	document.body.appendChild(textArea);
	textArea.focus();
	textArea.select();

	try {
		document.execCommand('copy');
		showToast('Đã sao chép link sản phẩm', 'success');
	} catch (err) {
		showToast('Không thể sao chép link', 'error');
	}

	document.body.removeChild(textArea);
}

// Global functions for inline event handlers
function changeMainImage(thumbnail) {
	if (window.productDetailManager) {
		window.productDetailManager.changeMainImage(thumbnail);
	}
}

function changeQuantity(change) {
	if (window.productDetailManager) {
		window.productDetailManager.changeQuantity(change);
	}
}

function addToCart(productId) {
	if (window.productDetailManager) {
		window.productDetailManager.addToCart(productId);
	}
}

function buyNow(productId) {
	if (window.productDetailManager) {
		window.productDetailManager.buyNow(productId);
	}
}

function toggleWishlist(productId) {
	if (window.productDetailManager) {
		window.productDetailManager.toggleWishlist(productId);
	}
}

function openImageZoom() {
	if (window.productDetailManager) {
		window.productDetailManager.openImageZoom();
	}
}

// Utility functions
function showToast(message, type = 'info') {
	// Use existing toast function from client.js or create a simple one
	if (typeof window.showToast === 'function') {
		window.showToast(message, type);
	} else {
		// Simple fallback toast
		const toast = document.createElement('div');
		toast.className = `alert alert-${
			type === 'error' ? 'danger' : type
		} position-fixed`;
		toast.style.cssText =
			'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
		toast.innerHTML = `
            ${message}
            <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
        `;

		document.body.appendChild(toast);

		setTimeout(() => {
			if (toast.parentElement) {
				toast.remove();
			}
		}, 5000);
	}
}

function updateCartCounter() {
	// Use existing cart counter function from client.js
	if (typeof window.updateCartCounterGlobal === 'function') {
		window.updateCartCounterGlobal();
	} else {
		// Simple fallback
		const cartItems = JSON.parse(localStorage.getItem('cart') || '[]');
		const totalItems = cartItems.reduce(
			(sum, item) => sum + item.quantity,
			0
		);

		const counters = document.querySelectorAll('.cart-counter');
		counters.forEach((counter) => {
			counter.textContent = totalItems;
			counter.style.display = totalItems > 0 ? 'inline' : 'none';
		});
	}
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
	window.productDetailManager = new ProductDetailManager();

	// Load wishlist state
	const productId = window.productData?.id;
	if (productId) {
		const wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
		if (wishlist.includes(productId)) {
			const wishlistBtn = document.querySelector('.wishlist-btn');
			const heartIcon = wishlistBtn?.querySelector('i');
			if (heartIcon) {
				heartIcon.classList.remove('far');
				heartIcon.classList.add('fas');
				wishlistBtn.classList.add('active');
			}
		}
	}

	// Smooth scroll for tab links
	document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
		anchor.addEventListener('click', function (e) {
			const target = document.querySelector(this.getAttribute('href'));
			if (target) {
				const offsetTop = target.offsetTop - 100;
				window.scrollTo({
					top: offsetTop,
					behavior: 'smooth',
				});
			}
		});
	});
});

// Keyboard shortcuts
document.addEventListener('keydown', function (e) {
	// Escape key closes image zoom
	if (e.key === 'Escape') {
		const zoomModal = bootstrap.Modal.getInstance(
			document.getElementById('imageZoomModal')
		);
		if (zoomModal) {
			zoomModal.hide();
		}
	}

	// Arrow keys for thumbnail navigation
	if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') {
		const thumbnails = document.querySelectorAll('.thumbnail');
		const activeThumbnail = document.querySelector('.thumbnail.active');

		if (thumbnails.length > 1 && activeThumbnail) {
			const currentIndex =
				Array.from(thumbnails).indexOf(activeThumbnail);
			let newIndex;

			if (e.key === 'ArrowLeft') {
				newIndex =
					currentIndex > 0 ? currentIndex - 1 : thumbnails.length - 1;
			} else {
				newIndex =
					currentIndex < thumbnails.length - 1 ? currentIndex + 1 : 0;
			}

			thumbnails[newIndex].click();
		}
	}
});
