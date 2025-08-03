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

    // Cart
    'cart' => 'HomeController@cart',

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
    'account/addAddress' => 'AccountController@addAddress',
    'account/editAddress/{id}' => 'AccountController@updateAddress',
    'account/deleteAddress/{id}' => 'AccountController@deleteAddress',
    'account/setDefaultAddress/{id}' => 'AccountController@setDefaultAddress',
    'account/orders' => 'AccountController@orders',
    'account/order/{id}' => 'AccountController@orderDetail',
    'account/addresses' => 'AccountController@addresses',
    'account/wishlist' => 'WishlistController@index',
    'account/wishlist/add' => 'WishlistController@add',
    'account/wishlist/remove' => 'WishlistController@remove',
    'account/wishlist/clear' => 'WishlistController@clear',
    'orders' => 'AccountController@orders',
    'orders/{id}' => 'AccountController@orderDetail',
    'addresses' => 'AccountController@addresses',

    // Wishlist
    'wishlist' => 'WishlistController@index',
    'wishlist/remove' => 'WishlistController@remove',

    // AJAX Routes
    'ajax/cart/add' => 'AjaxController@addToCart',
    'ajax/cart/update' => 'AjaxController@updateCart',
    'ajax/cart/remove' => 'AjaxController@removeFromCart',
    'ajax/cart/items' => 'AjaxController@getCartItems',
    'ajax/wishlist/toggle' => 'AjaxController@toggleWishlist',
    'ajax/product/data' => 'AjaxController@getProductData',

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

    // Vouchers
    'vouchers' => 'VoucherController@index',
    'vouchers/my-vouchers' => 'VoucherController@myVouchers',
    'vouchers/remove' => 'VoucherController@remove',
    'vouchers/save' => 'VoucherController@save',

    

    //Posts
    'blog' => 'PostController@index',
    'blog/{id}' => 'PostController@show',

];
