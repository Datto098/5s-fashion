/**
 * Checkout Manager - Phase 3.3
 * Handles checkout process including form validation, order processing, and payment
 */

console.log('Checkout.js loaded successfully');

// Force immediate template replacement test
console.log('=== TESTING TEMPLATE REPLACEMENT IMMEDIATELY ===');

const TEST_TEMPLATE = `
    <div class="order-item">
        <div class="item-name fw-bold">{name}</div>
        <div class="item-price text-danger fw-bold">{price}</div>
        <img src="{image}" alt="{name}">
    </div>
`;

const TEST_DATA = {
	name: 'Váy Maxi Nữ Hoa Nhí',
	price: '599,000 ₫',
	image: '/uploads/products/test.jpg',
};

let testResult = TEST_TEMPLATE;
console.log('Before replacement:', testResult);

// Manual replacement like in CheckoutManager - FIXED WITH global regex
testResult = testResult.replace(/\{name\}/g, TEST_DATA.name);
testResult = testResult.replace(/\{price\}/g, TEST_DATA.price);
testResult = testResult.replace(/\{image\}/g, TEST_DATA.image);

console.log('After replacement:', testResult);
console.log('Contains {name}?', testResult.includes('{name}'));
console.log('Contains product name?', testResult.includes('Váy Maxi'));

class CheckoutManager {
	constructor() {
		console.log('CheckoutManager constructor called');
		this.cart = null;
		this.addresses = [];
		this.selectedAddress = null;
		this.orderSummary = {
			subtotal: 0,
			shipping: 30000,
			discount: 0,
			total: 0,
		};
		console.log('CheckoutManager initialized');
	}

	async loadOrder() {
		try {
			const response = await fetch('/5s-fashion/ajax/cart/items');
			if (response.ok) {
				const data = await response.json();
				console.log('Cart API Response:', data);
				console.log('First cart item (if exists):', data.items?.[0]);

				if (data.success && data.items && data.items.length > 0) {
					this.cart = data.items;
					this.displayOrderItems();
					this.calculateTotal();
				} else {
					// Redirect to cart if empty
					window.location.href = '/?route=cart';
				}
			} else {
				throw new Error('Failed to load cart');
			}
		} catch (error) {
			console.error('Error loading order:', error);
			this.showError('Không thể tải thông tin đơn hàng');
		}
	}

