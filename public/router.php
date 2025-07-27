<?php
/**
 * Simple Router for PHP Built-in Server
 */

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = ltrim($uri, '/');

// Serve static files directly
if (preg_match('/\.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot|pdf)$/', $uri)) {
    return false; // Let PHP handle static files
}

// Special files
if ($uri === 'test.php') {
    return false;
}

// Set URL parameter for routing
if ($uri && $uri !== '') {
    $_GET['url'] = $uri;
}

// Load the main application
require_once __DIR__ . '/index.php';
?>
