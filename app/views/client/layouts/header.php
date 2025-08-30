<!-- Top Bar -->
<div class="top-bar bg-dark text-white py-2 d-none d-md-block">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="d-flex align-items-center">
                    <i class="fas fa-phone me-2"></i>
                    <span class="me-3">Hotline: 1900-xxxx</span>
                    <i class="fas fa-envelope me-2"></i>
                    <span>Email: info@zonefashion.com</span>
                </div>
            </div>
            <div class="col-md-6 text-end">
                <div class="d-flex align-items-center justify-content-end">
                    <div class="social-links me-3">
                        <a href="#" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-youtube"></i></a>
                    </div>
                    <?php if (isLoggedIn()): ?>
                        <?php $user = getUser(); ?>
                        <div class="dropdown user-dropdown">
                            <a class="text-white dropdown-toggle text-decoration-none" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-1"></i>
                                <?= htmlspecialchars($user['name'] ?? $user['full_name'] ?? 'User') ?>
                            </a>
                            <ul class="dropdown-menu user-dropdown-menu">
                                <?php if (isset($user['role']) && $user['role'] === 'admin'): ?>
                                    <li><a class="dropdown-item" href="<?= url('admin/dashboard') ?>"><i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="<?= url('account') ?>"><i class="fas fa-user me-2"></i>Tài Khoản</a></li>
                                <li><a class="dropdown-item" href="<?= url('orders') ?>"><i class="fas fa-shopping-bag me-2"></i>Đơn Hàng</a></li>
                                <li><a class="dropdown-item" href="<?= url('wishlist') ?>"><i class="fas fa-heart me-2"></i>Yêu Thích</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="<?= url('logout') ?>"><i class="fas fa-sign-out-alt me-2"></i>Đăng Xuất</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="<?= url('login') ?>" class="text-white me-3 text-decoration-none">
                            <i class="fas fa-sign-in-alt me-1"></i>Đăng Nhập
                        </a>
                        <a href="<?= url('register') ?>" class="text-white text-decoration-none">
                            <i class="fas fa-user-plus me-1"></i>Đăng Ký
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Header -->
<header class="main-header bg-white shadow-sm sticky-top">
    <div class="container">
        <div class="row align-items-center py-3">
            <!-- Logo -->
            <div class="col-6 col-md-3">
                <a href="<?= url() ?>" class="logo text-decoration-none">
                    <h2 class="mb-0 fw-bold text-primary">Zone Fashion</h2>
                </a>
            </div>

            <!-- Search Bar - Desktop -->
            <div class="col-md-6 d-none d-md-block">
                <form action="<?= url('search') ?>" method="GET" class="search-form">
                    <div class="input-group">
                        <input type="text" class="form-control" name="q" placeholder="Tìm kiếm sản phẩm..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Header Actions -->
            <div class="col-6 col-md-3">
                <div class="header-actions d-flex align-items-center justify-content-end">
                    <!-- Search Icon - Mobile -->
                    <button class="btn btn-link text-dark d-md-none me-2" data-bs-toggle="collapse" data-bs-target="#mobileSearch">
                        <i class="fas fa-search"></i>
                    </button>

                    <!-- Wishlist -->
                    <a href="<?= url('wishlist') ?>" class="btn btn-link text-dark me-2 position-relative">
                        <i class="fas fa-heart"></i>
                        <span class="badge bg-danger badge-sm position-absolute counter-badge" id="wishlist-count" style="display: none !important;">0</span>
                    </a>

                    <!-- Cart -->
                    <a href="<?= url('cart') ?>" class="btn btn-link text-dark me-3 position-relative">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="badge bg-primary badge-sm position-absolute counter-badge cart-count" id="cart-count" style="display: none !important;"><?= getCartCount() ?></span>
                    </a>

                    <!-- Mobile Menu Toggle -->
                    <button class="btn btn-link text-dark d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Search -->
        <div class="collapse d-md-none" id="mobileSearch">
            <div class="pb-3">
                <form action="<?= url('search') ?>" method="GET">
                    <div class="input-group">
                        <input type="text" class="form-control" name="q" placeholder="Tìm kiếm sản phẩm..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</header>

