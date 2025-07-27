<?php
/**
 * Debug Routes
 */
?>
<!DOCTYPE html>
<html>
<head>
    <title>5S Fashion - Debug Routes</title>

    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 20px;
            background: #f5f5f5;
        }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .route { background: #e9ecef; padding: 10px; margin: 5px 0; border-radius: 4px; }
        .route a { color: #dc3545; text-decoration: none; font-weight: bold; }
        .route a:hover { text-decoration: underline; }
        h1 { color: #dc3545; }
        .status { color: #28a745; font-weight: bold; }
        .info { background: #d4edda; padding: 10px; border-radius: 4px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🛍️ 5S Fashion - Routes Debug</h1>

        <div class="info">
            <strong>Current URL:</strong> <?= BASE_URL ?><br>
            <strong>Server Info:</strong> <?= $_SERVER['SERVER_NAME'] ?>:<?= $_SERVER['SERVER_PORT'] ?><br>
            <strong>Request URI:</strong> <?= $_SERVER['REQUEST_URI'] ?? 'N/A' ?><br>
            <strong>PHP Version:</strong> <?= PHP_VERSION ?><br>
            <strong>Status:</strong> <span class="status">✅ Server Running</span>
        </div>

        <h2>📋 Available Routes:</h2>

        <div class="route">
            <strong>🏠 Trang chủ:</strong>
            <a href="<?= BASE_URL ?>/" target="_blank"><?= BASE_URL ?>/</a>
        </div>

        <div class="route">
            <strong>👤 Admin Panel:</strong>
            <a href="<?= BASE_URL ?>/admin" target="_blank"><?= BASE_URL ?>/admin</a>
        </div>

        <div class="route">
            <strong>🔐 Admin Login:</strong>
            <a href="<?= BASE_URL ?>/admin/login" target="_blank"><?= BASE_URL ?>/admin/login</a>
        </div>

        <div class="route">
            <strong>📊 Admin Dashboard:</strong>
            <a href="<?= BASE_URL ?>/admin/dashboard" target="_blank"><?= BASE_URL ?>/admin/dashboard</a>
        </div>

        <div class="route">
            <strong>🛒 Shop (Client):</strong>
            <a href="<?= BASE_URL ?>/shop" target="_blank"><?= BASE_URL ?>/shop</a>
        </div>

        <div class="route">
            <strong>📦 Products:</strong>
            <a href="<?= BASE_URL ?>/products" target="_blank"><?= BASE_URL ?>/products</a>
        </div>

        <h2>🔧 Debug Info:</h2>
        <div style="background: #f8f9fa; padding: 15px; border-radius: 4px; font-family: monospace; font-size: 12px;">
            <strong>$_GET:</strong> <?= print_r($_GET, true) ?><br>
            <strong>$_SERVER['REQUEST_URI']:</strong> <?= $_SERVER['REQUEST_URI'] ?? 'N/A' ?><br>
            <strong>Parsed URL:</strong> <?= $_GET['url'] ?? 'No URL parameter' ?>
        </div>

        <h2>🔑 Login Info:</h2>
        <div class="info">
            <strong>Admin Email:</strong> admin@5sfashion.com<br>
            <strong>Admin Password:</strong> admin123
        </div>
    </div>
</body>
</html>
