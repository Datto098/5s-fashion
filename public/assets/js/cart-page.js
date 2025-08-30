/**
 * Cart Page JavaScript - Following UI Guidelines
 * Handles cart functionality, quantity updates, and checkout
 */

// Cart Page Manager
class CartPageManager {
	constructor() {
		this.cartItems = [];
		this.subtotal = 0;
		this.shippingFee = 30000;
		this.discount = 0;
		this.total = 0;
		this.init();
	}

	init() {
		console.log('Cart page loaded, initializing...');
		this.bindEvents();
		this.loadCartData();
	}

	bindEvents() {
		// Initialize checkout button with retry mechanism
		this.initCheckoutButton();

		// Initialize remove buttons
		this.initRemoveButtons();

		// Initialize quantity controls
		this.initQuantityControls();

		// Initialize promo code
		this.initPromoCode();
	}

	initCheckoutButton() {
		const initButton = () => {
			const checkoutBtn = document.getElementById('checkout-btn');
			if (checkoutBtn) {
				checkoutBtn.removeEventListener(
					'click',
					this.proceedToCheckout.bind(this)
				);
				checkoutBtn.addEventListener(
					'click',
					this.proceedToCheckout.bind(this)
				);
				console.log('Checkout button listener attached');
			} else {
				// Retry if button not found yet
				setTimeout(initButton, 100);
			}
		};
		initButton();
	}

	initRemoveButtons() {
		const removeButtons = document.querySelectorAll('.remove-cart-item');
		removeButtons.forEach((button) => {
			button.addEventListener('click', (e) => {
				const cartId = e.currentTarget.getAttribute('data-cart-id');
				if (cartId) {
					this.removeCartItem(cartId);
				}
			});
		});
		console.log('Remove buttons listeners attached:', removeButtons.length);
	}

	initQuantityControls() {
		// Quantity buttons
		document.querySelectorAll('.quantity-btn').forEach((button) => {
			button.addEventListener('click', (e) => {
				const action = e.currentTarget.onclick
					? e.currentTarget.onclick.toString().includes('increase')
						? 'increase'
						: 'decrease'
					: 'change';
				this.updateCartQuantity(e.currentTarget, action);
			});
		});

		// Quantity inputs
		document.querySelectorAll('.cart-quantity-input').forEach((input) => {
			input.addEventListener('change', (e) => {
				this.updateCartQuantity(e.currentTarget, 'change');
			});
		});
	}

	initPromoCode() {
		const promoBtn = document.querySelector('.promo-btn');
		const promoInput = document.getElementById('promo-code');

		if (promoBtn) {
			// Xóa sự kiện cũ trước khi thêm mới
			promoBtn.removeEventListener('click', this.applyPromoCode.bind(this));
			promoBtn.removeEventListener('click', removeVoucher);
			promoBtn.addEventListener('click', this.applyPromoCode.bind(this));
		}

		if (promoInput) {
			promoInput.addEventListener('keypress', (e) => {
				if (e.key === 'Enter') {
					this.applyPromoCode();
				}
			});
		}
	}

	loadCartData() {
		// Load initial cart data from DOM
		const cartItems = document.querySelectorAll('.cart-item');
		this.cartItems = Array.from(cartItems).map((item) => {
			const quantityInput = item.querySelector('.cart-quantity-input');
			const priceElement = item.querySelector('.product-price');
			const totalElement = item.querySelector('.item-total');

			return {
				id: quantityInput ? quantityInput.dataset.cartId : null,
				quantity: quantityInput ? parseInt(quantityInput.value) : 1,
				price: this.parsePrice(
					priceElement ? priceElement.textContent : '0'
				),
				total: this.parsePrice(
					totalElement ? totalElement.textContent : '0'
				),
			};
		});

		this.updateSummary();
	}

	parsePrice(priceString) {
		// Parse Vietnamese price format: "123.456 ₫" -> 123456
		return parseInt(priceString.replace(/[^\d]/g, '')) || 0;
	}

	formatPrice(price) {
		return new Intl.NumberFormat('vi-VN').format(price) + ' ₫';
	}

