<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>zone Fashion - Trang Chủ</title>

    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Chatbot CSS -->
    <link rel="stylesheet" href="<?= ASSET_URL ?>/css/chatbot.css">
    <link rel="stylesheet" href="<?= ASSET_URL ?>/css/chatbot-fix.css">

    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
        }
        h1 {
            color: #dc3545;
            margin-bottom: 10px;
            font-size: 2.5rem;
            font-weight: 700;
        }
        .subtitle {
            color: #6c757d;
            margin-bottom: 30px;
            font-size: 1.2rem;
        }
        .status {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            border: 1px solid #c3e6cb;
        }
        .routes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .route-card {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            border-left: 5px solid #dc3545;
            text-align: left;
            transition: transform 0.3s ease;
        }
        .route-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .route-card h3 {
            color: #dc3545;
            margin: 0 0 10px 0;
            font-size: 1.3rem;
        }
        .route-card p {
            color: #6c757d;
            margin: 5px 0;
            line-height: 1.5;
        }
        .route-link {
            display: inline-block;
            background: #dc3545;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px 5px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .route-link:hover {
            background: #c82333;
            transform: translateY(-2px);
        }
        .route-link.secondary {
            background: #6c757d;
        }
        .route-link.secondary:hover {
            background: #5a6268;
        }
        .route-link.success {
            background: #28a745;
        }
        .route-link.success:hover {
            background: #218838;
        }
        .login-info {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 20px;
            border-radius: 8px;
            margin: 30px 0;
        }
        .login-info h3 {
            color: #856404;
            margin-top: 0;
        }
        .login-info p {
            color: #856404;
            margin: 5px 0;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🛍️ zone Fashion</h1>
        <p class="subtitle">Hệ thống quản lý thời trang hiện đại</p>

        <div class="status">
            ✅ <strong>Hệ thống đang hoạt động:</strong> <?= BASE_URL ?> | Server: <?= $_SERVER['SERVER_NAME'] ?>:<?= $_SERVER['SERVER_PORT'] ?><br>
            📁 <strong>Assets URL:</strong> <?= ASSET_URL ?><br>
            🛣️ <strong>Script Path:</strong> <?= $_SERVER['SCRIPT_NAME'] ?>
        </div>

        <div class="routes-grid">
            <div class="route-card">
                <h3>👤 Admin Panel</h3>
                <p><strong>URL:</strong> <?= BASE_URL ?>/admin</p>
                <p><strong>Mô tả:</strong> Trang quản trị với dashboard, thống kê và quản lý hệ thống</p>
                <p><strong>Features:</strong> Dashboard, Statistics Cards, Quick Links</p>
                <a href="<?= BASE_URL ?>/admin" class="route-link">Truy cập Admin</a>
            </div>

            <div class="route-card">
                <h3>🔐 Admin Login</h3>
                <p><strong>URL:</strong> <?= BASE_URL ?>/admin/login</p>
                <p><strong>Mô tả:</strong> Trang đăng nhập cho quản trị viên</p>
                <p><strong>Features:</strong> Login Form, Authentication</p>
                <a href="<?= BASE_URL ?>/admin/login" class="route-link secondary">Đăng nhập Admin</a>
            </div>

            <div class="route-card">
                <h3>� Debug Tools</h3>
                <p><strong>URL:</strong> <?= BASE_URL ?>/debug</p>
                <p><strong>Mô tả:</strong> Công cụ debug và kiểm tra routes</p>
                <p><strong>Features:</strong> Routes List, Server Info, Debug Data</p>
                <a href="<?= BASE_URL ?>/debug" class="route-link success">Debug Routes</a>
            </div>

            <div class="route-card">
                <h3>🛒 Client Website</h3>
                <p><strong>URL:</strong> <?= BASE_URL ?>/shop</p>
                <p><strong>Mô tả:</strong> Trang web khách hàng (đang phát triển)</p>
                <p><strong>Status:</strong> Coming Soon...</p>
                <a href="<?= BASE_URL ?>/shop" class="route-link secondary">Shop (Soon)</a>
            </div>
        </div>

        <div class="login-info">
            <h3>🔑 Thông tin đăng nhập Admin</h3>
            <p><strong>Email:</strong> admin@zonefashion.com</p>
            <p><strong>Password:</strong> admin123</p>
            <p><strong>Role:</strong> Super Administrator</p>
        </div>

        <div>
            <h3>🚀 Quick Access</h3>
            <a href="<?= BASE_URL ?>/admin" class="route-link">📊 Admin Dashboard</a>
            <a href="<?= BASE_URL ?>/admin/login" class="route-link secondary">🔐 Admin Login</a>
            <a href="<?= BASE_URL ?>/debug" class="route-link success">🔧 Debug Tools</a>
        </div>

        <div style="background: #e3f2fd; border: 1px solid #1976d2; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h3 style="color: #1976d2; margin-top: 0;">🌐 Cách truy cập hệ thống:</h3>
            <p><strong>Cách 1 (PHP Built-in Server):</strong> <code>http://localhost:8080</code></p>
            <p><strong>Cách 2 (Apache/XAMPP/WAMP):</strong> <code>http://localhost/zone-fashion</code></p>
            <p><em>Cả hai cách đều hoạt động với cùng một codebase!</em></p>
        </div>

        <div class="footer">
            <p>&copy; 2025 zone Fashion. </p>
            <p>Framework: PHP MVC | Database: MySQL | Theme: Red-White-Gray</p>
        </div>
    </div>

    <!-- Chatbot JavaScript -->
    <script src="<?= ASSET_URL ?>/js/chatbot.js"></script>
</body>
</html>
