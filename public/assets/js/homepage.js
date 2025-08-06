/**
 * Homepage JavaScript
 * 5S Fashion E-commerce Platform
 */

document.addEventListener('DOMContentLoaded', function () {
	// Initialize homepage components
	initializeHomepage();
});

function initializeHomepage() {
	// Initialize hero slider with custom settings
	if (document.querySelector('.hero-slider')) {
		initializeHeroSlider();
	}

	// Initialize scroll animations
	initializeScrollAnimations();

	// Initialize parallax effects
	initializeParallaxEffects();

	// Initialize counters
	initializeCounters();
}

function initializeHeroSlider() {
	const heroSlider = new Swiper('.hero-slider', {
		loop: true,
		effect: 'fade',
		fadeEffect: {
			crossFade: true,
		},
		autoplay: {
			delay: 5000,
			disableOnInteraction: false,
			pauseOnMouseEnter: true,
		},
		speed: 1000,
		pagination: {
			el: '.swiper-pagination',
			clickable: true,
			dynamicBullets: true,
		},
		navigation: {
			nextEl: '.swiper-button-next',
			prevEl: '.swiper-button-prev',
		},
		keyboard: {
			enabled: true,
		},
		on: {
			slideChange: function () {
				// Animate slide content
				const activeSlide = this.slides[this.activeIndex];
				animateSlideContent(activeSlide);
			},
		},
	});

	// Animate first slide on load
	setTimeout(() => {
		const firstSlide = document.querySelector('.swiper-slide-active');
		if (firstSlide) {
			animateSlideContent(firstSlide);
		}
	}, 100);
}

function animateSlideContent(slide) {
	const title = slide.querySelector('.hero-title');
	const subtitle = slide.querySelector('.hero-subtitle');
	const actions = slide.querySelector('.hero-actions');

	// Reset animations
	[title, subtitle, actions].forEach((el) => {
		if (el) {
			el.style.opacity = '0';
			el.style.transform = 'translateY(50px)';
		}
	});

	// Animate elements
	setTimeout(() => {
		if (title) {
			title.style.transition = 'all 0.8s ease';
			title.style.opacity = '1';
			title.style.transform = 'translateY(0)';
		}
	}, 200);

	setTimeout(() => {
		if (subtitle) {
			subtitle.style.transition = 'all 0.8s ease';
			subtitle.style.opacity = '1';
			subtitle.style.transform = 'translateY(0)';
		}
	}, 400);

	setTimeout(() => {
		if (actions) {
			actions.style.transition = 'all 0.8s ease';
			actions.style.opacity = '1';
			actions.style.transform = 'translateY(0)';
		}
	}, 600);
}

function initializeScrollAnimations() {
	// Create intersection observer for scroll animations
	const observerOptions = {
		threshold: 0.1,
		rootMargin: '0px 0px -50px 0px',
	};

	const observer = new IntersectionObserver((entries) => {
		entries.forEach((entry) => {
			if (entry.isIntersecting) {
				entry.target.classList.add('animate');

				// Stagger animation for multiple elements
				const children = entry.target.querySelectorAll(
					'.feature-item, .category-card, .product-card'
				);
				children.forEach((child, index) => {
					setTimeout(() => {
						child.style.opacity = '1';
						child.style.transform = 'translateY(0)';
					}, index * 100);
				});
			}
		});
	}, observerOptions);

	// Observe sections
	document
		.querySelectorAll(
			'.features-section, .categories-section, .products-section'
		)
		.forEach((section) => {
			section.classList.add('scroll-animation');
			observer.observe(section);
		});

	// Prepare child elements for staggered animation
	document
		.querySelectorAll('.feature-item, .category-card, .product-card')
		.forEach((el) => {
			el.style.opacity = '0';
			el.style.transform = 'translateY(30px)';
			el.style.transition = 'all 0.6s ease';
		});
}

function initializeParallaxEffects() {
	// Simple parallax effect for hero section
	window.addEventListener('scroll', () => {
		const scrolled = window.pageYOffset;
		const parallaxElements = document.querySelectorAll('.hero-slide');

		parallaxElements.forEach((element) => {
			const speed = 0.5;
			element.style.transform = `translateY(${scrolled * speed}px)`;
		});
	});
}

