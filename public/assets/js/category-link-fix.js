/**
 * Category Link Fix - Direct method using hard-coded links
 */

document.addEventListener('DOMContentLoaded', function () {
	// Find all dropdown containers in the mega menu
	const categoryItems = document.querySelectorAll(
		'.category-megamenu .dropdown-header a, .category-megamenu .dropdown-item'
	);

	console.log('🔍 Found ' + categoryItems.length + ' category items to fix');

	// Replace each link with a direct navigation span
	categoryItems.forEach(function (item) {
		const href = item.getAttribute('href');
		const text = item.innerText;
		const html = item.innerHTML;

		if (href) {
			// Create a new span that will handle the click
			const directLink = document.createElement('span');
			directLink.innerHTML = html;
			directLink.className = 'direct-link ' + item.className;
			directLink.style.cursor = 'pointer';
			directLink.style.display = 'block';
			directLink.style.width = '100%';
			directLink.style.height = '100%';

			// Add click handler that directly navigates
			directLink.addEventListener('click', function (e) {
				e.stopPropagation();
				console.log('🚀 Direct navigation to: ' + href);
				window.location.href = href;
			});

			// Replace the original link with our span
			if (item.parentNode) {
				item.parentNode.replaceChild(directLink, item);
			}
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
