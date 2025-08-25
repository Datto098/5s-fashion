<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $meta_description ?? '5S Fashion - Thời trang nam nữ cao cấp, xu hướng mới nhất' ?>">
    <meta name="keywords" content="<?= $meta_keywords ?? 'thời trang, nam, nữ, cao cấp, 5s fashion' ?>">
    <title><?= $title ?? '5S Fashion - Thời trang cao cấp' ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= asset('images/favicon.ico') ?>">

    <!-- CSS - Load in correct order -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" rel="stylesheet">

    <!-- Brand Variables CSS - MUST load first -->
    <link href="<?= asset('css/brand-variables.css') ?>" rel="stylesheet">
    <link href="<?= asset('css/user-dropdown.css') ?>" rel="stylesheet">
    <link href="<?= asset('css/dropdown-clickable-fix.css') ?>" rel="stylesheet">
    <link href="<?= asset('css/category-navigation.css') ?>" rel="stylesheet">

    <!-- Check for simple menu -->
    <?php
    $siteConfig = require_once APP_PATH . '/config/site.php';
    $useSimpleMenu = $siteConfig['use_simple_menu'] ?? false;
    ?>    <!-- Base CSS - Always loaded after brand variables -->

    <!-- Custom dropdown styles -->
    <style>
        /* Dropdown container positioning */
        .dropdown {
            position: relative;
        }

        /* Dropdown toggle button styling */
        .dropdown-toggle {
            cursor: pointer;
            position: relative;
        }

        .dropdown-toggle::after {
            display: inline-block;
            margin-left: 0.255em;
            vertical-align: 0.255em;
            content: "";
            border-top: 0.3em solid;
            border-right: 0.3em solid transparent;
            border-bottom: 0;
            border-left: 0.3em solid transparent;
        }

        /* Dropdown menu styles */
        .dropdown-menu {
            display: none;
            position: absolute;
            background-color: #fff;
            min-width: 220px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.1);
            z-index: 1000;
            padding: 0.5rem 0;
            margin: 0.125rem 0 0;
            border: 1px solid rgba(0,0,0,0.1);
            border-radius: 0.375rem;
            animation: fadeIn 0.2s ease-out;
            transform-origin: top center;
            opacity: 0;
        }

        /* Mega menu for categories */
        .category-megamenu {
            width: 750px;
            max-width: 100vw;
            left: 50%;
            transform: translateX(-50%);
            padding: 1.5rem !important;
        }

        .category-megamenu .dropdown-header {
            padding: 0.5rem 0.5rem;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            margin-bottom: 0.5rem;
        }

        .category-megamenu .dropdown-header a {
            color: #333;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.2s;
            display: block;
            position: relative;
            z-index: 1200;
        }

        .category-megamenu .dropdown-header a:hover {
            color: var(--bs-primary);
        }

        .category-megamenu .dropdown-item {
            padding: 0.4rem 0.75rem;
            font-size: 0.9rem;
            position: relative;
            z-index: 1200;
        }

        /* Make sure all megamenu links are clickable */
        .category-megamenu a {
            cursor: pointer;
        }

        .category-megamenu .dropdown-item:hover {
            padding-left: 1rem;
            background-color: rgba(0,0,0,0.03);
        }        /* Ensure mega menu doesn't overflow viewport */
        @media (max-width: 992px) {
            .category-megamenu {
                width: 100%;
                left: 0;
                transform: none;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dropdown-menu.show {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }

        .dropdown-item {
            display: block;
            width: 100%;
            padding: 0.625rem 1.25rem;
            clear: both;
            text-align: inherit;
            white-space: nowrap;
            background-color: transparent;
            border: 0;
            text-decoration: none;
            color: #212529;
            font-size: 0.95rem;
            transition: all 0.15s ease;
            cursor: pointer;
            position: relative;
            z-index: 1100; /* Ensure it's above other elements */
        }

        .dropdown-item:hover, .dropdown-item:focus {
            background-color: #f8f9fa;
            color: #16181b;
            text-decoration: none;
            padding-left: 1.5rem;
        }

        /* Make sure links in dropdown are clickable */
        .dropdown-menu a.dropdown-item {
            display: block;
            width: 100%;
            height: 100%;
        }        .dropdown-item i, .dropdown-item svg {
            margin-right: 0.5rem;
            width: 1rem;
            text-align: center;
        }

        /* Divider within dropdown menu */
        .dropdown-divider {
            height: 0;
            margin: 0.5rem 0;
            overflow: hidden;
            border-top: 1px solid rgba(0,0,0,0.1);
        }

        /* Header within dropdown menu */
        .dropdown-header {
            display: block;
            padding: 0.5rem 1.25rem;
            margin-bottom: 0;
            font-size: 0.875rem;
            color: #6c757d;
            white-space: nowrap;
        }
    </style>

    <!-- Review styles for product detail page -->
    <?php if (isset($product)): ?>
    <style>
        .review-actions .btn {
            transition: all 0.2s ease;
        }
        .review-actions .btn:hover {
            transform: translateY(-2px);
        }
        .like-review-btn.liked {
            background-color: #28a745 !important;
            color: white !important;
            border-color: #28a745 !important;
        }
        .like-review-btn.liked .far.fa-thumbs-up {
            font-weight: 900; /* Chuyển từ biểu tượng outline sang solid khi đã like */
        }
        .delete-review-btn:hover {
            background-color: #dc3545;
            color: white;
        }
    </style>
    <?php endif; ?>
    <link href="<?= asset('css/base.css') ?>" rel="stylesheet">
    <link href="<?= asset('css/client.css') ?>" rel="stylesheet">
    <link href="<?= asset('css/components.css') ?>" rel="stylesheet">
    <link href="<?= asset('css/quick-view.css') ?>" rel="stylesheet">
    <link href="<?= asset('css/counter-smooth.css') ?>" rel="stylesheet">

    <!-- Custom CSS for current page - Loaded after base CSS -->
    <?php if (isset($custom_css)): ?>
        <?php foreach ($custom_css as $css): ?>
            <?php if (strpos($css, 'http') === 0): ?>
                <link href="<?= $css ?>" rel="stylesheet">
            <?php else: ?>
                <link href="<?= asset($css) ?>" rel="stylesheet">
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Layout fixes - Always loaded last to ensure consistency -->
    <link href="<?= asset('css/layout-fixes.css') ?>" rel="stylesheet">

    <!-- Dropdown fix CSS - load after all other styles -->
    <link href="<?= asset('css/dropdown-fix.css') ?>" rel="stylesheet">
    <!-- Hover dropdown CSS -->
    <link href="<?= asset('css/hover-dropdown.css') ?>" rel="stylesheet">

    <!-- Inline CSS -->
    <?php if (isset($inline_css)): ?>
        <style>
            <?= $inline_css ?>
        </style>
    <?php endif; ?>

    <style>
        /* Cart Sidebar Styles */
        .cart-sidebar {
            position: fixed;
            top: 0;
            right: -400px;
            width: 400px;
            height: 100vh;
            background: white;
            box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1050;
            transition: right 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .cart-sidebar.show {
            right: 0;
        }

        .cart-sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1040;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .cart-sidebar-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .cart-sidebar-header {
            padding: 1rem;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: between;
            align-items: center;
        }

        .cart-sidebar-header h5 {
            margin: 0;
            flex: 1;
        }

        .btn-close-cart {
            background: none;
            border: none;
            font-size: 1.2rem;
            color: #6c757d;
            cursor: pointer;
        }

        .cart-sidebar-body {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
        }

        .cart-sidebar-footer {
            padding: 1rem;
            border-top: 1px solid #dee2e6;
            background: #f8f9fa;
        }

        .cart-item img {
            width: 100%;
            object-fit: cover;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .cart-item {
            border-bottom: 1px solid #eee;
            padding: 1rem 0;
            /* display: flex; */
            gap: 1rem;
            align-items: center;
            justify-content: start;
        }


        #cart-items-container {
            padding: 20px
        }


        .cart-item:last-child {
            border-bottom: none;
        }

        .cart-item-image {
            flex-shrink: 0;
            width: 80px;
            height: 80px;
            border-radius: 8px;
            overflow: hidden;
        }

        .cart-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .cart-item-details {
            flex: 1;
            min-width: 0;
        }

        .cart-item-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.25rem;
            font-size: 0.9rem;
            line-height: 1.3;
        }

        .cart-item-variant {
            color: #666;
            font-size: 0.8rem;
            margin-bottom: 0.5rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .variant-tag {
            background: #f8f9fa;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            color: #495057;
            border: 1px solid #dee2e6;
        }

        .cart-item-price {
            font-weight: 600;
            color: #e74c3c;
            margin-bottom: 0.5rem;
        }

        .cart-item-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .quantity-btn {
            width: 28px;
            height: 28px;
            border: 1px solid #dee2e6;
            background: white;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .quantity-btn:hover {
            background: #f8f9fa;
            border-color: #adb5bd;
        }

        .quantity-input {
            width: 40px;
            height: 28px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            text-align: center;
            font-size: 0.9rem;
        }

        .remove-item {
            color: #dc3545;
            text-decoration: none;
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            transition: all 0.2s;
        }

        .remove-item:hover {
            background: #f8d7da;
            color: #721c24;
        }

        .cart-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            padding: 1rem 0;
            border-top: 2px solid #dee2e6;
        }

        .cart-actions {
            display: flex;
            gap: 0.5rem;
        }

        .cart-actions .btn {
            flex: 1;
            padding: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-outline-secondary {
            border-color: #6c757d;
            color: #6c757d;
        }

        .btn-outline-secondary:hover {
            background: #6c757d;
            color: white;
        }

        .empty-cart {
            text-align: center;
            padding: 2rem 1rem;
            color: #6c757d;
        }

        .empty-cart i {
            color: #dee2e6;
        }

        @media (max-width: 480px) {
            .cart-sidebar {
                width: 100%;
                right: -100%;
            }

            .cart-item {
                gap: 0.75rem;
            }

            .cart-item-image {
                width: 60px;
                height: 60px;
            }

            .cart-actions {
                flex-direction: column;
            }
        }
    </style>

    <!-- Global JavaScript Variables -->
    <script>
        // User authentication status - REQUIRED for cart functionality
        window.isLoggedIn = <?= isLoggedIn() ? 'true' : 'false' ?>;
        window.userId = <?= getUser() ? getUser()['id'] : 'null' ?>;

        // Base URLs
        window.baseUrl = '/5s-fashion';
        window.apiUrl = '/5s-fashion/ajax';

        // Current page info
        window.currentPage = '<?= $_SERVER['REQUEST_URI'] ?? '' ?>';

        console.log('User authentication status:', window.isLoggedIn);
        console.log('User ID:', window.userId);
    </script>
</head>

<body data-logged-in="<?= isLoggedIn() ? 'true' : 'false' ?>"
      data-user-id="<?= getUser() && !is_array(getUser()['id']) ? getUser()['id'] : '' ?>"
      class="<?= $body_class ?? '' ?>">
    <!-- Header -->
    <?php include_once VIEW_PATH . '/client/layouts/header.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <?php if (isset($show_breadcrumb) && $show_breadcrumb): ?>
            <?php include_once VIEW_PATH . '/client/layouts/breadcrumb.php'; ?>
        <?php endif; ?>

        <!-- Flash Messages -->
        <?php if (hasFlash()): ?>
            <div class="container">
                <?php foreach (getFlash() as $type => $message): ?>
                    <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show" role="alert">
                        <i class="fas fa-<?= $type === 'success' ? 'check-circle' : ($type === 'error' ? 'exclamation-circle' : 'info-circle') ?>"></i>
                        <?= htmlspecialchars($message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Page Content -->
        <div class="page-content">
            <?= $content ?? '' ?>
        </div>
    </main>

    <!-- Footer -->
    <?php include_once VIEW_PATH . '/client/layouts/footer.php'; ?>

    <!-- Cart Mini Sidebar -->
    <div class="cart-sidebar" id="cartSidebar">
        <div class="cart-sidebar-header">
            <h5><i class="fas fa-shopping-cart"></i> Giỏ Hàng</h5>
            <button class="btn-close-cart" onclick="toggleCartSidebar()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="cart-sidebar-body">
            <div id="cart-items">
                <!-- Cart items will be loaded here by cart.js -->
                <div class="empty-cart">
                    <i class="fas fa-shopping-cart"></i>
                    <p>Giỏ hàng trống</p>
                    <small>Thêm sản phẩm để bắt đầu mua sắm</small>
                </div>
            </div>
        </div>
        <div class="cart-sidebar-footer">
            <div id="cart-total">
                <!-- Total and actions will be updated by JavaScript -->
            </div>
        </div>
    </div>

    <!-- Cart Sidebar Overlay -->
    <div class="cart-sidebar-overlay" id="cartSidebarOverlay" onclick="toggleCartSidebar()"></div>

    <!-- Back to Top Button -->
    <button class="btn-back-to-top" id="backToTop">
        <i class="fas fa-chevron-up"></i>
    </button>

    <!-- Quick View Modal - Following UI.md Standards -->
    <div class="modal fade" id="quickViewModal" tabindex="-1" aria-labelledby="quickViewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content" style="border: none; border-radius: 15px; overflow: hidden;">
                <div class="modal-header" style="background: linear-gradient(135deg, var(--primary-color, #007bff), #0056b3); color: white; border-bottom: none; padding: 1.5rem;">
                    <h5 class="modal-title fw-bold" id="quickViewModalLabel">
                        <i class="fas fa-eye me-2"></i>
                        Xem Nhanh Sản Phẩm
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0" id="quickViewContent">
                    <div class="d-flex justify-content-center align-items-center" style="min-height: 300px;">
                        <div class="text-center">
                            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                                <span class="visually-hidden">Đang tải...</span>
                            </div>
                            <p class="text-muted">Đang tải thông tin sản phẩm...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript - Core Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <!-- Bootstrap dropdown fix - load right after Bootstrap -->
    <script src="<?= asset('js/bootstrap-dropdown-fix.js') ?>"></script>

    <!-- Define the file loading order and check for duplicates -->
    <?php
    // Define the shared scripts that should load first
    $core_scripts = [
        'js/notifications.js',
        'js/cart-manager.js',
        'js/wishlist-manager.js',
        'js/quick-view.js',
        'js/client.js?v=' . time()
    ];

    // Create/initialize $loaded_scripts to track what's been loaded
    if (!isset($loaded_scripts)) $loaded_scripts = [];

    // If custom_js isn't set, initialize it
    if (!isset($custom_js)) $custom_js = [];

    // Load core scripts first, avoiding duplicates
    foreach ($core_scripts as $script) {
        if (!in_array($script, $loaded_scripts)) {
            $loaded_scripts[] = $script;
            if (strpos($script, 'http') === 0) {
                echo '<script src="' . $script . '"></script>' . PHP_EOL;
            } else {
                echo '<script src="' . asset($script) . '"></script>' . PHP_EOL;
            }
        }
    }

    // Mark any duplicates in custom_js as already loaded
    $custom_js = array_diff($custom_js, $loaded_scripts);
    ?>

    <!-- Initialize global cart manager -->
    <script>
        // Global notification helper function - thay thế cho tất cả alert()
        window.showAlert = function(message, type = 'info', title = '') {
            if (window.notifications && typeof window.notifications.show === 'function') {
                window.notifications.show(message, type, title);
            } else if (typeof showNotification === 'function') {
                showNotification(message, type, title);
            } else {
                // Final fallback: console log thay vì alert
                console.log(`${type.toUpperCase()}: ${message}`);
            }
        };

        // Shorthand functions
        window.showSuccess = function(message, title = 'Thành công') {
            window.showAlert(message, 'success', title);
        };

        window.showError = function(message, title = 'Lỗi') {
            window.showAlert(message, 'error', title);
        };

        window.showWarning = function(message, title = 'Cảnh báo') {
            window.showAlert(message, 'warning', title);
        };

        window.showInfo = function(message, title = 'Thông tin') {
            window.showAlert(message, 'info', title);
        };

        // Initialize global cart manager - wait for unified system to be ready
        window.cartManager = window.cartManager || null;
        window.wishlistManager = window.wishlistManager || null;

        document.addEventListener('DOMContentLoaded', function() {
            // Wait for unified managers to be created
            const checkForUnified = () => {
                if (typeof unifiedCartManager !== 'undefined' && typeof unifiedWishlistManager !== 'undefined') {
                    window.cartManager = unifiedCartManager;
                    window.wishlistManager = unifiedWishlistManager;
                    window.unifiedCart = unifiedCartManager;
                    window.unifiedWishlist = unifiedWishlistManager;

                    console.log('Unified managers initialized:', { cartManager: window.cartManager, wishlistManager: window.wishlistManager });
                } else {
                    // If not ready yet, wait a bit more
                    setTimeout(checkForUnified, 50);
                }
            };
            checkForUnified();
        });
    </script>

    <!-- Custom JS for current page (only loads scripts not already loaded) -->
    <?php if (isset($custom_js) && !empty($custom_js)): ?>
        <?php foreach ($custom_js as $js): ?>
            <?php if (!in_array($js, $loaded_scripts)): ?>
                <?php $loaded_scripts[] = $js; ?>
                <?php if (strpos($js, 'http') === 0): ?>
                    <script src="<?= $js ?>"></script>
                <?php else: ?>
                    <script src="<?= asset($js) ?>"></script>
                <?php endif; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Inline JS -->
    <?php if (isset($inline_js)): ?>
        <script>
            <?= $inline_js ?>
        </script>
    <?php endif; ?>

    <!-- Special fix for category links -->
    <?php if ($useSimpleMenu): ?>
    <!-- Simple menu script -->
    <script src="<?= asset('js/simple-category-nav.js') ?>"></script>
    <?php else: ?>
    <!-- Mega menu fix script -->
    <script src="<?= asset('js/category-link-fix.js') ?>"></script>
    <?php endif; ?>

    <!-- Script to preserve category parameter in URLs -->
        <!-- Script to preserve category parameter in URLs -->
    <script src="<?= asset('js/preserve-category.js') ?>"></script>

    <!-- Specific fix for user dropdown menus -->
    <script src="<?= asset('js/user-dropdown-fix.js') ?>"></script>

    <!-- Đảm bảo các dropdown items có thể click được -->
    <script src="<?= asset('js/dropdown-clickable-fix.js') ?>"></script>

    <!-- Đánh dấu active cho danh mục đang xem -->
        <!-- Category active marker script -->
    <script src="<?= asset('js/category-active-marker.js') ?>"></script>

    <!-- Fix đặc biệt cho user menu dropdown -->
    <script src="<?= asset('js/user-menu-fix.js') ?>"></script>

    <!-- Category filter activator script for shop page -->
    <script src="/5s-fashion/public/assets/js/category-filter-activator.js"></script>


    <script>
        // Initialize systems on page load"></script>

    <!-- Specific fix for user dropdown menus -->
    <script src="<?= asset('js/user-dropdown-fix.js') ?>"></script>    <script>
        // Initialize systems on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Clear old localStorage data that might interfere
            console.log('🧹 Clearing old localStorage data...');
            localStorage.removeItem('wishlist'); // Remove old localStorage wishlist
            localStorage.removeItem('cart'); // Remove old localStorage cart

            // Add page-loaded class for CSS animations
            setTimeout(() => {
                document.body.classList.add('page-loaded');
            }, 100);

            // Initialize all dropdowns
            console.log('🔍 Initializing Bootstrap dropdowns...');
            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
            var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
              return new bootstrap.Dropdown(dropdownToggleEl);
            });

            console.log('✅ Page initialized - counters hidden via CSS');
        });
    </script>

    <!-- Review scripts for product detail page -->
    <?php if (isset($product)): ?>
    <script src="/5s-fashion/public/assets/js/review.js"></script>
    <?php endif; ?>

    <!-- Force initialize all dropdowns to ensure they work -->
    <script>
            // Initialize dropdowns with hover functionality
            document.addEventListener('DOMContentLoaded', function() {
                // First try Bootstrap's native way for initialization only
                if (typeof bootstrap !== 'undefined') {
                    try {
                        console.log('🔄 Initializing dropdowns (for structure only)...');
                        var dropdownTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
                        dropdownTriggerList.forEach(function(el) {
                            // Just initialize but don't use click events
                            var dropdownInstance = new bootstrap.Dropdown(el, {
                                // Prevent the dropdown from showing on click (we'll handle with hover)
                                autoClose: false
                            });

                            // Disable the click event that Bootstrap adds
                            el.removeEventListener('click', dropdownInstance._clickEvent);
                        });
                    } catch(e) {
                        console.error('Bootstrap dropdown initialization failed:', e);
                    }
                }

                console.log('🔄 Setting up hover functionality for dropdowns...');

                // Make sure dropdown-toggle links still work if they have an href
                document.querySelectorAll('.dropdown-toggle').forEach(function(toggle) {
                    if (toggle.tagName === 'A' && toggle.hasAttribute('href') && toggle.getAttribute('href') !== '#') {
                        toggle.addEventListener('click', function(e) {
                            // Allow direct navigation for links with real hrefs
                            console.log('Navigating to:', this.href);
                        });
                    }
                });            // We won't add any click handlers to dropdown items
            // Let the browser's default behavior handle the clicks
            console.log('🔄 Letting browser handle dropdown link clicks normally');

            // Function to position dropdown properly
            function positionDropdown(toggle, menu) {
                // Reset any previous positioning
                menu.style.top = '';
                menu.style.left = '';
                menu.style.right = '';

                const toggleRect = toggle.getBoundingClientRect();
                const menuRect = menu.getBoundingClientRect();
                const viewportWidth = window.innerWidth;

                // Check if this is a mega menu
                const isMegaMenu = menu.classList.contains('category-megamenu');

                if (isMegaMenu) {
                    // For mega menu, we don't need to set left/right as CSS handles it
                    // Just position below the toggle
                    menu.style.top = toggleRect.height + 'px';
                    return;
                }

                // Check if dropdown would go off screen to the right
                if (toggleRect.left + menuRect.width > viewportWidth) {
                    // Align to right edge of toggle
                    menu.style.right = '0';
                } else {
                    menu.style.left = '0';
                }

                // Position below the toggle
                menu.style.top = toggleRect.height + 'px';
            }

            // Handle dropdowns on hover instead of click
            document.querySelectorAll('.dropdown').forEach(function(dropdown) {
                // Show dropdown on mouse enter
                dropdown.addEventListener('mouseenter', function() {
                    const menu = this.querySelector('.dropdown-menu');
                    const toggle = this.querySelector('.dropdown-toggle');

                    if (menu && toggle) {
                        // Close all other dropdowns first
                        document.querySelectorAll('.dropdown-menu.show').forEach(function(openMenu) {
                            if (openMenu !== menu) {
                                openMenu.classList.remove('show');
                                if(openMenu.closest('.dropdown')) {
                                    openMenu.closest('.dropdown').classList.remove('show');
                                }
                            }
                        });

                        // Show this dropdown
                        menu.classList.add('show');
                        this.classList.add('show');

                        // Position dropdown
                        positionDropdown(toggle, menu);
                    }
                });

                // Hide dropdown on mouse leave
                dropdown.addEventListener('mouseleave', function() {
                    const menu = this.querySelector('.dropdown-menu');
                    if (menu) {
                        menu.classList.remove('show');
                        this.classList.remove('show');
                    }
                });
            });
            // Reposition dropdowns on window resize
            window.addEventListener('resize', function() {
                document.querySelectorAll('.dropdown-menu.show').forEach(function(menu) {
                    const toggle = menu.closest('.dropdown').querySelector('.dropdown-toggle');
                    if (toggle) {
                        positionDropdown(toggle, menu);
                    }
                });
            });

            // Remove any existing event handlers that might interfere with links
            setTimeout(function() {
                console.log('🔄 Removing problematic event handlers...');

                // SIMPLIFIED: Just ensure links are directly clickable
                document.querySelectorAll('.dropdown-menu a, .dropdown-item, .dropdown-header a, .category-link, .category-child-link').forEach(function(link) {
                    // First, remove all existing click handlers to avoid conflicts
                    const newLink = link.cloneNode(true);
                    if (link.parentNode) {
                        link.parentNode.replaceChild(newLink, link);
                    }

                    // Set essential styles
                    newLink.style.pointerEvents = 'auto';
                    newLink.style.cursor = 'pointer';

                    // Add a single, simple click handler for debugging only
                    if (newLink.tagName === 'A') {
                        newLink.addEventListener('click', function(e) {
                            // Don't interfere with navigation
                            console.log('📌 Direct navigation to:', this.href);
                        });
                    }
                });

                // Ensure all megamenu links work properly without replacing them
                setTimeout(function() {
                    // Get all links within the mega menu
                    var allLinks = document.querySelectorAll('.category-megamenu a, .dropdown-header a');
                    console.log('Found ' + allLinks.length + ' category menu links');

                    allLinks.forEach(function(link) {
                        // Keep the anchor elements but make sure they're clickable
                        link.style.cursor = 'pointer';
                        link.style.pointerEvents = 'auto';
                        link.style.position = 'relative';
                        link.style.zIndex = '1050';
                        link.style.display = 'block';

                        // Make sure the click event works
                        link.addEventListener('click', function(e) {
                            // This is just for debugging, the link will work normally
                            console.log('Navigating to:', this.href);
                        });
                    });
                }, 1000);
            }, 500);
        });
    </script>
</body>

</html>
