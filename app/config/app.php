<?php
/**
 * Application Configuration
 * 5S Fashion E-commerce Platform
 */

return [
    'app' => [
        'name' => '5S Fashion',
        'url' => $_ENV['APP_URL'] ?? 'http://localhost/5s-fashion',
        'environment' => $_ENV['APP_ENV'] ?? 'development',
        'debug' => $_ENV['APP_DEBUG'] ?? true,
        'timezone' => 'Asia/Ho_Chi_Minh',
        'locale' => 'vi',
        'key' => $_ENV['APP_KEY'] ?? 'base64:5s-fashion-secret-key-here',
    ],

    'session' => [
        'lifetime' => 120, // minutes
        'expire_on_close' => false,
        'encrypt' => false,
        'files' => __DIR__ . '/../../storage/sessions',
        'connection' => null,
        'table' => 'sessions',
        'store' => null,
        'lottery' => [2, 100],
        'cookie' => 'laravel_session',
        'path' => '/',
        'domain' => null,
        'secure' => false,
        'http_only' => true,
        'same_site' => null,
    ],

    'auth' => [
        'password_hash' => PASSWORD_DEFAULT,
        'password_cost' => 12,
        'session_key' => 'auth_user',
        'remember_token_name' => 'remember_token',
        'remember_lifetime' => 60 * 24 * 365, // 1 year in minutes
    ],

    'upload' => [
        'max_size' => 5242880, // 5MB in bytes
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'path' => '/uploads/',
        'product_path' => '/uploads/products/',
        'category_path' => '/uploads/categories/',
        'brand_path' => '/uploads/brands/',
        'user_path' => '/uploads/users/',
    ],

    'pagination' => [
        'per_page' => 15,
        'admin_per_page' => 25,
    ],

    'mail' => [
        'driver' => 'smtp',
        'host' => $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com',
        'port' => $_ENV['MAIL_PORT'] ?? 587,
        'username' => $_ENV['MAIL_USERNAME'] ?? '',
        'password' => $_ENV['MAIL_PASSWORD'] ?? '',
        'encryption' => $_ENV['MAIL_ENCRYPTION'] ?? 'tls',
        'from' => [
            'address' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@5sfashion.com',
            'name' => $_ENV['MAIL_FROM_NAME'] ?? '5S Fashion',
        ],
    ],
];
