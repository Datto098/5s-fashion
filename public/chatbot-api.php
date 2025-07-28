<?php
header('Content-Type: application/json; charset=utf-8');
mb_internal_encoding('UTF-8');

// Get request method and data
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

if ($method === 'POST' && isset($input['message'])) {
    $message = trim($input['message']);
    $message_lower = mb_strtolower($message, 'UTF-8');

    // Simple pattern matching
    if (preg_match('/(?:xin chào|hello|hi|chào)/i', $message_lower)) {
        $response = [
            'success' => true,
            'message' => 'Phản hồi thành công',
            'data' => [
                'message' => 'Xin chào! Tôi là trợ lý ảo 5S Fashion. Tôi có thể giúp bạn:\n\n• Tìm sản phẩm bán chạy, giảm giá\n• Thông tin đơn hàng, thanh toán\n• Hướng dẫn chọn size\n• Chính sách đổi trả\n\nBạn cần hỗ trợ gì?',
                'type' => 'greeting'
            ]
        ];
    } else if (preg_match('/(?:sản phẩm|sp).*(?:bán chạy|hot|nổi bật)/i', $message_lower)) {
        $response = [
            'success' => true,
            'message' => 'Phản hồi thành công',
            'data' => [
                'message' => 'Đây là những sản phẩm bán chạy nhất tại 5S Fashion:',
                'type' => 'best_selling',
                'products' => [
                    [
                        'id' => 1,
                        'name' => 'Áo thun cotton basic',
                        'price' => 299000,
                        'discount_percentage' => 20,
                        'final_price' => 239200,
                        'image' => null,
                        'url' => '#'
                    ]
                ]
            ]
        ];
    } else if (preg_match('/(?:giảm giá|khuyến mãi|sale|discount)/i', $message_lower)) {
        $response = [
            'success' => true,
            'message' => 'Phản hồi thành công',
            'data' => [
                'message' => 'Các sản phẩm đang được giảm giá tại 5S Fashion:',
                'type' => 'discounted',
                'products' => [
                    [
                        'id' => 2,
                        'name' => 'Quần jeans skinny',
                        'price' => 599000,
                        'discount_percentage' => 25,
                        'final_price' => 449250,
                        'image' => null,
                        'url' => '#'
                    ]
                ]
            ]
        ];
    } else if (preg_match('/(?:đơn hàng|order)/i', $message_lower)) {
        $response = [
            'success' => true,
            'message' => 'Phản hồi thành công',
            'data' => [
                'message' => 'Để kiểm tra đơn hàng, bạn có thể:\n• Đăng nhập vào tài khoản và xem "Đơn hàng của tôi"\n• Liên hệ hotline: 1900-xxxx với mã đơn hàng\n• Email: support@5sfashion.com',
                'type' => 'order_info'
            ]
        ];
    } else if (preg_match('/(?:thanh toán|payment)/i', $message_lower)) {
        $response = [
            'success' => true,
            'message' => 'Phản hồi thành công',
            'data' => [
                'message' => '5S Fashion hỗ trợ các phương thức thanh toán:\n• Thanh toán khi nhận hàng (COD)\n• Chuyển khoản ngân hàng\n• Ví điện tử (Momo, ZaloPay)\n• Thẻ tín dụng/ghi nợ',
                'type' => 'payment_info'
            ]
        ];
    } else if (preg_match('/(?:size|kích cỡ)/i', $message_lower)) {
        $response = [
            'success' => true,
            'message' => 'Phản hồi thành công',
            'data' => [
                'message' => 'Hướng dẫn chọn size:\n• S: 45-50kg, cao 1m50-1m60\n• M: 50-55kg, cao 1m55-1m65\n• L: 55-60kg, cao 1m60-1m70\n• XL: 60-65kg, cao 1m65-1m75\n\nBạn có thể xem bảng size chi tiết trong từng sản phẩm!',
                'type' => 'size_guide'
            ]
        ];
    } else {
        $response = [
            'success' => true,
            'message' => 'Phản hồi thành công',
            'data' => [
                'message' => 'Cảm ơn bạn đã liên hệ! Tôi có thể giúp bạn:\n• Xem sản phẩm bán chạy, giảm giá\n• Thông tin về đơn hàng, thanh toán\n• Hướng dẫn đổi trả, vận chuyển\n• Tư vấn size\n\nBạn có thể hỏi cụ thể hơn nhé!',
                'type' => 'default'
            ]
        ];
    }
} else {
    $response = [
        'success' => false,
        'message' => 'Invalid request method or missing message',
        'data' => null
    ];
}

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
