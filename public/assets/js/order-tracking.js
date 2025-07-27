/**
 * Order Tracking Manager - Phase 3.4
 * Handles order tracking functionality, search, timeline updates
 */

class OrderTrackingManager {
	constructor() {
		this.currentOrder = null;
		this.trackingData = this.generateSampleData();
		this.init();
	}

	init() {
		this.bindEvents();
		this.initializeComponents();

		// Auto-fill if order ID in URL
		const urlParams = new URLSearchParams(window.location.search);
		const orderId = urlParams.get('order');
		if (orderId) {
			document.getElementById('orderCode').value = orderId;
			this.searchOrder(orderId);
		}
	}

	bindEvents() {
		// Search form
		const searchForm = document.getElementById('trackingSearchForm');
		if (searchForm) {
			searchForm.addEventListener('submit', (e) => {
				e.preventDefault();
				this.handleSearch();
			});
		}

		// Quick search buttons
		document.querySelectorAll('.quick-search-btn').forEach((btn) => {
			btn.addEventListener('click', (e) => {
				e.preventDefault();
				const orderCode = btn.dataset.order;
				document.getElementById('orderCode').value = orderCode;
				this.searchOrder(orderCode);
			});
		});

		// Real-time search
		const orderCodeInput = document.getElementById('orderCode');
		if (orderCodeInput) {
			orderCodeInput.addEventListener(
				'input',
				this.debounce((e) => {
					const value = e.target.value.trim();
					if (value.length >= 6) {
						this.searchOrder(value);
					}
				}, 500)
			);
		}

		// Refresh button
		document.addEventListener('click', (e) => {
			if (e.target.matches('.refresh-tracking')) {
				e.preventDefault();
				this.refreshTracking();
			}
		});

		// Contact support buttons
		document.addEventListener('click', (e) => {
			if (e.target.matches('.contact-support')) {
				e.preventDefault();
				this.openSupportChat();
			}
		});

		// Print order button
		document.addEventListener('click', (e) => {
			if (e.target.matches('.print-order')) {
				e.preventDefault();
				this.printOrder();
			}
		});
	}

	initializeComponents() {
		// Initialize tooltips
		if (typeof bootstrap !== 'undefined') {
			const tooltipTriggerList = [].slice.call(
				document.querySelectorAll('[data-bs-toggle="tooltip"]')
			);
			tooltipTriggerList.map(function (tooltipTriggerEl) {
				return new bootstrap.Tooltip(tooltipTriggerEl);
			});
		}
	}

	handleSearch() {
		const orderCode = document.getElementById('orderCode').value.trim();
		const email = document.getElementById('customerEmail').value.trim();

		if (!orderCode) {
			this.showMessage('Vui lòng nhập mã đơn hàng', 'warning');
			return;
		}

		this.searchOrder(orderCode, email);
	}

	searchOrder(orderCode, email = '') {
		this.showLoading();

		// Simulate API call
		setTimeout(() => {
			const order = this.trackingData[orderCode];

			if (order) {
				this.currentOrder = order;
				this.displayTrackingResults(order);
				this.hideLoading();
			} else {
				this.showNoResults();
				this.hideLoading();
			}
		}, 1000);
	}

	displayTrackingResults(order) {
		const resultsContainer = document.getElementById('trackingResults');
		if (!resultsContainer) return;

		resultsContainer.innerHTML = this.generateTrackingHTML(order);
		resultsContainer.style.display = 'block';

		// Animate timeline items
		this.animateTimeline();

		// Scroll to results
		resultsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
	}

