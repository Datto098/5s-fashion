/**
 * Order Success Manager - Phase 3.4
 * Handles order success page functionality, animations, related products
 */

class OrderSuccessManager {
	constructor() {
		this.orderId = this.getOrderIdFromUrl();
		this.orderData = this.getOrderData();
		this.init();
	}

	init() {
		this.bindEvents();
		this.initializeAnimations();
		this.loadRelatedProducts();
		this.startCountdown();

		// Auto-redirect to tracking if order ID exists
		if (this.orderId) {
			this.updateTrackingLink();
		}
	}

	bindEvents() {
		// Continue shopping button
		document.addEventListener('click', (e) => {
			if (e.target.matches('.continue-shopping')) {
				e.preventDefault();
				this.continueShopping();
			}
		});

		// Track order button
		document.addEventListener('click', (e) => {
			if (e.target.matches('.track-order')) {
				e.preventDefault();
				this.trackOrder();
			}
		});

		// Print receipt button
		document.addEventListener('click', (e) => {
			if (e.target.matches('.print-receipt')) {
				e.preventDefault();
				this.printReceipt();
			}
		});

		// Social share buttons
		document.addEventListener('click', (e) => {
			if (e.target.matches('.share-facebook')) {
				e.preventDefault();
				this.shareOnFacebook();
			}
			if (e.target.matches('.share-twitter')) {
				e.preventDefault();
				this.shareOnTwitter();
			}
		});

		// Newsletter signup
		const newsletterForm = document.getElementById('newsletterForm');
		if (newsletterForm) {
			newsletterForm.addEventListener('submit', (e) => {
				e.preventDefault();
				this.subscribeNewsletter();
			});
		}

		// Related product actions
		document.addEventListener('click', (e) => {
			if (e.target.matches('.add-to-cart-related')) {
				e.preventDefault();
				const productId = e.target.dataset.productId;
				this.addRelatedToCart(productId);
			}
			if (e.target.matches('.add-to-wishlist-related')) {
				e.preventDefault();
				const productId = e.target.dataset.productId;
				this.addRelatedToWishlist(productId);
			}
		});

		// Download invoice
		document.addEventListener('click', (e) => {
			if (e.target.matches('.download-invoice')) {
				e.preventDefault();
				this.downloadInvoice();
			}
		});
	}

	initializeAnimations() {
		// Animate success checkmark
		setTimeout(() => {
			const checkmark = document.querySelector('.success-checkmark');
			if (checkmark) {
				checkmark.classList.add('animate');
			}
		}, 500);

		// Animate order details
		setTimeout(() => {
			const orderDetails =
				document.querySelectorAll('.order-detail-item');
			orderDetails.forEach((item, index) => {
				setTimeout(() => {
					item.style.opacity = '1';
					item.style.transform = 'translateY(0)';
				}, index * 100);
			});
		}, 1000);

		// Animate timeline
		setTimeout(() => {
			this.animateTimeline();
		}, 1500);

		// Animate related products
		setTimeout(() => {
			const relatedProducts = document.querySelectorAll(
				'.related-product-card'
			);
			relatedProducts.forEach((card, index) => {
				setTimeout(() => {
					card.style.opacity = '1';
					card.style.transform = 'translateY(0)';
				}, index * 150);
			});
		}, 2000);
	}

	animateTimeline() {
		const timelineItems = document.querySelectorAll('.timeline-item');
		timelineItems.forEach((item, index) => {
			setTimeout(() => {
				item.classList.add('animate');
			}, index * 300);
		});
	}

	loadRelatedProducts() {
		const relatedContainer = document.getElementById('relatedProducts');
		if (!relatedContainer) return;

		// Simulate loading related products
		setTimeout(() => {
			const products = this.getRelatedProducts();
			relatedContainer.innerHTML = products
				.map((product) => this.generateRelatedProductHTML(product))
				.join('');
		}, 1000);
	}

