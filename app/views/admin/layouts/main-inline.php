<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? '5S Fashion Admin' ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <!-- Admin CSS -->
    <link rel="stylesheet" href="/5s-fashion/public/assets/css/admin.css">

    <!-- Inline Admin CSS -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            color: #374151;
            line-height: 1.6;
        }

        .admin-layout {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .admin-sidebar {
            width: 250px;
            background: #1f2937;
            color: white;
            flex-shrink: 0;
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
            background: #3b82f6;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
        }

        .sidebar-title {
            font-size: 18px;
            font-weight: 600;
            margin: 0;
        }

        .sidebar-subtitle {
            font-size: 12px;
            color: #9ca3af;
            margin: 0;
        }

        .sidebar-nav {
            padding: 20px 0;
        }

        .nav-section-title {
            padding: 8px 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            color: #6b7280;
            letter-spacing: 0.5px;
        }

        .nav-item {
            margin: 2px 8px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: #d1d5db;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .nav-link:hover {
            background: #374151;
            color: white;
        }

        .nav-link.active {
            background: #3b82f6;
            color: white;
        }

        .nav-icon {
            width: 16px;
            text-align: center;
        }

        /* Main Content */
        .admin-main {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        /* Topbar */
        .admin-topbar {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 12px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
            cursor: pointer;
            color: #6b7280;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #6b7280;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .admin-profile-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            background: none;
            border: none;
            padding: 8px;
            cursor: pointer;
        }

        .admin-avatar {
            width: 32px;
            height: 32px;
            background: #e5e7eb;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
        }

        /* Content */
        .admin-content {
            /* margin-left: 250px; Account for fixed sidebar */
            padding: 24px;
            padding-top: 88px; /* Account for fixed topbar */
            flex: 1;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .stat-title {
            font-size: 14px;
            color: #6b7280;
            font-weight: 500;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            background: #eff6ff;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #3b82f6;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 8px;
        }

        .stat-change {
            font-size: 12px;
            color: #6b7280;
        }

        .stat-change.positive {
            color: #059669;
        }

        /* Content Cards */
        .content-card {
            background: white;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 24px;
        }

        .card-header {
            padding: 20px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h3 {
            font-size: 16px;
            font-weight: 600;
            color: #111827;
            margin: 0;
        }

        /* Tables */
        .table-responsive {
            overflow-x: auto;
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }

        .admin-table th,
        .admin-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .admin-table th {
            background: #f9fafb;
            font-weight: 600;
            color: #374151;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Alerts */
        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-success {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .admin-content {
                margin-left: 0;
                padding: 16px;
                padding-top: 80px;
            }

            .admin-sidebar {
                width: 60px;
            }

            .nav-text,
            .sidebar-title,
            .sidebar-subtitle {
                display: none;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Sidebar collapsed state */
        .admin-content.expanded {
            margin-left: 0;
        }

        .admin-topbar.expanded {
            left: 0;
        }

        /* Modal fixes - Force center positioning */
        .modal {
            z-index: 99999 !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            display: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .modal.show {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .modal-backdrop {
            z-index: 99998 !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            background-color: rgba(0, 0, 0, 0.5) !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .modal-dialog {
            margin: 0 !important;
            z-index: 100000 !important;
            position: relative !important;
            width: auto !important;
            max-width: 500px !important;
            pointer-events: none !important;
        }

        .modal-dialog.modal-lg {
            max-width: 800px !important;
        }

        .modal-content {
            background-color: #fff !important;
            border: 1px solid rgba(0,0,0,.2) !important;
            border-radius: 0.375rem !important;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15) !important;
            pointer-events: auto !important;
            position: relative !important;
            width: 100% !important;
            margin: 1rem !important;
        }

        /* Ensure modal is properly positioned and visible */
        body.modal-open {
            overflow: hidden !important;
            padding-right: 0 !important;
        }

        body.modal-open .modal {
            overflow-x: hidden !important;
            overflow-y: auto !important;
        }

        /* Force modal to appear above everything */
        .modal,
        .modal * {
            box-sizing: border-box !important;
        }

        /* Override any conflicting admin layout styles */
        .modal .admin-content,
        .modal .admin-sidebar,
        .modal .admin-topbar {
            position: static !important;
            z-index: auto !important;
        }

        /* Ensure modal appears above admin layout */
        .admin-layout,
        .admin-sidebar,
        .admin-topbar,
        .admin-content {
            z-index: 1 !important;
        }

        /* Additional modal centering */
        @media (min-width: 576px) {
            .modal-dialog {
                max-width: 500px !important;
                margin: 1.75rem auto !important;
            }

            .modal-dialog.modal-lg {
                max-width: 800px !important;
            }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <?php
        if (!defined('APP_PATH')) {
            define('APP_PATH', dirname(__FILE__) . '/../../../');
        }
        include APP_PATH . '/views/admin/layouts/sidebar.php';
        ?>

        <!-- Main Content -->
        <main class="admin-main">
            <!-- Top Bar -->
            <?php include APP_PATH . '/views/admin/layouts/topbar.php'; ?>

            <!-- Page Content -->
            <div class="admin-content">
                <!-- Messages -->
                <?php
                $messages_file = APP_PATH . '/views/admin/layouts/messages.php';
                if (file_exists($messages_file)) {
                    include $messages_file;
                }
                ?>

                <!-- Main Content -->
                <?= $content ?? '<p>No content available</p>' ?>
            </div>
        </main>
    </div>    <!-- Scripts -->
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Sidebar toggle
            const sidebarToggle = document.getElementById("sidebarToggle");
            const sidebar = document.getElementById("sidebar");

            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener("click", function() {
                    sidebar.classList.toggle("collapsed");
                });
            }

            // Add fade-in animation
            document.querySelector('.admin-content').style.opacity = '0';
            setTimeout(() => {
                document.querySelector('.admin-content').style.transition = 'opacity 0.3s';
                document.querySelector('.admin-content').style.opacity = '1';
            }, 100);
        });
    </script>
</body>
</html>
