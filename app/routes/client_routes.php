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
    'registerUser' => 'AuthController@register',
    'logout' => 'AuthController@logout',
    'forgot-password' => 'AuthController@forgotPasswordForm',
    'reset-password/{token}' => 'AuthController@resetPasswordForm',

    // Account
    'account' => 'AccountController@index',
    'account/profile' => 'AccountController@profile',
    'account/updateProfile' => 'AccountController@updateProfile',
    'account/password' => 'AccountController@passwordForm',
    'account/updatePassword' => 'AccountController@updatePassword',
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

    // Vouchers
    'vouchers' => 'VoucherController@index',
    'vouchers/my-vouchers' => 'VoucherController@myVouchers',
    'vouchers/remove' => 'VoucherController@remove',
    'vouchers/save' => 'VoucherController@save',

    // Order API routes for checkout
    'order/addresses' => 'OrderController@getAddresses',
    'order/addAddress' => 'OrderController@addAddress', 
    'order/editAddress/{id}' => 'OrderController@editAddress',
    'order/getAddress/{id}' => 'OrderController@getAddress',
    'order/updateAddress/{id}' => 'OrderController@updateAddress',
    'order/deleteAddress/{id}' => 'OrderController@deleteAddress',
    'order/setDefaultAddress/{id}' => 'OrderController@setDefaultAddress',
    'order/place' => 'OrderController@placeOrder',

    

    //Posts
    'blog' => 'PostController@index',
    'blog/{id}' => 'PostController@show',

    // Email Verification
    'verify-email/{token}' => 'VerifyController@email',

];
