/**
 * User Dropdown Fix
 * Script để giải quyết vấn đề dropdown menu không hoạt động
 * Chỉ áp dụng cho các dropdown tùy chỉnh, không áp dụng cho bootstrap dropdowns
 */

document.addEventListener('DOMContentLoaded', function () {
	// Tìm tất cả dropdown toggles trong header (ngoại trừ .user-dropdown vì nó dùng Bootstrap)
	const userDropdowns = document.querySelectorAll(
		'.top-bar .dropdown-toggle:not([data-bs-toggle]), .header-actions .dropdown-toggle:not([data-bs-toggle])'
	);

	console.log('📋 Custom dropdowns to initialize:', userDropdowns.length);

	userDropdowns.forEach(function (toggle) {
		// Xóa tất cả các sự kiện hiện có
		const newToggle = toggle.cloneNode(true);
		toggle.parentNode.replaceChild(newToggle, toggle);

		// Thêm xử lý click mới
		newToggle.addEventListener('click', function (event) {
			console.log('🔄 Custom dropdown clicked:', this.textContent);
			event.preventDefault();
			event.stopPropagation();

			// Tìm dropdown container và menu
			const dropdown = this.closest('.dropdown');
			const menu = dropdown.querySelector('.dropdown-menu');

			// Đóng tất cả các menu khác
			document
				.querySelectorAll('.dropdown-menu.show')
				.forEach(function (openMenu) {
					if (openMenu !== menu) {
						openMenu.classList.remove('show');
						openMenu.closest('.dropdown').classList.remove('show');
					}
				});

			// Toggle menu hiện tại
			dropdown.classList.toggle('show');
			menu.classList.toggle('show');

			// Định vị menu đúng cách
			if (menu.classList.contains('show')) {
				console.log('🔍 Showing dropdown menu:', menu);
				const rect = this.getBoundingClientRect();

				// Reset vị trí
				menu.style.position = 'absolute';
				menu.style.inset = 'auto';
				menu.style.transform = 'none';

				// Đặt vị trí menu dưới toggle
				menu.style.top = rect.height + 'px';
				menu.style.left = '0';

				// Đảm bảo menu hiển thị đúng
				menu.style.display = 'block';
				menu.style.visibility = 'visible';
				menu.style.opacity = '1';
				menu.style.pointerEvents = 'auto';
				menu.style.zIndex = '1050';

				// Kiểm tra nếu menu sẽ ra ngoài màn hình bên phải
				const menuRect = menu.getBoundingClientRect();
				if (menuRect.right > window.innerWidth) {
					menu.style.left = 'auto';
					menu.style.right = '0';
				}

				// Đảm bảo các liên kết trong dropdown có thể click được
				menu.querySelectorAll('a, .dropdown-item').forEach(function (
					link
				) {
					link.style.pointerEvents = 'auto';
					link.style.cursor = 'pointer';
					link.style.position = 'relative';
					link.style.zIndex = '1060';
				});
			}
		});
	});

	// Đóng dropdown khi nhấp ra ngoài
	document.addEventListener('click', function (event) {
		if (!event.target.closest('.dropdown')) {
			document
				.querySelectorAll('.dropdown-menu.show')
				.forEach(function (menu) {
					menu.classList.remove('show');
					if (menu.closest('.dropdown')) {
						menu.closest('.dropdown').classList.remove('show');
					}
				});
		}
	});

	// Đảm bảo các liên kết trong dropdown hoạt động bình thường
	document
		.querySelectorAll('.dropdown-menu a, .dropdown-item')
		.forEach(function (link) {
			link.addEventListener('click', function (event) {
				// Ghi log thử nghiệm
				console.log(
					'👆 Link clicked:',
					this.textContent,
					this.href || ''
				);

				// Cho phép chuyển hướng bình thường
				event.stopPropagation();
			});
		});

	console.log('✅ User dropdown fix applied');
});
