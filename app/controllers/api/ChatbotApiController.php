<?php
/**
 * Chatbot API Controller
 * 5S Fashion E-commerce Platform
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
            'message' => 'Cảm ơn bạn đã liên hệ! Tôi có thể giúp bạn:\n• Xem sản phẩm bán chạy, giảm giá, mới\n• Thông tin về đơn hàng, thanh toán\n• Hướng dẫn đổi trả, vận chuyển\n• Tư vấn size\n• Tìm kiếm sản phẩm theo từ khóa\n\nBạn có thể hỏi cụ thể hơn nhé!',
            'type' => 'default',
            'context' => $context
        ];

        // 1. Check for follow-up questions based on context
        if (isset($context['current_intent']) && $this->isFollowUpQuestion($message)) {
            return $this->handleFollowUp($message, $context);
        }

        // 2. Check for product search intent
        $searchPattern = '/(?:tìm|tìm kiếm|tìm sản phẩm|tìm đồ|tìm quần|tìm áo|hiển thị|show me|tìm mẫu|có|có bán|cửa hàng có).*?([\p{L}\s\d\-\+]+?)(?:\s+không|không\?|$|\?)/ui';
        if (preg_match($searchPattern, $message, $matches) && !empty($matches[1])) {
            $keywords = trim($matches[1]);
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
            // Sản phẩm bán chạy - extended patterns
            '/(?:sản phẩm|sp|đồ|quần áo|mẫu|món|hàng).*(?:bán chạy|hot|nổi bật|phổ biến|được ưa chuộng|được yêu thích|bán tốt)/i' => [
                'type' => 'best_selling',
                'intent' => 'best_selling_products',
                'response' => 'Đây là những sản phẩm bán chạy nhất tại 5S Fashion:'
            ],

            // Sản phẩm giảm giá - extended patterns
            '/(?:giảm giá|khuyến mãi|sale|discount|ưu đãi|đang giảm|đang sale|đang khuyến mãi|rẻ|hời)/i' => [
                'type' => 'discounted',
                'intent' => 'discounted_products',
                'response' => 'Các sản phẩm đang được giảm giá tại 5S Fashion:'
            ],

            // Sản phẩm mới - extended patterns
            '/(?:sản phẩm mới|sp mới|hàng mới|new|mới về|mới ra mắt|vừa về|vừa ra|hàng mới về)/i' => [
                'type' => 'new_products',
                'intent' => 'new_products',
                'response' => 'Những sản phẩm mới nhất tại 5S Fashion:'
            ],

            // Đơn hàng - extended patterns
            '/(?:đơn hàng|order|kiểm tra.*đơn|theo dõi.*đơn|tình trạng.*đơn|trạng thái.*đơn|đơn.*mua|mua hàng|đặt hàng)/i' => [
                'type' => 'order_info',
                'intent' => 'order_information',
                'response' => 'Để kiểm tra đơn hàng, bạn có thể:\n• Đăng nhập vào tài khoản và xem phần "Đơn hàng của tôi"\n• Liên hệ hotline: 1900-xxxx với mã đơn hàng\n• Email: support@5sfashion.com'
            ],

            // Thanh toán - extended patterns
            '/(?:thanh toán|payment|phương thức.*thanh toán|tt|trả tiền|trả bằng|tiền|cách.*thanh toán|trả góp|trả qua|qua thẻ)/i' => [
                'type' => 'payment_info',
                'intent' => 'payment_information',
                'response' => '5S Fashion hỗ trợ các phương thức thanh toán:\n• Thanh toán khi nhận hàng (COD)\n• Chuyển khoản ngân hàng\n• Ví điện tử (Momo, ZaloPay)\n• Thẻ tín dụng/ghi nợ'
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

            // Size/Kích cỡ - extended patterns
            '/(?:size|kích cỡ|kích thước|số đo|chọn.*size|chọn.*cỡ|cỡ|cỡ.*nào|size.*nào|size.*phù hợp)/i' => [
                'type' => 'size_guide',
                'intent' => 'size_guide',
                'response' => "<div class='size-guide-table'>
                    <h4>Hướng dẫn chọn size</h4>
                    <table>
                        <tr><th>Size</th><th>Cân nặng</th><th>Chiều cao</th></tr>
                        <tr><td>S</td><td>45-50kg</td><td>1m50-1m60</td></tr>
                        <tr><td>M</td><td>50-55kg</td><td>1m55-1m65</td></tr>
                        <tr><td>L</td><td>55-60kg</td><td>1m60-1m70</td></tr>
                        <tr><td>XL</td><td>60-65kg</td><td>1m65-1m75</td></tr>
                    </table>
                    <p>Bạn có thể xem bảng size chi tiết trong từng sản phẩm!</p>
                </div>"
            ],

            // Tư vấn thời trang - new pattern
            '/(?:tư vấn|tư vấn.*thời trang|phối đồ|cách phối|mặc.*sao|phối.*sao|phối.*thế nào|phối.*như thế nào|mặc.*với)/i' => [
                'type' => 'fashion_advice',
                'intent' => 'fashion_advice',
                'response' => 'Tư vấn thời trang:\n• Áo sơ mi trắng có thể phối với hầu hết quần jeans, quần âu\n• Quần jeans đen là item cơ bản, dễ phối với mọi loại áo\n• Áo thun basic là lựa chọn đơn giản nhưng luôn hiệu quả\n• Trang phục một màu (all black, all white) luôn mang lại vẻ sang trọng\n\nBạn cần tư vấn phối đồ với item cụ thể nào không?'
            ],

            // Thông tin cửa hàng - new pattern
            '/(?:cửa hàng|store|shop|địa chỉ|địa điểm|chỗ|nơi|ở đâu|chi nhánh)/i' => [
                'type' => 'store_info',
                'intent' => 'store_information',
                'response' => "<div class='store-info-card'>
                    <div class='store-info-header'>
                        <i class='fas fa-store'></i>
                        <h4>5S Fashion - Hệ Thống Cửa Hàng</h4>
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
                'response' => 'Xin chào! Rất vui được hỗ trợ bạn. Tôi là trợ lý ảo của 5S Fashion. Bạn cần tôi giúp gì hôm nay?'
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
                $response = [
                    'message' => $config['response'],
                    'type' => $config['type'],
                    'intent' => $config['intent'],
                    'context' => $context
                ];

                // Update context with current intent
                $response['context']['current_intent'] = $config['intent'];

                // Nếu là request về sản phẩm, thêm dữ liệu sản phẩm
                if (in_array($config['type'], ['best_selling', 'discounted', 'new_products'])) {
                    $response['products'] = $this->getProductsForType($config['type']);
                }

                return $response;
            }
        }

        // If no pattern matched, return default response
        return $response;
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
                // Show more products of the same type
                $productType = [
                    'best_selling_products' => 'best_selling',
                    'discounted_products' => 'discounted',
                    'new_products' => 'new_products'
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
}