	generateTrackingHTML(order) {
		return `
            <!-- Order Information -->
            <div class="row mb-4">
                <div class="col-lg-6 mb-3">
                    <div class="order-info-card">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-receipt me-2"></i>Thông tin đơn hàng
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="info-item">
                                    <span class="label">Mã đơn hàng:</span>
                                    <span class="value">${order.code}</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Ngày đặt:</span>
                                    <span class="value">${
										order.orderDate
									}</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Trạng thái:</span>
                                    <span class="value">
                                        <span class="badge bg-${
											order.statusColor
										}">${order.status}</span>
                                    </span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Tổng tiền:</span>
                                    <span class="value total-amount">${this.formatPrice(
										order.total
									)}</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Phương thức TT:</span>
                                    <span class="value">${
										order.paymentMethod
									}</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Giao hàng dự kiến:</span>
                                    <span class="value">${
										order.estimatedDelivery
									}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-3">
                    <div class="tracking-timeline-card">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-route me-2"></i>Lịch trình vận chuyển
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="tracking-timeline">
                                    ${this.generateTimelineHTML(order.timeline)}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-shopping-bag me-2"></i>Sản phẩm đã đặt (${
									order.items.length
								} sản phẩm)
                            </h5>
                        </div>
                        <div class="card-body">
                            ${order.items
								.map((item) => this.generateOrderItemHTML(item))
								.join('')}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipping Information -->
            <div class="row mb-4">
                <div class="col-lg-6 mb-3">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-truck me-2"></i>Thông tin vận chuyển
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="shipping-detail">
                                <strong>Đơn vị vận chuyển:</strong>
                                <span>${order.shipping.carrier}</span>
                            </div>
                            <div class="shipping-detail">
                                <strong>Mã vận đơn:</strong>
                                <span>${order.shipping.trackingNumber}</span>
                            </div>
                            <div class="shipping-detail">
                                <strong>Địa chỉ giao hàng:</strong>
                                <span>${order.shipping.address}</span>
                            </div>
                            <div class="shipping-detail">
                                <strong>Người nhận:</strong>
                                <span>${order.shipping.recipient}</span>
                            </div>
                            <div class="shipping-detail">
                                <strong>Số điện thoại:</strong>
                                <span>${order.shipping.phone}</span>
                            </div>
                            <div class="shipping-detail">
                                <strong>Ghi chú:</strong>
                                <span>${
									order.shipping.note || 'Không có'
								}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-3">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>Lưu ý quan trọng
                            </h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="fas fa-clock text-primary me-2"></i>
                                    Đơn hàng sẽ được giao trong giờ hành chính (8:00 - 17:30)
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-phone text-success me-2"></i>
                                    Shipper sẽ gọi trước khi giao hàng 15-30 phút
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-id-card text-info me-2"></i>
                                    Vui lòng chuẩn bị CMND/CCCD để xác thực
                                </li>
                                <li class="mb-0">
                                    <i class="fas fa-money-bill text-warning me-2"></i>
                                    ${
										order.paymentMethod === 'COD'
											? 'Chuẩn bị tiền mặt đúng số tiền'
											: 'Đơn hàng đã thanh toán online'
									}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Actions -->
            <div class="order-actions text-center">
                <button class="btn btn-outline-primary me-3 refresh-tracking">
                    <i class="fas fa-sync-alt me-2"></i>Làm mới thông tin
                </button>
                <button class="btn btn-outline-success me-3 print-order">
                    <i class="fas fa-print me-2"></i>In đơn hàng
                </button>
                <button class="btn btn-outline-danger contact-support">
                    <i class="fas fa-headset me-2"></i>Liên hệ hỗ trợ
                </button>
            </div>
        `;
	}

	generateTimelineHTML(timeline) {
		return timeline
			.map(
				(item) => `
            <div class="timeline-item ${item.status}">
                <div class="timeline-marker">
                    <i class="fas ${item.icon}"></i>
                </div>
                <div class="timeline-content">
                    <div class="timeline-time">${item.time}</div>
                    <div class="timeline-title">${item.title}</div>
                    <div class="timeline-description">${item.description}</div>
                    ${
						item.location
							? `<div class="timeline-location">
                        <i class="fas fa-map-marker-alt me-1"></i>${item.location}
                    </div>`
							: ''
					}
                </div>
            </div>
        `
			)
			.join('');
	}