	updateCartQuantity(element, action) {
		const cartItem = element.closest('.cart-item');
		const quantityInput = cartItem.querySelector('.cart-quantity-input');
		const cartId = quantityInput.dataset.cartId;
		let quantity = parseInt(quantityInput.value);

		// Calculate new quantity
		if (action === 'increase') {
			quantity++;
		} else if (action === 'decrease') {
			quantity = Math.max(1, quantity - 1);
		} else if (action === 'change') {
			quantity = Math.max(1, parseInt(element.value) || 1);
		}

		// Respect max stock from DOM (data-max-stock) if present
		const maxStockAttr = quantityInput.getAttribute('data-max-stock');
		const maxStock = maxStockAttr ? parseInt(maxStockAttr) : null;

		if (maxStock !== null && !isNaN(maxStock) && maxStock > 0) {
			if (quantity > maxStock) quantity = maxStock;
		}

		// Ensure minimum
		if (!quantity || isNaN(quantity) || quantity < 1) quantity = 1;

		// Update input value
		quantityInput.value = quantity;

		// Toggle plus/minus disabled states
		const plusBtn = cartItem.querySelector('.quantity-increase');
		const minusBtn = cartItem.querySelector('.quantity-decrease');
		if (minusBtn) minusBtn.disabled = quantity <= 1;
		if (plusBtn) plusBtn.disabled = maxStock !== null && maxStock > 0 ? quantity >= maxStock : false;


		// Send update to server
		this.updateQuantityOnServer(cartId, quantity);
	}

