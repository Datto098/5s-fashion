/**
 * URL Parameter Preserver
 * This script preserves the category parameter in URLs when navigating through the site
 */

document.addEventListener('DOMContentLoaded', function () {
	// Check if we're on a page with a category parameter
	const urlParams = new URLSearchParams(window.location.search);
	const categoryParam = urlParams.get('category');

	// Debug info for troubleshooting category issues
	console.log('Current URL parameters:', {
		fullUrl: window.location.href,
		pathname: window.location.pathname,
		search: window.location.search,
		categoryParam: categoryParam,
	});

	if (categoryParam) {
		console.log('Category parameter detected:', categoryParam);

		// Find all links that go to the shop page without a category parameter
		document.querySelectorAll('a[href*="/shop"]').forEach(function (link) {
			const linkUrl = new URL(link.href, window.location.origin);
			const linkParams = new URLSearchParams(linkUrl.search);

			// Only modify links that don't already have a category parameter
			if (
				!linkParams.has('category') &&
				linkUrl.pathname.includes('/shop')
			) {
				linkParams.set('category', categoryParam);
				linkUrl.search = linkParams.toString();
				link.href = linkUrl.toString();
				console.log('Updated shop link with category:', link.href);
			}
		});

		// Fix form submissions that might lose the category parameter
		document.querySelectorAll('form').forEach(function (form) {
			if (
				form.action &&
				form.action.includes('/shop') &&
				!form.querySelector('input[name="category"]')
			) {
				const hiddenInput = document.createElement('input');
				hiddenInput.type = 'hidden';
				hiddenInput.name = 'category';
				hiddenInput.value = categoryParam;
				form.appendChild(hiddenInput);
				console.log('Added hidden category input to form');
			}
		});
	}
});
