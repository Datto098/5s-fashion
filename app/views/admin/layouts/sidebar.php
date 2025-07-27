<?php
// Get current URI and determine active menu
$currentUri = $_SERVER['REQUEST_URI'];
$currentPath = parse_url($currentUri, PHP_URL_PATH);

// Helper function to check if menu is active
function isMenuActive($menuPath, $currentPath) {
    // Remove trailing slashes for consistent comparison
    $menuPath = rtrim($menuPath, '/');
    $currentPath = rtrim($currentPath, '/');

    // Special case for dashboard - exact match only
    if ($menuPath === '/5s-fashion/admin') {
        return $currentPath === '/5s-fashion/admin';
    }

    // For other menus, check if current path starts with menu path
    return strpos($currentPath, $menuPath) === 0;
}

// Function to get active class
function getActiveClass($menuPath, $currentPath) {
    return isMenuActive($menuPath, $currentPath) ? 'active' : '';
}
?>

<nav class="admin-sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <div class="sidebar-logo">5S</div>
            <div>
                <h1 class="sidebar-title">5S Fashion</h1>
                <p class="sidebar-subtitle">Admin Panel</p>
            </div>
        </div>
    </div>

    <div class="sidebar-nav">
        <div class="nav-section">
            <div class="nav-section-title">Tổng quan</div>
            <div class="nav-item">
                <a href="/5s-fashion/admin" class="nav-link <?= getActiveClass('/5s-fashion/admin', $currentPath) ?>">
                    <i class="fas fa-tachometer-alt nav-icon"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="/5s-fashion/admin/analytics" class="nav-link <?= getActiveClass('/5s-fashion/admin/analytics', $currentPath) ?>">
                    <i class="fas fa-chart-line nav-icon"></i>
                    <span class="nav-text">Thống kê</span>
                </a>
            </div>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Sản phẩm</div>
            <div class="nav-item">
                <a href="/5s-fashion/admin/products" class="nav-link <?= getActiveClass('/5s-fashion/admin/products', $currentPath) ?>">
                    <i class="fas fa-box nav-icon"></i>
                    <span class="nav-text">Danh sách sản phẩm</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="/5s-fashion/admin/products/create" class="nav-link <?= getActiveClass('/5s-fashion/admin/products/create', $currentPath) ?>">
                    <i class="fas fa-plus nav-icon"></i>
                    <span class="nav-text">Thêm sản phẩm</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="/5s-fashion/admin/categories" class="nav-link <?= getActiveClass('/5s-fashion/admin/categories', $currentPath) ?>">
                    <i class="fas fa-tags nav-icon"></i>
                    <span class="nav-text">Danh mục</span>
                </a>
            </div>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Đơn hàng</div>
            <div class="nav-item">
                <a href="/5s-fashion/admin/orders" class="nav-link <?= getActiveClass('/5s-fashion/admin/orders', $currentPath) ?>">
                    <i class="fas fa-shopping-cart nav-icon"></i>
                    <span class="nav-text">Danh sách đơn hàng</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="/5s-fashion/admin/orders/pending" class="nav-link <?= getActiveClass('/5s-fashion/admin/orders/pending', $currentPath) ?>">
                    <i class="fas fa-clock nav-icon"></i>
                    <span class="nav-text">Chờ xử lý</span>
                </a>
            </div>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Khách hàng</div>
            <div class="nav-item">
                <a href="/5s-fashion/admin/customers" class="nav-link <?= getActiveClass('/5s-fashion/admin/customers', $currentPath) ?>">
                    <i class="fas fa-users nav-icon"></i>
                    <span class="nav-text">Danh sách khách hàng</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="/5s-fashion/admin/reviews" class="nav-link <?= getActiveClass('/5s-fashion/admin/reviews', $currentPath) ?>">
                    <i class="fas fa-star nav-icon"></i>
                    <span class="nav-text">Đánh giá</span>
                </a>
            </div>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Hệ thống</div>
            <div class="nav-item">
                <a href="/5s-fashion/admin/settings" class="nav-link <?= getActiveClass('/5s-fashion/admin/settings', $currentPath) ?>">
                    <i class="fas fa-cog nav-icon"></i>
                    <span class="nav-text">Cài đặt</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="/5s-fashion/admin/users" class="nav-link <?= getActiveClass('/5s-fashion/admin/users', $currentPath) ?>">
                    <i class="fas fa-user-shield nav-icon"></i>
                    <span class="nav-text">Quản lý admin</span>
                </a>
            </div>
        </div>
    </div>
