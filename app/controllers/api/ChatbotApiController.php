<?php
/**
 * Chatbot API Controller
 * 5S Fashion E-commerce Platform
 */

class ChatbotApiController extends ApiController
{
    public function __construct()
    {
        parent::__construct();

        // Ensure UTF-8 encoding
        header('Content-Type: application/json; charset=utf-8');
        mb_internal_encoding('UTF-8');
        mb_http_output('UTF-8');
    }

    /**
     * Process chat message
     */
    public function chat()
    {
        // Debug logging
        error_log('Chatbot API called');
        error_log('Request data: ' . json_encode($this->requestData));

        $message = trim($this->requestData['message'] ?? '');

        error_log('Processed message: ' . $message);

        if (empty($message)) {
            ApiResponse::error('Tin nhắn không được để trống', 400);
        }

        // Phân tích tin nhắn và tạo phản hồi
        $response = $this->processMessage($message);

        ApiResponse::success($response, 'Phản hồi thành công');
    }

    /**
     * Get best selling products
     */
    public function getBestSellingProducts()
    {
        try {
            $sql = "SELECT p.*,
                           COALESCE(SUM(oi.quantity), 0) as total_sold,
                           COALESCE(AVG(r.rating), 0) as avg_rating
                    FROM products p
                    LEFT JOIN order_items oi ON p.id = oi.product_id
                    LEFT JOIN reviews r ON p.id = r.product_id
                    WHERE p.status = 'active'
                    GROUP BY p.id
                    ORDER BY total_sold DESC, p.created_at DESC
                    LIMIT 5";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $products = $stmt->fetchAll();

            ApiResponse::success([
                'products' => $this->formatProducts($products)
            ], 'Lấy sản phẩm bán chạy thành công');
        } catch (Exception $e) {
            ApiResponse::serverError('Lỗi khi lấy sản phẩm bán chạy');
        }
    }

    /**
     * Get discounted products
     */
    public function getDiscountedProducts()
    {
        try {
            $sql = "SELECT * FROM products
                    WHERE status = 'active'
                    AND discount_percentage > 0
                    ORDER BY discount_percentage DESC, created_at DESC
                    LIMIT 5";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $products = $stmt->fetchAll();

            ApiResponse::success([
                'products' => $this->formatProducts($products)
            ], 'Lấy sản phẩm giảm giá thành công');
        } catch (Exception $e) {
            ApiResponse::serverError('Lỗi khi lấy sản phẩm giảm giá');
        }
    }

    /**
     * Get new products
     */
    public function getNewProducts()
    {
        try {
            $sql = "SELECT * FROM products
                    WHERE status = 'active'
                    ORDER BY created_at DESC
                    LIMIT 5";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $products = $stmt->fetchAll();

            ApiResponse::success([
                'products' => $this->formatProducts($products)
            ], 'Lấy sản phẩm mới thành công');
        } catch (Exception $e) {
            ApiResponse::serverError('Lỗi khi lấy sản phẩm mới');
        }
    }