	generateRelatedProductHTML(product) {
		return `
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="related-product-card card h-100">
                    <div class="product-image-container position-relative">
                        <img src="${product.image}" class="card-img-top" alt="${
			product.name
		}">
                        <div class="product-overlay">
                            <button class="btn btn-outline-light btn-sm add-to-wishlist-related"
                                    data-product-id="${product.id}"
                                    data-bs-toggle="tooltip"
                                    title="Thêm vào yêu thích">
                                <i class="fas fa-heart"></i>
                            </button>
                        </div>
                        ${
							product.discount
								? `
                            <div class="discount-badge">-${product.discount}%</div>
                        `
								: ''
						}
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title">${product.name}</h6>
                        <div class="product-rating mb-2">
                            ${this.generateStarRating(product.rating)}
                            <small class="text-muted ms-1">(${
								product.reviews
							})</small>
                        </div>
                        <div class="product-price mb-3">
                            <span class="current-price">${this.formatPrice(
								product.price
							)}</span>
                            ${
								product.originalPrice
									? `
                                <span class="original-price ms-2">${this.formatPrice(
									product.originalPrice
								)}</span>
                            `
									: ''
							}
                        </div>
                        <div class="mt-auto">
                            <button class="btn btn-primary w-100 add-to-cart-related"
                                    data-product-id="${product.id}">
                                <i class="fas fa-shopping-cart me-2"></i>Thêm vào giỏ
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
	}

	generateStarRating(rating) {
		let stars = '';
		for (let i = 1; i <= 5; i++) {
			if (i <= rating) {
				stars += '<i class="fas fa-star text-warning"></i>';
			} else if (i - 0.5 <= rating) {
				stars += '<i class="fas fa-star-half-alt text-warning"></i>';
			} else {
				stars += '<i class="far fa-star text-warning"></i>';
			}
		}
		return stars;
	}

	startCountdown() {
		const countdownElement = document.getElementById('deliveryCountdown');
		if (!countdownElement) return;

		// Calculate delivery date (3 days from now)
		const deliveryDate = new Date();
		deliveryDate.setDate(deliveryDate.getDate() + 3);
		deliveryDate.setHours(17, 0, 0, 0); // 5 PM delivery time

		const updateCountdown = () => {
			const now = new Date().getTime();
			const distance = deliveryDate.getTime() - now;

			if (distance > 0) {
				const days = Math.floor(distance / (1000 * 60 * 60 * 24));
				const hours = Math.floor(
					(distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
				);
				const minutes = Math.floor(
					(distance % (1000 * 60 * 60)) / (1000 * 60)
				);
				const seconds = Math.floor((distance % (1000 * 60)) / 1000);

				countdownElement.innerHTML = `
                    <div class="countdown-item">
                        <span class="countdown-number">${days}</span>
                        <span class="countdown-label">Ngày</span>
                    </div>
                    <div class="countdown-item">
                        <span class="countdown-number">${hours}</span>
                        <span class="countdown-label">Giờ</span>
                    </div>
                    <div class="countdown-item">
                        <span class="countdown-number">${minutes}</span>
                        <span class="countdown-label">Phút</span>
                    </div>
                    <div class="countdown-item">
                        <span class="countdown-number">${seconds}</span>
                        <span class="countdown-label">Giây</span>
                    </div>
                `;
			} else {
				countdownElement.innerHTML =
					'<span class="text-success">Hàng đã được giao!</span>';
			}
		};

		// Update immediately and then every second
		updateCountdown();
		setInterval(updateCountdown, 1000);
	}

	continueShopping() {
		// Add smooth transition effect
		document.body.style.opacity = '0.8';
		document.body.style.transition = 'opacity 0.3s ease';

		setTimeout(() => {
			window.location.href = '/shop';
		}, 300);
	}

	trackOrder() {
		if (this.orderId) {
			window.location.href = `/order/tracking?order=${this.orderId}`;
		} else {
			window.location.href = '/order/tracking';
		}
	}

	updateTrackingLink() {
		const trackButtons = document.querySelectorAll('.track-order');
		trackButtons.forEach((button) => {
			button.href = `/order/tracking?order=${this.orderId}`;
		});
	}

	printReceipt() {
		// Create print-friendly version
		const printContent = this.generatePrintContent();
		const originalContent = document.body.innerHTML;

		document.body.innerHTML = printContent;
		window.print();
		document.body.innerHTML = originalContent;

		// Reinitialize after print
		setTimeout(() => {
			this.init();
		}, 500);
	}

	generatePrintContent() {
		const orderData = this.orderData;
		if (!orderData) return '';

		return `
            <div style="padding: 40px; font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto;">
                <div style="text-align: center; margin-bottom: 40px;">
                    <h1 style="color: #333; margin-bottom: 10px;">zone FASHION</h1>
                    <p style="color: #666; margin: 0;">Hóa đơn mua hàng</p>
                </div>

                <div style="border: 2px solid #ddd; padding: 30px; border-radius: 10px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 30px;">
                        <div>
                            <h3 style="color: #333; margin-bottom: 15px;">Thông tin đơn hàng</h3>
                            <p><strong>Mã đơn hàng:</strong> ${
								orderData.orderId
							}</p>
                            <p><strong>Ngày đặt:</strong> ${
								orderData.orderDate
							}</p>
                            <p><strong>Phương thức thanh toán:</strong> ${
								orderData.paymentMethod
							}</p>
                        </div>
                        <div>
                            <h3 style="color: #333; margin-bottom: 15px;">Thông tin giao hàng</h3>
                            <p><strong>Người nhận:</strong> ${
								orderData.customerName
							}</p>
                            <p><strong>Địa chỉ:</strong> ${
								orderData.shippingAddress
							}</p>
                            <p><strong>Điện thoại:</strong> ${
								orderData.customerPhone
							}</p>
                        </div>
                    </div>

                    <h3 style="color: #333; margin-bottom: 20px;">Chi tiết sản phẩm</h3>
                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
                        <thead>
                            <tr style="background-color: #f8f9fa;">
                                <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Sản phẩm</th>
                                <th style="padding: 12px; text-align: center; border: 1px solid #ddd;">Số lượng</th>
                                <th style="padding: 12px; text-align: right; border: 1px solid #ddd;">Đơn giá</th>
                                <th style="padding: 12px; text-align: right; border: 1px solid #ddd;">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${orderData.items
								.map(
									(item) => `
                                <tr>
                                    <td style="padding: 12px; border: 1px solid #ddd;">
                                        ${item.name}<br>
                                        <small style="color: #666;">Màu: ${
											item.color
										} | Size: ${item.size}</small>
                                    </td>
                                    <td style="padding: 12px; text-align: center; border: 1px solid #ddd;">${
										item.quantity
									}</td>
                                    <td style="padding: 12px; text-align: right; border: 1px solid #ddd;">${this.formatPrice(
										item.price
									)}</td>
                                    <td style="padding: 12px; text-align: right; border: 1px solid #ddd;">${this.formatPrice(
										item.price * item.quantity
									)}</td>
                                </tr>
                            `
								)
								.join('')}
                        </tbody>
                    </table>

                    <div style="text-align: right; margin-top: 20px;">
                        <p><strong>Tạm tính:</strong> ${this.formatPrice(
							orderData.subtotal
						)}</p>
                        <p><strong>Phí vận chuyển:</strong> ${this.formatPrice(
							orderData.shippingFee
						)}</p>
                        <p><strong>Giảm giá:</strong> -${this.formatPrice(
							orderData.discount
						)}</p>
                        <h3 style="color: #28a745;"><strong>Tổng cộng:</strong> ${this.formatPrice(
							orderData.total
						)}</h3>
                    </div>
                </div>

                <div style="text-align: center; margin-top: 40px; color: #666;">
                    <p>Cảm ơn bạn đã mua sắm tại zone Fashion!</p>
                    <p>Hotline: 1900-5555 | Email: support@zonefashion.com</p>
                </div>
            </div>
        `;
	}

	shareOnFacebook() {
		const url = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(
			window.location.href
		)}`;
		this.openShareWindow(url);
	}

