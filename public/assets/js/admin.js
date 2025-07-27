/**
 * 5S Fashion Admin Panel JavaScript
 * Main functionality for admin interface
 */

document.addEventListener('DOMContentLoaded', function () {
	// ============================================
	// SIDEBAR FUNCTIONALITY
	// ============================================

	// Sidebar toggle
	const sidebarToggle = document.querySelector('.sidebar-toggle');
	const sidebar = document.querySelector('.sidebar');
	const mainContent = document.querySelector('.main-content');

	if (sidebarToggle) {
		sidebarToggle.addEventListener('click', function () {
			if (window.innerWidth <= 768) {
				// Mobile: show/hide sidebar
				sidebar.classList.toggle('show');
			} else {
				// Desktop: collapse/expand sidebar
				sidebar.classList.toggle('collapsed');
				mainContent.classList.toggle('expanded');
			}
		});
	}

	// Menu accordion functionality
	const menuItems = document.querySelectorAll('.menu-item');
	menuItems.forEach((item) => {
		const link = item.querySelector('.menu-link');
		const submenu = item.querySelector('.menu-submenu');

		if (submenu && link) {
			link.addEventListener('click', function (e) {
				e.preventDefault();

				// Close other open menus
				menuItems.forEach((otherItem) => {
					if (otherItem !== item) {
						otherItem.classList.remove('open');
					}
				});

				// Toggle current menu
				item.classList.toggle('open');
			});
		}
	});

	// Close sidebar on mobile when clicking outside
	document.addEventListener('click', function (e) {
		if (window.innerWidth <= 768) {
			if (
				!sidebar.contains(e.target) &&
				!sidebarToggle.contains(e.target)
			) {
				sidebar.classList.remove('show');
			}
		}
	});

	// ============================================
	// ALERT FUNCTIONALITY
	// ============================================

	// Auto-dismiss alerts
	const alerts = document.querySelectorAll('.alert');
	alerts.forEach((alert) => {
		// Auto-dismiss after 5 seconds
		setTimeout(() => {
			alert.style.opacity = '0';
			setTimeout(() => {
				if (alert.parentNode) {
					alert.parentNode.removeChild(alert);
				}
			}, 300);
		}, 5000);

		// Manual dismiss
		const closeBtn = alert.querySelector('.alert-close');
		if (closeBtn) {
			closeBtn.addEventListener('click', function () {
				alert.style.opacity = '0';
				setTimeout(() => {
					if (alert.parentNode) {
						alert.parentNode.removeChild(alert);
					}
				}, 300);
			});
		}
	});

	// ============================================
	// FORM FUNCTIONALITY
	// ============================================

	// Form validation
	const forms = document.querySelectorAll('form[data-validate]');
	forms.forEach((form) => {
		form.addEventListener('submit', function (e) {
			if (!validateForm(form)) {
				e.preventDefault();
			}
		});

		// Real-time validation
		const inputs = form.querySelectorAll('input, select, textarea');
		inputs.forEach((input) => {
			input.addEventListener('blur', function () {
				validateField(input);
			});
		});
	});

	// File upload preview
	const fileInputs = document.querySelectorAll('input[type="file"]');
	fileInputs.forEach((input) => {
		input.addEventListener('change', function () {
			handleFilePreview(this);
		});
	});

	// ============================================
	// TABLE FUNCTIONALITY
	// ============================================

	// Select all checkboxes
	const selectAllCheckbox = document.querySelector('#selectAll');
	if (selectAllCheckbox) {
		selectAllCheckbox.addEventListener('change', function () {
			const checkboxes = document.querySelectorAll('.item-checkbox');
			checkboxes.forEach((checkbox) => {
				checkbox.checked = this.checked;
			});
			updateBulkActions();
		});
	}

	// Individual checkboxes
	const itemCheckboxes = document.querySelectorAll('.item-checkbox');
	itemCheckboxes.forEach((checkbox) => {
		checkbox.addEventListener('change', function () {
			updateBulkActions();
			updateSelectAll();
		});
	});

	// Bulk actions
	const bulkActionForm = document.querySelector('#bulkActionForm');
	if (bulkActionForm) {
		bulkActionForm.addEventListener('submit', function (e) {
			e.preventDefault();
			handleBulkAction();
		});
	}

	// ============================================
	// SEARCH FUNCTIONALITY
	// ============================================

	const searchInput = document.querySelector('.search-input');
	if (searchInput) {
		let searchTimeout;
		searchInput.addEventListener('input', function () {
			clearTimeout(searchTimeout);
			searchTimeout = setTimeout(() => {
				handleSearch(this.value);
			}, 500);
		});
	}

	// ============================================
	// MODAL FUNCTIONALITY
	// ============================================

	// Delete confirmation modals
	const deleteButtons = document.querySelectorAll('[data-delete-url]');
	deleteButtons.forEach((button) => {
		button.addEventListener('click', function (e) {
			e.preventDefault();
			showDeleteConfirmation(
				this.dataset.deleteUrl,
				this.dataset.itemName
			);
		});
	});

	// ============================================
	// TOAST NOTIFICATIONS
	// ============================================

	// Show toast notification
	window.showToast = function (message, type = 'info') {
		const toast = createToast(message, type);
		document.body.appendChild(toast);

		// Show toast
		setTimeout(() => {
			toast.classList.add('show');
		}, 100);

		// Auto-dismiss
		setTimeout(() => {
			toast.classList.remove('show');
			setTimeout(() => {
				if (toast.parentNode) {
					toast.parentNode.removeChild(toast);
				}
			}, 300);
		}, 3000);
	};

	// ============================================
	// UTILITY FUNCTIONS
	// ============================================

	// Format currency for display
	window.formatCurrency = function (amount) {
		return new Intl.NumberFormat('vi-VN', {
			style: 'currency',
			currency: 'VND',
		}).format(amount);
	};

	// Format date for display
	window.formatDate = function (date) {
		return new Intl.DateTimeFormat('vi-VN').format(new Date(date));
	};

	// Debounce function
	window.debounce = function (func, wait) {
		let timeout;
		return function executedFunction(...args) {
			const later = () => {
				clearTimeout(timeout);
				func(...args);
			};
			clearTimeout(timeout);
			timeout = setTimeout(later, wait);
		};
	};
});

