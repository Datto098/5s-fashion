<header class="admin-topbar">
    <div class="topbar-left">
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <div class="breadcrumb">
            <span class="breadcrumb-item">Admin</span>
            <?php if (isset($breadcrumb) && is_array($breadcrumb)): ?>
                <?php foreach ($breadcrumb as $item): ?>
                    <span class="breadcrumb-separator">/</span>
                    <span class="breadcrumb-item"><?= htmlspecialchars($item) ?></span>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="topbar-right">
        <div class="topbar-item">
            <button class="topbar-btn notification-btn">
                <i class="fas fa-bell"></i>
                <span class="notification-badge">3</span>
            </button>
        </div>

        <div class="topbar-item">
            <div class="admin-profile dropdown">
                <button class="admin-profile-btn dropdown-toggle">
                    <div class="admin-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="admin-info">
                        <span class="admin-name">Admin User</span>
                        <span class="admin-role">Administrator</span>
                    </div>
                    <i class="fas fa-chevron-down dropdown-icon"></i>
                </button>

                <div class="dropdown-menu">
                    <a href="<?= BASE_URL ?>/admin/profile" class="dropdown-item">
                        <i class="fas fa-user-circle"></i>
                        <span>Hồ sơ</span>
                    </a>
                    <a href="<?= BASE_URL ?>/admin/settings" class="dropdown-item">
                        <i class="fas fa-cog"></i>
                        <span>Cài đặt</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="<?= BASE_URL ?>/admin/logout" class="dropdown-item text-danger">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Đăng xuất</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<style>
.admin-topbar {
    background: white;
    height: 64px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 24px;
    border-bottom: 1px solid #e5e7eb;
    position: fixed;
    top: 0;
    right: 0;
    left: 250px;
    z-index: 1000;
    transition: left 0.3s ease;
}

.topbar-left {
    display: flex;
    align-items: center;
    gap: 16px;
}

.sidebar-toggle {
    background: none;
    border: none;
    padding: 8px;
    border-radius: 6px;
    color: #6b7280;
    cursor: pointer;
    font-size: 16px;
    transition: all 0.2s;
}

.sidebar-toggle:hover {
    background: #f3f4f6;
    color: #374151;
}

.breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #6b7280;
    font-size: 14px;
}

.breadcrumb-item {
    color: #374151;
}

.breadcrumb-separator {
    color: #9ca3af;
}

.topbar-right {
    display: flex;
    align-items: center;
    gap: 16px;
}

.topbar-item {
    position: relative;
}

.topbar-btn {
    background: none;
    border: none;
    padding: 10px;
    border-radius: 50%;
    color: #6b7280;
    cursor: pointer;
    font-size: 16px;
    position: relative;
    transition: all 0.2s;
}

.topbar-btn:hover {
    background: #f3f4f6;
    color: #374151;
}

.notification-badge {
    position: absolute;
    top: 6px;
    right: 6px;
    background: #ef4444;
    color: white;
    font-size: 10px;
    font-weight: 600;
    padding: 2px 5px;
    border-radius: 10px;
    min-width: 16px;
    text-align: center;
    line-height: 1.2;
}

.admin-profile {
    position: relative;
}

.admin-profile-btn {
    background: none;
    border: none;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px 12px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
}

.admin-profile-btn:hover {
    background: #f3f4f6;
}

.admin-avatar {
    width: 32px;
    height: 32px;
    background: #dc2626;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 14px;
}

.admin-info {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    text-align: left;
}

.admin-name {
    font-size: 14px;
    font-weight: 600;
    color: #111827;
    line-height: 1.2;
}

.admin-role {
    font-size: 12px;
    color: #6b7280;
    line-height: 1.2;
}

.dropdown-icon {
    font-size: 12px;
    color: #9ca3af;
    transition: transform 0.2s;
}

.dropdown-menu {
    position: absolute;
    top: 100%;
    right: 0;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    min-width: 200px;
    padding: 8px 0;
    margin-top: 8px;
    z-index: 1000;
    display: none;
    opacity: 0;
    transform: translateY(-10px);
    transition: all 0.2s;
}

.dropdown-menu.show {
    display: block;
    opacity: 1;
    transform: translateY(0);
}

.dropdown-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 16px;
    color: #374151;
    text-decoration: none;
    font-size: 14px;
    transition: all 0.2s;
}

.dropdown-item:hover {
    background: #f3f4f6;
    color: #111827;
}

.dropdown-item.text-danger {
    color: #dc2626;
}

.dropdown-item.text-danger:hover {
    background: #fef2f2;
    color: #dc2626;
}

.dropdown-divider {
    height: 1px;
    background: #e5e7eb;
    margin: 8px 0;
}

.dropdown-item i {
    width: 16px;
    text-align: center;
}

/* Responsive */
@media (max-width: 768px) {
    .admin-topbar {
        left: 0;
        padding: 0 16px;
    }

    .admin-info {
        display: none;
    }

    .breadcrumb {
        display: none;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dropdown toggle
    const dropdownToggle = document.querySelector('.dropdown-toggle');
    const dropdownMenu = document.querySelector('.dropdown-menu');

    if (dropdownToggle && dropdownMenu) {
        dropdownToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            dropdownMenu.classList.remove('show');
        });

        dropdownMenu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    // Sidebar toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.admin-sidebar');
    const topbar = document.querySelector('.admin-topbar');
    const content = document.querySelector('.admin-content');

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            if (sidebar) sidebar.classList.toggle('collapsed');
            if (topbar) topbar.classList.toggle('expanded');
            if (content) content.classList.toggle('expanded');
        });
    }
});
</script>
