/**
 * Client Website JavaScript
 * zone Fashion E-commerce Platform
 */

// Get base URL dynamically
const BASE_URL = (() => {
  const pathParts = window.location.pathname.split("/").filter((part) => part);
  // If we're in a subdirectory like /zone-fashion/, use it as base
  if (pathParts.length > 0 && !pathParts[0].includes(".")) {
    return "/" + pathParts[0];
  }
  // Otherwise use root
  return "";
})();

// Backwards-compat: provide a global firstColor if other inline scripts reference it
if (typeof window.firstColor === "undefined") window.firstColor = null;

// Global variables
let cart = JSON.parse(localStorage.getItem("cart")) || [];
// Wishlist is now handled via API, no localStorage needed

/**
 * Client Website JavaScript
 * zone Fashion E-commerce Platform
 */

// Helper function to get proper image URL
function getImageUrl(imagePath) {
  if (!imagePath) {
    return `${BASE_URL}/assets/images/no-image.jpg`;
  }

  // If it's already a full serve-file.php URL, return as is
  if (imagePath.startsWith(`${BASE_URL}/serve-file.php`)) {
    return imagePath;
  }

  // If it's a full HTTP URL, return as is
  if (imagePath.startsWith("http")) {
    return imagePath;
  }

  // Handle API response format: /uploads/products/filename.webp
  if (imagePath.startsWith("/uploads/products/")) {
    // Remove /uploads/ prefix since serve-file.php adds /public/uploads/ automatically
    const fileName = imagePath.replace("/uploads/", "");
    return `${BASE_URL}/serve-file.php?file=${encodeURIComponent(fileName)}`;
  }

  // Handle format: uploads/products/filename.webp
  if (imagePath.startsWith("uploads/products/")) {
    // Remove uploads/ prefix since serve-file.php adds /public/uploads/ automatically
    const fileName = imagePath.replace("uploads/", "");
    return `${BASE_URL}/serve-file.php?file=${encodeURIComponent(fileName)}`;
  }

  // For direct products/ path
  if (imagePath.startsWith("products/")) {
    return `${BASE_URL}/serve-file.php?file=${encodeURIComponent(imagePath)}`;
  }

  // Default fallback
  return `${BASE_URL}/assets/images/no-image.jpg`;
}

// DOM Ready
document.addEventListener("DOMContentLoaded", function () {
  initializeComponents();
  if (window.isLoggedIn === true || window.isLoggedIn === "true") {
    loadCartItemsFromServer();
  }
  updateWishlistCounterFromAPI();
  updateWishlistButtonsFromAPI();
  initializeBackToTop();
});

// Initialize all components
function initializeComponents() {
  // Initialize Swiper sliders
  if (document.querySelector(".hero-slider")) {
    initHeroSlider();
  }

  // Initialize product image galleries
  if (document.querySelector(".product-gallery")) {
    initProductGallery();
  }

  // Initialize tooltips
  initTooltips();

  // Initialize lazy loading
  initLazyLoading();
}

// Hero Slider
function initHeroSlider() {
  new Swiper(".hero-slider", {
    loop: true,
    autoplay: {
      delay: 5000,
      disableOnInteraction: false,
    },
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    effect: "fade",
    fadeEffect: {
      crossFade: true,
    },
  });
}

// Product Gallery
function initProductGallery() {
  // Product thumbnails slider
  const thumbsSwiper = new Swiper(".product-thumbs", {
    spaceBetween: 10,
    slidesPerView: 4,
    freeMode: true,
    watchSlidesProgress: true,
    breakpoints: {
      768: {
        slidesPerView: 6,
      },
    },
  });

  // Main product slider
  const mainSwiper = new Swiper(".product-gallery", {
    spaceBetween: 10,
    thumbs: {
      swiper: thumbsSwiper,
    },
    zoom: {
      maxRatio: 3,
    },
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
  });
}

// Cart Functions
function toggleCartSidebar() {
  const sidebar = document.getElementById("cartSidebar");
  const overlay = document.getElementById("cartSidebarOverlay");

  sidebar.classList.toggle("show");
  overlay.classList.toggle("show");

  if (sidebar.classList.contains("show")) {
    // Load fresh cart data from server when opening
    loadCartItemsFromServer();
    document.body.style.overflow = "hidden";
  } else {
    document.body.style.overflow = "";
  }
}

function closeCartSidebar() {
  const sidebar = document.getElementById("cartSidebar");
  const overlay = document.getElementById("cartSidebarOverlay");

  sidebar.classList.remove("show");
  overlay.classList.remove("show");
  document.body.style.overflow = "";
}

function viewCart() {
  window.location.href = `${BASE_URL}/cart`;
}

function checkout() {
  // Check if cart has items
  if (cart.length === 0) {
    showToast("Giỏ hàng trống", "warning");
    return;
  }
  window.location.href = `${BASE_URL}/checkout`;
}

function addToCart(productId, quantity = 1, variant = null, fromQuickView = false, eventSource = null) {
  // Prevent double execution
  if (window.addToCartInProgress) {
    console.log("AddToCart already in progress, skipping...");
    return;
  }

  window.addToCartInProgress = true;
  
  // Check if we need to close a modal after adding to cart
  const shouldCloseModal = fromQuickView || (window.location.hash === '#quickview');
  const modal = shouldCloseModal ? bootstrap.Modal.getInstance(
    document.getElementById("quickViewModal")
  ) : null;

  // Show loading state
  showLoading();

  // Make API call to add product to cart
  fetch(`${BASE_URL}/ajax/cart/add`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      product_id: productId,
      quantity: quantity,
      // include explicit variant_id so server-side code receives it even if it expects a top-level id
      variant_id:
        variant && (variant.id || variant.variant_id)
          ? variant.id || variant.variant_id
          : null,
      // include the full variant object for backwards-compatibility
      variant: variant || null,
      // include client-side price when available (helps server validation)
      price:
        variant && (variant.price || variant.sale_price)
          ? variant.price || variant.sale_price
          : null,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      hideLoading();
      window.addToCartInProgress = false; // Reset flag

      if (data.success) {
        // Show success message
        if (window.showSuccess) {
          window.showSuccess("Đã thêm sản phẩm vào giỏ hàng!");
        } else {
          showToast("Đã thêm sản phẩm vào giỏ hàng thành công!", "success");
        }

        // Update cart counter from server response
        updateCartCounter(data.cart_count);

        // Reload cart items from server
        loadCartItemsFromServer();
        
        // Close modal if needed (for QuickView)
        if (modal) {
          console.log("[DEBUG] Closing QuickView modal after cart add");
          modal.hide();
          
          // Clean up modal backdrop and body classes
          setTimeout(() => {
            const backdrops = document.querySelectorAll(".modal-backdrop");
            backdrops.forEach((backdrop) => backdrop.remove());
            document.body.style.overflow = "";
            document.body.classList.remove("modal-open");
            document.documentElement.classList.remove("modal-open");
          }, 100);
        }

        // Add animation effect (if eventSource exists)
        if (eventSource) {
          animateAddToCart(eventSource);
        }
      } else {
        showToast(data.message || "Có lỗi xảy ra!", "error");
      }
    })
    .catch((error) => {
      hideLoading();
      window.addToCartInProgress = false; // Reset flag on error
      console.error("Error:", error);
      showToast("Có lỗi xảy ra khi thêm vào giỏ hàng!", "error");
    });
}

function removeFromCart(key) {
  // Validate key
  if (key < 0 || key >= cart.length) {
    console.error("Invalid cart key:", key);
    return;
  }

  const item = cart[key];
  const productId = item.product_id || item.id; // Support both formats
  const variant = item.variant;

  // Send AJAX request to server to remove item from cart
  fetch(`${BASE_URL}/ajax/cart/remove`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      product_id: productId,
      variant: variant,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        // Remove from local cart array
        cart = cart.filter((item, index) => index != key);
        localStorage.setItem("cart", JSON.stringify(cart));

        // Reload cart from server to sync
        loadCartItemsFromServer();

        showToast("Đã xóa sản phẩm khỏi giỏ hàng!", "info");
      } else {
        console.error("Error removing from cart:", data.message);
        showToast(
          "Lỗi: " + (data.message || "Không thể xóa sản phẩm"),
          "error"
        );
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      showToast("Lỗi: Không thể kết nối server", "error");
    });
}

function updateCartQuantity(key, quantity) {
  if (quantity <= 0) {
    removeFromCart(key);
    return;
  }

  // Validate key
  if (key < 0 || key >= cart.length) {
    console.error("Invalid cart key:", key);
    return;
  }

  const item = cart[key];
  const productId = item.product_id || item.id; // Support both formats
  const variant = item.variant;

  // Send AJAX request to server to update quantity
  // If cart item has an id (server-side cart), send cart_key. Otherwise fall back to product_id (local cart format).
  const payload = { quantity: parseInt(quantity) };
  if (item && item.id) {
    payload.cart_key = item.id;
  } else if (productId) {
    payload.product_id = productId;
  }
  if (variant) payload.variant = variant;

  fetch(`${BASE_URL}/ajax/cart/update`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(payload),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        // Update local cart array
        if (cart[key]) {
          cart[key].quantity = parseInt(quantity);
          localStorage.setItem("cart", JSON.stringify(cart));
        }

        // Reload cart from server to sync
        loadCartItemsFromServer();
      } else {
        console.error("Error updating cart:", data.message);
        showToast(
          "Lỗi: " + (data.message || "Không thể cập nhật số lượng"),
          "error"
        );
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      showToast("Lỗi: Không thể kết nối server", "error");
    });
}

