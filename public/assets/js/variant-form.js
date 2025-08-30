document.addEventListener('DOMContentLoaded', function () {
	// Lấy reference đến modal và form tạo biến thể đơn
	const addVariantModal = document.getElementById('addVariantModal');
	if (!addVariantModal) return;

	const addVariantForm = addVariantModal.querySelector('form');
	if (!addVariantForm) return;

	// Tạo SKU mới mỗi khi modal mở
	addVariantModal.addEventListener('show.bs.modal', function () {
		// Tạo một SKU ngẫu nhiên với prefix sản phẩm nếu có
		const skuInput = addVariantForm.querySelector('input[name="sku"]');
		if (skuInput) {
			// Lấy SKU gốc từ data attribute nếu có
			const productSku = skuInput.getAttribute('data-product-sku') || '';
			// Thêm timestamp để đảm bảo duy nhất
			const timestamp = new Date().getTime().toString().slice(-6);
			const randomCode = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
			skuInput.value = productSku + '-' + timestamp + randomCode;
		}
	});

	// Reset form khi modal đóng
	addVariantModal.addEventListener('hidden.bs.modal', function () {
		addVariantForm.reset();
		// Bỏ chọn tất cả radio buttons và dropdowns
		addVariantForm
			.querySelectorAll('input[type="radio"], select')
			.forEach((element) => {
				if (element.type === 'radio') {
					element.checked = false;
				} else {
					element.selectedIndex = 0;
				}
			});
	});

	// Xử lý form submission bằng AJAX
	addVariantForm.addEventListener('submit', function (e) {
		e.preventDefault();

		// Hiển thị loading state
		const submitBtn = document.getElementById('createVariantBtn');
		const originalText = submitBtn.innerHTML;
		submitBtn.disabled = true;
		submitBtn.innerHTML =
			'<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang xử lý...';

		// Gửi form data
		fetch(addVariantForm.action, {
			method: 'POST',
			body: new FormData(addVariantForm),
			headers: {
				'X-Requested-With': 'XMLHttpRequest'
			}
		})
			.then((response) => response.json())
			.then((data) => {
				// Thông báo thành công
				if (data.success) {
					// Hiển thị thông báo nhỏ
					const toast = document.createElement('div');
					toast.className = 'position-fixed top-0 end-0 p-3';
					toast.style.zIndex = 9999;
					toast.innerHTML = `
						<div class="toast show" role="alert">
							<div class="toast-header bg-success text-white">
								<strong class="me-auto">Thông báo</strong>
								<button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
							</div>
							<div class="toast-body">
								${data.message}
							</div>
						</div>
					`;
					document.body.appendChild(toast);

					// Tự động đóng toast sau 3 giây
					setTimeout(() => {
						document.body.removeChild(toast);
					}, 3000);

					// Reset modal và để nó hiển thị để người dùng có thể tiếp tục tạo biến thể
					addVariantForm.reset();

					// Tạo SKU mới
					const skuInput = addVariantForm.querySelector('input[name="sku"]');
					if (skuInput) {
						const productSku = skuInput.getAttribute('data-product-sku') || '';
						const timestamp = new Date().getTime().toString().slice(-6);
						const randomCode = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
						skuInput.value = productSku + '-' + timestamp + randomCode;
					}

					// Tải lại danh sách biến thể nếu có
					loadVariants();
				} else {
					// Hiển thị thông báo lỗi
					alert(data.message || 'Có lỗi xảy ra khi tạo biến thể');
				}
			})
			.catch((error) => {
				console.error('Error:', error);
				alert('Có lỗi xảy ra khi tạo biến thể');
			})
			.finally(() => {
				// Khôi phục trạng thái nút
				submitBtn.disabled = false;
				submitBtn.innerHTML = originalText;
			});
	});

	// Định nghĩa hàm tải lại danh sách biến thể
	const loadVariants = function() {
		const variantsList = document.querySelector('.variants-list');
		if (!variantsList) return;

		// Lấy product ID từ URL
		const pathParts = window.location.pathname.split('/');
		const productIdIndex = pathParts.indexOf('products') + 1;
		if (productIdIndex > 0 && productIdIndex < pathParts.length) {
			const productId = pathParts[productIdIndex];

			// Tải lại danh sách biến thể
			fetch(`/zone-fashion/admin/products/${productId}/variants?format=json`, {
				headers: {
					'X-Requested-With': 'XMLHttpRequest'
				}
			})
				.then(response => response.json())
				.then(data => {
					if (data.html) {
						variantsList.innerHTML = data.html;
					}
				})
				.catch(error => {
					console.error('Error loading variants:', error);
				});
		}
	}
	});
});