	generateOrderItemHTML(item) {
		return `
            <div class="tracking-order-item">
                <div class="item-image">
                    <img src="${item.image}" alt="${
			item.name
		}" class="img-fluid">
                </div>
                <div class="item-details">
                    <div class="item-name">${item.name}</div>
                    <div class="item-variant">Màu: ${item.color} | Size: ${
			item.size
		}</div>
                    <div class="item-price">
                        <span class="price">Đơn giá: ${this.formatPrice(
							item.price
						)}</span>
                        <span class="quantity">SL: ${item.quantity}</span>
                    </div>
                </div>
                <div class="item-total">${this.formatPrice(
					item.price * item.quantity
				)}</div>
            </div>
        `;
	}

	showNoResults() {
		const resultsContainer = document.getElementById('trackingResults');
		if (!resultsContainer) return;

		resultsContainer.innerHTML = `
            <div class="no-results">
                <div class="alert alert-warning text-center">
                    <div class="mb-3">
                        <i class="fas fa-search fa-3x text-muted"></i>
                    </div>
                    <h5>Không tìm thấy đơn hàng</h5>
                    <p class="mb-3">Vui lòng kiểm tra lại mã đơn hàng hoặc email đã nhập.</p>
                    <div class="help-actions">
                        <button class="btn btn-outline-primary me-2" onclick="location.reload()">
                            <i class="fas fa-redo me-2"></i>Thử lại
                        </button>
                        <button class="btn btn-outline-success contact-support">
                            <i class="fas fa-headset me-2"></i>Liên hệ hỗ trợ
                        </button>
                    </div>
                </div>
            </div>
        `;

		resultsContainer.style.display = 'block';
		resultsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
	}

	animateTimeline() {
		const timelineItems = document.querySelectorAll('.timeline-item');
		timelineItems.forEach((item, index) => {
			setTimeout(() => {
				item.style.opacity = '1';
				item.style.transform = 'translateY(0)';
			}, index * 200);
		});
	}

	refreshTracking() {
		if (!this.currentOrder) return;

		this.showMessage('Đang cập nhật thông tin...', 'info');

		// Simulate refresh
		setTimeout(() => {
			this.displayTrackingResults(this.currentOrder);
			this.showMessage('Đã cập nhật thông tin mới nhất!', 'success');
		}, 1500);
	}

	printOrder() {
		if (!this.currentOrder) return;

		// Hide non-printable elements and print
		const originalContent = document.body.innerHTML;
		const printContent =
			document.getElementById('trackingResults').innerHTML;

		document.body.innerHTML = `
            <div style="padding: 20px; font-family: Arial, sans-serif;">
                <h2 style="text-align: center; margin-bottom: 30px;">
                    Chi tiết đơn hàng #${this.currentOrder.code}
                </h2>
                ${printContent}
            </div>
        `;

		window.print();
		document.body.innerHTML = originalContent;

		// Reinitialize after print
		setTimeout(() => {
			window.location.reload();
		}, 500);
	}

	openSupportChat() {
		// Simulate opening support chat
		this.showMessage('Đang kết nối với bộ phận hỗ trợ...', 'info');

		setTimeout(() => {
			alert(
				'Chức năng chat hỗ trợ sẽ được cập nhật sớm!\n\nVui lòng liên hệ:\n📞 1900-5555\n📧 support@5sfashion.com'
			);
		}, 1000);
	}

	showLoading() {
		const loadingOverlay = document.createElement('div');
		loadingOverlay.className = 'loading-overlay';
		loadingOverlay.id = 'loadingOverlay';
		loadingOverlay.innerHTML = `
            <div class="loading-content">
                <div class="loading-spinner"></div>
                <p class="mb-0">Đang tìm kiếm đơn hàng...</p>
            </div>
        `;
		document.body.appendChild(loadingOverlay);
	}

	hideLoading() {
		const loadingOverlay = document.getElementById('loadingOverlay');
		if (loadingOverlay) {
			loadingOverlay.remove();
		}
	}

