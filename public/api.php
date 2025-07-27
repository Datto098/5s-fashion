<?php

// Set error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set content type to JSON by default
header('Content-Type: application/json');

// Enable CORS for all API requests
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Start session
session_start();

// Include required files
require_once __DIR__ . '/../app/core/ApiResponse.php';
require_once __DIR__ . '/../app/core/ApiController.php';
require_once __DIR__ . '/../app/core/ApiRouter.php';

// Auto-load API controllers
spl_autoload_register(function ($className) {
    $baseDir = __DIR__ . '/../app/';

    // Try to load from controllers/api directory
    $file = $baseDir . 'controllers/api/' . $className . '.php';
    if (file_exists($file)) {
        require_once $file;
        return;
    }

    // Try to load from core directory
    $file = $baseDir . 'core/' . $className . '.php';
    if (file_exists($file)) {
        require_once $file;
        return;
    }

    // Try to load from models directory
    $file = $baseDir . 'models/' . $className . '.php';
    if (file_exists($file)) {
        require_once $file;
        return;
    }
});

try {
    // Include API routes
    require_once __DIR__ . '/../app/api/routes.php';

} catch (Exception $e) {
    // Log the error
    error_log('API Error: ' . $e->getMessage());

    // Return generic error response
    ApiResponse::serverError('An unexpected error occurred');
}