// ============================================
// HELPER FUNCTIONS
// ============================================

function validateForm(form) {
	let isValid = true;
	const inputs = form.querySelectorAll(
		'input[required], select[required], textarea[required]'
	);

	inputs.forEach((input) => {
		if (!validateField(input)) {
			isValid = false;
		}
	});

	return isValid;
}

function validateField(field) {
	const value = field.value.trim();
	const type = field.type;
	let isValid = true;
	let message = '';

	// Remove previous validation classes
	field.classList.remove('is-valid', 'is-invalid');

	// Required validation
	if (field.hasAttribute('required') && !value) {
		isValid = false;
		message = 'Trường này là bắt buộc';
	}

	// Email validation
	if (type === 'email' && value && !isValidEmail(value)) {
		isValid = false;
		message = 'Email không hợp lệ';
	}

	// Password validation
	if (type === 'password' && value && value.length < 8) {
		isValid = false;
		message = 'Mật khẩu phải có ít nhất 8 ký tự';
	}

	// Number validation
	if (type === 'number' && value && isNaN(value)) {
		isValid = false;
		message = 'Vui lòng nhập một số hợp lệ';
	}

	// Add validation classes
	field.classList.add(isValid ? 'is-valid' : 'is-invalid');

	// Show/hide error message
	const feedback = field.parentNode.querySelector('.invalid-feedback');
	if (feedback) {
		feedback.textContent = message;
		feedback.style.display = isValid ? 'none' : 'block';
	}

	return isValid;
}

function handleFilePreview(input) {
	const file = input.files[0];
	if (!file) return;

	// Check file type
	if (!file.type.startsWith('image/')) {
		showToast('Vui lòng chọn file hình ảnh', 'error');
		input.value = '';
		return;
	}

	// Check file size (5MB)
	if (file.size > 5 * 1024 * 1024) {
		showToast('File quá lớn. Vui lòng chọn file nhỏ hơn 5MB', 'error');
		input.value = '';
		return;
	}

	// Show preview
	const reader = new FileReader();
	reader.onload = function (e) {
		const previewContainer =
			input.parentNode.querySelector('.file-preview');
		if (previewContainer) {
			previewContainer.innerHTML = `<img src="${e.target.result}" alt="Preview" style="max-width: 200px; max-height: 200px; object-fit: cover; border-radius: 8px;">`;
		}
	};
	reader.readAsDataURL(file);
}

function updateBulkActions() {
	const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
	const bulkActions = document.querySelector('.bulk-actions');

	if (bulkActions) {
		if (checkedBoxes.length > 0) {
			bulkActions.style.display = 'block';
			bulkActions.querySelector('.selected-count').textContent =
				checkedBoxes.length;
		} else {
			bulkActions.style.display = 'none';
		}
	}
}

function updateSelectAll() {
	const selectAllCheckbox = document.querySelector('#selectAll');
	if (!selectAllCheckbox) return;

	const totalCheckboxes = document.querySelectorAll('.item-checkbox').length;
	const checkedCheckboxes = document.querySelectorAll(
		'.item-checkbox:checked'
	).length;

	selectAllCheckbox.checked =
		totalCheckboxes > 0 && totalCheckboxes === checkedCheckboxes;
	selectAllCheckbox.indeterminate =
		checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes;
}

function handleBulkAction() {
	const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
	const action = document.querySelector('#bulkAction').value;

	if (checkedBoxes.length === 0) {
		showToast('Vui lòng chọn ít nhất một mục', 'warning');
		return;
	}

	if (!action) {
		showToast('Vui lòng chọn hành động', 'warning');
		return;
	}

	// Collect IDs
	const ids = Array.from(checkedBoxes).map((cb) => cb.value);

	// Confirm action
	const actionText = document.querySelector(
		`#bulkAction option[value="${action}"]`
	).text;
	if (
		confirm(
			`Bạn có chắc chắn muốn ${actionText.toLowerCase()} ${
				ids.length
			} mục đã chọn?`
		)
	) {
		// Submit form or make AJAX request
		const form = document.querySelector('#bulkActionForm');
		const idsInput = document.createElement('input');
		idsInput.type = 'hidden';
		idsInput.name = 'ids';
		idsInput.value = ids.join(',');
		form.appendChild(idsInput);
		form.submit();
	}
}