function loadCartItems() {
  const cartContainer = document.getElementById("cartItems");
  const cartTotal = document.getElementById("cartTotal");

  if (!cartContainer) return;

  if (cart.length === 0) {
    cartContainer.innerHTML = `
            <div class="empty-cart text-center py-4">
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <p class="text-muted">Giỏ hàng trống</p>
                <a href="${BASE_URL}/shop" class="btn btn-primary btn-sm">Mua Sắm Ngay</a>
            </div>
        `;
    if (cartTotal) cartTotal.textContent = "0₫";
    return;
  }

  let html = "";
  let total = 0;

  cart.forEach((item, index) => {
    const itemTotal = item.price * item.quantity;
    total += itemTotal;

    html += `
            <div class="cart-item mb-3 pb-3 border-bottom">
                <div class="row align-items-center">
                    <div class="col-3">
                        <img src="${getImageUrl(
                          item.product_image || item.image
                        )}"
                             alt="${
                               item.product_name || item.name
                             }" class="img-fluid rounded">
                    </div>
                    <div class="col-9">
                        <h6 class="mb-1">${item.product_name || item.name}</h6>
                        ${
                          item.variant
                            ? `<small class="text-muted">${item.variant}</small>`
                            : ""
                        }
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="quantity-controls">
                                <button class="btn btn-sm btn-outline-secondary"
                                        onclick="updateCartQuantity(${index}, ${
      item.quantity - 1
    })">-</button>
                                <span class="mx-2">${item.quantity}</span>
                                <button class="btn btn-sm btn-outline-secondary"
                                        onclick="updateCartQuantity(${index}, ${
      item.quantity + 1
    })">+</button>
                            </div>
                            <div class="item-price">
                                <strong>${formatCurrency(itemTotal)}</strong>
                                <button class="btn btn-sm btn-link text-danger p-0 ms-2"
                                        onclick="removeFromCart(${index})"
                                        title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
  });

  cartContainer.innerHTML = html;
  if (cartTotal) cartTotal.textContent = formatCurrency(total);
}

// DEPRECATED: Use cartManager.updateCartCounter() instead
/*
function updateCartCounter(count = null) {
	const counter = document.getElementById('cart-count');
	if (counter) {
		const cartCount =
			count !== null
				? count
				: cart.reduce((sum, item) => sum + item.quantity, 0);
		counter.textContent = cartCount;

		if (cartCount > 0) {
			counter.style.display = 'inline';
		} else {
			counter.style.display = 'none';
		}
	}
}
*/

// Use global cartManager instead
function updateCartCounter(count = null) {
  if (window.cartManager) {
    window.cartManager.updateCartCounter(count);
  } else {
    // Fallback for direct counter update
    const counter = document.getElementById("cart-count");
    if (counter && count !== null) {
      counter.textContent = count;
      counter.style.display = count > 0 ? "inline" : "none";
    }
  }
}

// Load cart items from server session
function loadCartItemsFromServer() {
  return fetch(`${BASE_URL}/ajax/cart/items`, {
    method: "GET",
    headers: {
      "Content-Type": "application/json",
    },
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        // Convert server cart format to client format
        cart = Object.values(data.items).map((item) => ({
          product_id: item.product_id,
          name: item.product_name,
          image: item.product_image,
          price: parseFloat(item.price),
          quantity: parseInt(item.quantity),
          variant: item.variant,
        }));

        // Update localStorage as backup
        localStorage.setItem("cart", JSON.stringify(cart));

        // Update UI
        updateCartCounter(data.cart_count);
        loadCartItems();
      }
      return data;
    })
    .catch((error) => {
      console.error("Error loading cart:", error);
      // Fallback to localStorage if server fails
      loadCartFromLocalStorage();
      return { success: false };
    });
}

// Fallback function to load from localStorage
function loadCartFromLocalStorage() {
  const savedCart = localStorage.getItem("cart");
  if (savedCart) {
    cart = JSON.parse(savedCart);
  }
  updateCartCounter();
  loadCartItems();
}

// Wishlist Functions
function toggleWishlist(productId) {
  // Check if user is logged in
  const isLoggedIn = document.body.getAttribute("data-logged-in") === "true";

  if (!isLoggedIn) {
    showToast("Vui lòng đăng nhập để sử dụng danh sách yêu thích!", "warning");
    window.location.href = `${BASE_URL}/login`;
    return;
  }

  // Show loading state
  const button = document.querySelector(
    `[onclick*="toggleWishlist(${productId})"]`
  );
  const icon = button?.querySelector("i");
  const originalClass = icon?.className;

  if (icon) {
    icon.className = "fas fa-spinner fa-spin";
  }

  // Call API to toggle wishlist
  fetch(`${BASE_URL}/ajax/wishlist/toggle`, {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `product_id=${productId}`,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        showToast(data.message, "success");

        // Update button state
        if (icon) {
          if (data.in_wishlist) {
            icon.className = "fas fa-heart";
            button.classList.add("text-danger");
          } else {
            icon.className = "far fa-heart";
            button.classList.remove("text-danger");
          }
        }

        // Update wishlist counter
        updateWishlistCounterFromAPI();
      } else {
        showToast(data.message || "Có lỗi xảy ra!", "error");
        // Restore original icon
        if (icon && originalClass) {
          icon.className = originalClass;
        }
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      showToast("Có lỗi xảy ra khi kết nối!", "error");
      // Restore original icon
      if (icon && originalClass) {
        icon.className = originalClass;
      }
    });
}

// New function to get wishlist count from API
function updateWishlistCounterFromAPI() {
  const isLoggedIn = document.body.getAttribute("data-logged-in") === "true";

  if (!isLoggedIn) {
    const counter = document.getElementById("wishlist-count");
    if (counter) {
      counter.textContent = "0";
      counter.style.display = "none";
    }
    return;
  }

  fetch(`${BASE_URL}/wishlist/count`)
    .then((response) => response.json())
    .then((data) => {
      const counter = document.getElementById("wishlist-count");
      if (counter) {
        counter.textContent = data.count || 0;

        if (data.count > 0) {
          counter.style.display = "inline";
        } else {
          counter.style.display = "none";
        }
      }
    })
    .catch((error) => {
      console.error("Error getting wishlist count:", error);
    });
}

// Update wishlist button states from database
function updateWishlistButtonsFromAPI() {
  const isLoggedIn = document.body.getAttribute("data-logged-in") === "true";

  if (!isLoggedIn) {
    // If not logged in, ensure all buttons show empty state
    document
      .querySelectorAll('[onclick*="toggleWishlist"]')
      .forEach((button) => {
        const icon = button.querySelector("i");
        if (icon) {
          icon.classList.remove("fas");
          icon.classList.add("far");
          button.classList.remove("text-danger");
        }
      });
    return;
  }

  // Get current wishlist from API and update button states
  fetch(`${BASE_URL}/wishlist`)
    .then((response) => response.text())
    .then((html) => {
      // Parse the HTML to extract wishlist product IDs
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, "text/html");
      const wishlistItems = doc.querySelectorAll("[data-product-id]");
      const wishlistProductIds = Array.from(wishlistItems).map((item) =>
        parseInt(item.getAttribute("data-product-id"))
      );

      // Update all wishlist buttons
      document
        .querySelectorAll('[onclick*="toggleWishlist"]')
        .forEach((button) => {
          const productId = parseInt(
            button.getAttribute("onclick").match(/\d+/)[0]
          );
          const icon = button.querySelector("i");

          if (wishlistProductIds.includes(productId)) {
            if (icon) {
              icon.classList.remove("far");
              icon.classList.add("fas");
              button.classList.add("text-danger");
            }
          } else {
            if (icon) {
              icon.classList.remove("fas");
              icon.classList.add("far");
              button.classList.remove("text-danger");
            }
          }
        });
    })
    .catch((error) => {
      console.error("Error updating wishlist buttons:", error);
    });
}

// Utility Functions
function formatCurrency(amount) {
  return new Intl.NumberFormat("vi-VN", {
    style: "currency",
    currency: "VND",
  })
    .format(amount)
    .replace("₫", "₫");
}

function showToast(message, type = "info") {
  // Use unified notification system if available
  if (window.showNotification) {
    const typeMap = {
      success: "success",
      error: "error",
      warning: "warning",
      info: "info",
    };
    window.showNotification(message, typeMap[type] || "info");
    return;
  }

  // Fallback to Bootstrap toast
  const toast = document.createElement("div");
  toast.className = `toast align-items-center text-white bg-${
    type === "error" ? "danger" : type
  } border-0`;
  toast.setAttribute("role", "alert");
  toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-${
                  type === "success"
                    ? "check-circle"
                    : type === "error"
                    ? "exclamation-circle"
                    : "info-circle"
                } me-2"></i>
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;

  // Add to toast container or create one
  let container = document.querySelector(".toast-container");
  if (!container) {
    container = document.createElement("div");
    container.className = "toast-container position-fixed top-0 end-0 p-3";
    container.style.zIndex = "1070";
    document.body.appendChild(container);
  }

  container.appendChild(toast);

  // Show toast
  const bsToast = new bootstrap.Toast(toast);
  bsToast.show();

  // Remove toast element after it's hidden
  toast.addEventListener("hidden.bs.toast", function () {
    container.removeChild(toast);
  });
}

function showLoading() {
  let loader = document.getElementById("globalLoader");
  if (!loader) {
    loader = document.createElement("div");
    loader.id = "globalLoader";
    loader.className = "global-loader";
    loader.innerHTML = `
            <div class="loader-content">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Đang tải...</p>
            </div>
        `;
    document.body.appendChild(loader);
  }
  loader.style.display = "flex";
}

function hideLoading() {
  const loader = document.getElementById("globalLoader");
  if (loader) {
    loader.style.display = "none";
  }
}

function animateAddToCart(button) {
  button.classList.add("animate-pulse");
  setTimeout(() => {
    button.classList.remove("animate-pulse");
  }, 600);
}

// Initialize tooltips
function initTooltips() {
  const tooltipTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
  );
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });
}

// Initialize lazy loading
function initLazyLoading() {
  if ("IntersectionObserver" in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const img = entry.target;
          img.src = img.dataset.src;
          img.classList.remove("lazy");
          imageObserver.unobserve(img);
        }
      });
    });

    document.querySelectorAll("img[data-src]").forEach((img) => {
      imageObserver.observe(img);
    });
  }
}

// Back to top button
function initializeBackToTop() {
  const backToTopBtn = document.getElementById("backToTop");

  if (backToTopBtn) {
    window.addEventListener("scroll", function () {
      if (window.pageYOffset > 300) {
        backToTopBtn.classList.add("show");
      } else {
        backToTopBtn.classList.remove("show");
      }
    });

    backToTopBtn.addEventListener("click", function () {
      window.scrollTo({
        top: 0,
        behavior: "smooth",
      });
    });
  }
}

// Search functionality
function initializeSearch() {
  const searchInput = document.querySelector('input[name="q"]');
  if (searchInput) {
    // Add search suggestions
    searchInput.addEventListener(
      "input",
      debounce(function () {
        const query = this.value;
        if (query.length >= 2) {
          fetchSearchSuggestions(query);
        }
      }, 300)
    );
  }
}

function fetchSearchSuggestions(query) {
  fetch(`${BASE_URL}/api/search/suggestions?q=${encodeURIComponent(query)}`)
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        showSearchSuggestions(data.suggestions);
      }
    })
    .catch((error) => console.error("Search suggestions error:", error));
}

// Debounce function
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

// Add custom styles for loading and animations
const customCSS = `
.global-loader {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255,255,255,0.9);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.loader-content {
    text-align: center;
}

.animate-pulse {
    animation: pulse 0.6s ease-in-out;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

/* Quick View Modal Styles */
#quickViewModal .modal-dialog {
    max-width: 90%;
    width: 100%;
    margin: 1rem auto;
}

@media (min-width: 992px) {
    #quickViewModal .modal-dialog {
        max-width: 1000px;
    }
}

#quickViewModal .modal-body {
    padding: 2rem;
}

#quickViewModal .product-actions {
    margin-top: 1.5rem !important;
    padding-top: 1rem !important;
    border-top: 1px solid #eee !important;
    display: block !important;
    visibility: visible !important;
    width: 100% !important;
    height: auto !important;
    overflow: visible !important;
    position: static !important;
    z-index: 10 !important;
    background: white !important;
    padding: 1rem !important;
    margin-bottom: 1rem !important;
    opacity: 1 !important;
    flex-direction: row !important;
    justify-content: flex-start !important;
    gap: 0.5rem !important;
    top: auto !important;
    right: auto !important;
    left: auto !important;
    bottom: auto !important;
    transform: none !important;
}