<!-- Navigation Menu -->
<?php
// Load site configuration
$siteConfig = require_once APP_PATH . '/config/site.php';
$useSimpleMenu = $siteConfig['use_simple_menu'] ?? false;

// If simple menu is enabled, include it
if ($useSimpleMenu) {
    include_once APP_PATH . '/views/client/layouts/simple-category-nav.php';
} else {
    // Original mega menu
?>
    <nav class="main-nav bg-primary d-none d-lg-block">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <ul class="nav-menu d-flex align-items-center justify-content-center mb-0 list-unstyled">
                        <li class="nav-item">
                            <a class="nav-link text-white px-3 py-3" href="<?= url() ?>">
                                <i class="fas fa-home me-2"></i>Trang Chủ
                            </a>
                        </li>

                        <!-- Main Categories dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-white px-3 py-3" href="<?= url('shop') ?>" data-bs-toggle="dropdown">
                                <i class="fas fa-th-large me-2"></i>Danh Mục
                            </a>

                            <!-- Mega Menu for Categories -->
                            <div class="dropdown-menu category-megamenu p-4">
                                <div class="row">
                                    <?php if (isset($navCategories) && !empty($navCategories)): ?>
                                        <?php
                                        $categoryChunks = array_chunk($navCategories, ceil(count($navCategories) / 3));
                                        foreach ($categoryChunks as $columnCategories):
                                        ?>
                                            <div class="col-md-4 mb-3">
                                                <?php foreach ($columnCategories as $category): ?>
                                                    <h6 class="dropdown-header fw-bold mb-2">
                                                        <a href="<?= url('shop?category=' . $category['slug']) ?>" class="text-dark category-link">
                                                            <i class="fas <?= $category['slug'] === 'nam' ? 'fa-tshirt' : ($category['slug'] === 'nu' ? 'fa-female' : 'fa-tag') ?> me-2"></i>
                                                            <?= htmlspecialchars($category['name']) ?>
                                                        </a>
                                                    </h6> <?php if (!empty($category['children'])): ?>
                                                        <div class="ps-3 mb-3">
                                                            <?php foreach ($category['children'] as $child): ?>
                                                                <div class="mb-2">
                                                                    <a class="dropdown-item py-1 category-child-link" href="<?= url('shop?category=' . $child['slug']) ?>">
                                                                        <?= htmlspecialchars($child['name']) ?>
                                                                    </a>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </li>

                        <!-- Thời Trang Nam -->
                        <?php
                        $menCategory = array_filter($navCategories ?? [], function ($cat) {
                            return strtolower($cat['slug']) === 'nam' || strpos(strtolower($cat['name']), 'nam') !== false;
                        });
                        $menCategory = reset($menCategory);

                        if ($menCategory):
                        ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle text-white px-3 py-3" href="<?= url('shop?category=' . $menCategory['slug']) ?>" data-bs-toggle="dropdown">
                                    <i class="fas fa-tshirt me-2"></i>Thời Trang Nam
                                </a>
                                <?php if (!empty($menCategory['children'])): ?>
                                    <ul class="dropdown-menu">
                                        <?php foreach ($menCategory['children'] as $child): ?>
                                            <li><a class="dropdown-item" href="<?= url('shop?category=' . $child['slug']) ?>"><?= htmlspecialchars($child['name']) ?></a></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </li>
                        <?php endif; ?>

                        <!-- Thời Trang Nữ -->
                        <?php
                        $womenCategory = array_filter($navCategories ?? [], function ($cat) {
                            return strtolower($cat['slug']) === 'nu' || strpos(strtolower($cat['name']), 'nữ') !== false;
                        });
                        $womenCategory = reset($womenCategory);

                        if ($womenCategory):
                        ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle text-white px-3 py-3" href="<?= url('shop?category=' . $womenCategory['slug']) ?>" data-bs-toggle="dropdown">
                                    <i class="fas fa-female me-2"></i>Thời Trang Nữ
                                </a>
                                <?php if (!empty($womenCategory['children'])): ?>
                                    <ul class="dropdown-menu">
                                        <?php foreach ($womenCategory['children'] as $child): ?>
                                            <li><a class="dropdown-item" href="<?= url('shop?category=' . $child['slug']) ?>"><?= htmlspecialchars($child['name']) ?></a></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </li>
                        <?php endif; ?>

                        <li class="nav-item">
                            <a class="nav-link text-white px-3 py-3" href="<?= url('vouchers') ?>">
                                <i class="fas fa-ticket-alt me-2"></i>Voucher
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white px-3 py-3" href="<?= url('blog') ?>">
                                <i class="fas fa-newspaper me-2"></i>Blog
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
<?php }
// End of else (original mega menu)
?>