	displayOrderItems() {
		const orderItemsContainer = document.querySelector('.order-items');
		if (!orderItemsContainer || !this.cart || this.cart.length === 0) {
			console.warn('No order items container or empty cart');
			return;
		}

		const template = document.getElementById('orderItemTemplate');
		if (!template) {
			console.error('Order item template not found');
			return;
		}

		console.log(`Processing ${this.cart.length} cart items`);

		let html = '';
		this.cart.forEach((item, index) => {
			console.log(`Processing item ${index + 1}:`, item);

			// Clone template properly
			let templateContent = template.innerHTML;

			console.log('Raw template:', templateContent);

			// Debug log
			console.log('Cart item:', item);

			// Process image URL with better path handling
			let imageUrl = '';
			if (item.product_image && item.product_image.trim() !== '') {
				const imagePath = item.product_image.trim();

				// Remove any leading slashes and uploads/products/ prefix
				let cleanPath = imagePath
					.replace(/^\/+/, '')
					.replace(/^uploads\/products\//, '');

				// Check if it's already a full URL
				if (imagePath.startsWith('http')) {
					imageUrl = imagePath;
				} else {
					// Always use serve-file.php for relative paths
					imageUrl = `/5s-fashion/serve-file.php?file=${encodeURIComponent(
						'products/' + cleanPath
					)}`;
				}
			}

			console.log('Image processing:', {
				original: item.product_image,
				processed: imageUrl,
			});

			const productName = item.product_name || 'Sản phẩm';
			console.log('Product name processing:', {
				product_name: item.product_name,
				final: productName,
			});

			// Build variant info from the variant field
			let variantInfo = '';
			if (item.variant) {
				// variant might be a string or object, handle both cases
				if (typeof item.variant === 'string') {
					variantInfo = item.variant;
				} else if (typeof item.variant === 'object') {
					const parts = [];
					if (item.variant.color)
						parts.push(`Màu: ${item.variant.color}`);
					if (item.variant.size)
						parts.push(`Size: ${item.variant.size}`);
					variantInfo = parts.join(' | ');
				}
			}

			console.log('Product name:', productName);
			console.log('About to replace {name} with:', productName);

			// Perform replacements with validation
			const replacements = [
				[
					'{image}',
					imageUrl || '/5s-fashion/public/assets/images/no-image.jpg',
				],
				['{name}', productName],
				['{variant_info}', variantInfo],
				['{quantity}', item.quantity || 1],
				[
					'{price}',
					this.formatCurrency(
						(item.price || 0) * (item.quantity || 1)
					),
				],
			];

			replacements.forEach(([placeholder, value]) => {
				const beforeReplace = templateContent;
				// Use global regex to replace ALL occurrences (browser compatible)
				const regex = new RegExp(
					placeholder.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'),
					'g'
				);
				templateContent = templateContent.replace(regex, value);

				if (
					beforeReplace === templateContent &&
					beforeReplace.includes(placeholder)
				) {
					console.warn(
						`❌ Placeholder ${placeholder} was NOT replaced!`
					);
				} else if (beforeReplace !== templateContent) {
					console.log(`✅ Replaced ${placeholder} with:`, value);
				}
			});

			console.log('Final template content:', templateContent);

			html += templateContent;
		});

		orderItemsContainer.innerHTML = html;

		// Add error handling for images with better fallback
		orderItemsContainer
			.querySelectorAll('.item-image img')
			.forEach((img) => {
				img.onerror = function () {
					// Only set fallback once to prevent loops
					if (!this.src.includes('no-image.jpg')) {
						this.src =
							'/5s-fashion/public/assets/images/no-image.jpg';
						this.style.objectFit = 'contain';
						this.style.padding = '10px';
					}
				};

				// Add loading event for success feedback
				img.onload = function () {
					console.log('Image loaded successfully:', this.src);
				};
			});
	}

	calculateTotal() {
		if (!this.cart) return;

		this.orderSummary.subtotal = this.cart.reduce((total, item) => {
			return total + (item.price || 0) * (item.quantity || 1);
		}, 0);

		this.orderSummary.total =
			this.orderSummary.subtotal +
			this.orderSummary.shipping -
			this.orderSummary.discount;
		this.updateOrderSummary();
	}

	updateOrderSummary() {
		const summaryContainer = document.querySelector('.order-summary');
		if (!summaryContainer) return;

		// Only update the totals section, not the entire summary
		let totalsHtml = `
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin đơn hàng</h5>
                </div>
                <div class="card-body">
                    <div class="order-items">
                        <!-- Items will be populated by displayOrderItems -->
                    </div>

                    <hr>

                    <div class="order-totals">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tạm tính:</span>
                            <span>${this.formatCurrency(
								this.orderSummary.subtotal
							)}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Phí vận chuyển:</span>
                            <span>${this.formatCurrency(
								this.orderSummary.shipping
							)}</span>
                        </div>
                        ${
							this.orderSummary.discount > 0
								? `
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>Giảm giá:</span>
                            <span>-${this.formatCurrency(
								this.orderSummary.discount
							)}</span>
                        </div>
                        `
								: ''
						}
                        <hr>
                        <div class="d-flex justify-content-between fw-bold fs-5">
                            <span>Tổng cộng:</span>
                            <span class="text-primary">${this.formatCurrency(
								this.orderSummary.total
							)}</span>
                        </div>
                    </div>
                </div>
            </div>
        `;

		summaryContainer.innerHTML = totalsHtml;

		// Display items after updating summary structure
		this.displayOrderItems();
	}

	async initializeForm() {
		try {
			// Load customer addresses
			await this.loadAddresses();

			// Render checkout form
			this.renderCheckoutForm();
		} catch (error) {
			console.error('Error initializing form:', error);
			this.showError('Không thể khởi tạo form thanh toán');
		}
	}

	async loadAddresses() {
		try {
			const response = await fetch('/5s-fashion/order/addresses');
			console.log('Address API response status:', response.status);

			if (response.ok) {
				const data = await response.json();
				console.log('Load addresses response:', data);

				if (data.success) {
					this.addresses = data.addresses || [];
					console.log('Loaded addresses:', this.addresses);

					// Find default address
					const defaultAddr = this.addresses.find(
						(addr) => addr.is_default
					);
					if (defaultAddr) {
						this.selectedAddress = defaultAddr;
						console.log('Selected default address:', defaultAddr);
					}
				} else {
					console.error('Failed to load addresses:', data.message);
					// Initialize empty addresses array for graceful fallback
					this.addresses = [];
				}
			} else if (response.status === 401 || response.status === 403) {
				console.warn('User not authenticated, redirecting to login...');
				// Redirect to login if not authenticated
				window.location.href =
					'/5s-fashion/login?redirect=' +
					encodeURIComponent(window.location.pathname);
				return;
			} else {
				console.error('HTTP error loading addresses:', response.status);
				// Try to parse error response
				try {
					const errorData = await response.json();
					console.error('Error details:', errorData);
				} catch (e) {
					console.error('Could not parse error response');
				}
				this.addresses = [];
			}
		} catch (error) {
			console.error('Error loading addresses:', error);
			// Fallback: empty addresses array
			this.addresses = [];
		}
	}

	renderCheckoutForm() {
		const formContainer = document.getElementById('checkoutForm');
		if (!formContainer) return;

		formContainer.innerHTML = `
            <!-- Customer Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin giao hàng</h5>
                </div>
                <div class="card-body">
                    ${this.renderAddressSection()}
                </div>
            </div>

            <!-- Payment Method -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Phương thức thanh toán</h5>
                </div>
                <div class="card-body">
                    <div class="payment-methods">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                            <label class="form-check-label d-flex align-items-center" for="cod">
                                <i class="fas fa-money-bill text-success me-2"></i>
                                Thanh toán khi nhận hàng (COD)
                            </label>
                            <div class="text-muted small mt-1">Thanh toán bằng tiền mặt khi nhận hàng</div>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="payment_method" id="vnpay" value="vnpay">
                            <label class="form-check-label d-flex align-items-center" for="vnpay">
                                <i class="fas fa-credit-card text-primary me-2"></i>
                                Thanh toán VNPay
                            </label>
                            <div class="text-muted small mt-1">Thanh toán trực tuyến qua VNPay (ATM, Internet Banking, Visa, MasterCard)</div>
                        </div>

                        <!-- VNPay Bank Selection (ẩn mặc định) -->
                        <div id="vnpay-banks" class="mt-3" style="display: none;">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="bank_code" id="vnpayqr" value="VNPAYQR">
                                        <label class="form-check-label" for="vnpayqr">
                                            <img src="https://sandbox.vnpayment.vn/paymentv2/images/brands/vnpayqr.jpg" alt="VNPay QR" style="height: 24px;" class="me-2">
                                            VNPay QR
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="bank_code" id="vnbank" value="VNBANK">
                                        <label class="form-check-label" for="vnbank">
                                            <i class="fas fa-university text-primary me-2"></i>
                                            ATM/Internet Banking
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="bank_code" id="intcard" value="INTCARD">
                                        <label class="form-check-label" for="intcard">
                                            <i class="fab fa-cc-visa text-info me-2"></i>
                                            Thẻ quốc tế
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="bank_code" id="vietcombank" value="VIETCOMBANK">
                                        <label class="form-check-label" for="vietcombank">
                                            <img src="https://sandbox.vnpayment.vn/paymentv2/images/brands/vcb.jpg" alt="Vietcombank" style="height: 24px;" class="me-2">
                                            Vietcombank
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Notes -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Ghi chú đơn hàng</h5>
                </div>
                <div class="card-body">
                    <textarea class="form-control" name="order_notes" rows="3"
                              placeholder="Ghi chú cho người bán (tùy chọn)"></textarea>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-credit-card me-2"></i>
                    Đặt hàng ngay
                </button>
            </div>
        `;

		// Add form submit handler
		formContainer.addEventListener('submit', (e) => this.handleSubmit(e));
	}

	renderAddressSection() {
		if (this.addresses.length === 0) {
			return `
                <div class="text-center py-4">
                    <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Bạn chưa có địa chỉ giao hàng nào</p>
                    <button type="button" class="btn btn-primary" onclick="addressManager.showAddressModal()">
                        <i class="fas fa-plus me-2"></i>Thêm địa chỉ mới
                    </button>
                </div>
            `;
		}

		let html = `
            <div class="row">
                <div class="col-md-8">
                    <div class="address-list">
        `;

		this.addresses.forEach((addr, index) => {
			const isSelected =
				this.selectedAddress && this.selectedAddress.id === addr.id;
			html += `
                <div class="address-item ${
					isSelected ? 'selected' : ''
				}" onclick="checkoutManager.selectAddress(${addr.id})">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="shipping_address"
                               value="${addr.id}" ${
				isSelected ? 'checked' : ''
			}>
                        <label class="form-check-label w-100">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="fw-bold">${
										addr.name ||
										addr.full_name ||
										'Không có tên'
									}</div>
                                    <div class="text-muted">${
										addr.phone || 'Không có SĐT'
									}</div>
                                    <div class="address-text">${
										addr.address || 'Không có địa chỉ'
									}</div>
                                    ${
										addr.is_default
											? '<span class="badge bg-primary">Mặc định</span>'
											: ''
									}
                                </div>
                                <div class="address-actions">
                                    <button type="button" class="btn btn-sm btn-outline-primary me-1 mb-1"
                                            onclick="event.stopPropagation(); addressManager.editAddress(${
												addr.id
											})">
                                        Sửa
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="event.stopPropagation(); addressManager.deleteAddress(${
												addr.id
											})">
                                        Xóa
                                    </button>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
            `;
		});

		html += `
                    </div>
                </div>
                <div class="col-md-4">
                    <button type="button" class="btn btn-outline-primary w-100" onclick="addressManager.showAddressModal()">
                        <i class="fas fa-plus me-2"></i>Thêm địa chỉ mới
                    </button>
                </div>
            </div>
        `;

		return html;
	}

	selectAddress(addressId) {
		this.selectedAddress = this.addresses.find(
			(addr) => addr.id === addressId
		);

		// Update visual selection
		document.querySelectorAll('.address-item').forEach((item) => {
			item.classList.remove('selected');
		});
		event.currentTarget.classList.add('selected');

		// Update radio button
		const radio = document.querySelector(`input[value="${addressId}"]`);
		if (radio) radio.checked = true;
	}

	async handleSubmit(event) {
		event.preventDefault();

		// Prevent double submission
		const submitBtn = event.target.querySelector('button[type="submit"]');
		let originalText = '';

		if (submitBtn) {
			if (submitBtn.disabled) {
				console.log(
					'Form already being submitted, ignoring duplicate request'
				);
				return;
			}
			submitBtn.disabled = true;
			originalText = submitBtn.innerHTML;
			submitBtn.innerHTML =
				'<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
		}

		if (!this.selectedAddress) {
			this.showError('Vui lòng chọn địa chỉ giao hàng');
			if (submitBtn) {
				submitBtn.disabled = false;
				submitBtn.innerHTML = originalText;
			}
			return;
		}

		const formData = new FormData(event.target);
		const paymentMethod = formData.get('payment_method');

		// First create the order
		const orderData = {
			customer: {
				name:
					this.selectedAddress.name ||
					this.selectedAddress.full_name ||
					'',
				phone: this.selectedAddress.phone || '',
			},
			shipping: {
				address: this.selectedAddress.address || '',
			},
			payment: {
				method: paymentMethod,
			},
			order_notes: formData.get('order_notes') || '',
			items: this.cart,
			totals: this.orderSummary,
		};

		try {
			// Create order first
			const response = await fetch('/5s-fashion/order/place', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify(orderData),
			});

			if (response.ok) {
				const result = await response.json();
				if (result.success) {
					const orderCode = result.order_code;

					// Process payment based on method
					if (paymentMethod === 'vnpay') {
						await this.processVNPayPayment(orderCode, formData);
					} else if (paymentMethod === 'cod') {
						await this.processCODPayment(orderCode);
					}
				} else {
					this.showError(result.message || 'Không thể tạo đơn hàng');
					// Re-enable submit button on error
					if (submitBtn) {
						submitBtn.disabled = false;
						submitBtn.innerHTML = originalText;
					}
				}
			} else {
				this.showError('Có lỗi xảy ra khi tạo đơn hàng');
				// Re-enable submit button on error
				if (submitBtn) {
					submitBtn.disabled = false;
					submitBtn.innerHTML = originalText;
				}
			}
		} catch (error) {
			console.error('Error creating order:', error);
			this.showError('Có lỗi xảy ra khi tạo đơn hàng');
			// Re-enable submit button on error
			if (submitBtn) {
				submitBtn.disabled = false;
				submitBtn.innerHTML = originalText;
			}
		}
	}

