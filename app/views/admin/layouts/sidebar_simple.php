<?php
// Get current URI and determine active menu
$currentUri = $_SERVER['REQUEST_URI'];
$currentPath = parse_url($currentUri, PHP_URL_PATH);

// Helper function to check if menu is active
function isMenuActive($menuPath, $currentPath) {
    // Special case for dashboard - exact match only
    if ($menuPath === '/5s-fashion/admin' || $menuPath === '/5s-fashion/admin/') {
        return $currentPath === '/5s-fashion/admin' || $currentPath === '/5s-fashion/admin/' || $currentPath === '/5s-fashion/admin/index';
    }

    // For other menus, check if current path starts with menu path
    return strpos($currentPath, $menuPath) === 0;
}

// Function to get active class
function getActiveClass($menuPath, $currentPath) {
    return isMenuActive($menuPath, $currentPath) ? 'active' : '';
}
?>

<nav class="admin-sidebar-simple" id="sidebarSimple">
    <div class="sidebar-brand">
        <a href="/5s-fashion/admin" class="brand-link">
            <div class="brand-logo">5S</div>
            <span class="brand-text">5S Fashion</span>
        </a>
    </div>

    <div class="sidebar-menu">
        <ul class="nav-list">
            <li class="nav-item">
                <a href="/5s-fashion/admin" class="nav-link <?= getActiveClass('/5s-fashion/admin', $currentPath) ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/5s-fashion/admin/products" class="nav-link <?= getActiveClass('/5s-fashion/admin/products', $currentPath) ?>">
                    <i class="fas fa-box"></i>
                    <span>Sản phẩm</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/5s-fashion/admin/categories" class="nav-link <?= getActiveClass('/5s-fashion/admin/categories', $currentPath) ?>">
                    <i class="fas fa-tags"></i>
                    <span>Danh mục</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/5s-fashion/admin/orders" class="nav-link <?= getActiveClass('/5s-fashion/admin/orders', $currentPath) ?>">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Đơn hàng</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/5s-fashion/admin/customers" class="nav-link <?= getActiveClass('/5s-fashion/admin/customers', $currentPath) ?>">
                    <i class="fas fa-users"></i>
                    <span>Khách hàng</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/5s-fashion/admin/coupons" class="nav-link <?= getActiveClass('/5s-fashion/admin/coupons', $currentPath) ?>">
                    <i class="fas fa-ticket-alt"></i>
                    <span>Voucher</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/5s-fashion/admin/settings" class="nav-link <?= getActiveClass('/5s-fashion/admin/settings', $currentPath) ?>">
                    <i class="fas fa-cog"></i>
                    <span>Cài đặt</span>
                </a>
            </li>
        </ul>
    </div>
</nav>

<style>
.admin-sidebar-simple {
    width: 60px;
    background: var(--sidebar-bg, #1f2937);
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000;
    overflow-y: auto;
    transition: width 0.3s ease;
}

.admin-sidebar-simple:hover {
    width: 200px;
}

.sidebar-brand {
    padding: 1rem;
    text-align: center;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.brand-link {
    color: white;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
}

.brand-logo {
    width: 32px;
    height: 32px;
    background: var(--primary-color, #3b82f6);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: white;
    font-size: 14px;
}

.brand-text {
    margin-left: 0.75rem;
    font-weight: 600;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.admin-sidebar-simple:hover .brand-text {
    opacity: 1;
}

.sidebar-menu {
    padding: 1rem 0;
}

.nav-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.nav-item {
    margin-bottom: 4px;
}

.nav-link {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    color: rgba(255,255,255,0.8);
    text-decoration: none;
    transition: all 0.3s ease;
    position: relative;
}

.nav-link:hover {
    background: rgba(255,255,255,0.1);
    color: white;
}

.nav-link.active {
    background: var(--primary-color, #3b82f6);
    color: white;
}

.nav-link.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 3px;
    background: rgba(255,255,255,0.8);
}

.nav-link i {
    width: 20px;
    text-align: center;
    font-size: 16px;
}

.nav-link span {
    margin-left: 0.75rem;
    opacity: 0;
    transition: opacity 0.3s ease;
    white-space: nowrap;
}

.admin-sidebar-simple:hover .nav-link span {
    opacity: 1;
}

@media (max-width: 768px) {
    .admin-sidebar-simple {
        transform: translateX(-100%);
    }

    .admin-sidebar-simple.show {
        transform: translateX(0);
        width: 200px;
    }

    .admin-sidebar-simple.show .brand-text,
    .admin-sidebar-simple.show .nav-link span {
        opacity: 1;
    }
}
</style>
