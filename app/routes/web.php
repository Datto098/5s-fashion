<?php
/**
 * Client Website Routes
 * 5S Fashion E-commerce Platform
 */

// Include helpers
require_once APP_PATH . '/helpers/functions.php';

// Initialize Router
require_once APP_PATH . '/core/Router.php';
$router = new Router();

// Home routes
$router->get('/', 'HomeController@index');
$router->get('/home', 'HomeController@index');

// Shop routes
$router->get('/shop', 'HomeController@shop');
$router->get('/search', 'HomeController@shop'); // Search uses same shop method

// Product routes
$router->get('/product/{slug}', 'HomeController@product');

// Cart routes
$router->get('/cart', 'HomeController@cart');
$router->post('/cart/add', 'CartController@add');
$router->post('/cart/update', 'CartController@update');
$router->post('/cart/remove', 'CartController@remove');
$router->post('/cart/clear', 'CartController@clear');

// Checkout routes
$router->get('/checkout', 'HomeController@checkout');
$router->post('/checkout/process', 'CheckoutController@process');

// User Authentication routes
$router->get('/login', 'AuthController@loginForm');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@registerForm');
$router->post('/register', 'AuthController@register');
$router->get('/logout', 'AuthController@logout');
$router->get('/forgot-password', 'AuthController@forgotPasswordForm');
$router->post('/forgot-password', 'AuthController@forgotPassword');
$router->get('/reset-password/{token}', 'AuthController@resetPasswordForm');
$router->post('/reset-password', 'AuthController@resetPassword');

// User Account routes
$router->get('/account', 'AccountController@index');
$router->get('/account/profile', 'AccountController@profile');
$router->post('/account/profile', 'AccountController@updateProfile');
$router->get('/account/password', 'AccountController@passwordForm');
$router->post('/account/password', 'AccountController@updatePassword');
$router->get('/orders', 'AccountController@orders');
$router->get('/orders/{id}', 'AccountController@orderDetail');
$router->get('/addresses', 'AccountController@addresses');
$router->post('/addresses', 'AccountController@addAddress');
$router->put('/addresses/{id}', 'AccountController@updateAddress');
$router->delete('/addresses/{id}', 'AccountController@deleteAddress');

// Wishlist routes
$router->get('/wishlist', 'WishlistController@index');
$router->get('/wishlist/count', 'WishlistController@count');
$router->post('/wishlist/add', 'WishlistController@add');
$router->post('/wishlist/remove', 'WishlistController@remove');
$router->post('/wishlist/clear', 'WishlistController@clear');

// Voucher routes
$router->get('/vouchers', 'VoucherController@index');
$router->get('/vouchers/my-vouchers', 'VoucherController@myVouchers');
$router->post('/vouchers/save', 'VoucherController@save');
$router->post('/vouchers/remove', 'VoucherController@remove');
$router->get('/vouchers/validate', 'VoucherController@validate');
$router->get('/vouchers/get-valid', 'VoucherController@getValidVouchers');
$router->post('/vouchers/apply', 'VoucherController@apply');
$router->post('/vouchers/remove-applied', 'VoucherController@removeApplied');
$router->get('/vouchers/share/{id}', 'VoucherController@share');

// Static pages
$router->get('/about', 'HomeController@about');
$router->get('/contact', 'HomeController@contact');
$router->post('/contact', 'ContactController@submit');
$router->get('/shipping', 'PageController@shipping');
$router->get('/returns', 'PageController@returns');
$router->get('/privacy', 'PageController@privacy');
$router->get('/terms', 'PageController@terms');
$router->get('/size-guide', 'PageController@sizeGuide');
$router->get('/faq', 'PageController@faq');

// Newsletter
$router->post('/newsletter', 'NewsletterController@subscribe');

// Blog routes (future)
$router->get('/blog', 'BlogController@index');
$router->get('/blog/{slug}', 'BlogController@show');

// Category routes
$router->get('/category/{slug}', function($slug) {
    header("Location: /shop?category=" . urlencode($slug));
    exit;
});

// Brand routes
$router->get('/brand/{slug}', function($slug) {
    header("Location: /shop?brand=" . urlencode($slug));
    exit;
});

