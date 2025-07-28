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

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" rel="stylesheet">
    <link href="<?= asset('css/client.css') ?>" rel="stylesheet">

    <!-- Custom CSS for current page -->
    <?php if (isset($custom_css)): ?>
        <?php foreach ($custom_css as $css): ?>
            <link href="<?= asset($css) ?>" rel="stylesheet">
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Inline CSS -->
    <?php if (isset($inline_css)): ?>
        <style>
            <?= $inline_css ?>
        </style>
    <?php endif; ?>
</head>
<body data-logged-in="<?= isLoggedIn() ? 'true' : 'false' ?>">
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
                <!-- Cart items will be loaded here -->
            </div>
        </div>
        <div class="cart-sidebar-footer">
            <div class="cart-total">
                <strong>Tổng: <span id="cart-total">0₫</span></strong>
            </div>
            <div class="cart-actions">
                <a href="/cart" class="btn btn-outline-primary btn-sm">Xem Giỏ Hàng</a>
                <a href="/checkout" class="btn btn-primary btn-sm">Thanh Toán</a>
            </div>
        </div>
    </div>

    <!-- Cart Sidebar Overlay -->
    <div class="cart-sidebar-overlay" id="cartSidebarOverlay" onclick="toggleCartSidebar()"></div>

    <!-- Back to Top Button -->
    <button class="btn-back-to-top" id="backToTop">
        <i class="fas fa-chevron-up"></i>
    </button>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script src="<?= asset('js/client.js') ?>"></script>

    <!-- Custom JS for current page -->
    <?php if (isset($custom_js)): ?>
        <?php foreach ($custom_js as $js): ?>
            <script src="<?= asset($js) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Inline JS -->
    <?php if (isset($inline_js)): ?>
        <script>
            <?= $inline_js ?>
        </script>
    <?php endif; ?>

    <script>
        // Initialize cart on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadCartItems();
            updateCartCounter();
        });
    </script>
</body>
</html>