	shareOnTwitter() {
		const text = 'Vừa mua sắm thành công tại zone Fashion! 🛍️✨';
		const url = `https://twitter.com/intent/tweet?text=${encodeURIComponent(
			text
		)}&url=${encodeURIComponent(window.location.href)}`;
		this.openShareWindow(url);
	}

	openShareWindow(url) {
		window.open(
			url,
			'share',
			'width=600,height=400,scrollbars=yes,resizable=yes'
		);
	}

	subscribeNewsletter() {
		const email = document.getElementById('newsletterEmail').value;
		const subscribeBtn = document.querySelector('#newsletterForm button');

		if (!email) {
			this.showMessage('Vui lòng nhập email!', 'warning');
			return;
		}

		// Simulate subscription
		subscribeBtn.innerHTML =
			'<i class="fas fa-spinner fa-spin me-2"></i>Đang đăng ký...';
		subscribeBtn.disabled = true;

		setTimeout(() => {
			subscribeBtn.innerHTML =
				'<i class="fas fa-check me-2"></i>Đã đăng ký!';
			subscribeBtn.classList.remove('btn-outline-primary');
			subscribeBtn.classList.add('btn-success');

			this.showMessage(
				'Đăng ký nhận tin thành công! Cảm ơn bạn đã quan tâm.',
				'success'
			);

			document.getElementById('newsletterEmail').value = '';
		}, 1500);
	}

