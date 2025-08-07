<?php
/**
 * Client Routes Array
 * Simple array-based routes for App.php routing
 */

return [
    // Home
    '' => 'HomeController@index',
    'home' => 'HomeController@index',

    // Shop
    'shop' => 'HomeController@shop',
    'search' => 'HomeController@shop',

    // Product
    'product/{slug}' => 'HomeController@product',

    // Cart - Updated to use new CartController
    'cart' => 'CartController@index',
    'cart/debug' => 'CartController@debug',
    'cart/add' => 'CartController@add',
    'cart/update' => 'CartController@update',
    'cart/remove' => 'CartController@remove',
    'cart/clear' => 'CartController@clear',
    'cart/count' => 'CartController@count',
    'cart/get' => 'CartController@get',

    // Checkout
    'checkout' => 'HomeController@checkout',

    // Auth
    'login' => 'AuthController@loginForm',
    'register' => 'AuthController@registerForm',
    'logout' => 'AuthController@logout',
    'forgot-password' => 'AuthController@forgotPasswordForm',
    'reset-password/{token}' => 'AuthController@resetPasswordForm',

    // Account
    'account' => 'AccountController@index',
    'account/profile' => 'AccountController@profile',
    'account/password' => 'AccountController@passwordForm',
    'orders' => 'AccountController@orders',
    'orders/{id}' => 'AccountController@orderDetail',
    'addresses' => 'AccountController@addresses',

    // Wishlist
    'wishlist' => 'WishlistController@index',

    // AJAX Routes
    'ajax/cart/add' => 'AjaxController@addToCart',
    'ajax/cart/update' => 'AjaxController@updateCart',
    'ajax/cart/remove' => 'AjaxController@removeFromCart',
    'ajax/cart/items' => 'AjaxController@getCartItems',
    'ajax/wishlist/toggle' => 'AjaxController@toggleWishlist',
    'ajax/product/data' => 'AjaxController@getProductData',
    'ajax/product/quickview' => 'AjaxController@getProductForQuickView',

    // Wishlist Routes
    'wishlist/count' => 'WishlistController@count',

    // Static pages
    'about' => 'HomeController@about',
    'contact' => 'HomeController@contact',
    'shipping' => 'PageController@shipping',
    'returns' => 'PageController@returns',
    'privacy' => 'PageController@privacy',
    'terms' => 'PageController@terms',
    'size-guide' => 'PageController@sizeGuide',
    'faq' => 'PageController@faq',
];
