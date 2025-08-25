/**
 * Category Link Fix - Enhanced version that keeps anchor tags
 */

document.addEventListener('DOMContentLoaded', function () {
	// Find all dropdown containers in the mega menu
	const categoryItems = document.querySelectorAll(
		'.category-megamenu .dropdown-header a, .category-megamenu .dropdown-item'
	);

	console.log(
		'🔍 Found ' + categoryItems.length + ' category items to enhance'
	);

	// Enhance each link to make sure it's clickable
	categoryItems.forEach(function (item) {
		const href = item.getAttribute('href');

		if (href) {
			// Instead of replacing with span, enhance the original link
			item.classList.add('enhanced-link');
			item.style.cursor = 'pointer';
			item.style.display = 'block';
			item.style.width = '100%';
			item.style.height = '100%';
			item.style.position = 'relative';
			item.style.zIndex = '1050'; // Ensure it's above other elements
			item.style.pointerEvents = 'auto'; // Make sure clicks register

			// Add click handler to ensure navigation works
			item.addEventListener('click', function (e) {
				e.stopPropagation();
				console.log('🚀 Enhanced navigation to: ' + href);
				// Let the default browser behavior handle it
			});
		}
	});

	// Also fix dropdown toggles that have links
	document
		.querySelectorAll('.nav-link.dropdown-toggle')
		.forEach(function (toggle) {
			const href = toggle.getAttribute('href');
			if (href && !href.includes('#')) {
				toggle.addEventListener('click', function (e) {
					// If clicked directly on the toggle (not a child element), navigate
					if (e.target === toggle) {
						window.location.href = href;
					}
				});
			}
		});
});