#quickViewModal .product-actions .btn {
    width: auto !important;
    height: 45px !important;
    padding: 0.75rem 1.5rem !important;
    font-size: 1rem !important;
    font-weight: 600 !important;
    background: var(--bs-btn-bg) !important;
    backdrop-filter: none !important;
    display: inline-block !important;
    align-items: center !important;
    justify-content: center !important;
    position: relative !important;
    z-index: 11 !important;
    opacity: 1 !important;
    visibility: visible !important;
    border-radius: 0.375rem !important;
    line-height: 1.5 !important;
    text-align: center !important;
    vertical-align: middle !important;
    border: 1px solid transparent !important;
    cursor: pointer !important;
}

#quickViewModal .product-actions .btn-danger {
    background: #dc3545 !important;
    border-color: #dc3545 !important;
    color: white !important;
}

#quickViewModal .product-actions .btn-outline-secondary {
    background: transparent !important;
    border-color: #6c757d !important;
    color: #6c757d !important;
}

#quickViewModal .product-actions .btn:hover {
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 8px rgba(0,0,0,0.15) !important;
}

/* Force show on all screen sizes - Override global .product-actions */
#quickViewModal .product-actions {
    display: block !important;
    visibility: visible !important;
    height: auto !important;
    overflow: visible !important;
    opacity: 1 !important;
    position: static !important;
    flex-direction: row !important;
    justify-content: flex-start !important;
    gap: 0.5rem !important;
    top: auto !important;
    right: auto !important;
    transform: none !important;
}

@media (min-width: 768px) {
    #quickViewModal .product-actions {
        display: block !important;
        visibility: visible !important;
        height: auto !important;
        overflow: visible !important;
        opacity: 1 !important;
        position: static !important;
        flex-direction: row !important;
        justify-content: flex-start !important;
        gap: 0.5rem !important;
        top: auto !important;
        right: auto !important;
        transform: none !important;
    }

    #quickViewModal .product-actions .btn {
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
        margin-right: 0.5rem !important;
        height: 45px !important;
        min-height: 45px !important;
        position: static !important;
        top: auto !important;
        right: auto !important;
        transform: none !important;
    }
}

/* Add explicit styles to override any conflicting CSS */
#quickViewModal .product-actions .btn-danger {
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;
    color: #ffffff !important;
}

#quickViewModal .product-actions .btn-outline-secondary {
    background-color: transparent !important;
    border-color: #6c757d !important;
    color: #6c757d !important;
}

#quickViewModal .product-actions .btn-danger:hover {
    background-color: #c82333 !important;
    border-color: #bd2130 !important;
    color: #ffffff !important;
}

#quickViewModal .product-actions .btn-outline-secondary:hover {
    background-color: #6c757d !important;
    border-color: #6c757d !important;
    color: #ffffff !important;
}

@media (max-width: 767px) {
    #quickViewModal .product-actions .btn {
        width: 100% !important;
        margin-bottom: 0.5rem !important;
        margin-right: 0 !important;
    }

    #quickViewModal .modal-dialog {
        max-width: 95%;
    }
}

/* Very specific selectors to override any global .product-actions CSS */
#quickViewModal .modal-body .product-actions,
#quickViewModal .modal-content .product-actions,
.modal#quickViewModal .product-actions {
    position: static !important;
    opacity: 1 !important;
    top: auto !important;
    right: auto !important;
    bottom: auto !important;
    left: auto !important;
    transform: none !important;
    display: block !important;
    visibility: visible !important;
    flex-direction: row !important;
    justify-content: flex-start !important;
    gap: 0.5rem !important;
    background: white !important;
    padding: 1rem !important;
    margin-top: 1.5rem !important;
    border-top: 1px solid #eee !important;
}

/* Ensure modal backdrop is properly handled */
.modal-backdrop {
    transition: opacity 0.1zone linear;
}

.modal-backdrop.fade {
    opacity: 0;
}

.modal-backdrop.show {
    opacity: 0.5;
}

/* Force remove backdrop when needed */
body:not(.modal-open) .modal-backdrop {
    display: none !important;
}

