/**
 * Category Filter Checkbox Activator
 * This script automatically checks the appropriate category filter checkbox
 * based on the URL parameter
 */

document.addEventListener('DOMContentLoaded', function () {
	console.log(
		'⚙️ Category Filter Activator running... URL:',
		window.location.href
	);

	// Chỉ thực hiện trên trang shop
	if (!window.location.href.includes('/shop')) {
		console.log('⏭️ Not on shop page, skipping...');
		return;
	}

	// Get category from URL
	const urlParams = new URLSearchParams(window.location.search);
	const categorySlug = urlParams.get('category');

	// Debugging all URL parameters
	console.log('🔎 All URL parameters:');
	for (const [key, value] of urlParams.entries()) {
		console.log(`   ${key}: ${value}`);
	}

	if (!categorySlug) {
		console.log('🔍 No category parameter found in URL');
		return;
	}

	console.log('🔍 Category slug found in URL:', categorySlug);

	// Lấy dữ liệu từ phần tử JSON ẩn trong trang
	const categoryData = document.getElementById('category-data');
	if (categoryData) {
		try {
			console.log(
				'📄 Category data found. Content:',
				categoryData.textContent.trim()
			);
			const categoryMapping = JSON.parse(categoryData.textContent);
			console.log('🗺️ Parsed category mapping:', categoryMapping);

			// Tìm ID từ slug
			const categoryId = categoryMapping[categorySlug];

			if (categoryId) {
				console.log('🎯 Found category ID:', categoryId);

				// Tìm checkbox tương ứng với ID
				const checkbox = document.querySelector(
					`input[name="category"][value="${categoryId}"]`
				);
				if (checkbox) {
					console.log(
						'✓ Found checkbox for category ID:',
						categoryId
					);

					// Bỏ check "Tất cả"
					const allCheckbox = document.querySelector(
						'input[name="category"][value="all"]'
					);
					if (allCheckbox) {
						console.log('✓ Unchecking "all" checkbox');
						allCheckbox.checked = false;
					}

					// Check checkbox của danh mục
					console.log('✓ Checking category checkbox');
					checkbox.checked = true;

					// Thông báo chi tiết
					console.log(
						'✅ Checkbox đã được kích hoạt cho danh mục:',
						categorySlug
					);
					console.log('✅ ID danh mục:', categoryId);

					// Nếu cần kích hoạt sự kiện change để cập nhật bộ lọc
					if (typeof ShopManager !== 'undefined') {
						console.log(
							'⚡ Kích hoạt sự kiện change trên checkbox'
						);
						const event = new Event('change', { bubbles: true });
						checkbox.dispatchEvent(event);
					}
				} else {
					console.error(
						'❌ Checkbox not found for category ID:',
						categoryId
					);
				}
			} else {
				console.error(
					'❌ Category ID not found for slug:',
					categorySlug
				);
			}
		} catch (e) {
			console.error('❌ Error parsing category data:', e);
		}
	} else {
		console.error('❌ Category data element not found in page');
	}
});
