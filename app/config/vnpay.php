<?php
/**
 * VNPay Configuration
 * zone Fashion E-commerce Platform
 */

return [
    // VNPay API Configuration - Sandbox credentials for testing
    'vnp_TmnCode' => 'W9C1YDVN', // Mã định danh merchant kết nối (Terminal Id) - Replace with real TmnCode
    'vnp_HashSecret' => 'T8W0SV2H02R4GAM81PPKSN14C3D82KXI', // Secret key - Replace with real HashSecret
    'vnp_Url' => 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html', // URL thanh toán của VNPAY
    'vnp_Returnurl' => '', // URL thông báo kết quả giao dịch khi Khách hàng kết thúc thanh toán
    'vnp_apiUrl' => 'http://sandbox.vnpayment.vn/merchant_webapi/merchant.html', // URL API tra cứu giao dịch

    // Environment
    'environment' => 'sandbox', // sandbox hoặc production

    // Các ngân hàng hỗ trợ
    'banks' => [
        'VNPAYQR' => 'Thanh toán VNPAY QR',
        'VNBANK' => 'Thanh toán qua ATM-Tài khoản ngân hàng nội địa',
        'INTCARD' => 'Thanh toán qua thẻ quốc tế',
        'VISA' => 'Thanh toán qua thẻ VISA/MASTER',
        'MASTERCARD' => 'Thanh toán qua thẻ MASTERCARD',
        'JCB' => 'Thanh toán qua thẻ JCB',
        'UPI' => 'Thanh toán qua UPI',
        'VIB' => 'Ngân hàng VIB',
        'VIETCOMBANK' => 'Ngân hàng Vietcombank',
        'VIETINBANK' => 'Ngân hàng Vietinbank',
        'AGRIBANK' => 'Ngân hàng Agribank',
        'BIDV' => 'Ngân hàng BIDV',
        'SACOMBANK' => 'Ngân hàng Sacombank',
        'TECHCOMBANK' => 'Ngân hàng Techcombank',
        'VPBANK' => 'Ngân hàng VPBank',
        'MBBANK' => 'Ngân hàng MB',
        'ACB' => 'Ngân hàng ACB',
        'OCB' => 'Ngân hàng OCB',
        'SHB' => 'Ngân hàng SHB',
        'IVB' => 'Ngân hàng IVB',
        'TPBANK' => 'Ngân hàng TPBank'
    ]
];
