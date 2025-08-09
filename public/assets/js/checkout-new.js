/**
 * Checkout Manager - Phase 3.3
 * Handles checkout process including form validation, order processing, and payment
 */

class CheckoutManager {
	constructor() {
		this.cart = null;
		this.addresses = [];
		this.selectedAddress = null;
		this.orderSummary = {
			subtotal: 0,
			shipping: 30000,
			discount: 0,
			total: 0,
		};
	}

	async loadOrder() {
		try {
			const response = await fetch('/5s-fashion/ajax/cart/items');
			if (response.ok) {
				const data = await response.json();
				if (
					data.success &&
					data.cart_items &&
					data.cart_items.length > 0
				) {
					this.cart = data.cart_items;
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
		if (!orderItemsContainer || !this.cart) return;

		const template = document.getElementById('orderItemTemplate');
		if (!template) return;

		let html = '';
		this.cart.forEach((item) => {
			let templateContent = template.innerHTML;

			// Process image URL
			let imageUrl = '/5s-fashion/public/assets/images/placeholder.jpg';
			if (item.image) {
				const encodedImage = encodeURIComponent(item.image);
				imageUrl = `/5s-fashion/serve-file.php?file=products%2F${encodedImage}`;
			}

			// Build variant info
			let variantInfo = '';
			if (item.color || item.size) {
				const parts = [];
				if (item.color) parts.push(`Màu: ${item.color}`);
				if (item.size) parts.push(`Size: ${item.size}`);
				variantInfo = parts.join(' | ');
			}

			templateContent = templateContent
				.replace('{image}', imageUrl)
				.replace('{name}', item.product_name || item.name || 'Sản phẩm')
				.replace('{variant_info}', variantInfo)
				.replace('{quantity}', item.quantity || 1)
				.replace(
					'{price}',
					this.formatCurrency(
						(item.price || 0) * (item.quantity || 1)
					)
				);

			html += templateContent;
		});

		orderItemsContainer.innerHTML = html;
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

		summaryContainer.innerHTML = `
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

		// Re-populate items after updating summary
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
			if (response.ok) {
				const data = await response.json();
				if (data.success) {
					this.addresses = data.addresses || [];
					// Find default address
					const defaultAddr = this.addresses.find(
						(addr) => addr.is_default
					);
					if (defaultAddr) {
						this.selectedAddress = defaultAddr;
					}
				}
			}
		} catch (error) {
			console.error('Error loading addresses:', error);
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
                            <input class="form-check-input" type="radio" name="payment_method" id="bank_transfer" value="bank_transfer">
                            <label class="form-check-label d-flex align-items-center" for="bank_transfer">
                                <i class="fas fa-university text-primary me-2"></i>
                                Chuyển khoản ngân hàng
                            </label>
                            <div class="text-muted small mt-1">Chuyển khoản trước khi giao hàng</div>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="momo" value="momo">
                            <label class="form-check-label d-flex align-items-center" for="momo">
                                <i class="fab fa-cc-visa text-info me-2"></i>
                                Ví điện tử MoMo
                            </label>
                            <div class="text-muted small mt-1">Thanh toán qua ví MoMo</div>
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
                                    <div class="fw-bold">${addr.full_name}</div>
                                    <div class="text-muted">${addr.phone}</div>
                                    <div class="address-text">${
										addr.address_line
									}, ${addr.ward}, ${addr.district}, ${
				addr.province
			}</div>
                                    ${
										addr.is_default
											? '<span class="badge bg-primary">Mặc định</span>'
											: ''
									}
                                </div>
                                <div class="address-actions">
                                    <button type="button" class="btn btn-sm btn-outline-primary me-1"
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

		if (!this.selectedAddress) {
			this.showError('Vui lòng chọn địa chỉ giao hàng');
			return;
		}

		const formData = new FormData(event.target);
		const orderData = {
			address_id: this.selectedAddress.id,
			payment_method: formData.get('payment_method'),
			order_notes: formData.get('order_notes') || '',
			items: this.cart,
			totals: this.orderSummary,
		};

		try {
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
					// Redirect to success page
					window.location.href =
						'/5s-fashion/order/success/' + result.order_id;
				} else {
					this.showError(result.message || 'Không thể tạo đơn hàng');
				}
			} else {
				this.showError('Có lỗi xảy ra khi tạo đơn hàng');
			}
		} catch (error) {
			console.error('Error creating order:', error);
			this.showError('Có lỗi xảy ra khi tạo đơn hàng');
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
			const response = await fetch(
				'https://provinces.open-api.vn/api/p/'
			);
			if (response.ok) {
				const data = await response.json();
				this.provinces = data;
				this.populateProvinceSelect();
			}
		} catch (error) {
			console.error('Error loading provinces:', error);
		}
	}

	populateProvinceSelect() {
		const select = document.querySelector('select[name="province"]');
		if (!select) return;

		select.innerHTML = '<option value="">Chọn tỉnh/thành</option>';
		this.provinces.forEach((province) => {
			select.innerHTML += `<option value="${province.code}" data-name="${province.name}">${province.name}</option>`;
		});

		select.addEventListener('change', (e) =>
			this.loadDistricts(e.target.value)
		);
	}

	async loadDistricts(provinceCode) {
		if (!provinceCode) return;

		try {
			const response = await fetch(
				`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`
			);
			if (response.ok) {
				const data = await response.json();
				this.districts = data.districts || [];
				this.populateDistrictSelect();
			}
		} catch (error) {
			console.error('Error loading districts:', error);
		}
	}

	populateDistrictSelect() {
		const select = document.querySelector('select[name="district"]');
		if (!select) return;

		select.innerHTML = '<option value="">Chọn quận/huyện</option>';
		this.districts.forEach((district) => {
			select.innerHTML += `<option value="${district.code}" data-name="${district.name}">${district.name}</option>`;
		});

		select.addEventListener('change', (e) =>
			this.loadWards(e.target.value)
		);
	}

	async loadWards(districtCode) {
		if (!districtCode) return;

		try {
			const response = await fetch(
				`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`
			);
			if (response.ok) {
				const data = await response.json();
				this.wards = data.wards || [];
				this.populateWardSelect();
			}
		} catch (error) {
			console.error('Error loading wards:', error);
		}
	}

	populateWardSelect() {
		const select = document.querySelector('select[name="ward"]');
		if (!select) return;

		select.innerHTML = '<option value="">Chọn phường/xã</option>';
		this.wards.forEach((ward) => {
			select.innerHTML += `<option value="${ward.code}" data-name="${ward.name}">${ward.name}</option>`;
		});
	}

	async saveAddress() {
		const form = document.getElementById('addressForm');
		const formData = new FormData(form);

		// Get selected names (not just codes)
		const provinceSelect = form.querySelector('select[name="province"]');
		const districtSelect = form.querySelector('select[name="district"]');
		const wardSelect = form.querySelector('select[name="ward"]');

		const addressData = {
			full_name: formData.get('full_name'),
			phone: formData.get('phone'),
			province:
				provinceSelect.options[provinceSelect.selectedIndex]?.dataset
					.name || '',
			district:
				districtSelect.options[districtSelect.selectedIndex]?.dataset
					.name || '',
			ward:
				wardSelect.options[wardSelect.selectedIndex]?.dataset.name ||
				'',
			address_line: formData.get('address_line'),
			address_type: formData.get('address_type'),
			is_default: formData.get('is_default') ? 1 : 0,
		};

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