    /**
     * Process user message and generate response
     */
    private function processMessage($message)
    {
        $message = strtolower($message);

        // Patterns and responses
        $patterns = [
            // Sản phẩm bán chạy
            '/(?:sản phẩm|sp).*(?:bán chạy|hot|nổi bật|phổ biến)/i' => [
                'type' => 'best_selling',
                'response' => 'Đây là những sản phẩm bán chạy nhất tại 5S Fashion:'
            ],

            // Sản phẩm giảm giá
            '/(?:giảm giá|khuyến mãi|sale|discount|ưu đãi)/i' => [
                'type' => 'discounted',
                'response' => 'Các sản phẩm đang được giảm giá tại 5S Fashion:'
            ],

            // Sản phẩm mới
            '/(?:sản phẩm mới|sp mới|hàng mới|new)/i' => [
                'type' => 'new_products',
                'response' => 'Những sản phẩm mới nhất tại 5S Fashion:'
            ],

            // Đơn hàng
            '/(?:đơn hàng|order|kiểm tra.*đơn|theo dõi.*đơn)/i' => [
                'type' => 'order_info',
                'response' => 'Để kiểm tra đơn hàng, bạn có thể:\n• Đăng nhập vào tài khoản và xem phần "Đơn hàng của tôi"\n• Liên hệ hotline: 1900-xxxx với mã đơn hàng\n• Email: support@5sfashion.com'
            ],

            // Thanh toán
            '/(?:thanh toán|payment|phương thức.*thanh toán|tt)/i' => [
                'type' => 'payment_info',
                'response' => '5S Fashion hỗ trợ các phương thức thanh toán:\n• Thanh toán khi nhận hàng (COD)\n• Chuyển khoản ngân hàng\n• Ví điện tử (Momo, ZaloPay)\n• Thẻ tín dụng/ghi nợ'
            ],

            // Vận chuyển
            '/(?:vận chuyển|giao hàng|ship|shipping|phí.*ship)/i' => [
                'type' => 'shipping_info',
                'response' => 'Thông tin vận chuyển:\n• Miễn phí ship đơn hàng từ 500k\n• Giao hàng toàn quốc 2-5 ngày\n• Giao hàng nhanh trong ngày tại TP.HCM và Hà Nội\n• Phí ship: 30k nội thành, 50k ngoại thành'
            ],

            // Đổi trả
            '/(?:đổi.*trả|return|hoàn.*trả|bảo hành)/i' => [
                'type' => 'return_policy',
                'response' => 'Chính sách đổi trả:\n• Đổi trả trong 7 ngày\n• Sản phẩm còn nguyên tem, mác\n• Miễn phí đổi size lần đầu\n• Hoàn tiền 100% nếu lỗi từ nhà sản xuất'
            ],

            // Size/Kích cỡ
            '/(?:size|kích cỡ|kích thước|số đo)/i' => [
                'type' => 'size_guide',
                'response' => 'Hướng dẫn chọn size:\n• S: 45-50kg, cao 1m50-1m60\n• M: 50-55kg, cao 1m55-1m65\n• L: 55-60kg, cao 1m60-1m70\n• XL: 60-65kg, cao 1m65-1m75\n\nBạn có thể xem bảng size chi tiết trong từng sản phẩm!'
            ]
        ];

        // Tìm pattern phù hợp
        foreach ($patterns as $pattern => $config) {
            if (preg_match($pattern, $message)) {
                $response = [
                    'message' => $config['response'],
                    'type' => $config['type']
                ];

                // Nếu là request về sản phẩm, thêm dữ liệu sản phẩm
                if (in_array($config['type'], ['best_selling', 'discounted', 'new_products'])) {
                    $response['products'] = $this->getProductsForType($config['type']);
                }

                return $response;
            }
        }

        // Phản hồi mặc định
        return [
            'message' => 'Cảm ơn bạn đã liên hệ! Tôi có thể giúp bạn:\n• Xem sản phẩm bán chạy, giảm giá, mới\n• Thông tin về đơn hàng, thanh toán\n• Hướng dẫn đổi trả, vận chuyển\n• Tư vấn size\n\nBạn có thể hỏi cụ thể hơn nhé!',
            'type' => 'default'
        ];
    }

    /**
     * Get products for specific type
     */
    private function getProductsForType($type)
    {
        try {
            switch ($type) {
                case 'best_selling':
                    $sql = "SELECT p.*, COALESCE(SUM(oi.quantity), 0) as total_sold
                            FROM products p
                            LEFT JOIN order_items oi ON p.id = oi.product_id
                            WHERE p.status = 'active'
                            GROUP BY p.id
                            ORDER BY total_sold DESC
                            LIMIT 3";
                    break;

                case 'discounted':
                    $sql = "SELECT * FROM products
                            WHERE status = 'active' AND discount_percentage > 0
                            ORDER BY discount_percentage DESC
                            LIMIT 3";
                    break;

                case 'new_products':
                    $sql = "SELECT * FROM products
                            WHERE status = 'active'
                            ORDER BY created_at DESC
                            LIMIT 3";
                    break;

                default:
                    return [];
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $products = $stmt->fetchAll();
            return $this->formatProducts($products);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Format products for response
     */
    private function formatProducts($products)
    {
        return array_map(function($product) {
            $finalPrice = $product['price'];
            if ($product['discount_percentage'] > 0) {
                $finalPrice = $product['price'] * (100 - $product['discount_percentage']) / 100;
            }

            return [
                'id' => (int)$product['id'],
                'name' => $product['name'],
                'slug' => $product['slug'],
                'price' => (float)$product['price'],
                'discount_percentage' => (float)($product['discount_percentage'] ?? 0),
                'final_price' => $finalPrice,
                'image' => $product['image'] ? UPLOAD_URL . '/products/' . $product['image'] : null,
                'short_description' => substr($product['description'] ?? '', 0, 100) . '...',
                'url' => BASE_URL . '/product/' . $product['slug']
            ];
        }, $products);
    }
}