<!-- Mobile Menu Offcanvas -->
<div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="mobileMenu">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">
            <span class="fw-bold text-primary">zone Fashion</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <!-- User Info -->
        <?php if (isLoggedIn()): ?>
            <div class="user-info bg-light p-3 rounded mb-3">
                <div class="d-flex align-items-center">
                    <div class="avatar me-3">
                        <i class="fas fa-user-circle fa-2x text-primary"></i>
                    </div>
                    <div>
                        <div class="fw-bold"><?= htmlspecialchars(getUser()['name'] ?? getUser()['full_name'] ?? 'User') ?></div>
                        <small class="text-muted"><?= htmlspecialchars(getUser()['email']) ?></small>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="auth-links mb-3">
                <a href="/login" class="btn btn-primary w-100 mb-2">
                    <i class="fas fa-sign-in-alt me-2"></i>Đăng Nhập
                </a>
                <a href="/register" class="btn btn-outline-primary w-100">
                    <i class="fas fa-user-plus me-2"></i>Đăng Ký
                </a>
            </div>
        <?php endif; ?>

        <!-- Navigation Menu -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="<?= url() ?>"><i class="fas fa-home me-2"></i>Trang Chủ</a>
            </li>

            <?php if (isset($navCategories) && !empty($navCategories)): ?>
                <?php foreach ($navCategories as $index => $category): ?>
                    <li class="nav-item">
                        <?php if (!empty($category['children'])): ?>
                            <a class="nav-link" data-bs-toggle="collapse" href="#category<?= $category['id'] ?>">
                                <i class="fas <?= $category['slug'] === 'nam' ? 'fa-tshirt' : ($category['slug'] === 'nu' ? 'fa-female' : 'fa-tag') ?> me-2"></i>
                                <?= htmlspecialchars($category['name']) ?> <i class="fas fa-chevron-down float-end"></i>
                            </a>
                            <div class="collapse" id="category<?= $category['id'] ?>">
                                <ul class="navbar-nav ps-3">
                                    <?php foreach ($category['children'] as $child): ?>
                                        <li><a class="nav-link" href="<?= url('shop?category=' . $child['slug']) ?>"><?= htmlspecialchars($child['name']) ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php else: ?>
                            <a class="nav-link" href="<?= url('shop?category=' . $category['slug']) ?>">
                                <i class="fas fa-tag me-2"></i><?= htmlspecialchars($category['name']) ?>
                            </a>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link" href="/blog"><i class="fas fa-newspaper me-2"></i>Blog</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/contact"><i class="fas fa-envelope me-2"></i>Liên Hệ</a>
            </li>
        </ul>

        <!-- User Menu -->
        <?php if (isLoggedIn()): ?>
            <hr>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="/account"><i class="fas fa-user me-2"></i>Tài Khoản</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/orders"><i class="fas fa-shopping-bag me-2"></i>Đơn Hàng</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/wishlist"><i class="fas fa-heart me-2"></i>Yêu Thích</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-danger" href="/logout"><i class="fas fa-sign-out-alt me-2"></i>Đăng Xuất</a>
                </li>
            </ul>
        <?php endif; ?>
    </div>
</div>