function handleSearch(query) {
	// Add loading indicator
	const searchInput = document.querySelector('.search-input');
	searchInput.classList.add('loading');

	// Make AJAX request or redirect with query parameter
	const url = new URL(window.location);
	if (query.trim()) {
		url.searchParams.set('search', query);
	} else {
		url.searchParams.delete('search');
	}

	// Redirect to new URL
	window.location.href = url.toString();
}

function showDeleteConfirmation(deleteUrl, itemName) {
	const message = itemName
		? `Bạn có chắc chắn muốn xóa "${itemName}"?`
		: 'Bạn có chắc chắn muốn xóa mục này?';

	if (confirm(message + '\n\nHành động này không thể hoàn tác.')) {
		// Create form and submit
		const form = document.createElement('form');
		form.method = 'POST';
		form.action = deleteUrl;

		// Add CSRF token if available
		const csrfToken = document.querySelector('meta[name="csrf-token"]');
		if (csrfToken) {
			const tokenInput = document.createElement('input');
			tokenInput.type = 'hidden';
			tokenInput.name = 'csrf_token';
			tokenInput.value = csrfToken.content;
			form.appendChild(tokenInput);
		}

		// Add method override for DELETE
		const methodInput = document.createElement('input');
		methodInput.type = 'hidden';
		methodInput.name = '_method';
		methodInput.value = 'DELETE';
		form.appendChild(methodInput);

		document.body.appendChild(form);
		form.submit();
	}
}

function createToast(message, type) {
	const toast = document.createElement('div');
	toast.className = `toast toast-${type}`;
	toast.innerHTML = `
        <div class="toast-header">
            <strong class="toast-title">${getToastTitle(type)}</strong>
            <button type="button" class="toast-close" onclick="this.parentNode.parentNode.remove()">×</button>
        </div>
        <div class="toast-body">${message}</div>
    `;

	// Position toast
	toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        min-width: 300px;
        max-width: 400px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        z-index: 9999;
        transform: translateX(100%);
        transition: all 0.3s ease;
        border-left: 4px solid ${getToastColor(type)};
    `;

	return toast;
}

function getToastTitle(type) {
	const titles = {
		success: 'Thành công',
		error: 'Lỗi',
		warning: 'Cảnh báo',
		info: 'Thông báo',
	};
	return titles[type] || 'Thông báo';
}

function getToastColor(type) {
	const colors = {
		success: '#28a745',
		error: '#dc3545',
		warning: '#ffc107',
		info: '#17a2b8',
	};
	return colors[type] || '#17a2b8';
}

function isValidEmail(email) {
	const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
	return emailRegex.test(email);
}

// ============================================
// AJAX HELPERS
// ============================================

window.ajaxRequest = function (url, options = {}) {
	const defaults = {
		method: 'GET',
		headers: {
			'Content-Type': 'application/json',
			'X-Requested-With': 'XMLHttpRequest',
		},
	};

	// Add CSRF token if available
	const csrfToken = document.querySelector('meta[name="csrf-token"]');
	if (csrfToken) {
		defaults.headers['X-CSRF-TOKEN'] = csrfToken.content;
	}

	const config = Object.assign(defaults, options);

	return fetch(url, config)
		.then((response) => {
			if (!response.ok) {
				throw new Error(`HTTP error! status: ${response.status}`);
			}
			return response.json();
		})
		.catch((error) => {
			console.error('AJAX request failed:', error);
			showToast('Có lỗi xảy ra. Vui lòng thử lại.', 'error');
			throw error;
		});
};

// ============================================
// INITIALIZATION ON LOAD
// ============================================

// Add loading states to buttons
document.addEventListener('submit', function (e) {
	const submitBtn = e.target.querySelector('button[type="submit"]');
	if (submitBtn && !submitBtn.disabled) {
		submitBtn.disabled = true;
		submitBtn.innerHTML =
			'<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';

		// Re-enable after 10 seconds as fallback
		setTimeout(() => {
			submitBtn.disabled = false;
			submitBtn.innerHTML = submitBtn.dataset.originalText || 'Lưu';
		}, 10000);
	}
});

// Store original button text
document.querySelectorAll('button[type="submit"]').forEach((btn) => {
	btn.dataset.originalText = btn.innerHTML;
});

// Initialize tooltips (if using Bootstrap tooltips)
if (typeof bootstrap !== 'undefined') {
	const tooltipTriggerList = [].slice.call(
		document.querySelectorAll('[data-bs-toggle="tooltip"]')
	);
	tooltipTriggerList.map(function (tooltipTriggerEl) {
		return new bootstrap.Tooltip(tooltipTriggerEl);
	});
}
