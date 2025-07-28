<?php
// Simple test for chatbot API
header('Content-Type: application/json; charset=utf-8');
mb_internal_encoding('UTF-8');

$response = [
    'success' => true,
    'message' => 'Xin chào! Tôi là trợ lý ảo của 5S Fashion. Tôi có thể giúp bạn tìm sản phẩm và trả lời câu hỏi.',
    'data' => [
        'type' => 'welcome',
        'suggestions' => [
            'Sản phẩm bán chạy',
            'Sản phẩm giảm giá',
            'Thông tin đơn hàng',
            'Hướng dẫn size'
        ]
    ]
];

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
