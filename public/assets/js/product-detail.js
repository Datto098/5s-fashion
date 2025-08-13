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
		const reviewForm = document.querySelector('#review-add-form');
		if (reviewForm) {
			reviewForm.addEventListener('submit', this.handleReviewSubmit.bind(this));
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
		console.log(
			'🚫 Product-detail: toggleWishlist disabled - using unified system only'
		);
		return; // Let unified wishlist manager handle everything

		// OLD CODE DISABLED TO PREVENT CONFLICTS:
		// const wishlistBtn = document.querySelector('.wishlist-btn');
		// const heartIcon = wishlistBtn.querySelector('i');
		//
		// // Get current wishlist
		// let wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
		// const isInWishlist = wishlist.includes(productId);
		//
		// if (isInWishlist) {
		// 	// Remove from wishlist
		// 	wishlist = wishlist.filter((id) => id !== productId);
		// 	heartIcon.classList.remove('fas');
		// 	heartIcon.classList.add('far');
		// 	wishlistBtn.classList.remove('active');
		// 	showToast('Đã xóa khỏi danh sách yêu thích', 'info');
		// } else {
		// 	// Add to wishlist
		// 	wishlist.push(productId);
		// 	heartIcon.classList.remove('far');
		// 	heartIcon.classList.add('fas');
		// 	wishlistBtn.classList.add('active');
		// 	showToast('Đã thêm vào danh sách yêu thích', 'success');
		// }
		//
		// localStorage.setItem('wishlist', JSON.stringify(wishlist));

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
		console.log('handleReviewSubmit called');

		const formData = new FormData(e.target);
		const rating = formData.get('rating');
		const content = formData.get('content');
		
		console.log('Rating:', rating);
		console.log('Content:', content);
		console.log('Content length:', content ? content.trim().length : 0);

		if (!rating) {
			console.log('No rating selected');
			showToast('warning', 'Vui lòng chọn số sao đánh giá');
			return;
		}

		if (!content || content.trim().length < 10) {
			console.log('Content too short');
			showToast('warning', 'Vui lòng nhập nội dung đánh giá ít nhất 10 ký tự');
			return;
		}

		console.log('Validation passed, submitting form...');

		const submitBtn = e.target.querySelector('button[type="submit"]');
		const originalText = submitBtn.innerHTML;

		// Show loading
		submitBtn.innerHTML =
			'<i class="fas fa-spinner fa-spin me-2"></i>Đang gửi...';
		submitBtn.disabled = true;

		// Log form data for debugging
		console.log('Form data being submitted:');
		for (let pair of formData.entries()) {
			console.log(pair[0] + ': ' + pair[1]);
		}

		// Submit form using AJAX
		fetch('/5s-fashion/ajax/review/add', {
			method: 'POST',
			body: formData
		})
		.then(response => {
			console.log('Response status:', response.status);
			return response.json();
		})
		.then(data => {
			console.log('Response data:', data);
			if (data.success) {
				// Reset form
				e.target.reset();
				
				// Reset star ratings
				document.querySelectorAll('.star-rating label').forEach((label) => {
					label.style.color = '#ddd';
				});
				
				// Show success message
				showToast('success', data.message);
				
				// Reload the page after a short delay to show the new review
				setTimeout(() => {
					window.location.reload();
				}, 1500);
			} else {
				showToast('error', data.message || 'Có lỗi xảy ra khi gửi đánh giá');
				submitBtn.innerHTML = originalText;
				submitBtn.disabled = false;
			}
		})
		.catch(error => {
			console.error('Error:', error);
			showToast('error', 'Đã xảy ra lỗi khi gửi đánh giá');
			submitBtn.innerHTML = originalText;
			submitBtn.disabled = false;
		});
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
function showToast(type = 'info', message) {
    // Kiểm tra xem đã có container cho toast chưa
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }
    
    // Tạo ID duy nhất cho toast
    const toastId = 'toast-' + new Date().getTime();
    
    // Xác định class dựa trên loại thông báo
    let bgClass = 'bg-info';
    if (type === 'success') bgClass = 'bg-success';
    if (type === 'warning') bgClass = 'bg-warning';
    if (type === 'error' || type === 'danger') bgClass = 'bg-danger';
    
    try {
        // Tạo HTML cho toast
        const toastHTML = `
            <div id="${toastId}" class="toast align-items-center ${bgClass} text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        
        // Thêm toast vào container
        toastContainer.insertAdjacentHTML('beforeend', toastHTML);
        
        // Khởi tạo và hiển thị toast
        const toastElement = document.getElementById(toastId);
        if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
            const bsToast = new bootstrap.Toast(toastElement, { autohide: true, delay: 5000 });
            bsToast.show();
            
            // Xóa toast sau khi ẩn
            toastElement.addEventListener('hidden.bs.toast', function() {
                toastElement.remove();
            });
        } else {
            // Fallback if Bootstrap is not available
            toastElement.style.display = 'block';
            setTimeout(() => {
                toastElement.remove();
            }, 5000);
        }
    } catch (error) {
        // Simple fallback toast if anything goes wrong
        const alertToast = document.createElement('div');
        alertToast.className = `alert alert-${type === 'error' ? 'danger' : type} position-fixed`;
        alertToast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alertToast.innerHTML = `
            ${message}
            <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
        `;
        
        document.body.appendChild(alertToast);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            alertToast.remove();
        }, 5000);
    }

	setTimeout(() => {
		if (toast.parentElement) {
			toast.remove();
		}
	}, 5000);
}

function updateCartCounter() {
	// ALWAYS use unified cart manager - no fallbacks to prevent conflicts
	console.log(
		'📦 Product-detail: updateCartCounter called - doing nothing to prevent conflicts'
	);
	return; // Let unified manager handle everything automatically
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
	console.log('DOM loaded, initializing ProductDetailManager');
	
	// Prevent multiple initializations
	if (window.productDetailManagerInitialized) {
		console.log('ProductDetailManager already initialized, skipping');
		return;
	}
	
	window.productDetailManager = new ProductDetailManager();
	window.productDetailManagerInitialized = true;

	// Direct form handling as a fallback
	const reviewForm = document.querySelector('#review-add-form');
	if (reviewForm) {
		console.log('Found review form, adding event listener');
		reviewForm.addEventListener('submit', function(e) {
			e.preventDefault();
			console.log('Direct form handler called');
			
			if (window.productDetailManager && typeof window.productDetailManager.handleReviewSubmit === 'function') {
				console.log('Using ProductDetailManager handler');
				window.productDetailManager.handleReviewSubmit.call(window.productDetailManager, e);
			} else {
				console.log('Direct form handling - ProductDetailManager not available');
				
				const formData = new FormData(e.target);
				const rating = formData.get('rating');
				const content = formData.get('content');
				
				console.log('Direct - Rating:', rating);
				console.log('Direct - Content:', content);
				
				if (!rating) {
					alert('Vui lòng chọn số sao đánh giá');
					return;
				}

				if (!content || content.trim().length < 10) {
					alert('Vui lòng nhập nội dung đánh giá ít nhất 10 ký tự');
					return;
				}
				
				// Log form data for debugging
				console.log('Form data (direct):');
				for (let pair of formData.entries()) {
					console.log(pair[0] + ': ' + pair[1]);
				}
				
				// Submit the form using fetch
				fetch('/5s-fashion/ajax/review/add', {
					method: 'POST',
					body: formData
				})
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						alert('Cảm ơn bạn đã đánh giá sản phẩm!');
						setTimeout(() => {
							window.location.reload();
						}, 1000);
					} else {
						alert(data.message || 'Có lỗi xảy ra khi gửi đánh giá');
					}
				})
				.catch(error => {
					console.error('Error:', error);
					alert('Đã xảy ra lỗi khi gửi đánh giá');
				});
			}
		});
	} else {
		console.log('Review form not found!');
	}

	console.log(
		'🚫 Product-detail: localStorage wishlist loading disabled - using unified system only'
	);

	// OLD DISABLED CODE TO PREVENT CONFLICTS:
	// Load wishlist state
	// const productId = window.productData?.id;
	// if (productId) {
	// 	const wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
	// 	if (wishlist.includes(productId)) {
	// 		const wishlistBtn = document.querySelector('.wishlist-btn');
	// 		const heartIcon = wishlistBtn?.querySelector('i');
	// 		if (heartIcon) {
	// 			heartIcon.classList.remove('far');
	// 			heartIcon.classList.add('fas');
	// 			wishlistBtn.classList.add('active');
	// 		}
	// 	}
	// }

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