	updateQuantityOnServer(cartKey, newQuantity) {
		if (newQuantity < 1) return;

		// Show loading state
		this.showLoadingState(true);

		fetch('/zone-fashion/ajax/cart/update', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
			},
			body: JSON.stringify({
				cart_key: cartKey,
				quantity: newQuantity,
			}),
		})
			.then((response) => response.json())
			.then((data) => {
				if (data.success) {
					// If server reports clamping (quantity adjusted to stock), show a warning and reload
					if (data.clamped) {
						this.showNotification(
							data.message || 'Số lượng đã được điều chỉnh theo tồn kho',
							'warning'
						);
						// Reload so UI matches server state
						window.location.reload();
						return;
					}

					// Normal successful update
					window.location.reload();
				} else {
					this.showNotification(
						data.message || 'Có lỗi xảy ra',
						'error'
					);
				}
			})
			.catch((error) => {
				console.error('Error updating quantity:', error);
				this.showNotification(
					'Có lỗi xảy ra khi cập nhật số lượng',
					'error'
				);
			})
			.finally(() => {
				this.showLoadingState(false);
			});
	}

	removeCartItem(cartKey) {
		if (!confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?'))
			return;

		// Show loading state
		this.showLoadingState(true);

		fetch('/zone-fashion/ajax/cart/remove', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
			},
			body: JSON.stringify({
				cart_key: cartKey,
			}),
		})
			.then((response) => response.json())
			.then((data) => {
				if (data.success) {
					// Reload the page to reflect changes
					window.location.reload();
				} else {
					this.showNotification(
						data.message || 'Có lỗi xảy ra',
						'error'
					);
				}
			})
			.catch((error) => {
				console.error('Error removing item:', error);
				this.showNotification(
					'Có lỗi xảy ra khi xóa sản phẩm',
					'error'
				);
			})
			.finally(() => {
				this.showLoadingState(false);
			});
	}

	proceedToCheckout() {
		// Check if cart has items
		const cartItems = document.querySelectorAll('.cart-item');
		if (cartItems.length === 0) {
			this.showNotification(
				'Giỏ hàng trống. Vui lòng thêm sản phẩm trước khi thanh toán.',
				'warning'
			);
			return;
		}

		// Show loading state
		this.showLoadingState(true);

		// Add a small delay for better UX
		setTimeout(() => {
			// Redirect to checkout page
			window.location.href = '/zone-fashion/checkout';
		}, 500);
	}

	applyPromoCode() {
		const promoInput = document.getElementById('promo-code');
		const promoCode = promoInput ? promoInput.value.trim() : '';

		if (!promoCode) {
			this.showNotification('Vui lòng nhập mã giảm giá', 'info');
			document.getElementById('voucher-message').innerHTML = '<span class="text-info"><i class="fas fa-info-circle me-1"></i> Vui lòng nhập mã giảm giá để được giảm giá</span>';
			return;
		}

		// Show loading state for promo button
		const promoBtn = document.querySelector('.promo-btn');
		// Lưu nội dung gốc vào thuộc tính data để luôn khôi phục đúng
		if (promoBtn) {
			if (!promoBtn.dataset.originalText) {
				promoBtn.dataset.originalText = promoBtn.innerHTML;
			}
			promoBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
			promoBtn.disabled = true;
		}
		
		// Show loading message
		document.getElementById('voucher-message').innerHTML = '<span class="text-info"><i class="fas fa-spinner fa-spin me-1"></i> Đang kiểm tra...</span>';
		
		// Get the cart subtotal
		const subtotalElement = document.getElementById('subtotal');
		const subtotalText = subtotalElement.textContent;
		const subtotal = parseFloat(subtotalText.replace(/[^\d]/g, ''));
		
		// Get base URL
		const baseUrl = window.location.pathname.includes('/public') ? 
			window.location.origin + window.location.pathname.split('/public')[0] + '/public' : 
			window.location.origin + '/zone-fashion';
		
		// Call API to apply voucher (POST)
		fetch('/zone-fashion/vouchers/apply', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded',
				'Accept': 'application/json'
			},
			body: `code=${encodeURIComponent(promoCode)}&amount=${subtotal}`
		})
		.then(response => {
			if (!response.ok) {
				throw new Error('Network response was not ok');
			}
			return response.json();
		})
		.then(data => {
			if (data.success) {
				// Update UI with discount
				document.getElementById('discount').textContent = data.formatted_discount;
				// Tính lại tổng cộng: final_amount + phí ship 30,000
				const shippingFee = subtotal >= 500000 ? 0 : 30000; // Dynamic shipping fee
				const totalWithShipping = (parseInt(data.final_amount) + shippingFee);
				document.getElementById('total').textContent = this.formatPrice(totalWithShipping);
				// Show success message
				document.getElementById('voucher-message').innerHTML = '<span class="text-success"><i class="fas fa-check-circle me-1"></i> ' + 
					'Áp dụng mã giảm giá thành công: ' + data.formatted_discount + '</span>';
				this.showNotification('Áp dụng mã giảm giá thành công', 'success');
				// Đổi nút thành Xóa mã khi nhập mã thành công
				promoInput.disabled = true;
				promoBtn.innerHTML = '<i class="fas fa-times"></i> Xóa mã';
				promoBtn.classList.remove('btn-primary');
				promoBtn.classList.add('btn-outline-secondary');
				// Xóa sự kiện cũ và gán mới
				promoBtn.removeEventListener('click', applyPromoCode);
				promoBtn.removeEventListener('click', removeVoucher);
				promoBtn.addEventListener('click', removeVoucher);
				promoBtn.disabled = false;
			} else {
				// Show error message
				document.getElementById('voucher-message').innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-circle me-1"></i> ' +
					(data.message || 'Mã giảm giá không hợp lệ') + '</span>';
				this.showNotification(data.message || 'Mã giảm giá không hợp lệ', 'warning');
				// Restore button
				if (promoBtn) {
					promoBtn.innerHTML = promoBtn.dataset.originalText || 'Áp dụng';
					promoBtn.disabled = false;
				}
			}
		})
		.catch(error => {
			console.error('Error applying voucher:', error);
			document.getElementById('voucher-message').innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-circle me-1"></i> Có lỗi xảy ra khi áp dụng mã giảm giá. Vui lòng thử lại sau.</span>';
			this.showNotification('Có lỗi xảy ra khi áp dụng mã giảm giá. Vui lòng thử lại sau.', 'error');
			// Restore button
			if (promoBtn) {
				promoBtn.innerHTML = promoBtn.dataset.originalText || 'Áp dụng';
				promoBtn.disabled = false;
			}
		});
	}

	updateSummary() {
		// Calculate totals
		this.subtotal = this.cartItems.reduce(
			(sum, item) => sum + item.total,
			0
		);
	this.shippingFee = this.subtotal >= 500000 ? 0 : 30000; // Dynamic shipping fee
		this.total = this.subtotal + this.shippingFee - this.discount;

		// Update DOM elements
		const subtotalElement = document.getElementById('subtotal');
		const discountElement = document.getElementById('discount');
		const totalElement = document.getElementById('total');
		const shippingElement = document.querySelector('.summary-value.text-success');

		if (subtotalElement)
			subtotalElement.textContent = this.formatPrice(this.subtotal);
		if (discountElement)
			discountElement.textContent = this.formatPrice(this.discount);
		if (shippingElement)
			shippingElement.textContent = this.formatPrice(this.shippingFee);
		if (totalElement)
			totalElement.textContent = this.formatPrice(this.total);
	}

	showLoadingState(show) {
		const checkoutBtn = document.getElementById('checkout-btn');
		const removeButtons = document.querySelectorAll('.remove-cart-item');
		const quantityButtons = document.querySelectorAll('.quantity-btn');

		if (show) {
			if (checkoutBtn) {
				checkoutBtn.disabled = true;
				checkoutBtn.innerHTML =
					'<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...';
			}
			removeButtons.forEach((btn) => (btn.disabled = true));
			quantityButtons.forEach((btn) => (btn.disabled = true));
		} else {
			if (checkoutBtn) {
				checkoutBtn.disabled = false;
				checkoutBtn.innerHTML =
					'<i class="fas fa-credit-card me-2"></i>Thanh toán';
			}
			removeButtons.forEach((btn) => (btn.disabled = false));
			quantityButtons.forEach((btn) => (btn.disabled = false));
		}
	}

	showNotification(message, type = 'info') {
		// Create toast notification following UI guidelines
		const toast = document.createElement('div');
		toast.className = `toast-notification toast-${type}`;

		// Toast content
		const content = document.createElement('div');
		content.className = 'toast-content';

		const icon = document.createElement('i');
		icon.className = this.getNotificationIcon(type);

		const text = document.createElement('span');
		text.textContent = message;

		content.appendChild(icon);
		content.appendChild(text);
		toast.appendChild(content);

		// Add to toast container or body
		const container =
			document.querySelector('.toast-container') || document.body;
		container.appendChild(toast);

		// Show toast with animation
		setTimeout(() => toast.classList.add('show'), 100);

		// Hide toast after 4 seconds
		setTimeout(() => {
			toast.classList.remove('show');
			setTimeout(() => toast.remove(), 300);
		}, 4000);
	}

	getNotificationIcon(type) {
		const icons = {
			success: 'fas fa-check-circle',
			error: 'fas fa-exclamation-circle',
			warning: 'fas fa-exclamation-triangle',
			info: 'fas fa-info-circle',
		};
		return icons[type] || icons.info;
	}
}