	async processVNPayPayment(orderCode, formData) {
		try {
			// Show processing overlay
			this.showPaymentProcessing();

			const bankCode = formData.get('bank_code') || '';

			const paymentData = {
				order_code: orderCode,
				bank_code: bankCode,
			};

			const response = await fetch('/5s-fashion/payment/vnpay', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify(paymentData),
			});

			if (response.ok) {
				const result = await response.json();
				if (result.success) {
					// Redirect to VNPay payment page
					window.location.href = result.payment_url;
				} else {
					this.hidePaymentProcessing();
					this.showError(
						result.message || 'Không thể tạo thanh toán VNPay'
					);
				}
			} else {
				this.hidePaymentProcessing();
				this.showError('Có lỗi xảy ra khi tạo thanh toán VNPay');
			}
		} catch (error) {
			this.hidePaymentProcessing();
			console.error('Error processing VNPay payment:', error);
			this.showError('Có lỗi xảy ra khi tạo thanh toán VNPay');
		}
	}

	async processCODPayment(orderCode) {
		try {
			console.log('[COD JS] Starting COD payment for order:', orderCode);

			// Show processing overlay
			this.showPaymentProcessing();

			const paymentData = {
				order_code: orderCode,
			};

			console.log('[COD JS] Sending payment data:', paymentData);

			const response = await fetch('/5s-fashion/payment/cod', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify(paymentData),
			});

			console.log('[COD JS] Response received:', {
				ok: response.ok,
				status: response.status,
				statusText: response.statusText,
				headers: Object.fromEntries(response.headers.entries()),
			});

			if (response.ok) {
				const responseText = await response.text();
				console.log('[COD JS] Raw response text:', responseText);

				try {
					const result = JSON.parse(responseText);
					console.log('[COD JS] Parsed result:', result);

					if (result.success) {
						console.log(
							'[COD JS] Success! Redirecting to:',
							result.redirect_url
						);
						// Redirect to success page
						window.location.href = result.redirect_url;
					} else {
						console.error(
							'[COD JS] Payment failed:',
							result.message
						);
						this.hidePaymentProcessing();
						this.showError(
							result.message || 'Không thể tạo đơn hàng COD'
						);
					}
				} catch (parseError) {
					console.error('[COD JS] JSON parse error:', parseError);
					console.error(
						'[COD JS] Response was not valid JSON:',
						responseText
					);
					this.hidePaymentProcessing();
					this.showError('Server trả về dữ liệu không hợp lệ');
				}
			} else {
				console.error(
					'[COD JS] HTTP error:',
					response.status,
					response.statusText
				);
				const errorText = await response.text();
				console.error('[COD JS] Error response:', errorText);
				this.hidePaymentProcessing();
				this.showError(
					'Có lỗi xảy ra khi tạo đơn hàng COD (HTTP ' +
						response.status +
						')'
				);
			}
		} catch (error) {
			this.hidePaymentProcessing();
			console.error('[COD JS] Network/fetch error:', error);
			this.showError('Có lỗi mạng xảy ra khi tạo đơn hàng COD');
		}
	}

	showPaymentProcessing() {
		const overlay = document.getElementById('paymentProcessing');
		if (overlay) {
			overlay.classList.add('show');
		}
	}

	hidePaymentProcessing() {
		const overlay = document.getElementById('paymentProcessing');
		if (overlay) {
			overlay.classList.remove('show');
		}
	}

	formatCurrency(amount) {
		return new Intl.NumberFormat('vi-VN', {
			style: 'currency',
			currency: 'VND',
		}).format(amount);
	}

	showError(message) {
		const errorModal = new bootstrap.Modal(
			document.getElementById('errorModal')
		);
		document.getElementById('errorMessage').textContent = message;
		errorModal.show();
	}
}