</nav>

<style>
.admin-sidebar {
    width: 250px;
    background: #1f2937;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000;
    overflow-y: auto;
    transition: transform 0.3s ease;
}

.admin-sidebar.collapsed {
    transform: translateX(-100%);
}

.sidebar-header {
    padding: 20px;
    border-bottom: 1px solid #374151;
}

.sidebar-brand {
    display: flex;
    align-items: center;
    gap: 12px;
}

.sidebar-logo {
    width: 40px;
    height: 40px;
    background: #dc2626;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: white;
    font-size: 16px;
}

.sidebar-title {
    font-size: 18px;
    font-weight: 600;
    margin: 0;
    color: white;
    line-height: 1.2;
}

.sidebar-subtitle {
    font-size: 12px;
    color: #9ca3af;
    margin: 0;
    line-height: 1.2;
}

.sidebar-nav {
    padding: 16px 0;
}

.nav-section {
    margin-bottom: 24px;
}

.nav-section-title {
    padding: 8px 20px 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    color: #6b7280;
    letter-spacing: 0.5px;
}

.nav-item {
    margin: 2px 12px;
}

.nav-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 16px;
    color: #d1d5db;
    text-decoration: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s ease;
    position: relative;
}

.nav-link:hover {
    background: #374151;
    color: white;
    transform: translateX(2px);
}

.nav-link.active {
    background: #dc2626;
    color: white;
    box-shadow: 0 2px 4px rgba(220, 38, 38, 0.3);
}

.nav-link.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 3px;
    background: #fca5a5;
    border-radius: 0 2px 2px 0;
}

.nav-icon {
    width: 16px;
    height: 16px;
    text-align: center;
    font-size: 14px;
    flex-shrink: 0;
}

.nav-text {
    flex: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Hover effects for icons */
.nav-link:hover .nav-icon {
    transform: scale(1.1);
}

.nav-link.active .nav-icon {
    color: #fca5a5;
}

/* Scrollbar styling */
.admin-sidebar::-webkit-scrollbar {
    width: 4px;
}

.admin-sidebar::-webkit-scrollbar-track {
    background: #1f2937;
}

.admin-sidebar::-webkit-scrollbar-thumb {
    background: #4b5563;
    border-radius: 2px;
}

.admin-sidebar::-webkit-scrollbar-thumb:hover {
    background: #6b7280;
}

/* Responsive */
@media (max-width: 768px) {
    .admin-sidebar {
        transform: translateX(-100%);
        transition: transform 0.3s ease;
    }

    .admin-sidebar.show {
        transform: translateX(0);
    }
}

/* Animation for nav items */
.nav-item {
    animation: slideIn 0.3s ease forwards;
    opacity: 0;
}

.nav-item:nth-child(1) { animation-delay: 0.1s; }
.nav-item:nth-child(2) { animation-delay: 0.2s; }
.nav-item:nth-child(3) { animation-delay: 0.3s; }
.nav-item:nth-child(4) { animation-delay: 0.4s; }
.nav-item:nth-child(5) { animation-delay: 0.5s; }

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Badge styles for future use */
.nav-badge {
    background: #dc2626;
    color: white;
    font-size: 10px;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 10px;
    margin-left: auto;
}
</style>
