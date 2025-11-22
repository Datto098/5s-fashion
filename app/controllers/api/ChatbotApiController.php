<?php
/**
 * Chatbot API Controller
 * zone Fashion E-commerce Platform
 */

// Define a fallback ApiController if it doesn't exist
if (!class_exists('ApiController')) {
    class ApiController {
        protected $db;
        protected $requestMethod;
        protected $requestData;

        public function __construct() {
            // Basic initialization
            $this->requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
            $json = file_get_contents('php://input');
            $this->requestData = json_decode($json, true) ?: [];
        }
    }
}

class ChatbotApiController extends ApiController
{
    // Session memory to track conversation context
    private $conversationContext = [];

    public function __construct()
    {
        try {
            // Check if ApiController exists before calling parent constructor
            if (class_exists('ApiController')) {
                parent::__construct();
            } else {
                throw new Exception('ApiController class not found');
            }

            // Initialize conversation context from session if available
            if (isset($_SESSION['chatbot_context'])) {
                $this->conversationContext = $_SESSION['chatbot_context'];
            }
        } catch (Exception $e) {
            // Log the error
            error_log('ChatbotApiController Error: ' . $e->getMessage());

            // Direct access mode - set up manually
            // Enable CORS
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

            // Get request method
            $this->requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

            // Parse JSON input
            $json = file_get_contents('php://input');
            $this->requestData = json_decode($json, true) ?: [];

            // Initialize database
            $config = require ROOT_PATH . '/app/config/database.php';
            $db = $config['connections']['mysql'];
            $this->db = new PDO(
                "mysql:host={$db['host']};dbname={$db['database']};charset=utf8mb4",
                $db['username'],
                $db['password'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            // Check for session
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Initialize conversation context from session if available
            if (isset($_SESSION['chatbot_context'])) {
                $this->conversationContext = $_SESSION['chatbot_context'];
            }
        }

        // Ensure UTF-8 encoding
        header('Content-Type: application/json; charset=utf-8');
        mb_internal_encoding('UTF-8');
        mb_http_output('UTF-8');
    }    /**
     * Process chat message
     */
    public function chat()
    {
        // Debug logging
        error_log('Chatbot API called');
        error_log('Request data: ' . json_encode($this->requestData));

        $message = trim($this->requestData['message'] ?? '');
        $sessionId = $this->requestData['sessionId'] ?? null;

        error_log('Processed message: ' . $message);

        if (empty($message)) {
            ApiResponse::error('Tin nhắn không được để trống', 400);
        }

        // Extract conversation context
        $previousContext = isset($_SESSION['chatbot_context']) ? $_SESSION['chatbot_context'] : [];

        // Phân tích tin nhắn và tạo phản hồi
        $response = $this->processMessage($message, $previousContext);

        // Save updated context to session
        $_SESSION['chatbot_context'] = $response['context'];
        unset($response['context']); // Don't send context to frontend

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
                    WHERE p.status = 'published' OR p.status = ''
                    GROUP BY p.id
                    ORDER BY total_sold DESC, p.created_at DESC
                    LIMIT 5";

            // Sử dụng Database singleton để lấy kết nối
            $db = Database::getInstance();
            $products = $db->fetchAll($sql);

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
                    WHERE (status = 'published' OR status = '')
                    AND discount_percentage > 0
                    ORDER BY discount_percentage DESC, created_at DESC
                    LIMIT 5";

            $db = Database::getInstance();
            $products = $db->fetchAll($sql);

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
                    WHERE (status = 'published' OR status = '')
                    ORDER BY created_at DESC
                    LIMIT 5";

            $db = Database::getInstance();
            $products = $db->fetchAll($sql);

            ApiResponse::success([
                'products' => $this->formatProducts($products)
            ], 'Lấy sản phẩm mới thành công');
        } catch (Exception $e) {
            ApiResponse::serverError('Lỗi khi lấy sản phẩm mới');
        }
    }

    /**
     * Process user message and generate response with enhanced NLP
     */
    public function processMessage($message, $previousContext = [])
    {
        $message = mb_strtolower($message);

        // Initialize context if not exists
        $context = $previousContext;
        $context['last_message'] = $message;
        $context['last_intent'] = $context['current_intent'] ?? null;

        // Default response
        $response = [
            'message' => "Xin chào! Rất vui được hỗ trợ bạn. Tôi là trợ lý ảo của zone Fashion. Bạn cần tôi giúp gì hôm nay?",
            'type' => 'default',
            'context' => $context
        ];

        // 1. Check for follow-up questions based on context
        if (isset($context['current_intent']) && $this->isFollowUpQuestion($message)) {
            return $this->handleFollowUp($message, $context);
        }

        // 2. Check for product search intent (exclude rating/review queries)
        $searchPattern = '/(?:tìm|tìm kiếm|tìm sản phẩm|tìm đồ|tìm quần|tìm áo|hiển thị|show me|tìm mẫu|có|có bán|cửa hàng có).*?([\p{L}\s\d\-\+]+?)(?:\s+không|không\?|$|\?)/ui';
        if (preg_match($searchPattern, $message, $matches) && !empty($matches[1])) {
            $keywords = trim($matches[1]);
            
            // Skip if this is a rating/review query
            if (preg_match('/(?:đánh giá|rating|review|feedback|chất lượng)/i', $keywords)) {
                // Let it fall through to step 4 (intent patterns)
            } else {
                $context['current_intent'] = 'product_search';
                $context['search_keywords'] = $keywords;

                $products = $this->searchProducts($keywords);

                if (count($products) > 0) {
                    return [
                        'message' => "Đây là kết quả tìm kiếm cho \"{$keywords}\":",
                        'type' => 'product_search',
                        'products' => $products,
                        'context' => $context
                    ];
                } else {
                    return [
                        'message' => "Xin lỗi, tôi không tìm thấy sản phẩm nào phù hợp với từ khóa \"{$keywords}\". Bạn có thể thử với từ khóa khác hoặc hỏi tôi về sản phẩm bán chạy, sản phẩm giảm giá.",
                        'type' => 'product_search_empty',
                        'context' => $context
                    ];
                }
            }
        }

        // 3. Check for price range search
        $priceRangePattern = '/(?:tìm|tìm sản phẩm|có sản phẩm|hiển thị).*?(?:giá|giá từ|giá trong khoảng|khoảng giá).*?(\d[\d\.,]*)\s*(?:đến|tới|đến|đ|k|nghìn|ngàn|vnd|-)\s*(\d[\d\.,]*)/ui';
        if (preg_match($priceRangePattern, $message, $matches)) {
            $minPrice = $this->normalizePrice($matches[1]);
            $maxPrice = $this->normalizePrice($matches[2]);

            $context['current_intent'] = 'price_range_search';
            $context['min_price'] = $minPrice;
            $context['max_price'] = $maxPrice;

            $products = $this->searchProducts(null, null, $minPrice, $maxPrice);

            if (count($products) > 0) {
                return [
                    'message' => "Đây là các sản phẩm có giá từ " . number_format($minPrice) . "đ đến " . number_format($maxPrice) . "đ:",
                    'type' => 'price_range_search',
                    'products' => $products,
                    'context' => $context
                ];
            } else {
                return [
                    'message' => "Xin lỗi, tôi không tìm thấy sản phẩm nào trong khoảng giá từ " . number_format($minPrice) . "đ đến " . number_format($maxPrice) . "đ. Bạn có thể thử với khoảng giá khác hoặc hỏi tôi về sản phẩm bán chạy.",
                    'type' => 'price_range_search_empty',
                    'context' => $context
                ];
            }
        }

        // 4. Check for intent patterns - similar to original but improved
        $patterns = [
            // Tìm số lượng sản phẩm có đánh giá tốt cụ thể (ví dụ: "3 sản phẩm đánh giá tốt", "5 áo review cao")
            '/(?:(\d+)\s*(?:sản phẩm|sp|đồ|món|hàng|áo|quần|váy|đầm)\s*(?:có\s*)?(?:đánh giá tốt|rating cao|review tốt|đánh giá cao|chất lượng tốt))|(?:(\d+)\s*(áo|quần|váy|đầm)\s*(?:có\s*)?(?:đánh giá tốt|rating cao|review tốt))/i' => [
                'type' => 'search_multiple_best_rated_by_category',
                'intent' => 'search_multiple_best_rated_by_category',
                'response' => 'search_multiple_best_rated_by_category'
            ],

            // Tìm 1 sản phẩm có đánh giá tốt nhất theo loại cụ thể (ví dụ: "áo đánh giá tốt nhất", "váy review cao nhất")
            '/(?:(áo|quần|váy|đầm|jacket|hoodie|polo|shirt|jean|kaki|short|dress|skirt)\s*(?:nào\s*)?(?:có\s*)?(?:đánh giá tốt nhất|rating cao nhất|review tốt nhất|đánh giá cao nhất|chất lượng tốt nhất))/i' => [
                'type' => 'search_single_best_rated_by_category',
                'intent' => 'search_single_best_rated_by_category',
                'response' => 'search_single_best_rated_by_category'
            ],

            // Tìm 1 sản phẩm có đánh giá tốt nhất chung (ví dụ: "sản phẩm nào có đánh giá tốt nhất")
            '/(?:sản phẩm|sp|đồ|món|hàng|item)\s*(?:nào\s*)?(?:có\s*)?(?:đánh giá tốt nhất|rating cao nhất|review tốt nhất|đánh giá cao nhất|chất lượng tốt nhất)/i' => [
                'type' => 'search_single_best_rated_general',
                'intent' => 'search_single_best_rated_general',
                'response' => 'search_single_best_rated_general'
            ],

            // Tìm sản phẩm có đánh giá/rating tốt (multiple, general) - đặt sau các pattern cụ thể
            '/(?:sản phẩm|sp|đồ|món|hàng|item).*(?:đánh giá tốt|rating cao|review tốt|đánh giá cao|chất lượng tốt|được đánh giá cao|có đánh giá tốt|có rating tốt)/i' => [
                'type' => 'search_best_rated_products',
                'intent' => 'search_best_rated_products',
                'response' => 'search_best_rated_products'
            ],

            // Tìm số lượng sản phẩm rẻ nhất cụ thể (ví dụ: "5 áo rẻ nhất", "3 quần giá tốt")
            '/(?:(\d+)\s*(áo|quần|váy|đầm|jacket|hoodie|polo|shirt|jean|kaki|short|dress|skirt)\s*(?:rẻ nhất|giá rẻ|giá tốt|giá thấp|bèo|hạ))|(?:(áo|quần|váy|đầm)\s*(\d+)\s*(?:rẻ nhất|giá rẻ|giá tốt))/i' => [
                'type' => 'search_multiple_cheapest_by_category',
                'intent' => 'search_multiple_cheapest_by_category',
                'response' => 'search_multiple_cheapest_by_category'
            ],

            // Tìm số lượng sản phẩm đắt nhất cụ thể (ví dụ: "5 áo đắt nhất", "3 quần giá cao")
            '/(?:(\d+)\s*(áo|quần|váy|đầm|jacket|hoodie|polo|shirt|jean|kaki|short|dress|skirt)\s*(?:đắt nhất|giá đắt|giá cao|cao nhất))|(?:(áo|quần|váy|đầm)\s*(\d+)\s*(?:đắt nhất|giá đắt|giá cao))/i' => [
                'type' => 'search_multiple_expensive_by_category',
                'intent' => 'search_multiple_expensive_by_category',
                'response' => 'search_multiple_expensive_by_category'
            ],

            // Tìm 1 sản phẩm rẻ nhất theo loại (ví dụ: "áo rẻ nhất", "quần rẻ nhất")
            '/(?:(áo|quần|váy|đầm|jacket|hoodie|polo|shirt|jean|kaki|short|dress|skirt)\s*(?:nào\s*)?(?:rẻ nhất|giá rẻ nhất|giá thấp nhất|bèo nhất))|(?:(?:rẻ nhất|giá rẻ nhất|thấp nhất|giá thấp nhất|bèo nhất|hạ nhất)\s*(?:áo|quần|váy|đầm))/i' => [
                'type' => 'search_single_cheapest_by_category',
                'intent' => 'search_single_cheapest_by_category',
                'response' => 'search_single_cheapest_by_category'
            ],

            // Tìm 1 sản phẩm đắt nhất theo loại (ví dụ: "áo đắt nhất", "quần đắt nhất")
            '/(?:(áo|quần|váy|đầm|jacket|hoodie|polo|shirt|jean|kaki|short|dress|skirt)\s*(?:nào\s*)?(?:đắt nhất|giá đắt nhất|giá cao nhất|cao nhất))|(?:(?:đắt nhất|giá đắt nhất|cao nhất|giá cao nhất)\s*(?:áo|quần|váy|đầm))/i' => [
                'type' => 'search_single_expensive_by_category',
                'intent' => 'search_single_expensive_by_category',
                'response' => 'search_single_expensive_by_category'
            ],

            // Sản phẩm bán chạy - extended patterns
            '/(?:sản phẩm|sp|đồ|quần áo|mẫu|món|hàng).*(?:bán chạy|hot|nổi bật|phổ biến|được ưa chuộng|được yêu thích|bán tốt)/i' => [
                'type' => 'best_selling',
                'intent' => 'best_selling_products',
                'response' => 'Đây là những sản phẩm bán chạy nhất tại zone Fashion:'
            ],

            // Sản phẩm giảm giá - removed 'rẻ' to avoid conflict
            '/(?:giảm giá|khuyến mãi|sale|discount|ưu đãi|đang giảm|đang sale|đang khuyến mãi|hời)/i' => [
                'type' => 'discounted',
                'intent' => 'discounted_products',
                'response' => 'Các sản phẩm đang được giảm giá tại zone Fashion:'
            ],

            // Sản phẩm mới - extended patterns
            '/(?:sản phẩm mới|sp mới|hàng mới|new|mới về|mới ra mắt|vừa về|vừa ra|hàng mới về)/i' => [
                'type' => 'new_products',
                'intent' => 'new_products',
                'response' => 'Những sản phẩm mới nhất tại zone Fashion:'
            ],



            // Product pricing consultation - AI-powered
            '/(?:áo|quần|sản phẩm|món|item).*(?:rẻ nhất|giá rẻ|giá tốt|tiết kiệm|ưu đãi|giảm giá|giá.*thấp|phải chăng)/i' => [
                'type' => 'price_consultation', 
                'intent' => 'ai_consultation',
                'use_ai' => true,
                'context' => 'pricing',
                'system_prompt' => '
Bạn là chuyên gia bán hàng thời trang của Zone Fashion. Hãy tư vấn sản phẩm giá tốt cho khách hàng dựa trên database sản phẩm.

Các loại sản phẩm phổ biến:
- Áo thun basic: 200k-350k
- Áo sơ mi: 350k-650k  
- Quần jeans: 450k-850k
- Áo khoác hoodie: 550k-950k
- Đầm/Váy: 400k-800k

Lưu ý:
- Luôn đề xuất xem sản phẩm giảm giá
- Gợi ý sản phẩm basic để tiết kiệm
- Đề xuất mua nhiều món để được free ship

Hãy trả lời thân thiện và gợi ý cụ thể.'
            ],
            
            // Product rating consultation - AI-powered  
            '/(?:sản phẩm|món|áo|quần|item).*(?:đánh giá.*tốt|rating.*cao|chất lượng.*tốt|tốt nhất|phổ biến|bán chạy|review.*tốt|ưa chuộng)/i' => [
                'type' => 'rating_consultation',
                'intent' => 'ai_consultation', 
                'use_ai' => true,
                'context' => 'ratings',
                'system_prompt' => '
Bạn là chuyên gia tư vấn thời trang của Zone Fashion. Hãy gợi ý sản phẩm chất lượng tốt, được đánh giá cao.

Sản phẩm nổi bật:
- Áo thun basic trắng/đen: 4.8/5 sao - chất cotton mềm mại
- Quần jeans skinny: 4.7/5 sao - form chuẩn, bền đẹp
- Áo sơ mi trắng: 4.6/5 sao - phù hợp đi làm đi chơi
- Hoodie unisex: 4.8/5 sao - ấm áp, thoáng mát
- Đầm midi: 4.5/5 sao - kiểu dáng thanh lịch

Lưu ý:
- Đề xuất sản phẩm có đánh giá trên 4.5 sao
- Nêu rõ ưu điểm của sản phẩm
- Gợi ý xem review thật trước khi mua

Hãy trả lời nhiệt tình và đưa ra gợi ý cụ thể.'
            ],

            // Fashion styling consultation - AI-powered
            '/(?:tư vấn|tư vấn.*thời trang|phối đồ|cách phối|mặc.*sao|phối.*sao|phối.*thế nào|phối.*như thế nào|mặc.*với|outfit|style|phong cách)/i' => [
                'type' => 'fashion_styling',
                'intent' => 'ai_consultation',
                'use_ai' => true,
                'context' => 'styling', 
                'system_prompt' => '
Bạn là stylist chuyên nghiệp của Zone Fashion. Hãy tư vấn cách phối đồ và style phù hợp.

Các combo phối đồ kinh điển:
- Áo sơ mi trắng + quần jeans xanh + giày sneaker: phong cách trẻ trung
- Áo thun đen + quần âu + giày tây: lịch lãm nhưng không quá chính thức
- Hoodie + quần jogger: thoải mái, thích hợp ở nhà
- Đầm midi + áo khoác jeans: nữ tính nhưng cá tính
- Blazer + quần jeans + giày cao gót: sang trọng mà không gây gắng

Màu sắc phối hợp:
- Trắng + Đen: kinh điển
- Xanh navy + Trắng: tươi mới 
- Be/Nâu + Trắng: nhẹ nhàng
- Xám + Hồng nude: thanh lịch

Hãy tư vấn cụ thể và thực tế.'
            ],

            // Đơn hàng - extended patterns
            '/(?:đơn hàng|order|kiểm tra.*đơn|theo dõi.*đơn|tình trạng.*đơn|trạng thái.*đơn|đơn.*mua|mua hàng|đặt hàng)/i' => [
                'type' => 'order_info',
                'intent' => 'order_information',
                'response' => "<div class='order-info-block'>
                    <h4><i class='fas fa-box'></i> Hướng dẫn kiểm tra đơn hàng</h4>
                    <ul class='order-info-list'>
                        <li><i class='fas fa-user-circle'></i> <b>Đăng nhập</b> vào tài khoản và xem mục <b>Đơn hàng của tôi</b></li>
                        <li><i class='fas fa-phone'></i> Gọi <b>Hotline: <a href='tel:1900xxxx'>1900-xxxx</a></b> (cung cấp mã đơn hàng)</li>
                        <li><i class='fas fa-envelope'></i> Email: <a href='mailto:support@zonefashion.com'>support@zonefashion.com</a></li>
                    </ul>
                    <p class='order-info-note'>Nếu cần hỗ trợ nhanh, hãy gửi mã đơn hàng qua hotline hoặc email trên!</p>
                </div>"
            ],

            // Thanh toán - extended patterns
            '/(?:thanh toán|payment|phương thức.*thanh toán|tt|trả tiền|trả bằng|tiền|cách.*thanh toán|trả góp|trả qua|qua thẻ)/i' => [
                'type' => 'payment_info',
                'intent' => 'payment_information',
                'response' => 'zone Fashion hỗ trợ các phương thức thanh toán:\n• Thanh toán khi nhận hàng (COD)\n• Chuyển khoản ngân hàng\n• Ví điện tử (Momo, ZaloPay)\n• Thẻ tín dụng/ghi nợ'
            ],

            // Vận chuyển - extended patterns
            '/(?:vận chuyển|giao hàng|ship|shipping|phí.*ship|ship.*phí|giao.*hàng.*mất|mất.*bao|bao.*lâu|lâu|thời gian|ship.*đến|giao.*đến)/i' => [
                'type' => 'shipping_info',
                'intent' => 'shipping_information',
                'response' => 'Thông tin vận chuyển:\n• Miễn phí ship đơn hàng từ 500k\n• Giao hàng toàn quốc 2-5 ngày\n• Giao hàng nhanh trong ngày tại TP.HCM và Hà Nội\n• Phí ship: 30k nội thành, 50k ngoại thành'
            ],

            // Đổi trả - extended patterns
            '/(?:đổi.*trả|return|hoàn.*trả|bảo hành|trả.*lại|đổi.*lại|hoàn.*tiền|không.*vừa.*ý|không.*ưng|không.*thích|không.*vừa)/i' => [
                'type' => 'return_policy',
                'intent' => 'return_policy',
                'response' => 'Chính sách đổi trả:\n• Đổi trả trong 7 ngày\n• Sản phẩm còn nguyên tem, mác\n• Miễn phí đổi size lần đầu\n• Hoàn tiền 100% nếu lỗi từ nhà sản xuất'
            ],

            // Size/Kích cỡ - AI-powered consultation
            '/(?:size|kích cỡ|kích thước|số đo|chọn.*size|chọn.*cỡ|cỡ|cỡ.*nào|size.*nào|size.*phù hợp|cao.*nặng|nặng.*cao|chiều cao.*cân nặng|cân nặng.*chiều cao|mặc.*size|tôi.*cao|tôi.*nặng|với.*cao|với.*nặng|hướng dẫn.*size|hướng dẫn.*chọn.*size|hướng dẫn.*cỡ)/i' => [
                'type' => 'size_consultation',
                'intent' => 'ai_consultation',
                'use_ai' => true,
                'context' => 'size_guide',
                'system_prompt' => '
Bạn là chuyên gia tư vấn size thời trang của Zone Fashion. Hãy đưa ra KÍCH CỠ CỤ THỂ cho khách hàng.

Bảng size chuẩn Zone Fashion:
- Size S: 45-50kg, chiều cao 1m50-1m60
- Size M: 50-55kg, chiều cao 1m55-1m65  
- Size L: 55-60kg, chiều cao 1m60-1m70
- Size XL: 60-65kg, chiều cao 1m65-1m75
- Size XXL: 65-75kg, chiều cao 1m70-1m80
- Size XXXL: 75-85kg, chiều cao 1m75-1m85

Quy tắc tư vấn:
1. Nếu nặng hơn mức tiêu chuẩn của size đó 5kg trở lên → chọn size lớn hơn 1 bậc
2. Áo sơ mi/polo thường ôm hơn áo thun → nên chọn size lớn hơn
3. Nếu thích áo rộng → chọn size lớn hơn 1 bậc
4. Nếu cân nặng ở giữa 2 mức → ưu tiên theo chiều cao

QUAN TRỌNG: Phải đưa ra kết luận rõ ràng "Tôi khuyên bạn chọn size [X]" và giải thích lý do ngắn gọn.

Ví dụ trả lời: "Với chiều cao 1m7 và cân nặng 80kg, tôi khuyên bạn chọn size XXL. Do bạn nặng hơn mức chuẩn của size XL (65kg) khá nhiều, size XXL sẽ vừa vặn và thoải mái hơn."'
            ],

            // Thông tin cửa hàng - new pattern
            '/(?:cửa hàng|store|shop|địa chỉ|địa điểm|chỗ|nơi|ở đâu|chi nhánh)/i' => [
                'type' => 'store_info',
                'intent' => 'store_information',
                'response' => "<div class='store-info-card'>
                    <div class='store-info-header'>
                        <i class='fas fa-store'></i>
                        <h4>zone Fashion - Hệ Thống Cửa Hàng</h4>
                    </div>
                    <div class='store-branches'>
                        <div class='branch-item'>
                            <div class='branch-name'><i class='fas fa-map-marker-alt'></i> Chi nhánh 1</div>
                            <div class='branch-address'>123 Nguyễn Trãi, Q.1, TP.HCM</div>
                        </div>
                        <div class='branch-item'>
                            <div class='branch-name'><i class='fas fa-map-marker-alt'></i> Chi nhánh 2</div>
                            <div class='branch-address'>456 Lê Lợi, Q.3, TP.HCM</div>
                        </div>
                        <div class='branch-item'>
                            <div class='branch-name'><i class='fas fa-map-marker-alt'></i> Chi nhánh 3</div>
                            <div class='branch-address'>789 Cách Mạng Tháng 8, Q.10, TP.HCM</div>
                        </div>
                    </div>
                    <div class='store-info-footer'>
                        <div class='info-item'><i class='far fa-clock'></i> Giờ mở cửa: 8:00 - 22:00 (tất cả các ngày)</div>
                        <div class='info-item'><i class='fas fa-phone-alt'></i> Hotline: 1900-xxxx</div>
                    </div>
                </div>"
            ],

            // Chăm sóc sản phẩm - new pattern
            '/(?:chăm sóc|bảo quản|giặt|giặt.*sao|giặt.*thế nào|bảo quản.*sao|làm sạch)/i' => [
                'type' => 'product_care',
                'intent' => 'product_care',
                'response' => 'Hướng dẫn chăm sóc sản phẩm:\n• Giặt tay với nước lạnh hoặc giặt máy với chế độ nhẹ\n• Không dùng chất tẩy mạnh\n• Phơi trong bóng râm, tránh ánh nắng trực tiếp\n• Là ủi ở nhiệt độ thấp hoặc trung bình\n• Bảo quản nơi khô ráo, thoáng mát\n\nMỗi sản phẩm sẽ có hướng dẫn chi tiết trên tem mác.'
            ],

            // Chào hỏi - new pattern
            '/(?:xin chào|chào|hello|hi|hey|xin chào|good morning|good afternoon|chào buổi|xinchào)/i' => [
                'type' => 'greeting',
                'intent' => 'greeting',
                'response' => 'Xin chào! Rất vui được hỗ trợ bạn. Tôi là trợ lý ảo của zone Fashion. Bạn cần tôi giúp gì hôm nay?'
            ],

            // Cảm ơn - new pattern
            '/(?:cảm ơn|thank|thanks|cám ơn|cảm ơn bạn|thank you)/i' => [
                'type' => 'thanks',
                'intent' => 'thanks',
                'response' => 'Không có gì! Rất vui được hỗ trợ bạn. Nếu có thắc mắc gì thêm, đừng ngại hỏi tôi nhé!'
            ]
        ];

        // Tìm pattern phù hợp
        foreach ($patterns as $pattern => $config) {
            if (preg_match($pattern, $message)) {
                error_log("[Chatbot Debug] Matched pattern: $pattern");
                error_log("[Chatbot Debug] Config: " . json_encode($config));
                
                // Check if this pattern uses AI
                if (isset($config['use_ai']) && $config['use_ai'] === true) {
                    error_log("[Chatbot Debug] Using AI for pattern: $pattern");
                    
                    // Use AI for consultation
                    $systemPrompt = $config['system_prompt'] ?? '';
                    $fullPrompt = $systemPrompt . "\n\nCâu hỏi của khách hàng: " . $message . "\n\nHãy trả lời một cách tự nhiên và hữu ích:";
                    
                    error_log("[Chatbot Debug] Full prompt: " . substr($fullPrompt, 0, 200) . "...");
                    
                    $aiResponse = $this->askGemini($fullPrompt);
                    error_log("[Chatbot Debug] AI Response: " . ($aiResponse ? substr($aiResponse, 0, 100) . "..." : 'NULL'));
                    
                    if ($aiResponse) {
                        return [
                            'message' => $aiResponse,
                            'type' => $config['type'],
                            'intent' => $config['intent'],
                            'context' => $context
                        ];
                    }
                    
                    error_log("[Chatbot Debug] AI failed, using fallback");
                    
                    // If AI fails, fall back to default response
                    $response = [
                        'message' => 'Xin lỗi, tôi đang gặp sự cố kỹ thuật. Bạn có thể hỏi lại sau hoặc liên hệ hotline để được hỗ trợ trực tiếp.',
                        'type' => $config['type'],
                        'intent' => $config['intent'],
                        'context' => $context
                    ];
                } else {
                    error_log("[Chatbot Debug] Using predefined response for pattern: $pattern");
                    
                    // Use predefined response
                    $response = [
                        'message' => $config['response'],
                        'type' => $config['type'],
                        'intent' => $config['intent'],
                        'context' => $context
                    ];
                }

                // Update context with current intent
                $response['context']['current_intent'] = $config['intent'];

                // Nếu là request về sản phẩm, thêm dữ liệu sản phẩm
                if (in_array($config['type'], ['best_selling', 'discounted', 'new_products', 'cheapest_products'])) {
                    $response['products'] = $this->getProductsForType($config['type']);
                }
                
                // Xử lý tìm kiếm 1 sản phẩm rẻ nhất theo danh mục
                if ($config['type'] === 'search_single_cheapest_by_category') {
                    $categoryKeyword = $this->extractCategoryFromMessage($message);
                    $products = $this->searchCheapestProductsByCategory($categoryKeyword, 1); // Chỉ lấy 1 sản phẩm
                    $response['products'] = $products;
                    $response['message'] = $this->generateSingleCheapestMessage($categoryKeyword, $products);
                }
                
                // Xử lý tìm kiếm 1 sản phẩm đắt nhất theo danh mục
                if ($config['type'] === 'search_single_expensive_by_category') {
                    $categoryKeyword = $this->extractCategoryFromMessage($message);
                    $products = $this->searchExpensiveProductsByCategory($categoryKeyword, 1); // Chỉ lấy 1 sản phẩm
                    $response['products'] = $products;
                    $response['message'] = $this->generateSingleExpensiveMessage($categoryKeyword, $products);
                }
                
                // Xử lý tìm kiếm nhiều sản phẩm rẻ nhất theo số lượng
                if ($config['type'] === 'search_multiple_cheapest_by_category') {
                    list($categoryKeyword, $quantity) = $this->extractCategoryAndQuantityFromMessage($message);
                    $products = $this->searchCheapestProductsByCategory($categoryKeyword, $quantity);
                    $response['products'] = $products;
                    $response['message'] = $this->generateMultipleCheapestMessage($categoryKeyword, $products, $quantity);
                }
                
                // Xử lý tìm kiếm nhiều sản phẩm đắt nhất theo số lượng
                if ($config['type'] === 'search_multiple_expensive_by_category') {
                    list($categoryKeyword, $quantity) = $this->extractCategoryAndQuantityFromMessage($message);
                    $products = $this->searchExpensiveProductsByCategory($categoryKeyword, $quantity);
                    $response['products'] = $products;
                    $response['message'] = $this->generateMultipleExpensiveMessage($categoryKeyword, $products, $quantity);
                }

                // Xử lý tìm kiếm sản phẩm có đánh giá tốt nhất
                if ($config['type'] === 'search_best_rated_products') {
                    $products = $this->searchBestRatedProducts();
                    $response['products'] = $products;
                    $response['message'] = $this->generateBestRatedMessage($products);
                }

                // Xử lý tìm kiếm 1 sản phẩm có đánh giá tốt nhất theo category
                if ($config['type'] === 'search_single_best_rated_by_category') {
                    $categoryKeyword = $this->extractCategoryFromMessage($message);
                    $products = $this->searchBestRatedProductsByCategory($categoryKeyword, 1);
                    $response['products'] = $products;
                    $response['message'] = $this->generateSingleBestRatedMessage($categoryKeyword, $products);
                }

                // Xử lý tìm kiếm 1 sản phẩm có đánh giá tốt nhất chung
                if ($config['type'] === 'search_single_best_rated_general') {
                    $products = $this->searchBestRatedProducts(1);
                    $response['products'] = $products;
                    $response['message'] = $this->generateSingleBestRatedGeneralMessage($products);
                }

                // Xử lý tìm kiếm nhiều sản phẩm có đánh giá tốt theo category và số lượng
                if ($config['type'] === 'search_multiple_best_rated_by_category') {
                    list($categoryKeyword, $quantity) = $this->extractCategoryAndQuantityFromBestRatedMessage($message);
                    if ($categoryKeyword) {
                        $products = $this->searchBestRatedProductsByCategory($categoryKeyword, $quantity);
                        $response['message'] = $this->generateMultipleBestRatedMessage($categoryKeyword, $products, $quantity);
                    } else {
                        $products = $this->searchBestRatedProducts($quantity);
                        $response['message'] = $this->generateMultipleBestRatedGeneralMessage($products, $quantity);
                    }
                    $response['products'] = $products;
                }

                // Xử lý tìm kiếm sản phẩm rẻ nhất theo danh mục (legacy - để tương thích)
                if ($config['type'] === 'search_cheapest_by_category') {
                    $categoryKeyword = $this->extractCategoryFromMessage($message);
                    $products = $this->searchCheapestProductsByCategory($categoryKeyword);
                    $response['products'] = $products;
                    $response['message'] = $this->generateCheapestMessage($categoryKeyword, $products);
                }
                
                // Xử lý tìm kiếm sản phẩm đắt nhất theo danh mục (legacy - để tương thích)
                if ($config['type'] === 'search_expensive_by_category') {
                    $categoryKeyword = $this->extractCategoryFromMessage($message);
                    $products = $this->searchExpensiveProductsByCategory($categoryKeyword);
                    $response['products'] = $products;
                    $response['message'] = $this->generateExpensiveMessage($categoryKeyword, $products);
                }

                return $response;
            }
        }

        // If no pattern matched, call Gemini API for general questions
        error_log("[Chatbot Debug] No pattern matched for message: '$message', calling Gemini API");
        $geminiAnswer = $this->askGemini($message);
        error_log("[Chatbot Debug] Gemini API response: " . ($geminiAnswer ? $geminiAnswer : 'NULL/EMPTY'));
        
        if ($geminiAnswer) {
            return [
                'message' => $geminiAnswer,
                'type' => 'gemini_answer',
                'context' => $context
            ];
        }

        // If even Gemini fails, return default response
        return [
            'message' => 'Xin lỗi, tôi không hiểu câu hỏi của bạn. Bạn có thể hỏi về sản phẩm, giá cả, hoặc các dịch vụ của chúng tôi.',
            'type' => 'fallback',
            'context' => $context
        ];
    }

    /**
     * Ask Gemini API for general knowledge questions
     */
    private function askGemini($question)
    {
        try {
            // Get API key from database
            $apiKey = $this->getActiveGeminiApiKey();
            if (!$apiKey) {
                error_log("[Gemini Debug] No active API key found in database");
                return null;
            }

            error_log("[Gemini Debug] Using API key ID: " . $apiKey['id'] . ", Name: " . $apiKey['name']);

            // Use the correct endpoint for Gemini Pro
            $url = 'https://generativelanguage.googleapis.com/v1/models/gemini-2.0-flash:generateContent?key=' . $apiKey['api_key'];
            $postData = [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $question]
                        ]
                    ]
                ]
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            
            // SSL configuration
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            
            // Use certificate bundle if available
            $certPath = __DIR__ . '/../../certs/cacert.pem';
            if (file_exists($certPath)) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                curl_setopt($ch, CURLOPT_CAINFO, $certPath);
            }

            $result = curl_exec($ch);
            $curlError = curl_error($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            // Log debug information
            $logMsg = date('Y-m-d H:i:s') . " [Gemini API Debug]\n";
            $logMsg .= "API Key ID: " . $apiKey['id'] . "\n";
            $logMsg .= "Request: " . json_encode($postData, JSON_UNESCAPED_UNICODE) . "\n";
            $logMsg .= "HTTP Code: $httpCode\n";
            $logMsg .= "cURL Error: $curlError\n";
            $logMsg .= "Response: $result\n";
            
            if (!is_dir(__DIR__ . '/../../logs')) {
                mkdir(__DIR__ . '/../../logs', 0755, true);
            }
            file_put_contents(__DIR__ . '/../../logs/debug.log', $logMsg, FILE_APPEND);

            if ($curlError) {
                error_log("[Gemini Debug] cURL Error: $curlError");
                $this->updateApiKeyStatus($apiKey['id'], 'failed', $curlError);
                return null;
            }

            if ($httpCode !== 200) {
                error_log("[Gemini Debug] HTTP Error: $httpCode");
                $this->updateApiKeyStatus($apiKey['id'], 'failed', "HTTP Error: $httpCode");
                return null;
            }

            if ($result) {
                $data = json_decode($result, true);
                error_log("[Gemini Debug] Parsed JSON: " . print_r($data, true));
                
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $geminiText = $data['candidates'][0]['content']['parts'][0]['text'];
                    error_log("[Gemini Debug] Found text: " . $geminiText);
                    
                    // Update usage statistics
                    $this->updateApiKeyUsage($apiKey['id']);
                    $this->updateApiKeyStatus($apiKey['id'], 'success');
                    
                    return nl2br(htmlspecialchars($geminiText));
                } else {
                    error_log("[Gemini Debug] Text not found in expected path");
                    if (isset($data['error'])) {
                        $errorMsg = $data['error']['message'] ?? 'Unknown API error';
                        $this->updateApiKeyStatus($apiKey['id'], 'failed', $errorMsg);
                    }
                    return null;
                }
            } else {
                error_log("[Gemini Debug] No result from API");
                return null;
            }
        } catch (Exception $e) {
            error_log("[Gemini Debug] Exception: " . $e->getMessage());
            if (isset($apiKey['id'])) {
                $this->updateApiKeyStatus($apiKey['id'], 'failed', $e->getMessage());
            }
            return null;
        }
    }

    /**
     * Get products for specific type with pagination
     */
    private function getProductsForType($type, $limit = 5, $offset = 0)
    {
        try {
            switch ($type) {
                case 'best_selling':
                    $sql = "SELECT p.*,
                            CASE
                                WHEN p.sale_price IS NOT NULL AND p.sale_price > 0 AND p.sale_price < p.price
                                THEN ROUND(((p.price - p.sale_price) / p.price) * 100)
                                ELSE 0
                            END as discount_percentage
                            FROM products p
                            WHERE p.status = 'published' OR p.status = ''
                            ORDER BY p.featured DESC, p.created_at DESC
                            LIMIT :offset, :limit";
                    break;

                case 'discounted':
                    $sql = "SELECT *,
                            CASE
                                WHEN sale_price IS NOT NULL AND sale_price > 0 AND sale_price < price
                                THEN ROUND(((price - sale_price) / price) * 100)
                                ELSE 0
                            END as discount_percentage
                            FROM products
                            WHERE (status = 'published' OR status = '') AND
                                  (sale_price IS NOT NULL AND sale_price > 0 AND sale_price < price)
                            ORDER BY discount_percentage DESC
                            LIMIT :offset, :limit";
                    break;

                case 'new_products':
                    $sql = "SELECT *,
                            CASE
                                WHEN sale_price IS NOT NULL AND sale_price > 0 AND sale_price < price
                                THEN ROUND(((price - sale_price) / price) * 100)
                                ELSE 0
                            END as discount_percentage
                            FROM products
                            WHERE status = 'published' OR status = ''
                            ORDER BY created_at DESC
                            LIMIT :offset, :limit";
                    break;

                case 'cheapest_products':
                    $sql = "SELECT *,
                            CASE
                                WHEN sale_price IS NOT NULL AND sale_price > 0 AND sale_price < price
                                THEN ROUND(((price - sale_price) / price) * 100)
                                ELSE 0
                            END as discount_percentage,
                            CASE
                                WHEN sale_price IS NOT NULL AND sale_price > 0 AND sale_price < price
                                THEN sale_price
                                ELSE price
                            END as final_price
                            FROM products
                            WHERE status = 'published' OR status = ''
                            ORDER BY final_price ASC
                            LIMIT :offset, :limit";
                    break;

                default:
                    return [];
            }

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            $products = $stmt->fetchAll();
            return $this->formatProducts($products);
        } catch (Exception $e) {
            error_log('Error getting products: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Format products for response
     */
    private function formatProducts($products)
    {
        return array_map(function($product) {
            // Lấy giá gốc từ bảng products
            $price = isset($product['price']) ? (float)$product['price'] : 0;
            $finalPrice = $price;
            $discount = 0;

            // Nếu có sale_price nhỏ hơn price, sử dụng nó làm giá cuối và tính % giảm giá
            if (isset($product['sale_price']) && $product['sale_price'] > 0 && $product['sale_price'] < $price) {
                $finalPrice = (float)$product['sale_price'];
                if ($price > 0) {
                    $discount = round((($price - $finalPrice) / $price) * 100);
                }
            }
            // Hoặc sử dụng discount_percentage từ truy vấn SQL nếu có
            else if (isset($product['discount_percentage'])) {
                $discount = (float)$product['discount_percentage'];
                if ($discount > 0) {
                    $finalPrice = $price * (100 - $discount) / 100;
                }
            }

            // Đảm bảo các trường dữ liệu không bị null
            return [
                'id' => (int)$product['id'],
                'name' => $product['name'] ?? 'Sản phẩm',
                'slug' => $product['slug'] ?? '',
                'price' => $price,
                'discount_percentage' => $discount,
                'final_price' => number_format($finalPrice, 2, '.', ''),
                'image' => !empty($product['featured_image'])
                    ? BASE_URL . '/serve-file.php?file=' . urlencode(ltrim($product['featured_image'], '/'))
                    : BASE_URL . '/public/assets/images/no-image.jpg',
                'short_description' => isset($product['description']) ? substr($product['description'], 0, 100) . '...' : 'Không có mô tả.',
                'url' => BASE_URL . '/product/' . ($product['slug'] ?? 'san-pham')
            ];
        }, $products);
    }

    /**
     * Search products by keywords or criteria
     */
    public function searchProducts($keywords = null, $categoryId = null, $minPrice = null, $maxPrice = null, $limit = 5, $offset = 0)
    {
        try {
            $params = [];
            $conditions = ["status = 'active' OR status = ''"];

            // Add search conditions
            if (!empty($keywords)) {
                $conditions[] = "(name LIKE :keywords OR description LIKE :keywords)";
                $params[':keywords'] = "%{$keywords}%";
            }

            if (!empty($categoryId)) {
                $conditions[] = "category_id = :categoryId";
                $params[':categoryId'] = $categoryId;
            }

            if (!empty($minPrice)) {
                $conditions[] = "price >= :minPrice";
                $params[':minPrice'] = $minPrice;
            }

            if (!empty($maxPrice)) {
                $conditions[] = "price <= :maxPrice";
                $params[':maxPrice'] = $maxPrice;
            }

            $whereClause = implode(" AND ", $conditions);

            $sql = "SELECT * FROM products
                    WHERE {$whereClause}
                    ORDER BY created_at DESC
                    LIMIT :offset, :limit";

            $stmt = $this->db->prepare($sql);

            // Bind parameters
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            $products = $stmt->fetchAll();

            return $this->formatProducts($products);
        } catch (Exception $e) {
            error_log('Error searching products: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Check if a message is a follow-up question
     */
    private function isFollowUpQuestion($message)
    {
        $followUpPatterns = [
            '/^(?:xem thêm|thêm|show more|next|tiếp|tiếp theo|còn nữa không|còn|hiện thêm)/i',
            '/^(?:chi tiết hơn|more|tell me more|cho mình biết thêm|nói thêm|giải thích)/i',
            '/^(?:ok|yes|có|đúng vậy|được|tiếp tục|next)/i'
        ];

        foreach ($followUpPatterns as $pattern) {
            if (preg_match($pattern, $message)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Handle follow-up questions based on previous context
     */
    private function handleFollowUp($message, $context)
    {
        $intent = $context['current_intent'] ?? '';
        $response = [
            'message' => 'Xin lỗi, tôi không hiểu ý bạn. Bạn có thể hỏi rõ hơn được không?',
            'type' => 'default',
            'context' => $context
        ];

        switch ($intent) {
            case 'best_selling_products':
            case 'discounted_products':
            case 'new_products':
            case 'cheapest_products':
                // Show more products of the same type
                $productType = [
                    'best_selling_products' => 'best_selling',
                    'discounted_products' => 'discounted',
                    'new_products' => 'new_products',
                    'cheapest_products' => 'cheapest_products'
                ][$intent];

                $offset = $context['product_offset'] ?? 0;
                $limit = 5;
                $newOffset = $offset + $limit;

                $products = $this->getProductsForType($productType, $limit, $newOffset);

                if (count($products) > 0) {
                    $response = [
                        'message' => 'Đây là những sản phẩm tiếp theo:',
                        'type' => $productType,
                        'products' => $products,
                        'context' => $context
                    ];
                    $response['context']['product_offset'] = $newOffset;
                } else {
                    $response = [
                        'message' => 'Xin lỗi, không còn sản phẩm nào khác để hiển thị.',
                        'type' => $productType,
                        'context' => $context
                    ];
                }
                break;

            case 'product_search':
                // Show more search results
                $keywords = $context['search_keywords'] ?? '';
                $offset = $context['search_offset'] ?? 0;
                $limit = 5;
                $newOffset = $offset + $limit;

                $products = $this->searchProducts($keywords, null, null, null, $limit, $newOffset);

                if (count($products) > 0) {
                    $response = [
                        'message' => "Đây là những kết quả tìm kiếm tiếp theo cho \"{$keywords}\":",
                        'type' => 'product_search',
                        'products' => $products,
                        'context' => $context
                    ];
                    $response['context']['search_offset'] = $newOffset;
                } else {
                    $response = [
                        'message' => "Xin lỗi, không còn kết quả tìm kiếm nào khác cho \"{$keywords}\".",
                        'type' => 'product_search_empty',
                        'context' => $context
                    ];
                }
                break;

            case 'fashion_advice':
                // Provide more detailed fashion advice
                $response = [
                    'message' => "Một số gợi ý thời trang khác:\n• Layer áo khoác denim với áo hoodie bên trong tạo phong cách trẻ trung\n• Đầm liền kết hợp giày sneaker là outfit casual dễ mặc\n• Áo blazer với quần jeans và áo thun là công thức không bao giờ lỗi mốt\n• Tone màu trung tính (đen, trắng, be) dễ phối và phù hợp mọi hoàn cảnh\n\nBạn quan tâm đến phong cách nào? Tôi có thể tư vấn cụ thể hơn.",
                    'type' => 'fashion_advice',
                    'context' => $context
                ];
                break;

            default:
                // Default follow-up
                $response = [
                    'message' => 'Bạn cần tôi giúp gì thêm không?',
                    'type' => 'default',
                    'context' => $context
                ];
                break;
        }

        return $response;
    }

    /**
     * Normalize price values from user input
     */
    private function normalizePrice($priceString)
    {
        // Remove any non-numeric characters except decimal point
        $price = preg_replace('/[^\d.,]/', '', $priceString);

        // Replace comma with dot for decimal
        $price = str_replace(',', '.', $price);

        // Convert to integer
        if (strpos($price, '.') !== false) {
            // Has decimal point
            $parts = explode('.', $price);
            if (count($parts) === 2 && strlen($parts[1]) <= 2) {
                // Normal decimal number
                return floatval($price);
            } else {
                // Thousand separator
                return floatval(str_replace('.', '', $price));
            }
        }

        return floatval($price);
    }

    /**
     * Get active Gemini API key from database
     */
    private function getActiveGeminiApiKey()
    {
        try {
            $sql = "SELECT * FROM gemini_api_keys 
                    WHERE status = 'active' 
                      AND (daily_limit = 0 OR current_daily_usage < daily_limit)
                      AND (monthly_limit = 0 OR current_monthly_usage < monthly_limit)
                      AND (last_test_status IS NULL OR last_test_status = 'success')
                    ORDER BY 
                      current_daily_usage ASC,
                      current_monthly_usage ASC,
                      last_used_at ASC
                    LIMIT 1";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error getting active Gemini API key: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Update API key usage statistics
     */
    private function updateApiKeyUsage($keyId)
    {
        try {
            $sql = "UPDATE gemini_api_keys SET 
                        current_daily_usage = current_daily_usage + 1,
                        current_monthly_usage = current_monthly_usage + 1,
                        usage_count = usage_count + 1,
                        last_used_at = NOW(),
                        updated_at = NOW()
                    WHERE id = ?";

            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$keyId]);
        } catch (Exception $e) {
            error_log("Error updating API key usage: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update API key test status
     */
    private function updateApiKeyStatus($keyId, $status, $errorMessage = null)
    {
        try {
            $sql = "UPDATE gemini_api_keys SET 
                        last_test_at = NOW(),
                        last_test_status = ?,
                        last_error_message = ?,
                        updated_at = NOW()
                    WHERE id = ?";

            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$status, $errorMessage, $keyId]);
        } catch (Exception $e) {
            error_log("Error updating API key status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Extract category keyword from user message
     */
    private function extractCategoryFromMessage($message)
    {
        $categoryMappings = [
            'áo' => ['áo', 'shirt', 'tshirt', 'polo', 'hoodie', 'jacket', 'blazer'],
            'quần' => ['quần', 'jean', 'kaki', 'short', 'pants', 'trouser'], 
            'váy' => ['váy', 'skirt'],
            'đầm' => ['đầm', 'dress']
        ];

        foreach ($categoryMappings as $category => $keywords) {
            foreach ($keywords as $keyword) {
                // Sử dụng regex linh hoạt hơn cho tiếng Việt
                if (preg_match('/(?:^|\\s)' . preg_quote($keyword, '/') . '(?:\\s|$|[^\\w])/ui', $message)) {
                    return $category;
                }
            }
        }

        return null; // Không tìm thấy category cụ thể
    }

    /**
     * Search cheapest products by category
     */
    private function searchCheapestProductsByCategory($categoryKeyword, $limit = 8)
    {
        try {
            $conditions = ["(status = 'published' OR status = '')"];
            $params = [];

            // Thêm điều kiện tìm kiếm theo category nếu có
            if ($categoryKeyword) {
                // Tạo search patterns cho từng category
                $searchPatterns = [];
                switch ($categoryKeyword) {
                    case 'áo':
                        $searchPatterns = ['áo', 'shirt', 'polo', 'hoodie', 'jacket', 'blazer', 'blouse'];
                        break;
                    case 'quần':
                        $searchPatterns = ['quần', 'jean', 'kaki', 'short', 'pants', 'trouser'];
                        break;
                    case 'váy':
                        $searchPatterns = ['váy', 'skirt'];
                        break;
                    case 'đầm':
                        $searchPatterns = ['đầm', 'dress'];
                        break;
                }

                if (!empty($searchPatterns)) {
                    $nameConditions = [];
                    foreach ($searchPatterns as $i => $pattern) {
                        $paramName = ":pattern{$i}";
                        $nameConditions[] = "name LIKE {$paramName}";
                        $params[$paramName] = "%{$pattern}%";
                    }
                    $conditions[] = "(" . implode(" OR ", $nameConditions) . ")";
                }
            }

            $whereClause = implode(" AND ", $conditions);

            $sql = "SELECT *,
                    CASE
                        WHEN sale_price IS NOT NULL AND sale_price > 0 AND sale_price < price
                        THEN sale_price
                        ELSE price
                    END as final_price,
                    CASE
                        WHEN sale_price IS NOT NULL AND sale_price > 0 AND sale_price < price
                        THEN ROUND(((price - sale_price) / price) * 100)
                        ELSE 0
                    END as discount_percentage
                    FROM products
                    WHERE {$whereClause}
                    ORDER BY final_price ASC
                    LIMIT :limit";

            $stmt = $this->db->prepare($sql);
            
            // Bind parameters
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            
            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $this->formatProducts($products);
        } catch (Exception $e) {
            error_log('Error searching cheapest products by category: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Generate message for cheapest products
     */
    private function generateCheapestMessage($categoryKeyword, $products)
    {
        if (empty($products)) {
            if ($categoryKeyword) {
                return "Xin lỗi, hiện tại chúng tôi không có sản phẩm {$categoryKeyword} nào. Bạn có thể xem các sản phẩm khác.";
            } else {
                return "Xin lỗi, hiện tại không có sản phẩm nào. Vui lòng thử lại sau.";
            }
        }

        $count = count($products);
        $categoryText = $categoryKeyword ? " {$categoryKeyword}" : "";
        
        if ($count == 1) {
            $message = "Đây là sản phẩm{$categoryText} có giá tốt nhất:";
        } else {
            $message = "Đây là {$count} sản phẩm{$categoryText} có giá tốt nhất:";
        }

        return $message;
    }

    /**
     * Search most expensive products by category
     */
    private function searchExpensiveProductsByCategory($categoryKeyword, $limit = 8)
    {
        try {
            $conditions = ["(status = 'published' OR status = '')"];
            $params = [];

            // Thêm điều kiện tìm kiếm theo category nếu có
            if ($categoryKeyword) {
                // Tạo search patterns cho từng category
                $searchPatterns = [];
                switch ($categoryKeyword) {
                    case 'áo':
                        $searchPatterns = ['áo', 'shirt', 'polo', 'hoodie', 'jacket', 'blazer', 'blouse'];
                        break;
                    case 'quần':
                        $searchPatterns = ['quần', 'jean', 'kaki', 'short', 'pants', 'trouser'];
                        break;
                    case 'váy':
                        $searchPatterns = ['váy', 'skirt'];
                        break;
                    case 'đầm':
                        $searchPatterns = ['đầm', 'dress'];
                        break;
                }

                if (!empty($searchPatterns)) {
                    $nameConditions = [];
                    foreach ($searchPatterns as $i => $pattern) {
                        $paramName = ":pattern{$i}";
                        $nameConditions[] = "name LIKE {$paramName}";
                        $params[$paramName] = "%{$pattern}%";
                    }
                    $conditions[] = "(" . implode(" OR ", $nameConditions) . ")";
                }
            }

            $whereClause = implode(" AND ", $conditions);

            $sql = "SELECT *,
                    CASE
                        WHEN sale_price IS NOT NULL AND sale_price > 0 AND sale_price < price
                        THEN sale_price
                        ELSE price
                    END as final_price,
                    CASE
                        WHEN sale_price IS NOT NULL AND sale_price > 0 AND sale_price < price
                        THEN ROUND(((price - sale_price) / price) * 100)
                        ELSE 0
                    END as discount_percentage
                    FROM products
                    WHERE {$whereClause}
                    ORDER BY final_price DESC
                    LIMIT :limit";

            $stmt = $this->db->prepare($sql);
            
            // Bind parameters
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            
            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $this->formatProducts($products);
        } catch (Exception $e) {
            error_log('Error searching expensive products by category: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Generate message for most expensive products
     */
    private function generateExpensiveMessage($categoryKeyword, $products)
    {
        if (empty($products)) {
            if ($categoryKeyword) {
                return "Xin lỗi, hiện tại chúng tôi không có sản phẩm {$categoryKeyword} nào. Bạn có thể xem các sản phẩm khác.";
            } else {
                return "Xin lỗi, hiện tại không có sản phẩm nào. Vui lòng thử lại sau.";
            }
        }

        $count = count($products);
        $categoryText = $categoryKeyword ? " {$categoryKeyword}" : "";
        
        if ($count == 1) {
            $message = "Đây là sản phẩm{$categoryText} đắt nhất:";
        } else {
            $message = "Đây là {$count} sản phẩm{$categoryText} đắt nhất:";
        }

        return $message;
    }

    /**
     * Extract category and quantity from user message
     */
    private function extractCategoryAndQuantityFromMessage($message)
    {
        // Pattern để extract số lượng và category
        $patterns = [
            '/(\d+)\s*(áo|quần|váy|đầm|jacket|hoodie|polo|shirt|jean|kaki|short|dress|skirt)/i',
            '/(áo|quần|váy|đầm)\s*(\d+)/i'
        ];

        $quantity = 5; // default
        $categoryKeyword = null;

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $message, $matches)) {
                if (isset($matches[1]) && isset($matches[2])) {
                    // Kiểm tra thứ tự: số đầu hay category đầu
                    if (is_numeric($matches[1])) {
                        $quantity = (int)$matches[1];
                        $categoryKeyword = $this->mapCategoryKeyword($matches[2]);
                    } else {
                        $categoryKeyword = $this->mapCategoryKeyword($matches[1]);
                        $quantity = (int)$matches[2];
                    }
                    break;
                }
            }
        }

        // Nếu không extract được category từ pattern trên, dùng method cũ
        if (!$categoryKeyword) {
            $categoryKeyword = $this->extractCategoryFromMessage($message);
        }

        return [$categoryKeyword, $quantity];
    }

    /**
     * Map category keyword to standard form
     */
    private function mapCategoryKeyword($keyword)
    {
        $mappings = [
            'áo' => 'áo', 'shirt' => 'áo', 'polo' => 'áo', 'hoodie' => 'áo', 'jacket' => 'áo',
            'quần' => 'quần', 'jean' => 'quần', 'kaki' => 'quần', 'short' => 'quần',
            'váy' => 'váy', 'skirt' => 'váy',
            'đầm' => 'đầm', 'dress' => 'đầm'
        ];

        return $mappings[strtolower($keyword)] ?? null;
    }

    /**
     * Generate message for single cheapest product
     */
    private function generateSingleCheapestMessage($categoryKeyword, $products)
    {
        if (empty($products)) {
            if ($categoryKeyword) {
                return "Xin lỗi, hiện tại chúng tôi không có sản phẩm {$categoryKeyword} nào. Bạn có thể xem các sản phẩm khác.";
            } else {
                return "Xin lỗi, hiện tại không có sản phẩm nào. Vui lòng thử lại sau.";
            }
        }

        $categoryText = $categoryKeyword ? " {$categoryKeyword}" : "";
        return "Đây là sản phẩm{$categoryText} rẻ nhất:";
    }

    /**
     * Generate message for single most expensive product
     */
    private function generateSingleExpensiveMessage($categoryKeyword, $products)
    {
        if (empty($products)) {
            if ($categoryKeyword) {
                return "Xin lỗi, hiện tại chúng tôi không có sản phẩm {$categoryKeyword} nào. Bạn có thể xem các sản phẩm khác.";
            } else {
                return "Xin lỗi, hiện tại không có sản phẩm nào. Vui lòng thử lại sau.";
            }
        }

        $categoryText = $categoryKeyword ? " {$categoryKeyword}" : "";
        return "Đây là sản phẩm{$categoryText} đắt nhất:";
    }

    /**
     * Generate message for multiple cheapest products
     */
    private function generateMultipleCheapestMessage($categoryKeyword, $products, $quantity)
    {
        if (empty($products)) {
            if ($categoryKeyword) {
                return "Xin lỗi, hiện tại chúng tôi không có sản phẩm {$categoryKeyword} nào. Bạn có thể xem các sản phẩm khác.";
            } else {
                return "Xin lỗi, hiện tại không có sản phẩm nào. Vui lòng thử lại sau.";
            }
        }

        $count = count($products);
        $categoryText = $categoryKeyword ? " {$categoryKeyword}" : "";
        
        if ($count == 1) {
            return "Đây là sản phẩm{$categoryText} rẻ nhất có sẵn:";
        } else {
            return "Đây là {$count} sản phẩm{$categoryText} rẻ nhất:";
        }
    }

    /**
     * Generate message for multiple most expensive products
     */
    private function generateMultipleExpensiveMessage($categoryKeyword, $products, $quantity)
    {
        if (empty($products)) {
            if ($categoryKeyword) {
                return "Xin lỗi, hiện tại chúng tôi không có sản phẩm {$categoryKeyword} nào. Bạn có thể xem các sản phẩm khác.";
            } else {
                return "Xin lỗi, hiện tại không có sản phẩm nào. Vui lòng thử lại sau.";
            }
        }

        $count = count($products);
        $categoryText = $categoryKeyword ? " {$categoryKeyword}" : "";
        
        if ($count == 1) {
            return "Đây là sản phẩm{$categoryText} đắt nhất có sẵn:";
        } else {
            return "Đây là {$count} sản phẩm{$categoryText} đắt nhất:";
        }
    }

    /**
     * Search products with best ratings/reviews
     */
    private function searchBestRatedProducts($limit = 8)
    {
        try {
            // Tìm sản phẩm có featured = 1 hoặc sắp xếp theo created_at (giả sử sản phẩm mới = chất lượng tốt)
            // Hoặc có thể thêm rating field vào database sau này
            $sql = "SELECT *,
                    CASE
                        WHEN sale_price IS NOT NULL AND sale_price > 0 AND sale_price < price
                        THEN sale_price
                        ELSE price
                    END as final_price,
                    CASE
                        WHEN sale_price IS NOT NULL AND sale_price > 0 AND sale_price < price
                        THEN ROUND(((price - sale_price) / price) * 100)
                        ELSE 0
                    END as discount_percentage
                    FROM products
                    WHERE (status = 'published' OR status = '')
                    ORDER BY 
                        featured DESC,
                        CASE WHEN sale_price IS NOT NULL AND sale_price > 0 THEN 1 ELSE 0 END DESC,
                        created_at DESC
                    LIMIT :limit";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $this->formatProducts($products);
        } catch (Exception $e) {
            error_log('Error searching best rated products: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Generate message for best rated products
     */
    private function generateBestRatedMessage($products)
    {
        if (empty($products)) {
            return "Xin lỗi, hiện tại không có sản phẩm nào. Vui lòng thử lại sau.";
        }

        $count = count($products);
        
        if ($count == 1) {
            return "Đây là sản phẩm có đánh giá tốt nhất:";
        } else {
            return "Đây là {$count} sản phẩm có đánh giá tốt nhất:";
        }
    }

    /**
     * Search best rated products by category
     */
    private function searchBestRatedProductsByCategory($categoryKeyword, $limit = 8)
    {
        try {
            $conditions = ["(status = 'published' OR status = '')"];
            $params = [];

            // Thêm điều kiện tìm kiếm theo category nếu có
            if ($categoryKeyword) {
                // Tạo search patterns cho từng category
                $searchPatterns = [];
                switch ($categoryKeyword) {
                    case 'áo':
                        $searchPatterns = ['áo', 'shirt', 'polo', 'hoodie', 'jacket', 'blazer', 'blouse'];
                        break;
                    case 'quần':
                        $searchPatterns = ['quần', 'jean', 'kaki', 'short', 'pants', 'trouser'];
                        break;
                    case 'váy':
                        $searchPatterns = ['váy', 'skirt'];
                        break;
                    case 'đầm':
                        $searchPatterns = ['đầm', 'dress'];
                        break;
                }

                if (!empty($searchPatterns)) {
                    $nameConditions = [];
                    foreach ($searchPatterns as $i => $pattern) {
                        $paramName = ":pattern{$i}";
                        $nameConditions[] = "name LIKE {$paramName}";
                        $params[$paramName] = "%{$pattern}%";
                    }
                    $conditions[] = "(" . implode(" OR ", $nameConditions) . ")";
                }
            }

            $whereClause = implode(" AND ", $conditions);

            $sql = "SELECT *,
                    CASE
                        WHEN sale_price IS NOT NULL AND sale_price > 0 AND sale_price < price
                        THEN sale_price
                        ELSE price
                    END as final_price,
                    CASE
                        WHEN sale_price IS NOT NULL AND sale_price > 0 AND sale_price < price
                        THEN ROUND(((price - sale_price) / price) * 100)
                        ELSE 0
                    END as discount_percentage
                    FROM products
                    WHERE {$whereClause}
                    ORDER BY 
                        featured DESC,
                        CASE WHEN sale_price IS NOT NULL AND sale_price > 0 THEN 1 ELSE 0 END DESC,
                        created_at DESC
                    LIMIT :limit";

            $stmt = $this->db->prepare($sql);
            
            // Bind parameters
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            
            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $this->formatProducts($products);
        } catch (Exception $e) {
            error_log('Error searching best rated products by category: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Extract category and quantity from best rated message
     */
    private function extractCategoryAndQuantityFromBestRatedMessage($message)
    {
        // Pattern để extract số lượng và category cho đánh giá
        $patterns = [
            '/(\d+)\s*(?:sản phẩm|sp|đồ|món|hàng|áo|quần|váy|đầm)/i',
            '/(\d+)\s*(áo|quần|váy|đầm)/i'
        ];

        $quantity = 5; // default
        $categoryKeyword = null;

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $message, $matches)) {
                if (isset($matches[1]) && isset($matches[2])) {
                    $quantity = (int)$matches[1];
                    $categoryKeyword = $this->mapCategoryKeyword($matches[2]);
                    break;
                } else if (isset($matches[1])) {
                    $quantity = (int)$matches[1];
                    // Không có category cụ thể, extract từ message
                    $categoryKeyword = $this->extractCategoryFromMessage($message);
                    break;
                }
            }
        }

        // Nếu không extract được category từ pattern trên, dùng method cũ
        if (!$categoryKeyword) {
            $categoryKeyword = $this->extractCategoryFromMessage($message);
        }

        return [$categoryKeyword, $quantity];
    }

    /**
     * Generate message for single best rated product by category
     */
    private function generateSingleBestRatedMessage($categoryKeyword, $products)
    {
        if (empty($products)) {
            if ($categoryKeyword) {
                return "Xin lỗi, hiện tại chúng tôi không có sản phẩm {$categoryKeyword} nào. Bạn có thể xem các sản phẩm khác.";
            } else {
                return "Xin lỗi, hiện tại không có sản phẩm nào. Vui lòng thử lại sau.";
            }
        }

        $categoryText = $categoryKeyword ? " {$categoryKeyword}" : "";
        return "Đây là sản phẩm{$categoryText} có đánh giá tốt nhất:";
    }

    /**
     * Generate message for single best rated product (general)
     */
    private function generateSingleBestRatedGeneralMessage($products)
    {
        if (empty($products)) {
            return "Xin lỗi, hiện tại không có sản phẩm nào. Vui lòng thử lại sau.";
        }

        return "Đây là sản phẩm có đánh giá tốt nhất:";
    }

    /**
     * Generate message for multiple best rated products by category
     */
    private function generateMultipleBestRatedMessage($categoryKeyword, $products, $quantity)
    {
        if (empty($products)) {
            if ($categoryKeyword) {
                return "Xin lỗi, hiện tại chúng tôi không có sản phẩm {$categoryKeyword} nào. Bạn có thể xem các sản phẩm khác.";
            } else {
                return "Xin lỗi, hiện tại không có sản phẩm nào. Vui lòng thử lại sau.";
            }
        }

        $count = count($products);
        $categoryText = $categoryKeyword ? " {$categoryKeyword}" : "";
        
        if ($count == 1) {
            return "Đây là sản phẩm{$categoryText} có đánh giá tốt có sẵn:";
        } else {
            return "Đây là {$count} sản phẩm{$categoryText} có đánh giá tốt nhất:";
        }
    }

    /**
     * Generate message for multiple best rated products (general)
     */
    private function generateMultipleBestRatedGeneralMessage($products, $quantity)
    {
        if (empty($products)) {
            return "Xin lỗi, hiện tại không có sản phẩm nào. Vui lòng thử lại sau.";
        }

        $count = count($products);
        
        if ($count == 1) {
            return "Đây là sản phẩm có đánh giá tốt có sẵn:";
        } else {
            return "Đây là {$count} sản phẩm có đánh giá tốt nhất:";
        }
    }
}