/**
 * Checkout Address Manager
 * Handles address management for checkout
 */
class CheckoutAddressManager {
	constructor() {
		this.provinces = [];
		this.districts = [];
		this.wards = [];
		this.map = null;
		this.marker = null;
		this.selectedLocation = null;
	}

	async showAddressModal(addressId = null) {
		const modal = new bootstrap.Modal(
			document.getElementById('addressModal')
		);

		if (addressId) {
			// Edit mode
			const address = checkoutManager.addresses.find(
				(addr) => addr.id === addressId
			);
			if (address) {
				this.populateAddressForm(address);
			}
		} else {
			// Add new mode
			document.getElementById('addressForm').reset();
		}

		// Load provinces if not loaded
		if (this.provinces.length === 0) {
			await this.loadProvinces();
		}

		modal.show();
	}

	async loadProvinces() {
		try {
			// Use Vietnam provinces API that supports CORS
			const response = await fetch(
				'https://vapi.vnappmob.com/api/province/',
				{
					method: 'GET',
					headers: {
						'Content-Type': 'application/json',
					},
				}
			);

			if (response.ok) {
				const data = await response.json();
				console.log('Provinces API response:', data);

				// Handle different API response format
				this.provinces = data.results || data || [];
				this.populateProvinceSelect();
			} else {
				throw new Error(`API returned ${response.status}`);
			}
		} catch (error) {
			console.error('Error loading provinces:', error);

			// Fallback: use static data for major provinces
			this.provinces = [
				{ province_id: '1', province_name: 'Thành phố Hà Nội' },
				{ province_id: '79', province_name: 'Thành phố Hồ Chí Minh' },
				{ province_id: '48', province_name: 'Thành phố Đà Nẵng' },
				{ province_id: '31', province_name: 'Thành phố Hải Phòng' },
				{ province_id: '92', province_name: 'Thành phố Cần Thơ' },
				{ province_id: '4', province_name: 'Tỉnh Cao Bằng' },
				{ province_id: '6', province_name: 'Tỉnh Bắc Kạn' },
				{ province_id: '8', province_name: 'Tỉnh Tuyên Quang' },
			];
			this.populateProvinceSelect();

			this.showAddressError(
				'Đang sử dụng danh sách tỉnh/thành phố cơ bản. Một số tỉnh có thể chưa đầy đủ.'
			);
		}
	}