	showMessage(message, type = 'info') {
		// Remove existing messages
		document
			.querySelectorAll('.tracking-message')
			.forEach((msg) => msg.remove());

		const messageDiv = document.createElement('div');
		messageDiv.className = `alert alert-${type} tracking-message`;
		messageDiv.innerHTML = `
            <i class="fas fa-${this.getMessageIcon(type)} me-2"></i>
            ${message}
        `;

		const container = document.querySelector(
			'.tracking-section .container'
		);
		if (container) {
			container.insertBefore(messageDiv, container.firstChild);

			// Auto hide after 3 seconds
			setTimeout(() => {
				messageDiv.remove();
			}, 3000);
		}
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

	debounce(func, wait) {
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

	generateSampleData() {
		return {
			FS240101001: {
				code: 'FS240101001',
				status: 'Đang giao hàng',
				statusColor: 'primary',
				orderDate: '15/12/2024',
				estimatedDelivery: '18/12/2024',
				total: 1250000,
				paymentMethod: 'COD (Thanh toán khi nhận hàng)',
				timeline: [
					{
						status: 'completed',
						time: '15/12/2024 - 09:30',
						title: 'Đơn hàng đã được xác nhận',
						description:
							'Đơn hàng của bạn đã được xác nhận và đang chuẩn bị.',
						location: '5S Fashion Store - TP.HCM',
						icon: 'fa-check',
					},
					{
						status: 'completed',
						time: '15/12/2024 - 14:20',
						title: 'Đang đóng gói sản phẩm',
						description:
							'Các sản phẩm đang được đóng gói cẩn thận.',
						location: 'Kho 5S Fashion - Quận 1',
						icon: 'fa-box',
					},
					{
						status: 'completed',
						time: '16/12/2024 - 08:15',
						title: 'Đã bàn giao cho đơn vị vận chuyển',
						description:
							'Đơn hàng đã được chuyển cho Giao Hàng Nhanh.',
						location: 'Hub GHN - TP.HCM',
						icon: 'fa-shipping-fast',
					},
					{
						status: 'active',
						time: '17/12/2024 - 07:00',
						title: 'Đang trên đường giao hàng',
						description:
							'Shipper đang trên đường đến địa chỉ của bạn.',
						location: 'Đang di chuyển đến Quận 3',
						icon: 'fa-truck',
					},
					{
						status: 'pending',
						time: 'Dự kiến 18/12/2024',
						title: 'Giao hàng thành công',
						description: 'Đơn hàng sẽ được giao đến bạn.',
						location: '123 Lê Văn Sỹ, Q.3, TP.HCM',
						icon: 'fa-home',
					},
				],
				items: [
					{
						name: 'Áo Sơ Mi Nam Trắng Classic',
						color: 'Trắng',
						size: 'L',
						price: 450000,
						quantity: 1,
						image: '/public/assets/images/products/shirt-white.jpg',
					},
					{
						name: 'Quần Jeans Nam Slim Fit',
						color: 'Xanh đậm',
						size: '32',
						price: 650000,
						quantity: 1,
						image: '/public/assets/images/products/jeans-blue.jpg',
					},
					{
						name: 'Giày Sneaker Nam Sport',
						color: 'Đen',
						size: '42',
						price: 890000,
						quantity: 1,
						image: '/public/assets/images/products/sneaker-black.jpg',
					},
				],
				shipping: {
					carrier: 'Giao Hàng Nhanh (GHN)',
					trackingNumber: 'GHN123456789',
					recipient: 'Nguyễn Văn An',
					phone: '0901234567',
					address: '123 Lê Văn Sỹ, Phường 1, Quận 3, TP.HCM',
					note: 'Gọi trước khi giao 15 phút',
				},
			},
			FS240101002: {
				code: 'FS240101002',
				status: 'Đã giao hàng',
				statusColor: 'success',
				orderDate: '10/12/2024',
				estimatedDelivery: '13/12/2024',
				total: 890000,
				paymentMethod: 'Chuyển khoản ngân hàng',
				timeline: [
					{
						status: 'completed',
						time: '10/12/2024 - 10:15',
						title: 'Đơn hàng đã được xác nhận',
						description: 'Đơn hàng của bạn đã được xác nhận.',
						location: '5S Fashion Store - TP.HCM',
						icon: 'fa-check',
					},
					{
						status: 'completed',
						time: '11/12/2024 - 09:30',
						title: 'Đã bàn giao vận chuyển',
						description:
							'Đơn hàng đã được chuyển cho Viettel Post.',
						location: 'Hub Viettel Post - TP.HCM',
						icon: 'fa-shipping-fast',
					},
					{
						status: 'completed',
						time: '13/12/2024 - 14:20',
						title: 'Giao hàng thành công',
						description:
							'Đơn hàng đã được giao thành công đến khách hàng.',
						location: '456 Nguyễn Thị Minh Khai, Q.1',
						icon: 'fa-check-circle',
					},
				],
				items: [
					{
						name: 'Váy Maxi Nữ Hoa Nhí',
						color: 'Hồng',
						size: 'M',
						price: 890000,
						quantity: 1,
						image: '/public/assets/images/products/dress-floral.jpg',
					},
				],
				shipping: {
					carrier: 'Viettel Post',
					trackingNumber: 'VTP987654321',
					recipient: 'Trần Thị Bích',
					phone: '0909876543',
					address:
						'456 Nguyễn Thị Minh Khai, Phường Bến Nghé, Quận 1, TP.HCM',
					note: 'Để hàng với bảo vệ nếu không có người',
				},
			},
			FS240101003: {
				code: 'FS240101003',
				status: 'Đang xử lý',
				statusColor: 'warning',
				orderDate: '18/12/2024',
				estimatedDelivery: '22/12/2024',
				total: 1580000,
				paymentMethod: 'Thanh toán qua Momo',
				timeline: [
					{
						status: 'completed',
						time: '18/12/2024 - 16:45',
						title: 'Đơn hàng đã được tiếp nhận',
						description:
							'Đơn hàng của bạn đã được tiếp nhận và đang chờ xác nhận.',
						location: '5S Fashion Store - TP.HCM',
						icon: 'fa-receipt',
					},
					{
						status: 'active',
						time: '19/12/2024 - 08:00',
						title: 'Đang xác nhận đơn hàng',
						description:
							'Chúng tôi đang xác nhận thông tin và kiểm tra hàng tồn kho.',
						location: '5S Fashion Store - TP.HCM',
						icon: 'fa-hourglass-half',
					},
					{
						status: 'pending',
						time: 'Dự kiến 19/12/2024',
						title: 'Chuẩn bị hàng hóa',
						description: 'Sản phẩm sẽ được chuẩn bị và đóng gói.',
						location: 'Kho 5S Fashion',
						icon: 'fa-box',
					},
					{
						status: 'pending',
						time: 'Dự kiến 20/12/2024',
						title: 'Bàn giao vận chuyển',
						description:
							'Đơn hàng sẽ được chuyển cho đơn vị vận chuyển.',
						location: 'Hub vận chuyển',
						icon: 'fa-shipping-fast',
					},
				],
				items: [
					{
						name: 'Bộ Vest Nam Cao Cấp',
						color: 'Xám',
						size: 'L',
						price: 1580000,
						quantity: 1,
						image: '/public/assets/images/products/suit-gray.jpg',
					},
				],
				shipping: {
					carrier: 'Chưa xác định',
					trackingNumber: 'Chưa có',
					recipient: 'Lê Minh Tuấn',
					phone: '0912345678',
					address: '789 Võ Văn Tần, Phường 6, Quận 3, TP.HCM',
					note: 'Giao hàng trong giờ hành chính',
				},
			},
		};
	}
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
	window.orderTrackingManager = new OrderTrackingManager();
});
