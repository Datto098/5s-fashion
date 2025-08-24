/**
 * Dropdown Item Clickable Fix
 * Đảm bảo các liên kết trong dropdown menu có thể click được
 */

document.addEventListener('DOMContentLoaded', function () {
	// Tìm tất cả các dropdown items và thêm CSS để đảm bảo chúng có thể click
	document.querySelectorAll('.dropdown-item').forEach(function (item) {
		// Đặt cursor là pointer
		item.style.cursor = 'pointer';

		// Đảm bảo pointer-events là auto
		item.style.pointerEvents = 'auto';

		// Đặt z-index cao để đảm bảo không bị che lấp bởi các phần tử khác
		item.style.position = 'relative';
		item.style.zIndex = 1050;
	});

	console.log('🛠️ Dropdown item clickable fix applied');
});