	populateProvinceSelect() {
		const select = document.querySelector('select[name="province"]');
		if (!select) return;

		select.innerHTML = '<option value="">Chọn tỉnh/thành</option>';
		this.provinces.forEach((province) => {
			// Handle different API response formats
			const code = province.province_id || province.code || province.id;
			const name = province.province_name || province.name;
			select.innerHTML += `<option value="${code}" data-name="${name}">${name}</option>`;
		});

		select.addEventListener('change', (e) =>
			this.loadDistricts(e.target.value)
		);
	}

	async loadDistricts(provinceCode) {
		if (!provinceCode) return;

		try {
			// Use alternative API endpoint that supports CORS
			const response = await fetch(
				`https://vapi.vnappmob.com/api/province/district/${provinceCode}`,
				{
					method: 'GET',
					headers: {
						'Content-Type': 'application/json',
					},
				}
			);

			if (response.ok) {
				const data = await response.json();
				console.log('Districts API response:', data);

				// Handle different API response format
				this.districts = data.results || data.districts || [];
				this.populateDistrictSelect();
			} else {
				throw new Error(`API returned ${response.status}`);
			}
		} catch (error) {
			console.error('Error loading districts:', error);

			// Fallback: provide basic districts for major cities
			if (provinceCode === '79') {
				// Ho Chi Minh City
				this.districts = [
					{ district_id: '760', district_name: 'Quận 1' },
					{ district_id: '769', district_name: 'Quận 3' },
					{ district_id: '778', district_name: 'Quận 10' },
					{ district_id: '783', district_name: 'Quận Tân Bình' },
					{ district_id: '794', district_name: 'Quận Bình Thạnh' },
				];
			} else if (provinceCode === '1') {
				// Hanoi
				this.districts = [
					{ district_id: '1', district_name: 'Quận Ba Đình' },
					{ district_id: '5', district_name: 'Quận Hoàn Kiếm' },
					{ district_id: '6', district_name: 'Quận Tây Hồ' },
					{ district_id: '7', district_name: 'Quận Long Biên' },
					{ district_id: '8', district_name: 'Quận Cầu Giấy' },
				];
			} else {
				this.districts = [
					{
						district_id: 'default',
						district_name: 'Quận/Huyện trung tâm',
					},
				];
			}

			this.populateDistrictSelect();
			this.showAddressError('Đang sử dụng danh sách quận/huyện cơ bản.');
		}
	}

