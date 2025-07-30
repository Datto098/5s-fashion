<?php
/**
 * Application Constants
 * 5S Fashion E-commerce Platform
 */

// Application Paths
define('ROOT_PATH', dirname(dirname(__DIR__)));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('VIEW_PATH', APP_PATH . '/views');
define('MODEL_PATH', APP_PATH . '/models');
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');

// URL Constants
$isPublicDir = false; 
$defaultUrl = ($_SERVER['SERVER_PORT'] == '8080')
    ? 'http://localhost:8080'
    : 'http://localhost/5s-fashion';
define('BASE_URL', rtrim($_ENV['APP_URL'] ?? $defaultUrl, '/'));

// Asset URLs - adjust based on whether we're in public/ or root
if ($isPublicDir) {
    define('ASSET_URL', BASE_URL . '/assets');
    define('UPLOAD_URL', BASE_URL . '/uploads');
} else {
    define('ASSET_URL', BASE_URL . '/public/assets');
    define('UPLOAD_URL', BASE_URL . '/public/uploads');
}// Application States
define('APP_ENV', $_ENV['APP_ENV'] ?? 'development');
define('APP_DEBUG', filter_var($_ENV['APP_DEBUG'] ?? true, FILTER_VALIDATE_BOOLEAN));

// User Roles
define('ROLE_ADMIN', 'admin');
define('ROLE_CUSTOMER', 'customer');

// User Status
define('STATUS_ACTIVE', 'active');
define('STATUS_INACTIVE', 'inactive');
define('STATUS_BANNED', 'banned');

// Product Status
define('PRODUCT_DRAFT', 'draft');
define('PRODUCT_PUBLISHED', 'published');
define('PRODUCT_OUT_OF_STOCK', 'out_of_stock');

// Order Status
define('ORDER_PENDING', 'pending');
define('ORDER_PROCESSING', 'processing');
define('ORDER_SHIPPED', 'shipped');
define('ORDER_DELIVERED', 'delivered');
define('ORDER_CANCELLED', 'cancelled');
define('ORDER_REFUNDED', 'refunded');

// Payment Methods
define('PAYMENT_COD', 'cod');
define('PAYMENT_VNPAY', 'vnpay');
define('PAYMENT_MOMO', 'momo');
define('PAYMENT_BANK_TRANSFER', 'bank_transfer');

// Payment Status
define('PAYMENT_PENDING', 'pending');
define('PAYMENT_COMPLETED', 'completed');
define('PAYMENT_FAILED', 'failed');
define('PAYMENT_REFUNDED', 'refunded');

// Category Status
define('CATEGORY_ACTIVE', 'active');
define('CATEGORY_INACTIVE', 'inactive');

// Stock Movement Types
define('STOCK_IN', 'in');
define('STOCK_OUT', 'out');
define('STOCK_ADJUSTMENT', 'adjustment');

// Image Sizes
define('IMAGE_SIZES', [
    'thumbnail' => [150, 150],
    'medium' => [300, 300],
    'large' => [800, 800],
    'extra_large' => [1200, 1200]
]);

// Flash Message Types
define('FLASH_SUCCESS', 'success');
define('FLASH_ERROR', 'error');
define('FLASH_WARNING', 'warning');
define('FLASH_INFO', 'info');

// Pagination
define('DEFAULT_PER_PAGE', 15);
define('ADMIN_PER_PAGE', 25);
define('MAX_PER_PAGE', 100);

// Cache TTL (in seconds)
define('CACHE_TTL_SHORT', 300);    // 5 minutes
define('CACHE_TTL_MEDIUM', 3600);  // 1 hour
define('CACHE_TTL_LONG', 86400);   // 24 hours

// Validation Rules
define('MIN_PASSWORD_LENGTH', 8);
define('MAX_UPLOAD_SIZE', 5242880); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Color Scheme (for admin theme)
define('THEME_COLORS', [
    'primary' => '#dc3545',     // Red
    'secondary' => '#6c757d',   // Gray
    'success' => '#28a745',     // Green
    'info' => '#17a2b8',        // Cyan
    'warning' => '#ffc107',     // Yellow
    'danger' => '#dc3545',      // Red
    'light' => '#f8f9fa',       // Light Gray
    'dark' => '#343a40',        // Dark Gray
    'white' => '#ffffff',       // White
    'muted' => '#6c757d'        // Muted Gray
]);