.color-option {
  border-radius: 999px !important;
  padding: 4px 6px !important;
  min-width: 32px;
  min-height: 32px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

/* Out-of-stock color styling: visually dim but remain clickable */
.color-out-of-stock {
  opacity: 0.5 !important;
  pointer-events: auto !important; /* ensure still clickable */
  cursor: pointer !important;
  filter: grayscale(30%);
}

/* Quantity control styles */
.quantity-control {
  border: 1px solid #ced4da;
  border-radius: 40px;
  overflow: hidden;
  background: #fff;
  width: 260px;
}
.quantity-control .btn-qty {
  flex: 0 0 70px;
  max-width: 70px;
  font-weight: 500;
  border: none !important;
  border-radius: 0 !important;
  background: #fff !important;
  box-shadow: none !important;
}
.quantity-control .btn-qty:first-child { border-right: 1px solid #e5e7eb !important; }
.quantity-control .btn-qty:last-child { border-left: 1px solid #e5e7eb !important; }
.quantity-control .btn-qty:hover:not(:disabled) { background:#f8f9fa !important; }
.quantity-control .btn-qty:active { transform: scale(0.96); }
.quantity-control .btn-qty:disabled { opacity: 0.4; cursor: not-allowed !important; }
.quantity-control input[type=number] {
  border: none !important;
  box-shadow: none !important;
  outline: none !important;
  background: #fff !important;
  font-weight: 500;
  font-size: 16px;
}
.quantity-control input[type=number]:focus { outline: none; box-shadow: none; }
/* Remove default spinners */
.quantity-control input[type=number]::-webkit-outer-spin-button,
.quantity-control input[type=number]::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
.quantity-control input[type=number] { -moz-appearance: textfield; }
`;

// Add custom CSS to head
const styleSheet = document.createElement("style");
styleSheet.textContent = customCSS;
document.head.appendChild(styleSheet);

/**
 * Quick View Modal Functions
 */
function quickView(productId) {
  console.log("QuickView for product ID:", productId);

  // Ensure cart is loaded before showing product (so we can compute remaining stock accurately)
  const preloadCart = () => {
    try {
      return loadCartItemsFromServer()
        .catch(() => {}) // Ignore errors
        .finally(() => showQuickViewModal());
    } catch (e) {
      console.warn("Failed to preload cart, continuing with modal", e);
      showQuickViewModal();
      return Promise.resolve();
    }
  };

  // Function to show the modal after cart is loaded
  const showQuickViewModal = () => {
    console.log("Showing modal after cart load");
    // Show modal immediately with loading state
    const modal = new bootstrap.Modal(document.getElementById("quickViewModal"));

    // Add event listener to clean up when modal is hidden
    const modalElement = document.getElementById("quickViewModal");
    modalElement.addEventListener(
      "hidden.bs.modal",
      function () {
        // Clean up any remaining backdrops
        const backdrops = document.querySelectorAll(".modal-backdrop");
        backdrops.forEach((backdrop) => backdrop.remove());

        // Ensure body classes and styles are restored
        document.body.style.overflow = "";
        document.body.classList.remove("modal-open");
        document.documentElement.classList.remove("modal-open");

        // Reset selected variant
        selectedVariant = null;
      },
      { once: true }
    ); // Use once: true to automatically remove the listener after first use

    modal.show();

    // Set loading content
    document.getElementById("quickViewContent").innerHTML = `
      <div class="d-flex justify-content-center align-items-center" style="min-height: 300px;">
          <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Đang tải...</span>
          </div>
      </div>
    `;
  };
  
  // Start preloading cart then show modal
  preloadCart();

  // Fetch product data
  fetch(`${BASE_URL}/ajax/product/quickview?id=${productId}`)
    .then((response) => response.json())
    .then((data) => {
      console.log("QuickView API Response:", data);

      if (data.success) {
        const productData = data.product || data.data;
        console.log("Product data:", productData);

        if (productData) {
          // Ensure we have the latest cart from server before rendering so reserved quantities are reflected
          try {
            const p = loadCartItemsFromServer();
            if (p && typeof p.then === 'function') {
              p.catch(() => {}).finally(() => renderQuickViewContent(productData));
            } else {
              renderQuickViewContent(productData);
            }
          } catch (e) {
            renderQuickViewContent(productData);
          }
        } else {
          throw new Error("Không có dữ liệu sản phẩm");
        }
      } else {
        throw new Error(data.message || "Không thể tải thông tin sản phẩm");
      }
    })
    .catch((error) => {
      console.error("QuickView error:", error);

      // For testing - add fallback mock data
      if (productId == 1) {
        console.log("Using fallback mock data for testing");

        renderQuickViewContent(mockProduct);
      } else {
        document.getElementById("quickViewContent").innerHTML = `
					<div class="alert alert-danger text-center">
						<i class="fas fa-exclamation-triangle me-2"></i>
						${error.message || "Có lỗi xảy ra khi tải sản phẩm"}
					</div>
				`;
      }
    });
}

function renderQuickViewContent(product) {
  console.log("renderQuickViewContent called with:", product);

  // Validate product data
  if (!product) {
    console.error("Product is undefined in renderQuickViewContent");
    document.getElementById("quickViewContent").innerHTML = `
			<div class="alert alert-danger text-center">
				<i class="fas fa-exclamation-triangle me-2"></i>
				Không có dữ liệu sản phẩm
			</div>
		`;
    return;
  }

  // Store product data globally for variant handling
  window.currentQuickViewProduct = product;

  const imageUrl = getImageUrl(product.featured_image || product.image);
  const currentPrice = product.sale_price || product.price;
  const originalPrice = product.sale_price ? product.price : null;

  // Calculate discount percentage
  let discountBadge = "";
  if (originalPrice && product.sale_price) {
    const discount = Math.round(
      ((originalPrice - product.sale_price) / originalPrice) * 100
    );
    discountBadge = `<span class="badge bg-danger position-absolute" style="top: 10px; left: 10px;">-${discount}%</span>`;
  }

  // Build variants HTML (group by color, dedupe sizes per color)
  let variantsHTML = "";
  if (product.variants && product.variants.length > 0) {
    // Group variants by color
    const variantsByColor = {};
    product.variants.forEach((v) => {
      const cname = (v.color || "Default").trim();
      if (!variantsByColor[cname]) variantsByColor[cname] = [];
      variantsByColor[cname].push(v);
    });

    // Build color list with color_code
    const colorList = Object.keys(variantsByColor).map((name) => ({
      name,
      color_code:
        (variantsByColor[name].find((x) => x.color_code) || {}).color_code ||
        "#ccc",
    }));

    // Choose preferred color: first color that has stock, otherwise first color
    let preferredColor = null;
    for (const c of colorList) {
      const any = (variantsByColor[c.name] || []).some(
        (v) => (v.stock_quantity || v.stock) > 0
      );
      if (any) {
        preferredColor = c.name;
        break;
      }
    }
    if (!preferredColor && colorList.length) preferredColor = colorList[0].name;

    // Render sizes for preferredColor (deduplicated by size)
    // Helper function to calculate cart quantity for a variant
    function getCartQtyFor(productId, variantId) {
      try {
        return (cart || []).reduce((sum, it) => {
          const pid = it.product_id || it.id || it.productId || null;
          if (!pid || String(pid) !== String(productId)) return sum;
          
          if (variantId) {
            const v = it.variant || {};
            const vids = String(v.id || v.variant_id || '');
            return (vids === String(variantId)) ? sum + (parseInt(it.quantity) || 0) : sum;
          }
          
          if (!it.variant || !it.variant.id) return sum + (parseInt(it.quantity) || 0);
          return sum;
        }, 0);
      } catch (e) {
        console.warn("Error calculating cart qty", e);
        return 0;
      }
    }
    
    // Use available_stock directly from server instead of recalculating
    (product.variants || []).forEach(v => {
      // Use server-provided available_stock if available, otherwise keep old logic as fallback
      if (v.available_stock !== undefined) {
        v._available_stock = parseInt(v.available_stock);
      } else {
        const baseStock = parseInt(v.stock_quantity || v.stock || 0);
        // Don't subtract cart quantity again since server already did that
        v._available_stock = baseStock;
      }
      console.log(`[DEBUG] Variant ${v.id} (${v.color} ${v.size}): available=${v._available_stock}`);
    });
    
    const sizesForPref = variantsByColor[preferredColor] || [];
    const seen = new Set();
    const sizeButtons = sizesForPref
      .filter((v) => {
        const k = (v.size || "One Size").trim();
        if (seen.has(k)) return false;
        seen.add(k);
        return true;
      })
      .map((variant, index) => {
        // Use calculated available stock
        const outOfStock = !(variant._available_stock > 0);
        return `
        <button type="button" class="btn btn-outline-secondary size-option ${
          !outOfStock && index === 0 ? "active" : ""
        }" data-variant-id="${variant.id}" data-size="${
          variant.size
        }" data-price="${variant.price}" data-color="${
          variant.color
        }" data-stock="${variant._available_stock || 0}" data-base-stock="${
          variant.stock_quantity || variant.stock || 0
        }" onclick="selectSize('${variant.id}', '${variant.size}', ${
          variant.price
        }, '${variant.color}')" ${
          outOfStock ? 'disabled style="opacity:0.5;cursor:not-allowed;"' : ""
        }>
          ${variant.size}
        </button>
      `;
      })
      .join("");

    // Render color buttons
    const colorButtons = colorList
      .map(
        (colorObj, idx) => `
      <button type="button" class="btn btn-outline-secondary color-option ${
        colorObj.name === preferredColor ? "active" : ""
      }" data-color="${colorObj.name}" onclick="selectColor('${
          colorObj.name
        }')" title="${colorObj.name}">
        <span class="color-swatch" style="display:inline-block;width:18px;height:18px;border-radius:50%;background:${
          colorObj.color_code
        };border:1px solid #ccc;vertical-align:middle;"></span>
      </button>
    `
      )
      .join("");

    variantsHTML = `
      <div class="variant-selection mb-3">
        <h6>Chọn Màu Sắc:</h6>
        <div class="color-options mb-3">
          ${colorButtons}
        </div>
        <h6>Chọn Kích Thước:</h6>
        <div class="size-options mb-3" id="sizeOptions">
          ${sizeButtons}
        </div>
      </div>
    `;
  }

  // Create the complete modal content
  // Determine if the product has any stock (use this for initial Add-to-Cart state)
  let hasAnyStock = false;

  // helper: compute quantity of this product (and optional variant) currently in the cart
  function cartQuantityFor(productId, variantId = null) {
    console.log("[DEBUG] cartQuantityFor checking", productId, variantId);
    try {
      if (!cart || !Array.isArray(cart)) {
        console.log("[DEBUG] cart is not available:", typeof cart);
        return 0;
      }
      
      const result = cart.reduce((sum, it) => {
        const pid = it.product_id || it.id || it.productId || null;
        if (!pid || String(pid) !== String(productId)) {
          return sum;
        }
        
        // If variantId provided, match variant id; otherwise count only non-variant items
        if (variantId) {
          const v = it.variant || {};
          const vids = String(v.id || v.variant_id || '');
          if (vids === String(variantId)) {
            console.log("[DEBUG] Found matching variant in cart:", it.quantity, "of", variantId);
            return sum + (parseInt(it.quantity) || 0);
          }
          return sum;
        }
        
        // No variantId: count items that either have no variant or have a falsy variant id
        if (!it.variant || !it.variant.id) {
          console.log("[DEBUG] Found matching product in cart:", it.quantity, "of", productId);
          return sum + (parseInt(it.quantity) || 0);
        }
        
        return sum;
      }, 0);
      
      console.log("[DEBUG] cartQuantityFor result:", result);
      return result;
    } catch (e) {
      console.error("[DEBUG] Error calculating cart quantity:", e);
      return 0;
    }
  }

  if (product.variants && product.variants.length > 0) {
    console.log("[DEBUG] Processing product with variants");
    // For products with variants, check if any variant has stock after subtracting items in cart
    try {
      if (typeof firstColor !== "undefined" && firstColor) {
        console.log("[DEBUG] Using firstColor for variants:", firstColor);
        const variantsForFirstColor = variantsByColor[firstColor] || [];
        console.log("[DEBUG] Variants for first color:", variantsForFirstColor.length);
        
        hasAnyStock = variantsForFirstColor.some((v) => {
          // Use server-provided available_stock if available
          if (v.available_stock !== undefined) {
            v._available_stock = parseInt(v.available_stock);
          } else {
            // Fallback to old logic
            v._available_stock = parseInt(v.stock_quantity || v.stock || 0) || 0;
          }
          
          console.log(`[DEBUG] Variant ${v.id}: available=${v._available_stock}`);
          return v._available_stock > 0;
        });
      } else {
        console.log("[DEBUG] Checking all variants");
        hasAnyStock = !!product.variants.some((v) => {
          // Use server-provided available_stock if available
          if (v.available_stock !== undefined) {
            v._available_stock = parseInt(v.available_stock);
          } else {
            // Fallback to old logic
            v._available_stock = parseInt(v.stock_quantity || v.stock || 0) || 0;
          }
          
          console.log(`[DEBUG] Variant ${v.id}: available=${v._available_stock}`);
          return v._available_stock > 0;
        });
      }
      
      console.log("[DEBUG] hasAnyStock after variant check:", hasAnyStock);
    } catch (e) {
      console.error("[DEBUG] Error in variant stock check:", e);
      // fallback to original check if anything goes wrong
      hasAnyStock = !!product.variants.some((v) => (v.stock_quantity || v.stock) > 0);
    }
  } else {
    console.log("[DEBUG] Processing product without variants");
    // For products without variants, use available_stock from server
    try {
      // Use server-provided available_stock if available
      if (product.available_stock !== undefined) {
        product._available_stock = parseInt(product.available_stock);
      } else {
        // Fallback to old logic if server didn't provide available_stock
        const base = parseInt(product.stock_quantity || product.stock || 0) || 0;
        product._available_stock = base; // Don't subtract cart quantity again
      }
      
      // Expose available stock for badge display
      hasAnyStock = !!(product.in_stock && product._available_stock > 0);
      
      console.log("[DEBUG] Non-variant product stock:", {
        base: base,
        reserved: reserved,
        remaining: remaining,
        hasAnyStock: hasAnyStock
      });
    } catch (e) {
      console.error("[DEBUG] Error in product stock check:", e);
      hasAnyStock = !!(
        product.in_stock && (product.stock_quantity > 0 || product.stock > 0)
      );
    }
  }

  const content = `
        <div class="row g-4">
            <div class="col-lg-6 col-md-12 mb-4 mb-lg-0">
                <div class="product-image-container position-relative">
                    ${discountBadge}
                    <img src="${imageUrl}" alt="${
    product.name
  }" class="img-fluid rounded w-100" style="max-height: 500px; object-fit: cover;">
                </div>
            </div>
            <div class="col-lg-6 col-md-12">
                <div class="product-details h-100 d-flex flex-column">
                    <h4 class="product-title mb-2">${product.name}</h4>

                    <div class="product-price mb-3">
                        <span class="current-price h4 text-danger fw-bold">${formatCurrency(
                          currentPrice
                        )}</span>
                        ${
                          originalPrice
                            ? `<span class="original-price text-muted text-decoration-line-through ms-2">${formatCurrency(
                                originalPrice
                              )}</span>`
                            : ""
                        }
                        <div class="mt-2">
                          <span class="badge ${
                            hasAnyStock ? "bg-success" : "bg-danger"
                          } fs-6">
                            ${
                              hasAnyStock
                                ? '<i class="fas fa-check-circle me-1"></i>Còn hàng'
                                : '<i class="fas fa-times-circle me-1"></i>Hết hàng'
                            }
                            ${
                              hasAnyStock
                                ? product._available_stock 
                                  ? ` (${product._available_stock})`
                                  : product.stock_quantity 
                                    ? ` (${product.stock_quantity})`
                                    : ""
                                : ""
                            }
                          </span>
                        </div>
                    </div>

                    ${
                      product.rating
                        ? `
                        <div class="product-rating mb-3">
                            <div class="stars">
                                ${generateStars(product.rating)}
                                <span class="ms-2 text-muted">(${
                                  product.review_count || 0
                                } đánh giá)</span>
                            </div>
                        </div>
                    `
                        : ""
                    }

                    ${
                      product.description
                        ? `
                        <div class="product-description mb-3">
                            <p class="text-muted">${product.description.substring(
                              0,
                              200
                            )}${
                            product.description.length > 200 ? "..." : ""
                          }</p>
                        </div>
                    `
                        : ""
                    }

                    ${variantsHTML}

                    <div class="quantity-selection mb-3">
                        <label class="form-label">Số Lượng:</label>
                        <div class="quantity-control d-flex align-items-stretch" style="width:260px;">
                          <button type="button" class="btn btn-outline-secondary btn-qty" data-dir="-" aria-label="Giảm">-</button>
                          <input id="quantityInput" type="number" class="form-control text-center" value="1" min="1" style="border-radius:0 !important;">
                          <button type="button" class="btn btn-outline-secondary btn-qty" data-dir="+" aria-label="Tăng">+</button>
                        </div>
                    </div>

                    <div class="product-actions d-block mt-auto">
            <button id="quickview-add-button" class="${
              hasAnyStock
                ? "btn btn-danger btn-lg me-2 mb-2 quickview-add-to-cart"
                : "btn btn-secondary btn-lg me-2 mb-2"
            }" onclick="addToCartFromQuickView(${
    product.id
  })" ${hasAnyStock ? '' : 'disabled="disabled"'} aria-disabled="${hasAnyStock ? 'false' : 'true'}" style="${
    hasAnyStock ? '' : 'cursor: not-allowed !important; pointer-events: none !important; background-color: #6c757d !important; border-color: #6c757d !important;'
  }">
        ${
          hasAnyStock
            ? '<i class="fas fa-shopping-cart me-2"></i>Thêm vào giỏ hàng'
            : '<i class="fas fa-times me-2"></i>Hết Hàng'
        }
      </button>
                        <button class="btn btn-outline-secondary btn-lg mb-2" onclick="toggleWishlist(${
                          product.id
                        })">
                            <i class="far fa-heart"></i>
                        </button>
                    </div>

                    <div class="product-meta mt-3">
                        ${
                          product.category_name
                            ? `<small class="text-muted">Danh mục: ${product.category_name}</small>`
                            : ""
                        }
                    </div>
                </div>
            </div>
        </div>
    `;

  // Set content to modal
  document.getElementById("quickViewContent").innerHTML = content;

  // Prevent automatic focus (blinking caret) on quantity input when modal opens
  setTimeout(() => {
    const qtyAuto = document.getElementById('quantityInput');
    if (qtyAuto && document.activeElement === qtyAuto) {
      qtyAuto.blur();
    }
  }, 10);

  // Clean, robust quantity control init (no inline onclick reliance)
  (function setupQuickViewQuantity(productData){
    setTimeout(() => {
      const wrapper = document.querySelector('#quickViewContent .quantity-control');
      if(!wrapper) return;
      const input = wrapper.querySelector('#quantityInput');
      if(!input) return;
      // compute max from selected variant or product
      let max = 0;
      
      // Helper to compute cart quantity for a product/variant
      function getCartQty(pid, vid) {
        try {
          return (cart || []).reduce((sum, it) => {
            const itemPid = it.product_id || it.id || it.productId || null;
            if (!itemPid || String(itemPid) !== String(pid)) return sum;
            // If variant ID provided, only count matching variant
            if (vid) {
              const v = it.variant || {};
              const vids = String(v.id || v.variant_id || '');
              return (vids === String(vid)) ? sum + (parseInt(it.quantity) || 0) : sum;
            }
            // No variant ID, count all non-variant or falsy variant.id items
            if (!it.variant || !it.variant.id) return sum + (parseInt(it.quantity) || 0);
            return sum;
          }, 0);
        } catch (e) {
          return 0;
        }
      }
      
      if (window.selectedVariant && (window.selectedVariant.stock_quantity || window.selectedVariant.stock)) {
        const baseStock = parseInt(window.selectedVariant.stock_quantity || window.selectedVariant.stock || 0);
        const inCart = getCartQty(productData.id, window.selectedVariant.id);
        max = Math.max(0, baseStock - inCart);
      }
      
      if(!max && productData){
        if(productData.variants && productData.variants.length){
          // Find variant with most available stock after cart subtraction
          let bestVariant = null;
          let bestRemaining = 0;
          
          for (const v of productData.variants) {
            const baseStock = parseInt(v.stock_quantity || v.stock || 0);
            const inCart = getCartQty(productData.id, v.id);
            const remaining = Math.max(0, baseStock - inCart);
            
            if (remaining > bestRemaining) {
              bestRemaining = remaining;
              bestVariant = v;
            }
          }
          
          if (bestVariant) max = bestRemaining;
        } else {
          // Simple product - use _available_stock if calculated earlier
          max = productData._available_stock !== undefined 
            ? productData._available_stock
            : parseInt(productData.stock_quantity || productData.stock || 0) || 0;
        }
      }
      if(!max || max<1) max = 999; // fallback large
      input.max = max;
      input.readOnly = false;

      function clamp(val){
        const min = parseInt(input.min)||1;
        const m = parseInt(input.max)||999;
        let v = parseInt(val)||min;
        if(v<min) v=min; if(v>m) v=m; return v;
      }
      function update(){
        input.value = clamp(input.value);
        const dec = wrapper.querySelector('[data-dir="-"]');
        const inc = wrapper.querySelector('[data-dir="+"]');
        const v = parseInt(input.value)||1;
        const m = parseInt(input.max)||999;
        if(dec){ dec.disabled = v<=1; }
        if(inc){ inc.disabled = v>=m; }
      }
      if(!wrapper._bound){
        wrapper.addEventListener('click', e => {
          const btn = e.target.closest('.btn-qty');
          if(!btn) return;
            const dir = btn.getAttribute('data-dir');
            let cur = parseInt(input.value)||1;
            cur += (dir === '-' ? -1 : 1);
            input.value = cur;
            update();
        });
        input.addEventListener('input', () => update());
        input.addEventListener('blur', () => update());
        input.addEventListener('keydown', e => {
          if(e.key==='ArrowUp'){ e.preventDefault(); input.value = parseInt(input.value||1)+1; update(); }
          if(e.key==='ArrowDown'){ e.preventDefault(); input.value = parseInt(input.value||1)-1; update(); }
        });
        wrapper._bound = true;
      }
      update();
    }, 15);
  })(product);

  // Expose product data and initialize color/size UI in the modal
  try {
    window.currentQuickViewProduct = product;
    // pick preferred color (first with stock) if available
    setTimeout(() => {
      try {
        const colorBtns = Array.from(
          document.querySelectorAll(
            "#quickViewContent .color-option, .color-options .color-option"
          )
        );
        let chosen = null;
        if (colorBtns.length) {
          for (const btn of colorBtns) {
            const name = btn.getAttribute("data-color");
            const variantsForColor = (product.variants || []).filter(
              (v) => (v.color || "Default").trim() === (name || "Default")
            );
            const hasStock = variantsForColor.some(
              (v) => (v.stock_quantity || v.stock) > 0
            );
            if (hasStock) {
              chosen = name;
              break;
            }
          }
          if (!chosen) chosen = colorBtns[0].getAttribute("data-color");
          if (chosen) {
            try {
              selectColor(chosen);
            } catch (e) {
              console.warn("auto selectColor failed", e);
            }
          }
        }
      } catch (e) {
        console.warn("init quickview selection failed", e);
      }
    }, 30);
  } catch (e) {}

  // Expose current product data for other handlers and initialize selected variant UI
  try {
    window.currentQuickViewProduct = product;
  } catch (e) {}
  // Ensure the displayed price reflects a concrete auto-selected variant (choose first in-stock variant)
  try {
    // prefer a variant that has stock
    let initialCandidate = null;
    if (product.variants && product.variants.length) {
      initialCandidate =
        product.variants.find((v) => (v.stock_quantity || v.stock) > 0) ||
        product.variants[0];
    }
    if (initialCandidate) {
      // Only sync internal selection (don't mutate DOM) so we don't override user clicks.
      selectedVariant = {
        id: initialCandidate.id,
        size: initialCandidate.size,
        price: parseFloat(initialCandidate.price) || 0,
        color: initialCandidate.color,
        stock_quantity: parseInt(
          initialCandidate.stock_quantity || initialCandidate.stock || 0
        ),
      };
      // Update qty input max (no DOM active changes)
      try {
        const qtyInput = document.getElementById("quantityInput");
        if (qtyInput) {
          const maxQty =
            selectedVariant.stock_quantity || parseInt(product.stock) || 10;
          qtyInput.max = maxQty;
          if (parseInt(qtyInput.value) > maxQty) qtyInput.value = maxQty;
        }
      } catch (e) {}
    }
  } catch (e) {
    console.warn("initial quickview selection error", e);
  }

  // Update add to cart button state
  setTimeout(() => {
    console.log("[DEBUG] Button update - hasAnyStock:", hasAnyStock, 
                "available_stock:", product._available_stock,
                "cart contents:", JSON.stringify(cart));
                
    // Apply direct button updates for immediate visual feedback
    const addToCartBtn = document.querySelector("#quickview-add-button") || 
                         document.querySelector(".quickview-add-to-cart");
    
    if (addToCartBtn) {
      console.log("[DEBUG] Directly updating button state for visibility:", hasAnyStock ? "IN STOCK" : "OUT OF STOCK");
      
      // Force button ID to be set if needed
      if (!addToCartBtn.id) {
        addToCartBtn.id = "quickview-add-button";
      }
      
      // Explicit visual updates
      if (!hasAnyStock) {
        // OUT OF STOCK - Apply styling directly to button
        addToCartBtn.disabled = true;
        addToCartBtn.setAttribute("aria-disabled", "true");
        addToCartBtn.setAttribute("disabled", "disabled");
        addToCartBtn.classList.remove("btn-danger");
        addToCartBtn.classList.add("btn-secondary");
        addToCartBtn.innerHTML = '<i class="fas fa-times me-2"></i>Hết Hàng';
        
        // Force style overrides to ensure visibility
        addToCartBtn.style.cssText = `
          opacity: 1 !important;
          display: inline-block !important;
          visibility: visible !important;
          background-color: #6c757d !important;
          color: white !important;
          cursor: not-allowed !important;
          pointer-events: none !important;
        `;
      } else {
        // IN STOCK
        addToCartBtn.disabled = false;
        addToCartBtn.removeAttribute("aria-disabled");
        addToCartBtn.removeAttribute("disabled");
        addToCartBtn.classList.remove("btn-secondary");
        addToCartBtn.classList.add("btn-danger");
        addToCartBtn.innerHTML = '<i class="fas fa-shopping-cart me-2"></i>Thêm vào giỏ hàng';
        
        // Reset any inline styles
        addToCartBtn.style.cssText = '';
      }
    }
    
    // Also call updateAddToCartState for consistency with rest of system
    console.log("[DEBUG] Calling updateAddToCartState for full system update");
    updateAddToCartState(hasAnyStock);

    // Ensure quantity controls (+ / -) reflect the actual max after render
    try {
      const qtyInput = document.getElementById("quantityInput");
      if (qtyInput) {
        const maxQty =
          parseInt(qtyInput.getAttribute("max")) ||
          parseInt(qtyInput.max) ||
          10;
        const cur = parseInt(qtyInput.value) || 1;
        const btnInc = qtyInput.parentElement.querySelector(
          'button[onclick*="changeQuantity(1)"]'
        );
        const btnDec = qtyInput.parentElement.querySelector(
          'button[onclick*="changeQuantity(-1)"]'
        );
        // enable/disable based on current value vs max
        if (btnInc) btnInc.disabled = cur >= maxQty;
        if (btnDec) btnDec.disabled = cur <= 1;
        // ensure input not disabled when stock available
        qtyInput.disabled = maxQty <= 0;
      }
    } catch (err) {
      console.warn("sync quickview qty controls failed", err);
    }
  }, 50);

  // Debug: Log to console để kiểm tra
  console.log("Quick view content rendered successfully");
  console.log(
    "Modal height:",
    document.getElementById("quickViewContent").scrollHeight
  );

  // Debug: Check if buttons exist and styles
  setTimeout(() => {
    const addToCartBtn = document.querySelector(
      "#quickViewModal .product-actions .btn-danger"
    );
    const wishlistBtn = document.querySelector(
      "#quickViewModal .product-actions .btn-outline-secondary"
    );
    const productActions = document.querySelector(
      "#quickViewModal .product-actions"
    );
    const modalContent = document.querySelector("#quickViewContent");

    console.log("=== DEBUGGING BUTTON VISIBILITY ===");
    console.log(
      "Modal content HTML:",
      modalContent?.innerHTML.substring(0, 500)
    );
    console.log("Add to cart button found:", !!addToCartBtn);
    console.log("Wishlist button found:", !!wishlistBtn);
    console.log("Product actions div found:", !!productActions);

    if (addToCartBtn) {
      const styles = window.getComputedStyle(addToCartBtn);
      const rect = addToCartBtn.getBoundingClientRect();
      console.log("Add to cart button styles:", {
        display: styles.display,
        visibility: styles.visibility,
        opacity: styles.opacity,
        width: styles.width,
        height: styles.height,
        position: styles.position,
        zIndex: styles.zIndex,
        background: styles.backgroundColor,
        color: styles.color,
      });
      console.log("Add to cart button position:", {
        top: rect.top,
        left: rect.left,
        width: rect.width,
        height: rect.height,
        visible: rect.width > 0 && rect.height > 0,
      });
    }

    if (productActions) {
      const styles = window.getComputedStyle(productActions);
      const rect = productActions.getBoundingClientRect();
      console.log("Product actions styles:", {
        display: styles.display,
        visibility: styles.visibility,
        width: styles.width,
        height: styles.height,
        overflow: styles.overflow,
      });
      console.log("Product actions position:", {
        top: rect.top,
        left: rect.left,
        width: rect.width,
        height: rect.height,
        visible: rect.width > 0 && rect.height > 0,
      });
    }

    // Force make buttons visible if they exist but not showing
    if (
      addToCartBtn &&
      (!addToCartBtn.offsetWidth || !addToCartBtn.offsetHeight)
    ) {
      console.log("Force showing buttons...");
      addToCartBtn.style.cssText = `
				display: inline-block !important;
				visibility: visible !important;
				opacity: 1 !important;
				width: auto !important;
				height: 45px !important;
				min-width: 160px !important;
				background-color: #dc3545 !important;
				border-color: #dc3545 !important;
				color: white !important;
				padding: 0.75rem 1.5rem !important;
				border-radius: 0.375rem !important;
				margin-right: 0.5rem !important;
				position: static !important;
				top: auto !important;
				right: auto !important;
				bottom: auto !important;
				left: auto !important;
				transform: none !important;
				z-index: 999 !important;
			`;
    }

    if (
      wishlistBtn &&
      (!wishlistBtn.offsetWidth || !wishlistBtn.offsetHeight)
    ) {
      wishlistBtn.style.cssText = `
				display: inline-block !important;
				visibility: visible !important;
				opacity: 1 !important;
				width: auto !important;
				height: 45px !important;
				min-width: 160px !important;
				background-color: transparent !important;
				border-color: #6c757d !important;
				color: #6c757d !important;
				padding: 0.75rem 1.5rem !important;
				border-radius: 0.375rem !important;
				position: static !important;
				top: auto !important;
				right: auto !important;
				bottom: auto !important;
				left: auto !important;
				transform: none !important;
				z-index: 999 !important;
			`;
    }

    // Also force the product-actions container
    if (productActions) {
      productActions.style.cssText = `
				position: static !important;
				opacity: 1 !important;
				top: auto !important;
				right: auto !important;
				bottom: auto !important;
				left: auto !important;
				transform: none !important;
				display: block !important;
				visibility: visible !important;
				flex-direction: row !important;
				justify-content: flex-start !important;
				gap: 0.5rem !important;
				background: white !important;
				padding: 1rem !important;
				margin-top: 1.5rem !important;
				border-top: 1px solid #eee !important;
				width: 100% !important;
				height: auto !important;
				overflow: visible !important;
				z-index: 10 !important;
			`;
    }

    console.log("=== END DEBUGGING ===");
  }, 300);
}

// Global variables for variant selection
let selectedVariant = null;

// Helper: enable/disable primary add-to-cart buttons and update label
function updateAddToCartState(available) {
  console.log("[DEBUG] updateAddToCartState called with available =", available);
  
  // If caller didn't provide explicit available, infer from modal DOM
  if (typeof available === "undefined") {
    try {
      const modal = document.getElementById("quickViewContent") || document;
      // Prefer active size
      const active = modal.querySelector(".size-option.active");
      if (active) {
        const s = parseInt(active.getAttribute("data-stock") || "0");
        available = s > 0;
      } else {
        // fallback: any size with stock
        const any = Array.from(modal.querySelectorAll(".size-option")).some(
          (el) => parseInt(el.getAttribute("data-stock") || "0") > 0
        );
        available = !!any;
      }
    } catch (e) {
      available = false;
    }
  }

  console.log("[DEBUG] Final availability calculated as:", available);

  // Find button in the quick view modal first
  const addBtn = document.getElementById("quickview-add-button") || 
                document.querySelector(".quickview-add-to-cart") ||
                document.querySelector(".btn-primary-action") ||
                document.querySelector(".add-to-cart") ||
                document.getElementById("add-to-cart-btn");
                
  console.log("[DEBUG] addBtn found:", !!addBtn, addBtn ? addBtn.className : "");
  
  // Get all add to cart buttons
  const globalAddBtns = Array.from(
    document.querySelectorAll(
      ".add-to-cart, #add-to-cart-btn, .add-to-cart-btn, .quickview-add-to-cart, #quickview-add-button"
    )
  );
  
  console.log("[DEBUG] Found", globalAddBtns.length, "global add buttons");

  const apply = (el, avail) => {
    if (!el) {
      console.log("[DEBUG] No element to update");
      return;
    }
    
    console.log("[DEBUG] Updating button", el.id || el.className, "to", avail ? "enabled" : "disabled");
    
    if (!avail) {
      // Explicitly set properties for disabled state
      el.disabled = true;
      el.setAttribute("aria-disabled", "true");
      el.setAttribute("disabled", "disabled");
      // make button look gray instead of danger
      el.classList.remove("btn-danger");
      el.classList.add("btn-secondary");
      el.innerHTML = '<i class="fas fa-times me-2"></i>Hết Hàng';
      // Force style overrides
      el.style.cssText = `
        opacity: 1 !important;
        display: inline-block !important;
        visibility: visible !important;
        background-color: #6c757d !important;
        border-color: #6c757d !important;
        color: white !important;
        cursor: not-allowed !important;
        pointer-events: none !important;
      `;
      console.log("[DEBUG] Button disabled with forced styles");
    } else {
      // Explicitly remove all disabled attributes
      el.disabled = false;
      el.removeAttribute("aria-disabled");
      el.removeAttribute("disabled");
      // restore primary danger look
      el.classList.remove("btn-secondary");
      el.classList.add("btn-danger");
      el.innerHTML = '<i class="fas fa-shopping-cart me-2"></i>Thêm vào giỏ hàng';
      // Clear any forced styles
      el.style.cssText = '';
      console.log("[DEBUG] Button enabled");
    }
    
    // Force repaint to ensure changes apply
    try {
      const display = el.style.display;
      el.style.display = 'none';
      el.offsetHeight; // Trigger a repaint
      el.style.display = display || '';
      
      // Double check button state is correct after repaint
      if (!avail) {
        // Safety check to ensure button looks disabled
        setTimeout(() => {
          if (!el.classList.contains('btn-secondary')) {
            el.classList.remove("btn-danger");
            el.classList.add("btn-secondary");
            console.log("[DEBUG] Re-applied btn-secondary class after repaint");
          }
        }, 10);
      }
    } catch (e) {
      console.warn("Repaint failed", e);
    }
  };

  // Update main button
  apply(addBtn, available);
  
  // Update all other buttons
  globalAddBtns.forEach((b) => apply(b, available));
}

function selectColor(color) {
  // Update color selection UI
  document
    .querySelectorAll(".color-option")
    .forEach((btn) => btn.classList.remove("active"));
  document.querySelector(`[data-color="${color}"]`).classList.add("active");

  // Get current product data to update size options
  let currentProductData = window.currentQuickViewProduct;
  // Group variants by color (make it available later in this function)
  let variantsByColor = {};
  if (currentProductData && currentProductData.variants) {
    currentProductData.variants.forEach((variant) => {
      if (!variantsByColor[variant.color]) variantsByColor[variant.color] = [];
      variantsByColor[variant.color].push(variant);
    });

    // Update size options for selected color
    const sizeOptionsContainer = document.getElementById("sizeOptions");
    if (sizeOptionsContainer && variantsByColor[color]) {
      sizeOptionsContainer.innerHTML = variantsByColor[color]
        .map((variant, index) => {
          const disabled =
            variant.stock_quantity <= 0
              ? 'disabled style="opacity:0.5;pointer-events:none;"'
              : "";
          return `
				<button type="button" class="btn btn-outline-secondary size-option ${
          index === 0 && variant.stock_quantity > 0 ? "active" : ""
        }"
						data-variant-id="${variant.id}" data-size="${variant.size}"
						data-price="${variant.price}" data-color="${variant.color}" data-stock="${
            variant.stock_quantity || 0
          }"
						onclick="selectSize('${variant.id}', '${variant.size}', ${variant.price}, '${
            variant.color
          }')" ${disabled}>
					${variant.size}
				</button>
			`;
        })
        .join("");

      // Auto-select first size còn hàng của màu này
      const firstAvailable = variantsByColor[color].find(
        (v) => v.stock_quantity > 0
      );
      if (firstAvailable) {
        selectSize(
          firstAvailable.id,
          firstAvailable.size,
          firstAvailable.price,
          firstAvailable.color
        );
      }
    }
  }

  // Update color buttons disabled state based on available variants for each color
  if (currentProductData && currentProductData.variants) {
    const colorBtns = document.querySelectorAll(".color-option");
    colorBtns.forEach((btn) => {
      const c = btn.getAttribute("data-color");
      const variantsForColor =
        variantsByColor[c] ||
        currentProductData.variants.filter((v) => v.color === c);
      const hasStock = variantsForColor.some(
        (v) => (v.stock_quantity || v.stock) > 0
      );
      // Don't set .disabled so clicks still work; use class + aria-disabled for semantics
      if (!hasStock) {
        btn.classList.add("color-out-of-stock");
        btn.setAttribute("aria-disabled", "true");
        btn.style.opacity = 0.5;
      } else {
        btn.classList.remove("color-out-of-stock");
        btn.removeAttribute("aria-disabled");
        btn.style.opacity = 1;
      }
    });

    // Update add-to-cart button state for the selected color
    const variantsForSelected = variantsByColor[color] || [];
    const selectedColorHasStock = variantsForSelected.some(
      (v) => (v.stock_quantity || v.stock) > 0
    );

    // If no variants for this color, show message
    const sizeOptionsContainer = document.getElementById("sizeOptions");
    if (variantsForSelected.length === 0) {
      if (sizeOptionsContainer)
        sizeOptionsContainer.innerHTML =
          '<p class="text-muted">Không có biến thể cho màu này.</p>';
      selectedVariant = null;
      updateAddToCartState(false);
    } else if (!selectedColorHasStock) {
      // Render sizes disabled
      if (sizeOptionsContainer) {
        sizeOptionsContainer.innerHTML = variantsForSelected
          .map(
            (variant) => `
            <button type="button" class="btn btn-outline-secondary size-option"
              data-variant-id="${variant.id}" data-size="${variant.size}"
              data-price="${variant.price}" data-color="${
              variant.color
            }" data-stock="${variant.stock_quantity || variant.stock || 0}"
              disabled style="opacity:0.5;pointer-events:none;">
              ${variant.size}
            </button>
          `
          )
          .join("");
      }
      selectedVariant = null;
      updateAddToCartState(false);
    } else {
      updateAddToCartState(true);
    }
  }

  console.log("Selected color:", color);
}

function selectSize(variantId, size, price, color) {
  // Update size selection UI
  document
    .querySelectorAll(".size-option")
    .forEach((btn) => btn.classList.remove("active"));
  document
    .querySelector(`[data-variant-id="${variantId}"]`)
    .classList.add("active");

  // Helper to compute cart quantity for this product/variant
  function getCartQty(pid, vid) {
    try {
      return (cart || []).reduce((sum, it) => {
        const itemPid = it.product_id || it.id || it.productId || null;
        if (!itemPid || String(itemPid) !== String(pid)) return sum;
        // If variant ID provided, only count matching variant
        if (vid) {
          const v = it.variant || {};
          const vids = String(v.id || v.variant_id || '');
          return (vids === String(vid)) ? sum + (parseInt(it.quantity) || 0) : sum;
        }
        // No variant ID, count all non-variant or falsy variant.id items
        if (!it.variant || !it.variant.id) return sum + (parseInt(it.quantity) || 0);
        return sum;
      }, 0);
    } catch (e) {
      console.warn("Error calculating cart quantity", e);
      return 0;
    }
  }

  // Get base stock quantity
  const getBaseStock = function() {
    const attr = document
      .querySelector(`[data-variant-id="${variantId}"]`)
      ?.getAttribute("data-stock");
    const fromAttr = parseInt(attr || "0");
    if (!isNaN(fromAttr) && fromAttr > 0) return fromAttr;
    
    try {
      const prod = window.currentQuickViewProduct;
      if (prod && prod.variants) {
        const found = prod.variants.find(
          (v) => String(v.id) === String(variantId)
        );
        if (found) return parseInt(found.stock_quantity || found.stock || 0);
      }
    } catch (e) {}
    
    return fromAttr || 0;
  };
  
  const baseStock = getBaseStock();
  const prod = window.currentQuickViewProduct || {};
  const inCart = getCartQty(prod.id, variantId);
  const availableStock = Math.max(0, baseStock - inCart);

  // Update selected variant with complete information including available stock
  selectedVariant = {
    id: variantId,
    size: size,
    price: parseFloat(price),
    color: color,
    stock_quantity: baseStock,
    available_stock: availableStock
  };

  // Update price display
  // Prefer the quick-view modal price element if present, otherwise fallback to global
  let priceElement = document.querySelector("#quickViewContent .current-price");
  if (!priceElement) priceElement = document.querySelector(".current-price");
  if (priceElement) {
    priceElement.textContent = formatCurrency(price);
  }
  
  // Update stock badge with available remaining quantity
  try {
    const badge = document.querySelector("#quickViewContent .badge");
    if (badge) {
      if (selectedVariant.available_stock > 0) {
        badge.className = "badge bg-success fs-6";
        badge.innerHTML = `<i class="fas fa-check-circle me-1"></i>Còn hàng (${selectedVariant.available_stock})`;
      } else {
        badge.className = "badge bg-danger fs-6";
        badge.innerHTML = '<i class="fas fa-times-circle me-1"></i>Hết hàng';
      }
    }
  } catch (e) {
    console.warn("Error updating stock badge", e);
  }

  // Cập nhật max số lượng theo tồn kho còn lại của biến thể (đã trừ lượng trong cart)
  const maxQty = selectedVariant.available_stock || 0;
  const qtyInput = document.getElementById("quantityInput");
  if (qtyInput) {
    qtyInput.max = maxQty;
    if (parseInt(qtyInput.value) > maxQty) qtyInput.value = maxQty;
    // Disable nút cộng nếu đạt max
    const btnInc = qtyInput.parentElement.querySelector(
      '[data-dir="+"]'
    );
    if (btnInc) btnInc.disabled = parseInt(qtyInput.value) >= maxQty;
    const btnDec = qtyInput.parentElement.querySelector(
      '[data-dir="-"]'
    );
    if (btnDec) btnDec.disabled = parseInt(qtyInput.value) <= 1;
  }

  // Update add-to-cart state based on selected variant's available stock (after subtracting cart)
  const avail = selectedVariant && selectedVariant.available_stock > 0;
  console.log("[DEBUG] Selected variant available stock:", selectedVariant.available_stock);
  console.log("[DEBUG] Calling updateAddToCartState with availability:", !!avail);
  
  // Apply direct changes to button for immediate feedback
  const addToCartBtn = document.getElementById("quickview-add-button") || 
                      document.querySelector(".quickview-add-to-cart");
  
  if (addToCartBtn) {
    console.log("[DEBUG] Direct update of button in selectSize");
    if (!avail) {
      addToCartBtn.disabled = true;
      addToCartBtn.setAttribute("aria-disabled", "true");
      addToCartBtn.setAttribute("disabled", "disabled");
      addToCartBtn.classList.remove("btn-danger");
      addToCartBtn.classList.add("btn-secondary");
      addToCartBtn.innerHTML = '<i class="fas fa-times me-2"></i>Hết Hàng';
      addToCartBtn.style.cssText = `
        opacity: 1 !important;
        display: inline-block !important;
        visibility: visible !important;
        background-color: #6c757d !important;
        border-color: #6c757d !important;
        color: white !important;
        cursor: not-allowed !important;
        pointer-events: none !important;
      `;
    } else {
      addToCartBtn.disabled = false;
      addToCartBtn.removeAttribute("aria-disabled");
      addToCartBtn.removeAttribute("disabled");
      addToCartBtn.classList.remove("btn-secondary");
      addToCartBtn.classList.add("btn-danger");
      addToCartBtn.innerHTML = '<i class="fas fa-shopping-cart me-2"></i>Thêm vào giỏ hàng';
      addToCartBtn.style.cssText = '';
    }
  }
  
  // Also use the centralized function to ensure consistency
  updateAddToCartState(!!avail);

  console.log("Selected variant:", selectedVariant);
}

function changeQuantity(delta) {
  const quantityInput = document.getElementById("quantityInput");
  const maxQty = parseInt(quantityInput.max) || 10;
  const currentQty = parseInt(quantityInput.value) || 1;
  const newQty = Math.max(1, Math.min(maxQty, currentQty + delta));
  quantityInput.value = newQty;
  // Disable nút cộng nếu đạt max
  const btnInc = quantityInput.parentElement.querySelector(
    'button[onclick*="changeQuantity(1)"]'
  );
  if (btnInc) btnInc.disabled = newQty >= maxQty;
  const btnDec = quantityInput.parentElement.querySelector(
    'button[onclick*="changeQuantity(-1)"]'
  );
  if (btnDec) btnDec.disabled = newQty <= 1;
}

function addToCartFromQuickView(productId) {
  const quantity =
    parseInt(document.getElementById("quantityInput").value) || 1;

  // Check if product has variants and ensure one is selected
  const currentProductData = window.currentQuickViewProduct;

  // Check if we have product data
  if (!currentProductData) {
    if (window.showError) {
      window.showError("Không tìm thấy thông tin sản phẩm");
    } else {
      showToast("Không tìm thấy thông tin sản phẩm", "error");
    }
    return;
  }
  
  // Get modal instance once at the beginning - used for both product types
  const modal = bootstrap.Modal.getInstance(
    document.getElementById("quickViewModal")
  );
  
  // For non-variant products, check stock directly
  if (
    !(currentProductData.variants && currentProductData.variants.length > 0)
  ) {
    // Check if product is in stock
    if (
      !currentProductData.in_stock ||
      !(currentProductData.stock_quantity > 0)
    ) {
      if (window.showError) {
        window.showError("Sản phẩm đã hết hàng");
      } else {
        showToast("Sản phẩm đã hết hàng", "error");
      }
      return;
    }

    console.log("[DEBUG] Adding non-variant product to cart from QuickView");
    
    // Show loading state
    showLoading();
    window.addToCartInProgress = true;

    // Make direct API call instead of delegating to avoid event object issues
    fetch(`${BASE_URL}/ajax/cart/add`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        product_id: productId,
        quantity: quantity,
        variant_id: null,
        variant: null,
      }),
    })
      .then(response => response.json())
      .then(data => {
        hideLoading();
        window.addToCartInProgress = false;

        if (data.success) {
          // Use unified notifications if available
          if (window.showSuccess) {
            window.showSuccess("Đã thêm sản phẩm vào giỏ hàng!");
          } else {
            showToast("Đã thêm sản phẩm vào giỏ hàng thành công!", "success");
          }
          updateCartCounter(data.cart_count);
          
          // Close modal directly here
          if (modal) {
            console.log("[DEBUG] Closing QuickView modal after non-variant add");
            modal.hide();
            
            // Clean up modal backdrop and body classes
            setTimeout(() => {
              const backdrops = document.querySelectorAll(".modal-backdrop");
              backdrops.forEach(backdrop => backdrop.remove());
              document.body.style.overflow = "";
              document.body.classList.remove("modal-open");
              document.documentElement.classList.remove("modal-open");
            }, 100);
          }
        } else {
          showToast(data.message || "Có lỗi xảy ra!", "error");
        }
      })
      .catch(error => {
        hideLoading();
        window.addToCartInProgress = false;
        console.error("Error adding non-variant product:", error);
        showToast("Có lỗi xảy ra khi thêm vào giỏ hàng!", "error");
      });
      
    return;
  }

  // For products with variants
  if (
    currentProductData &&
    currentProductData.variants &&
    currentProductData.variants.length > 0
  ) {
    // If user hasn't selected a variant (modal auto-init might have failed),
    // pick the first in-stock variant or fallback to the first variant.
    if (!selectedVariant) {
      const candidate =
        currentProductData.variants.find(
          (v) => (v.stock_quantity || v.stock) > 0
        ) || currentProductData.variants[0];
      if (candidate) {
        selectedVariant = {
          id: candidate.id,
          size: candidate.size,
          price: parseFloat(candidate.price) || 0,
          color: candidate.color,
        };
        // Update UI safely
        try {
          selectSize(
            candidate.id,
            candidate.size,
            candidate.price,
            candidate.color
          );
        } catch (e) {}
      } else {
        // Use unified notifications
        if (window.showWarning) {
          window.showWarning("Vui lòng chọn màu sắc và kích thước!");
        } else {
          showToast("Vui lòng chọn màu sắc và kích thước!", "warning");
        }
        return;
      }
    }
  }

  // Prepare variant data
  let variant = null;
  let variantId = null;

  if (selectedVariant) {
    // Send variant ID to match backend logic
    variantId = selectedVariant.id;
    variant = {
      id: selectedVariant.id,
      size: selectedVariant.size,
      color: selectedVariant.color,
      price: selectedVariant.price,
    };
  }

  console.log("Adding to cart from QuickView:", {
    productId,
    quantity,
    variant,
    variantId,
  });

  // Modal instance was already captured at the beginning of this function
  // Make API call with variant ID
  if (window.addToCartInProgress) {
    console.log("AddToCart already in progress, skipping...");
    return;
  }

  window.addToCartInProgress = true;
  showLoading();

  // Debug: Log the URL and data being sent
  console.log("BASE_URL:", BASE_URL);
  console.log("Full URL:", `${BASE_URL}/ajax/cart/add`);
  console.log("Request data:", {
    product_id: productId,
    quantity: quantity,
    variant_id: variantId,
  });

  fetch(`${BASE_URL}/ajax/cart/add`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      product_id: productId,
      quantity: quantity,
      variant_id: variantId,
      variant: variant,
    }),
  })
    .then((response) => {
      console.log("Response status:", response.status);
      console.log("Response headers:", response.headers);

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      return response.json();
    })
    .then((data) => {
      console.log("Response data:", data);
      hideLoading();
      window.addToCartInProgress = false;

      if (data.success) {
        // Use unified notifications
        if (window.showSuccess) {
          window.showSuccess("Đã thêm sản phẩm vào giỏ hàng!");
        } else {
          showToast("Đã thêm sản phẩm vào giỏ hàng thành công!", "success");
        }
        updateCartCounter(data.cart_count);

        // Close modal immediately after success
        if (modal) {
          console.log("[DEBUG] Closing QuickView modal after variant add");
          modal.hide();
          
          // Clean up modal backdrop and body classes for extra assurance
          setTimeout(() => {
            const backdrops = document.querySelectorAll(".modal-backdrop");
            backdrops.forEach((backdrop) => backdrop.remove());
            document.body.style.overflow = "";
            document.body.classList.remove("modal-open");
            document.documentElement.classList.remove("modal-open");
          }, 100);
        } else {
          console.log("[DEBUG] No modal instance found to close");
        }
      } else {
        // Use unified notifications
        if (window.showError) {
          window.showError(data.message || "Có lỗi xảy ra!");
        } else {
          showToast(data.message || "Có lỗi xảy ra!", "error");
        }
      }
    })
    .catch((error) => {
      hideLoading();
      window.addToCartInProgress = false;
      console.error("Error:", error);
      // Use unified notifications
      if (window.showError) {
        window.showError("Có lỗi xảy ra khi thêm vào giỏ hàng!");
      } else {
        showToast("Có lỗi xảy ra khi thêm vào giỏ hàng!", "error");
      }
    });

  // Also remove any leftover backdrop manually
  setTimeout(() => {
    // Remove any remaining modal backdrops
    const backdrops = document.querySelectorAll(".modal-backdrop");
    backdrops.forEach((backdrop) => backdrop.remove());

    // Ensure body overflow is restored
    document.body.style.overflow = "";
    document.body.classList.remove("modal-open");

    // Remove modal-open class from html if it exists
    document.documentElement.classList.remove("modal-open");
  }, 100);
}

function generateStars(rating) {
  const fullStars = Math.floor(rating);
  const hasHalfStar = rating % 1 !== 0;
  const emptyStars = 5 - Math.ceil(rating);

  let starsHTML = "";

  // Full stars
  for (let i = 0; i < fullStars; i++) {
    starsHTML += '<i class="fas fa-star text-warning"></i>';
  }

  // Half star
  if (hasHalfStar) {
    starsHTML += '<i class="fas fa-star-half-alt text-warning"></i>';
  }

  // Empty stars
  for (let i = 0; i < emptyStars; i++) {
    starsHTML += '<i class="far fa-star text-warning"></i>';
  }

  return starsHTML;
}

function showToast(message, type = "info") {
  // Use unified notification system if available
  if (window.showNotification) {
    const typeMap = {
      success: "success",
      error: "error",
      warning: "warning",
      info: "info",
    };
    window.showNotification(message, typeMap[type] || "info");
    return;
  }

  // Fallback to custom toast
  document
    .querySelectorAll(".toast-notification")
    .forEach((toast) => toast.remove());

  const toast = document.createElement("div");
  toast.className = `toast-notification position-fixed`;
  toast.style.cssText = `
        top: 20px;
        right: 20px;
        z-index: 9999;
        padding: 15px 20px;
        background: ${
          type === "success"
            ? "#28a745"
            : type === "error"
            ? "#dc3545"
            : "#17a2b8"
        };
        color: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transform: translateX(100%);
        transition: transform 0.3s ease;
    `;

  const icon =
    type === "success"
      ? "check-circle"
      : type === "error"
      ? "exclamation-triangle"
      : "info-circle";
  toast.innerHTML = `<i class="fas fa-${icon} me-2"></i>${message}`;

  document.body.appendChild(toast);

  // Animate in
  setTimeout(() => (toast.style.transform = "translateX(0)"), 100);

  // Remove after 3 seconds
  setTimeout(() => {
    toast.style.transform = "translateX(100%)";
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}
