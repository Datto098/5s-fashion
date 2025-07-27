<?php

require_once __DIR__ . '/../core/ApiRouter.php';

// Initialize API Router
$router = new ApiRouter();

// Health check endpoint
$router->get('/health', function() {
    ApiResponse::success([
        'status' => 'OK',
        'version' => '1.0.0',
        'timestamp' => date('c'),
        'environment' => 'development'
    ], 'API is running');
});

// API Info endpoint
$router->get('/', function() {
    ApiResponse::success([
        'name' => '5S Fashion API',
        'version' => '1.0.0',
        'description' => 'REST API for 5S Fashion e-commerce platform',
        'endpoints' => [
            'GET /api/health' => 'Health check',
            'GET /api/products' => 'Get all products',
            'GET /api/products/{id}' => 'Get product by ID',
            'GET /api/categories' => 'Get all categories',
            'POST /api/auth/login' => 'User login',
            'POST /api/auth/register' => 'User registration'
        ],
        'documentation' => 'https://your-domain.com/api/docs'
    ], 'Welcome to 5S Fashion API');
});

// Products API routes
$router->get('/products', 'ProductApiController@index');
$router->get('/products/{id}', 'ProductApiController@show');
$router->post('/products', 'ProductApiController@store');
$router->put('/products/{id}', 'ProductApiController@update');
$router->delete('/products/{id}', 'ProductApiController@destroy');

// Categories API routes
$router->get('/categories', 'CategoryApiController@index');
$router->get('/categories/{id}', 'CategoryApiController@show');
$router->get('/categories/{id}/products', 'CategoryApiController@products');

// Brands API routes
$router->get('/brands', 'BrandApiController@index');
$router->get('/brands/{id}', 'BrandApiController@show');
$router->get('/brands/{id}/products', 'BrandApiController@products');

// Authentication API routes
$router->post('/auth/register', 'AuthApiController@register');
$router->post('/auth/login', 'AuthApiController@login');
$router->post('/auth/logout', 'AuthApiController@logout');
$router->post('/auth/refresh', 'AuthApiController@refresh');
$router->get('/auth/profile', 'AuthApiController@profile');
$router->put('/auth/profile', 'AuthApiController@updateProfile');
$router->put('/auth/password', 'AuthApiController@changePassword');

// Orders API routes
$router->get('/orders', 'OrderApiController@index');
$router->get('/orders/{id}', 'OrderApiController@show');
$router->post('/orders', 'OrderApiController@store');
$router->put('/orders/{id}', 'OrderApiController@update');
$router->delete('/orders/{id}', 'OrderApiController@destroy');

// Cart API routes
$router->get('/cart', 'CartApiController@index');
$router->post('/cart/add', 'CartApiController@add');
$router->put('/cart/{id}', 'CartApiController@update');
$router->delete('/cart/{id}', 'CartApiController@remove');
$router->delete('/cart/clear', 'CartApiController@clear');

// Orders API routes
$router->get('/orders', 'OrderApiController@index');
$router->get('/orders/{id}', 'OrderApiController@show');
$router->post('/orders', 'OrderApiController@store');
$router->put('/orders/{id}', 'OrderApiController@update');

// Reviews API routes
$router->get('/reviews', 'ReviewApiController@index');
$router->get('/products/{id}/reviews', 'ReviewApiController@productReviews');
$router->post('/reviews', 'ReviewApiController@store');
$router->put('/reviews/{id}', 'ReviewApiController@update');
$router->delete('/reviews/{id}', 'ReviewApiController@destroy');

// Search API routes
$router->get('/search', 'SearchApiController@search');
$router->get('/search/suggestions', 'SearchApiController@suggestions');

// Wishlist API routes
$router->get('/wishlist', 'WishlistApiController@index');
$router->post('/wishlist/add', 'WishlistApiController@add');
$router->delete('/wishlist/{id}', 'WishlistApiController@remove');
$router->delete('/wishlist/clear', 'WishlistApiController@clear');
$router->post('/wishlist/toggle', 'WishlistApiController@toggle');
$router->get('/wishlist/check/{id}', 'WishlistApiController@check');
$router->post('/wishlist/move-to-cart', 'WishlistApiController@moveToCart');

// User API routes
$router->get('/user/orders', 'UserApiController@orders');
$router->get('/user/orders/{id}', 'UserApiController@orderDetails');
$router->get('/user/wishlist', 'UserApiController@wishlist');
$router->get('/user/addresses', 'UserApiController@addresses');
$router->post('/user/addresses', 'UserApiController@addAddress');
$router->put('/user/addresses/{id}', 'UserApiController@updateAddress');
$router->delete('/user/addresses/{id}', 'UserApiController@deleteAddress');

// Error handling for undefined routes
$router->any('/{path}', function() {
    ApiResponse::notFound('API endpoint not found');
});

// Dispatch the request
$router->dispatch();