	addRelatedToCart(productId) {
		const button = document.querySelector(
			`[data-product-id="${productId}"]`
		);
		const originalText = button.innerHTML;

		button.innerHTML =
			'<i class="fas fa-spinner fa-spin me-2"></i>Đang thêm...';
		button.disabled = true;

		// Simulate adding to cart
		setTimeout(() => {
			button.innerHTML = '<i class="fas fa-check me-2"></i>Đã thêm!';
			button.classList.remove('btn-primary');
			button.classList.add('btn-success');

			this.showMessage('Đã thêm sản phẩm vào giỏ hàng!', 'success');

			// Reset button after 2 seconds
			setTimeout(() => {
				button.innerHTML = originalText;
				button.classList.remove('btn-success');
				button.classList.add('btn-primary');
				button.disabled = false;
			}, 2000);
		}, 1000);
	}

	addRelatedToWishlist(productId) {
		const button = document.querySelector(
			`.add-to-wishlist-related[data-product-id="${productId}"]`
		);

		button.innerHTML = '<i class="fas fa-heart text-danger"></i>';
		button.classList.add('added-to-wishlist');

		this.showMessage('Đã thêm vào danh sách yêu thích!', 'success');
	}

	downloadInvoice() {
		// Get order ID from URL or page data
		const urlParams = new URLSearchParams(window.location.search);
		const orderId = urlParams.get('order_id') || this.orderId || this.getOrderIdFromPage();
		
		if (!orderId) {
			this.showMessage('Không tìm thấy mã đơn hàng', 'error');
			return;
		}

		// Show loading message
		this.showMessage('Đang tạo hóa đơn PDF...', 'info');

		// Create download link for PDF
		const downloadUrl = `/zone-fashion/order/downloadInvoice?order_id=${orderId}`;
		
		// Create hidden iframe to trigger download
		const iframe = document.createElement('iframe');
		iframe.style.display = 'none';
		iframe.src = downloadUrl;
		document.body.appendChild(iframe);
		
		// Clean up iframe after download starts
		setTimeout(() => {
			document.body.removeChild(iframe);
			this.showMessage('Hóa đơn PDF đang được tải xuống...', 'success');
		}, 1000);
	}

