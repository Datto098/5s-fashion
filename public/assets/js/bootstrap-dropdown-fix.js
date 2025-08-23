/**
 * Bootstrap Dropdown Fix
 *
 * Script này ghi đè một số hành vi mặc định của Bootstrap Dropdown
 * để đảm bảo các liên kết trong dropdown menu luôn có thể click được
 */

document.addEventListener('DOMContentLoaded', function () {
	// Chờ một chút để Bootstrap đã khởi tạo
	setTimeout(function () {
		// Tìm tất cả các nút dropdown bootstrap
		const bootstrapDropdownToggles = document.querySelectorAll(
			'[data-bs-toggle="dropdown"]'
		);

		bootstrapDropdownToggles.forEach(function (toggle) {
			// Khi dropdown hiển thị, đảm bảo các liên kết trong đó có thể click được
			toggle.addEventListener('shown.bs.dropdown', function () {
				const menu =
					document.querySelector(
						`[aria-labelledby="${toggle.id}"]`
					) || this.nextElementSibling;

				if (menu && menu.classList.contains('dropdown-menu')) {
					// Đảm bảo menu hiển thị đúng
					menu.style.display = 'block';
					menu.style.visibility = 'visible';
					menu.style.opacity = '1';
					menu.style.pointerEvents = 'auto';

					// Đảm bảo tất cả các liên kết trong menu có thể click được
					menu.querySelectorAll('.dropdown-item, a').forEach(
						function (item) {
							item.style.pointerEvents = 'auto';
							item.style.position = 'relative';
							item.style.zIndex = '1060';
							item.style.cursor = 'pointer';

							// Đảm bảo sự kiện click không bị chặn
							item.addEventListener('click', function (e) {
								// Cho phép sự kiện click được truyền đi
								e.stopPropagation();

								// Nếu là thẻ a, đảm bảo nó có thể điều hướng
								if (
									this.tagName.toLowerCase() === 'a' &&
									this.getAttribute('href')
								) {
									console.log(
										'👆 Dropdown link clicked:',
										this.getAttribute('href')
									);
								}
							});
						}
					);
				}
			});
		});

		console.log('🔧 Bootstrap Dropdown Fix applied');
	}, 500);
});