	populateDistrictSelect() {
		const select = document.querySelector('select[name="district"]');
		if (!select) return;

		select.innerHTML = '<option value="">Chọn quận/huyện</option>';
		this.districts.forEach((district) => {
			// Handle different API response formats
			const code = district.district_id || district.code || district.id;
			const name = district.district_name || district.name;
			select.innerHTML += `<option value="${code}" data-name="${name}">${name}</option>`;
		});

		select.addEventListener('change', (e) =>
			this.loadWards(e.target.value)
		);
	}

	async loadWards(districtCode) {
		if (!districtCode) return;

		try {
			// Use Vietnam wards API that supports CORS
			const response = await fetch(
				`https://vapi.vnappmob.com/api/province/ward/${districtCode}`,
				{
					method: 'GET',
					headers: {
						'Content-Type': 'application/json',
					},
				}
			);

			if (response.ok) {
				const data = await response.json();
				console.log('Wards API response:', data);

				// Handle different API response format
				this.wards = data.results || data.wards || [];
				this.populateWardSelect();
			} else {
				throw new Error(`API returned ${response.status}`);
			}
		} catch (error) {
			console.error('Error loading wards:', error);

			// Fallback: provide basic wards
			this.wards = [
				{ ward_id: 'default-1', ward_name: 'Phường 1' },
				{ ward_id: 'default-2', ward_name: 'Phường 2' },
				{ ward_id: 'default-3', ward_name: 'Phường 3' },
				{ ward_id: 'default-other', ward_name: 'Phường/Xã khác' },
			];

			this.populateWardSelect();
			this.showAddressError('Đang sử dụng danh sách phường/xã cơ bản.');
		}
	}