// Global functions for backward compatibility
function updateCartQuantity(element, action) {
	if (window.cartPageManager) {
		window.cartPageManager.updateCartQuantity(element, action);
	}
}

function removeCartItem(cartKey) {
	if (window.cartPageManager) {
		window.cartPageManager.removeCartItem(cartKey);
	}
}


// Hàm toàn cục để xóa mã giảm giá
function removeVoucher(e) {
	// Ngăn chặn sự kiện mặc định và lan truyền
	if (e) {
		e.preventDefault();
		e.stopPropagation();
	}
	
	// Hiển thị thông báo thân thiện
	const voucherMsg = document.getElementById('voucher-message');
	if (voucherMsg) {
		voucherMsg.innerHTML = '<span class="text-info"><i class="fas fa-info-circle me-1"></i> Đã xóa mã giảm giá, bạn có thể nhập mã khác</span>';
	}
	
	// Reset UI
	const promoInput = document.getElementById('promo-code');
	const promoBtn = document.querySelector('.promo-btn');
	
	if (promoInput) {
		promoInput.value = '';
		promoInput.disabled = false;
		promoInput.focus(); // Focus vào ô nhập để người dùng có thể nhập mã mới ngay
	}
	
	if (promoBtn) {
		promoBtn.innerHTML = '<i class="fas fa-check"></i> Áp dụng';
		promoBtn.classList.remove('btn-outline-secondary');
		promoBtn.classList.add('btn-primary');
		promoBtn.disabled = false;
		// Xóa sự kiện onclick cũ và gán lại
		promoBtn.removeEventListener('click', applyPromoCode);
		promoBtn.removeEventListener('click', removeVoucher);
		promoBtn.addEventListener('click', applyPromoCode);
	}
	
	// Reset giá trị giảm giá
	if (window.cartPageManager) {
		window.cartPageManager.discount = 0;
		window.cartPageManager.updateSummary();
		window.cartPageManager.showNotification('Đã xóa mã giảm giá, bạn có thể nhập mã khác', 'info');
	}
}
function proceedToCheckout() {
	if (window.cartPageManager) {
		window.cartPageManager.proceedToCheckout();
	}
}

function applyPromoCode() {
	if (window.cartPageManager) {
		window.cartPageManager.applyPromoCode();
	}
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function () {
	window.cartPageManager = new CartPageManager();
	// Xóa mọi thông báo voucher cũ khi load lại trang
	var voucherMsg = document.getElementById('voucher-message');
	if (voucherMsg) voucherMsg.innerHTML = '';
	// Reset lại giao diện nút Áp dụng về trạng thái ban đầu
	var promoBtn = document.querySelector('.promo-btn');
	var promoInput = document.getElementById('promo-code');
	if (promoBtn) {
		promoBtn.innerHTML = '<i class="fas fa-check"></i> Áp dụng';
		promoBtn.classList.remove('btn-outline-secondary');
		promoBtn.classList.add('btn-primary');
		promoBtn.disabled = false;
		// Xóa sự kiện cũ và gán mới
		promoBtn.removeEventListener('click', applyPromoCode);
		promoBtn.removeEventListener('click', removeVoucher);
		promoBtn.addEventListener('click', applyPromoCode);
	}
	// Đảm bảo input không bị disabled
	if (promoInput) {
		promoInput.disabled = false;
		promoInput.value = '';
	}

	// Clamp quantity inputs and set plus/minus button states on load
	document.querySelectorAll('.cart-quantity-input').forEach((input) => {
		const maxStock = parseInt(input.getAttribute('data-max-stock')) || null;
		let val = parseInt(input.value) || 1;
		if (maxStock && val > maxStock) {
			input.value = maxStock;
			val = maxStock;
		}
		const cartItem = input.closest('.cart-item');
		if (cartItem) {
			const plus = cartItem.querySelector('.quantity-increase');
			const minus = cartItem.querySelector('.quantity-decrease');
			if (minus) minus.disabled = val <= 1;
			if (plus) plus.disabled = maxStock ? val >= maxStock : false;
		}
	});
});