/**
 * Simple Category Navigation JavaScript
 * This script adds simple behavior for the simplified category menu
 */

document.addEventListener('DOMContentLoaded', function () {
	// Add toggle behavior for mobile display if needed
	const categoryItems = document.querySelectorAll(
		'.simple-category-nav .category-item'
	);

	if (window.innerWidth < 768) {
		categoryItems.forEach((item) => {
			const children = item.querySelector('.subcategories');
			const link = item.querySelector('.category-link');

			if (children) {
				// Add toggle indicator
				const toggleIcon = document.createElement('span');
				toggleIcon.innerHTML = ' <i class="fas fa-chevron-down"></i>';
				toggleIcon.className = 'toggle-icon';
				link.appendChild(toggleIcon);

				// Hide subcategories initially on mobile
				children.style.display = 'none';

				// Add click handler to toggle subcategories
				link.addEventListener('click', function (e) {
					if (window.innerWidth < 768) {
						e.preventDefault();

						// Toggle visibility
						if (children.style.display === 'none') {
							children.style.display = 'block';
							toggleIcon.innerHTML =
								' <i class="fas fa-chevron-up"></i>';
						} else {
							children.style.display = 'none';
							toggleIcon.innerHTML =
								' <i class="fas fa-chevron-down"></i>';
						}
					}
				});
			}
		});
	}

	// Add smooth hover effect for desktop
	if (window.innerWidth >= 768) {
		categoryItems.forEach((item) => {
			const children = item.querySelector('.subcategories');
			if (children) {
				// Add hover behavior for desktop
				item.addEventListener('mouseenter', function () {
					children.style.display = 'block';
					children.style.opacity = '0';
					setTimeout(() => {
						children.style.opacity = '1';
					}, 10);
				});

				item.addEventListener('mouseleave', function () {
					children.style.opacity = '0';
					setTimeout(() => {
						children.style.display = 'none';
					}, 200);
				});
			}
		});
	}
});