	populateWardSelect() {
		const select = document.querySelector('select[name="ward"]');
		if (!select) return;

		select.innerHTML = '<option value="">Chọn phường/xã</option>';
		this.wards.forEach((ward) => {
			// Handle different API response formats
			const code = ward.ward_id || ward.code || ward.id;
			const name = ward.ward_name || ward.name;
			select.innerHTML += `<option value="${code}" data-name="${name}">${name}</option>`;
		});
	}

	showAddressError(message) {
		console.error('Address API Error:', message);

		// Show notification if available
		if (typeof showToast === 'function') {
			showToast(message, 'error');
		} else if (typeof alert === 'function') {
			alert(message);
		}

		// Or create a simple error display in the modal
		const errorDiv = document.createElement('div');
		errorDiv.className = 'alert alert-danger mt-2';
		errorDiv.textContent = message;

		const modal = document.querySelector('#addressModal .modal-body');
		if (modal) {
			const existingError = modal.querySelector('.alert-danger');
			if (existingError) {
				existingError.remove();
			}
			modal.appendChild(errorDiv);

			// Auto remove after 5 seconds
			setTimeout(() => {
				if (errorDiv.parentNode) {
					errorDiv.remove();
				}
			}, 5000);
		}
	}

	async saveAddress() {
		const form = document.getElementById('addressForm');
		const formData = new FormData(form);

		// Get selected names (not just codes)
		const provinceSelect = form.querySelector('select[name="province"]');
		const districtSelect = form.querySelector('select[name="district"]');
		const wardSelect = form.querySelector('select[name="ward"]');

		const addressData = {
			// Backend expects 'name' and 'address', not 'full_name' and 'address_line'
			name: formData.get('full_name'),
			phone: formData.get('phone'),
			address: `${formData.get('address_line')}, ${
				wardSelect.options[wardSelect.selectedIndex]?.dataset.name || ''
			}, ${
				districtSelect.options[districtSelect.selectedIndex]?.dataset
					.name || ''
			}, ${
				provinceSelect.options[provinceSelect.selectedIndex]?.dataset
					.name || ''
			}`,
			note: formData.get('note') || '', // Optional note field
			is_default: formData.get('is_default') ? 1 : 0,
		};

		console.log('Sending address data:', addressData);

		try {
			const response = await fetch('/5s-fashion/order/addAddress', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify(addressData),
			});

			if (response.ok) {
				const result = await response.json();
				if (result.success) {
					// Close modal and reload addresses
					bootstrap.Modal.getInstance(
						document.getElementById('addressModal')
					).hide();
					await checkoutManager.loadAddresses();
					checkoutManager.renderCheckoutForm();
				} else {
					alert(result.message || 'Không thể lưu địa chỉ');
				}
			} else {
				alert('Có lỗi xảy ra khi lưu địa chỉ');
			}
		} catch (error) {
			console.error('Error saving address:', error);
			alert('Có lỗi xảy ra khi lưu địa chỉ');
		}
	}

	async editAddress(addressId) {
		// Implementation for editing existing address
		await this.showAddressModal(addressId);
	}

	async deleteAddress(addressId) {
		if (!confirm('Bạn có chắc chắn muốn xóa địa chỉ này?')) return;

		try {
			const response = await fetch(
				`/5s-fashion/order/deleteAddress/${addressId}`,
				{
					method: 'DELETE',
				}
			);

			if (response.ok) {
				const result = await response.json();
				if (result.success) {
					alert(result.message);
					await checkoutManager.loadAddresses();
					checkoutManager.renderCheckoutForm();
				} else {
					alert(result.message || 'Không thể xóa địa chỉ');
				}
			} else {
				alert('Có lỗi xảy ra khi xóa địa chỉ');
			}
		} catch (error) {
			alert('Có lỗi xảy ra khi xóa địa chỉ');
		}
	}

	async setDefaultAddress(id) {
		try {
			const response = await fetch(
				`/5s-fashion/order/setDefaultAddress/${id}`,
				{
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
					},
					body: JSON.stringify({ is_default: true }),
				}
			);

			if (response.ok) {
				const result = await response.json();
				if (result.success) {
					alert(result.message);
					window.location.reload();
				} else {
					alert(result.message || 'Không thể đặt địa chỉ mặc định');
				}
			} else {
				alert('Có lỗi xảy ra khi đặt địa chỉ mặc định');
			}
		} catch (error) {
			alert('Có lỗi xảy ra khi đặt địa chỉ mặc định');
		}
	}

	populateAddressForm(address) {
		const form = document.getElementById('addressForm');
		if (!form) return;

		form.querySelector('[name="full_name"]').value =
			address.full_name || '';
		form.querySelector('[name="phone"]').value = address.phone || '';
		form.querySelector('[name="address_line"]').value =
			address.address_line || '';

		// Set address type
		const addressType = form.querySelector(
			`[name="address_type"][value="${address.address_type}"]`
		);
		if (addressType) addressType.checked = true;

		// Set default checkbox
		const isDefault = form.querySelector('[name="is_default"]');
		if (isDefault) isDefault.checked = address.is_default == 1;
	}
}

