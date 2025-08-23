/**
 * User Dropdown Menu Fix
 * Script đặc biệt để đảm bảo user dropdown menu trong header hoạt động đúng
 */

document.addEventListener('DOMContentLoaded', function () {
	// Chờ đảm bảo Bootstrap đã được khởi tạo
	setTimeout(function () {
		// Tìm user dropdown menu cụ thể
		const userDropdownToggle = document.querySelector(
			'.user-dropdown .dropdown-toggle[data-bs-toggle="dropdown"]'
		);
		const userDropdownMenu = document.querySelector(
			'.user-dropdown .dropdown-menu'
		);

		if (userDropdownToggle && userDropdownMenu) {
			console.log('🔍 Found user dropdown menu');

			// Đảm bảo menu hoạt động đúng
			userDropdownToggle.addEventListener('click', function (event) {
				// Ghi log để debug
				console.log('👆 User dropdown toggle clicked');

				// Đảm bảo Bootstrap toggle vẫn hoạt động bình thường
				// Chúng ta không ngăn sự kiện mặc định
			});

			// Đặc biệt: Đảm bảo tất cả các liên kết trong user dropdown menu có thể click được
			userDropdownMenu
				.querySelectorAll('.dropdown-item, a')
				.forEach(function (item) {
					// Đặt style trực tiếp để đảm bảo khả năng click
					item.style.cursor = 'pointer';
					item.style.pointerEvents = 'auto';
					item.style.position = 'relative';
					item.style.zIndex = '1060'; // Z-index cao

					// Thêm sự kiện click - đảm bảo liên kết hoạt động
					item.addEventListener('click', function (event) {
						console.log(
							'👉 User dropdown item clicked:',
							this.textContent,
							this.href || ''
						);

						// Không cần ngăn sự kiện mặc định để liên kết vẫn hoạt động
						// Nhưng chúng ta ngăn sự kiện lan truyền (bubble) để tránh đóng menu
						event.stopPropagation();
					});
				});

			// Đặc biệt: theo dõi hiển thị/ẩn của menu dropdown
			userDropdownToggle.addEventListener(
				'shown.bs.dropdown',
				function () {
					console.log('✅ User dropdown shown');
					userDropdownMenu.style.display = 'block';
					userDropdownMenu.style.visibility = 'visible';
					userDropdownMenu.style.opacity = '1';
					userDropdownMenu.style.pointerEvents = 'auto';
					userDropdownMenu.style.zIndex = '1060';
				}
			);

			console.log('✅ User dropdown menu fix applied');
		} else {
			console.log('❌ User dropdown menu or toggle not found');
		}
	}, 500); // Chờ 500ms để đảm bảo Bootstrap đã được khởi tạo
});
