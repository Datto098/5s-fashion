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
    'search' => 'HomeController@search',

    // Product
    'product/{slug}' => 'HomeController@product',

    
    // Review (via AJAX)
    'ajax/review/like/{id}' => 'AjaxController@reviewLike',
    'ajax/review/delete/{id}' => 'AjaxController@reviewDelete',
    'ajax/review/add' => 'AjaxController@reviewAdd',

    // Cart
    'cart' => 'HomeController@cart',

    // Checkout
    'checkout' => 'HomeController@checkout',

    // Auth
    'login' => 'AuthController@loginForm',
    'register' => 'AuthController@registerForm',
    'registerUser' => 'AuthController@register',
    'logout' => 'AuthController@logout',
    'forgot-password' => 'AuthController@forgotPasswordForm',
    'reset-password/{token}' => 'AuthController@resetPasswordForm',
    
    // Google OAuth
    'auth/google' => 'GoogleAuthController@login',
    'auth/google-callback' => 'GoogleAuthController@callback',

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
    'ajax/wishlist/list' => 'AjaxController@getWishlistList',
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
    'vouchers/apply' => 'VoucherController@apply',

    // Order API routes for checkout
    'order/addresses' => 'OrderController@getAddresses',
    'order/addAddress' => 'OrderController@addAddress',
    'order/editAddress/{id}' => 'OrderController@editAddress',
    'order/getAddress/{id}' => 'OrderController@getAddress',
    'order/updateAddress/{id}' => 'OrderController@updateAddress',
    'order/deleteAddress/{id}' => 'OrderController@deleteAddress',
    'order/setDefaultAddress/{id}' => 'OrderController@setDefaultAddress',
    'order/place' => 'OrderController@placeOrder',
    'order/success/{id}' => 'OrderController@success',
    'order/success' => 'OrderController@success',
    'order/tracking' => 'OrderController@tracking',
    'order/downloadInvoice' => 'OrderController@downloadInvoice',

    // Payment routes
    'payment/methods' => 'PaymentController@getMethods',
    'payment/vnpay' => 'PaymentController@vnpay',
    'payment/vnpay/return' => 'PaymentController@vnpayReturn',
    'payment/cod' => 'PaymentController@cod',
    'payment/momo' => 'PaymentController@momo',
    'payment/momo/return' => 'PaymentController@momoReturn',
    'payment/bank-transfer' => 'PaymentController@bankTransfer',



    //Posts
    'blog' => 'PostController@index',
    'blog/{id}' => 'PostController@show',

    // Email Verification
    'verify-email/{token}' => 'VerifyController@email',

];