// Global instances
let checkoutManager;
let addressManager;

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function () {
	console.log('DOM Content Loaded - Initializing checkout');

	checkoutManager = new CheckoutManager();
	addressManager = new CheckoutAddressManager();

	console.log('About to load order...');
	checkoutManager.loadOrder();

	console.log('About to initialize form...');
	checkoutManager.initializeForm();

	// Initialize payment method handlers
	initializePaymentHandlers();
});

// Payment method handlers
function initializePaymentHandlers() {
	const vnpayRadio = document.getElementById('vnpay');
	const codRadio = document.getElementById('cod');
	const vnpayBanks = document.getElementById('vnpay-banks');

	if (vnpayRadio && codRadio && vnpayBanks) {
		// Show/hide VNPay bank options
		vnpayRadio.addEventListener('change', function () {
			if (this.checked) {
				vnpayBanks.style.display = 'block';
				// Auto-select first bank option
				const firstBank = vnpayBanks.querySelector(
					'input[type="radio"]'
				);
				if (firstBank) firstBank.checked = true;
			}
		});

		codRadio.addEventListener('change', function () {
			if (this.checked) {
				vnpayBanks.style.display = 'none';
				// Uncheck all bank options
				vnpayBanks
					.querySelectorAll('input[type="radio"]')
					.forEach((radio) => {
						radio.checked = false;
					});
			}
		});
	}
}

console.log('Checkout.js fully loaded and ready');
