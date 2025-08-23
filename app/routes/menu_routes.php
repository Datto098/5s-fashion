<?php
// Admin menu setup route

// Add menu settings to routes
$router->get('/admin/settings/menu', function() {
    $_GET['url'] = 'admin/settings/menu';
    require_once APP_PATH . '/core/App.php';
    $app = new App();
});

$router->post('/admin/settings/menu', function() {
    $_GET['url'] = 'admin/settings/menu/update';
    require_once APP_PATH . '/core/App.php';
    $app = new App();
});
