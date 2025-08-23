/**
 * Category Active Marker
 * Script để đánh dấu danh mục đang được chọn trong menu
 */

document.addEventListener('DOMContentLoaded', function () {
	// Lấy tham số category từ URL hiện tại
	const urlParams = new URLSearchParams(window.location.search);
	const currentCategory = urlParams.get('category');

	if (currentCategory) {
		console.log('🔍 Đang xem danh mục:', currentCategory);

		// Đánh dấu active cho menu đơn giản
		const simpleLinks = document.querySelectorAll(
			'.simple-category-nav .category-link, .simple-category-nav .subcategory-link'
		);
		simpleLinks.forEach((link) => {
			const href = link.getAttribute('href');
			if (href && href.includes('category=' + currentCategory)) {
				console.log('✅ Đánh dấu active cho:', link.textContent.trim());
				link.classList.add('active');

				// Nếu là subcategory, mở và đánh dấu parent category
				const parentCategoryItem = link.closest('.category-item');
				if (
					parentCategoryItem &&
					!link.classList.contains('category-link')
				) {
					const parentLink =
						parentCategoryItem.querySelector('.category-link');
					if (parentLink) {
						parentLink.classList.add('parent-active');
					}

					const subcategoriesEl =
						parentCategoryItem.querySelector('.subcategories');
					if (subcategoriesEl) {
						subcategoriesEl.style.display = 'block';
					}
				}
			}
		});

		// Đánh dấu active cho mega menu
		const megaMenuLinks = document.querySelectorAll(
			'.category-megamenu a.category-link, .category-megamenu a.category-child-link, .dropdown-menu a.dropdown-item'
		);
		megaMenuLinks.forEach((link) => {
			const href = link.getAttribute('href');
			if (href && href.includes('category=' + currentCategory)) {
				link.classList.add('active');
				link.style.color = 'var(--bs-primary)';
				link.style.fontWeight = 'bold';
			}
		});

		// Đánh dấu active cho các menu dropdown chính (Nam, Nữ)
		const mainNavLinks = document.querySelectorAll(
			'.nav-item.dropdown > a.dropdown-toggle'
		);
		mainNavLinks.forEach((link) => {
			const href = link.getAttribute('href');
			if (href && href.includes('category=' + currentCategory)) {
				link.classList.add('active');
				link.style.color = '#fff';
				link.style.backgroundColor = 'rgba(255,255,255,0.15)';
			}

			// Kiểm tra nếu category hiện tại thuộc dropdown này
			const dropdownMenu = link.nextElementSibling;
			if (
				dropdownMenu &&
				dropdownMenu.querySelectorAll(
					'a[href*="' + currentCategory + '"]'
				).length > 0
			) {
				link.classList.add('parent-active');
				link.style.backgroundColor = 'rgba(255,255,255,0.15)';
			}
		});
	}

	// Active class styling
	const style = document.createElement('style');
	style.textContent = `
        .category-link.active, .subcategory-link.active, .dropdown-item.active {
            color: var(--bs-primary) !important;
            font-weight: bold !important;
        }
        .nav-link.active {
            background-color: rgba(255,255,255,0.15);
        }
        .nav-link.parent-active:after {
            transform: rotate(180deg);
        }
        .simple-category-nav .category-link.parent-active {
            color: var(--bs-primary);
        }
    `;
	document.head.appendChild(style);

	// Kiểm tra URL khi đã load xong trang
	window.addEventListener('load', function () {
		if (currentCategory) {
			// Thêm một chút độ trễ để đảm bảo tất cả DOM đã được tải
			setTimeout(() => {
				// Cuộn đến danh mục đang active (nếu có)
				const activeElement = document.querySelector(
					'.category-link.active, .subcategory-link.active'
				);
				if (activeElement) {
					activeElement.scrollIntoView({
						behavior: 'smooth',
						block: 'center',
					});
				}

				console.log('✓ Hoàn tất đánh dấu danh mục active');
			}, 300);
		}
	});
});
