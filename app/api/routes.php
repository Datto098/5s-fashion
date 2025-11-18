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
        'name' => 'zone Fashion API',
        'version' => '1.0.0',
        'description' => 'REST API for zone Fashion e-commerce platform',
        'endpoints' => [
            'GET /api/health' => 'Health check',
            'GET /api/products' => 'Get all products',
            'GET /api/products/{id}' => 'Get product by ID',
            'GET /api/categories' => 'Get all categories',
            'POST /api/auth/login' => 'User login',
            'POST /api/auth/register' => 'User registration'
        ],
        'documentation' => 'https://your-domain.com/api/docs'
    ], 'Welcome to zone Fashion API');
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

// Orders API routes
$router->get('/orders/search', 'OrderTrackingController@search');
$router->get('/orders/{id}/timeline', 'OrderTrackingController@timeline');
$router->post('/orders/{id}/cancel', 'OrderTrackingController@cancel');
$router->get('/orders/{id}/shipping', 'OrderTrackingController@shipping');
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

// Customer confirm received
$router->post('/orders/{id}/confirm', 'OrderConfirmController@confirm');

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

// Voucher API routes
$router->get('/vouchers', 'VoucherApiController@index');
$router->get('/vouchers/featured', 'VoucherApiController@featured');
$router->post('/voucher/save', 'VoucherApiController@save');
$router->get('/voucher/check', 'VoucherApiController@check');
$router->get('/auth/check', 'AuthApiController@check');
$router->post('/vouchers/apply', 'VoucherController@apply');

// Chatbot API routes
$router->post('/chatbot/chat', 'ChatbotApiController@chat');
$router->get('/chatbot/products/best-selling', 'ChatbotApiController@getBestSellingProducts');
$router->get('/chatbot/products/discounted', 'ChatbotApiController@getDiscountedProducts');
$router->get('/chatbot/products/new', 'ChatbotApiController@getNewProducts');

// User API routes
$router->get('/user/orders', 'UserApiController@orders');
$router->get('/user/orders/{id}', 'UserApiController@orderDetails');
$router->get('/user/wishlist', 'UserApiController@wishlist');
$router->get('/user/addresses', 'UserApiController@addresses');
$router->post('/user/addresses', 'UserApiController@addAddress');
$router->put('/user/addresses/{id}', 'UserApiController@updateAddress');
$router->delete('/user/addresses/{id}', 'UserApiController@deleteAddress');

// Search API routes
$router->get('/search/suggestions', function() {
    try {
        $query = $_GET['q'] ?? '';
        
        if (empty(trim($query))) {
            ApiResponse::success(['suggestions' => []], 'No query provided');
            return;
        }

        // Database connection
        $db = Database::getInstance();
        $searchTerm = '%' . $query . '%';
        
        $sql = "SELECT p.id, p.name, p.slug, p.price, p.sale_price, p.featured_image,
                       c.name as category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'published' 
                AND (p.name LIKE ? OR p.description LIKE ?)
                ORDER BY p.name ASC
                LIMIT 6";
        
        $products = $db->fetchAll($sql, [$searchTerm, $searchTerm]);
        
        // Debug: Log product data
        error_log("Found products: " . json_encode($products));
        
        $suggestions = [];
        foreach ($products as $product) {
            $effectivePrice = ($product['sale_price'] > 0) ? $product['sale_price'] : $product['price'];
            $originalPrice = $product['price'];
            $hasDiscount = $product['sale_price'] > 0 && $product['sale_price'] < $product['price'];
            $discountPercent = $hasDiscount ? round((($originalPrice - $effectivePrice) / $originalPrice) * 100) : 0;

            // Debug: Log image path
            error_log("Product image: " . $product['featured_image']);

            $suggestions[] = [
                'id' => (int)$product['id'],
                'name' => $product['name'],
                'slug' => $product['slug'],
                'image' => $product['featured_image'] ?: 'no-image.jpg', // Default if null
                'price' => (int)$effectivePrice,
                'original_price' => (int)$originalPrice,
                'has_discount' => $hasDiscount,
                'discount_percent' => $discountPercent,
                'category' => $product['category_name'] ?? '',
                'url' => url('product/' . $product['slug'])
            ];
        }

        ApiResponse::success([
            'suggestions' => $suggestions,
            'total' => count($products),
            'query' => $query
        ], 'Search results retrieved successfully');

    } catch (Exception $e) {
        error_log("Search API error: " . $e->getMessage());
        ApiResponse::serverError('Search failed: ' . $e->getMessage());
    }
});

// Error handling for undefined routes
$router->any('/{path}', function() {
    ApiResponse::notFound('API endpoint not found');
});

// Dispatch the request
$router->dispatch();