function initializeCounters() {
	// Animated counters (if any statistics are shown)
	const counters = document.querySelectorAll('[data-counter]');

	const counterObserver = new IntersectionObserver((entries) => {
		entries.forEach((entry) => {
			if (entry.isIntersecting) {
				const counter = entry.target;
				const target = parseInt(counter.dataset.counter);
				animateCounter(counter, target);
				counterObserver.unobserve(counter);
			}
		});
	});

	counters.forEach((counter) => {
		counterObserver.observe(counter);
	});
}

function animateCounter(element, target) {
	let current = 0;
	const increment = target / 100;
	const duration = 2000; // 2 seconds
	const stepTime = duration / 100;

	const timer = setInterval(() => {
		current += increment;
		if (current >= target) {
			current = target;
			clearInterval(timer);
		}
		element.textContent = Math.floor(current).toLocaleString();
	}, stepTime);
}

// Enhanced product interactions
function enhanceProductCards() {
	document.querySelectorAll('.product-card').forEach((card) => {
		// Add hover effects
		card.addEventListener('mouseenter', function () {
			this.style.transform = 'translateY(-5px)';
			this.style.boxShadow = '0 15px 35px rgba(0,0,0,0.15)';
		});

		card.addEventListener('mouseleave', function () {
			this.style.transform = 'translateY(0)';
			this.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
		});

		// Add quick view functionality
		const quickViewBtn = card.querySelector('[onclick*="quickView"]');
		if (quickViewBtn) {
			quickViewBtn.addEventListener('click', function (e) {
				e.preventDefault();
				const productId = this.getAttribute('onclick').match(/\d+/)[0];
				quickView(productId);
			});
		}
	});
}

// Newsletter form enhancement
function enhanceNewsletterForm() {
	const newsletterForm = document.querySelector('.newsletter-form');
	if (newsletterForm) {
		newsletterForm.addEventListener('submit', function (e) {
			e.preventDefault();

			const email = this.querySelector('input[type="email"]').value;
			const button = this.querySelector('button');
			const originalText = button.innerHTML;

			// Show loading state
			button.innerHTML =
				'<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...';
			button.disabled = true;

			// Simulate API call
			setTimeout(() => {
				// Reset button
				button.innerHTML = originalText;
				button.disabled = false;

				// Show success message
				showToast('Cảm ơn bạn đã đăng ký nhận tin!', 'success');

				// Clear form
				this.querySelector('input[type="email"]').value = '';
			}, 1500);
		});
	}
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
	enhanceProductCards();
	enhanceNewsletterForm();
});

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
	anchor.addEventListener('click', function (e) {
		e.preventDefault();
		const target = document.querySelector(this.getAttribute('href'));
		if (target) {
			target.scrollIntoView({
				behavior: 'smooth',
				block: 'start',
			});
		}
	});
});

// Add loading states for all buttons
document.querySelectorAll('.btn').forEach((btn) => {
	if (btn.type === 'submit' || btn.hasAttribute('data-loading')) {
		btn.addEventListener('click', function () {
			if (!this.disabled) {
				const originalText = this.innerHTML;
				this.innerHTML =
					'<i class="fas fa-spinner fa-spin me-2"></i>Đang tải...';
				this.disabled = true;

				// Re-enable after 2 seconds (adjust as needed)
				setTimeout(() => {
					this.innerHTML = originalText;
					this.disabled = false;
				}, 2000);
			}
		});
	}
});

// Performance optimization: Lazy load images
function lazyLoadImages() {
	const images = document.querySelectorAll('img[data-src]');

	if ('IntersectionObserver' in window) {
		const imageObserver = new IntersectionObserver((entries, observer) => {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {
					const img = entry.target;
					img.src = img.dataset.src;
					img.classList.remove('lazy');
					img.classList.add('loaded');
					imageObserver.unobserve(img);
				}
			});
		});

		images.forEach((img) => imageObserver.observe(img));
	} else {
		// Fallback for older browsers
		images.forEach((img) => {
			img.src = img.dataset.src;
			img.classList.remove('lazy');
		});
	}
}

// Initialize lazy loading
lazyLoadImages();

// Add custom cursor effects for interactive elements
document
	.querySelectorAll('.category-card, .product-card, .btn')
	.forEach((el) => {
		el.style.cursor = 'pointer';
	});
