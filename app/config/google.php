<?php
/**
 * Cấu hình Google OAuth
 * 
 * File này chứa các thông tin cấu hình để kết nối với Google OAuth API
 * Bạn cần tạo project trên Google Cloud Console và lấy Client ID, Client Secret
 * https://console.cloud.google.com/
 */

return [
    // Thông tin client từ Google Cloud Console
    'client_id' => '840556197129-47mrm4f3io6uj4t1v8s3u9jj2e8amtj7.apps.googleusercontent.com',
    'client_secret' => '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',
    
    // URL redirect sau khi đăng nhập Google thành công
    'redirect_uri' => 'http://localhost/5s-fashion/auth/google-callback',
    
    // Phạm vi quyền yêu cầu từ Google
    'scopes' => [
        'email',
        'profile',
        'openid'
    ]
];