	getOrderIdFromPage() {
		// Try to get order ID from various sources on the page
		const orderCodeElement = document.getElementById('orderCode');
		if (orderCodeElement) {
			const orderCode = orderCodeElement.textContent.trim();
			// Extract numeric ID from order code if needed
			const match = orderCode.match(/\d+/);
			return match ? match[0] : null;
		}
		
		// Try to get from URL path
		const pathMatch = window.location.pathname.match(/\/order\/success\/(\d+)/);
		if (pathMatch) {
			return pathMatch[1];
		}
		
		return null;
	}

	showMessage(message, type = 'info') {
		// Remove existing messages
		document
			.querySelectorAll('.success-message')
			.forEach((msg) => msg.remove());

		const messageDiv = document.createElement('div');
		messageDiv.className = `alert alert-${type} success-message fixed-top`;
		messageDiv.style.cssText =
			'top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999; max-width: 500px;';
		messageDiv.innerHTML = `
            <i class="fas fa-${this.getMessageIcon(type)} me-2"></i>
            ${message}
        `;

		document.body.appendChild(messageDiv);

		// Auto hide after 3 seconds
		setTimeout(() => {
			messageDiv.remove();
		}, 3000);
	}

	getMessageIcon(type) {
		const icons = {
			success: 'check-circle',
			warning: 'exclamation-triangle',
			danger: 'times-circle',
			info: 'info-circle',
		};
		return icons[type] || 'info-circle';
	}

	formatPrice(price) {
		return new Intl.NumberFormat('vi-VN', {
			style: 'currency',
			currency: 'VND',
		}).format(price);
	}

	getOrderIdFromUrl() {
		const urlParams = new URLSearchParams(window.location.search);
		return urlParams.get('order') || 'FS240101001';
	}

	getOrderData() {
		// Simulate getting order data from API
		return {
			orderId: this.orderId,
			orderDate: '15/12/2024',
			customerName: 'Nguyễn Văn An',
			customerPhone: '0901234567',
			shippingAddress: '123 Lê Văn Sỹ, Phường 1, Quận 3, TP.HCM',
			paymentMethod: 'COD (Thanh toán khi nhận hàng)',
			subtotal: 1990000,
			shippingFee: 30000,
			discount: 200000,
			total: 1820000,
			items: [
				{
					name: 'Áo Sơ Mi Nam Trắng Classic',
					color: 'Trắng',
					size: 'L',
					price: 450000,
					quantity: 1,
				},
				{
					name: 'Quần Jeans Nam Slim Fit',
					color: 'Xanh đậm',
					size: '32',
					price: 650000,
					quantity: 1,
				},
				{
					name: 'Giày Sneaker Nam Sport',
					color: 'Đen',
					size: '42',
					price: 890000,
					quantity: 1,
				},
			],
		};
	}

	getRelatedProducts() {
		return [
			{
				id: 'P001',
				name: 'Áo Polo Nam Premium',
				price: 380000,
				originalPrice: 450000,
				discount: 15,
				rating: 4.5,
				reviews: 128,
				image: '/public/assets/images/products/polo-navy.jpg',
			},
			{
				id: 'P002',
				name: 'Quần Khaki Nam Classic',
				price: 520000,
				rating: 4.3,
				reviews: 95,
				image: '/public/assets/images/products/khaki-beige.jpg',
			},
			{
				id: 'P003',
				name: 'Giày Da Nam Oxford',
				price: 1200000,
				originalPrice: 1400000,
				discount: 14,
				rating: 4.7,
				reviews: 203,
				image: '/public/assets/images/products/oxford-brown.jpg',
			},
			{
				id: 'P004',
				name: 'Thắt Lưng Da Thật',
				price: 280000,
				rating: 4.4,
				reviews: 67,
				image: '/public/assets/images/products/belt-black.jpg',
			},
		];
	}
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
	window.orderSuccessManager = new OrderSuccessManager();
});