// AJAX endpoints for client
$router->post('/ajax/init-session', 'AjaxController@initSession');
$router->post('/ajax/cart/add', 'AjaxController@addToCart');
$router->post('/ajax/cart/update', 'AjaxController@updateCart');
$router->post('/ajax/cart/remove', 'AjaxController@removeFromCart');
$router->get('/ajax/cart/items', 'AjaxController@getCartItems');
$router->post('/ajax/wishlist/toggle', 'AjaxController@toggleWishlist');
$router->get('/ajax/search/suggestions', 'AjaxController@searchSuggestions');
$router->get('/ajax/product/{id}', 'AjaxController@getProduct');

// Admin routes (use proper routing system)
$router->get('/admin', function() {
    // Forward to admin routing system
    $_GET['url'] = 'admin';
    require_once APP_PATH . '/core/App.php';
    $app = new App();
});

$router->get('/admin/{controller}', function($controller) {
    // Forward to admin routing system
    $_GET['url'] = 'admin/' . $controller;
    require_once APP_PATH . '/core/App.php';
    $app = new App();
});

$router->get('/admin/{controller}/{action}', function($controller, $action) {
    // Forward to admin routing system
    $_GET['url'] = 'admin/' . $controller . '/' . $action;
    require_once APP_PATH . '/core/App.php';
    $app = new App();
});

$router->get('/admin/{controller}/{action}/{id}', function($controller, $action, $id) {
    // Forward to admin routing system
    $_GET['url'] = 'admin/' . $controller . '/' . $action . '/' . $id;
    require_once APP_PATH . '/core/App.php';
    $app = new App();
});

// Additional admin routes for different patterns
$router->get('/admin/{controller}/{id}/{action}', function($controller, $id, $action) {
    // Forward to admin routing system - handle /admin/products/1/edit pattern
    $_GET['url'] = 'admin/' . $controller . '/' . $id . '/' . $action;
    require_once APP_PATH . '/core/App.php';
    $app = new App();
});

// Admin POST routes
$router->post('/admin/{controller}', function($controller) {
    $_GET['url'] = 'admin/' . $controller;
    require_once APP_PATH . '/core/App.php';
    $app = new App();
});

$router->post('/admin/{controller}/{action}', function($controller, $action) {
    $_GET['url'] = 'admin/' . $controller . '/' . $action;
    require_once APP_PATH . '/core/App.php';
    $app = new App();
});

$router->post('/admin/{controller}/{action}/{id}', function($controller, $action, $id) {
    $_GET['url'] = 'admin/' . $controller . '/' . $action . '/' . $id;
    require_once APP_PATH . '/core/App.php';
    $app = new App();
});

$router->post('/admin/{controller}/{id}/{action}', function($controller, $id, $action) {
    $_GET['url'] = 'admin/' . $controller . '/' . $id . '/' . $action;
    require_once APP_PATH . '/core/App.php';
    $app = new App();
});

// Additional routes for nested controllers like /admin/products/1/variants/generate
$router->post('/admin/{controller}/{id}/{subcontroller}/{action}', function($controller, $id, $subcontroller, $action) {
    $_GET['url'] = 'admin/' . $controller . '/' . $id . '/' . $subcontroller . '/' . $action;
    require_once APP_PATH . '/core/App.php';
    $app = new App();
});

$router->get('/admin/{controller}/{id}/{subcontroller}', function($controller, $id, $subcontroller) {
    $_GET['url'] = 'admin/' . $controller . '/' . $id . '/' . $subcontroller;
    require_once APP_PATH . '/core/App.php';
    $app = new App();
});

$router->get('/admin/{controller}/{id}/{subcontroller}/{action}', function($controller, $id, $subcontroller, $action) {
    $_GET['url'] = 'admin/' . $controller . '/' . $id . '/' . $subcontroller . '/' . $action;
    require_once APP_PATH . '/core/App.php';
    $app = new App();
});

// 404 Error handler
$router->set404(function() {
    require_once VIEW_PATH . '/errors/404.php';
});

// Process the request
try {
    $router->run();
} catch (Exception $e) {
    // Log error
    error_log("Router Error: " . $e->getMessage());

    // Show error page
    if (APP_DEBUG) {
        echo "<h1>Router Error</h1>";
        echo "<p>" . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    } else {
        require_once VIEW_PATH . '/errors/500.php';
    }
}
?>
