<?php
/**
 * zone Fashion E-commerce Platform
 * Root Entry Point - Client Website
 */

// Load environment variables
if (file_exists('.env')) {
    $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && substr($line, 0, 1) !== '#') {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// Define constants
require_once 'app/config/constants.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Load core classes
require_once APP_PATH . '/core/Database.php';
require_once APP_PATH . '/core/Model.php';
require_once APP_PATH . '/core/Controller.php';

// Check if this is an admin request
$url = $_GET['url'] ?? '';
$isAdminRequest = (strpos($url, 'admin') === 0);

if ($isAdminRequest) {
    // Handle admin routing directly with App.php
    require_once APP_PATH . '/core/App.php';
    $app = new App();
    exit; // Stop execution after handling admin routes
} elseif (preg_match('#^api/#', $url) || preg_match('#^vouchers/#', $url)) {
    // Handle API and vouchers routing
    require_once __DIR__ . '/app/api/routes.php';
    exit;
} else {
    // Handle client website routing with Router
    require_once APP_PATH . '/core/Router.php';
    require_once APP_PATH . '/helpers/functions.php';
    require_once APP_PATH . '/routes/web.php';
    exit; // Stop execution after handling client routes
}
?>
