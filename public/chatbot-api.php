<?php
/**
 * Standalone Chatbot API Handler
 */

// Define basic paths
if (!defined("ROOT_PATH")) {
    define("ROOT_PATH", dirname(__FILE__, 2));
}

// Include necessary files - use custom include to avoid redefining constants
if (!function_exists('safe_require_once')) {
    function safe_require_once($file) {
        require_once $file;
    }
}

// Include database connection
safe_require_once(ROOT_PATH . "/app/core/Database.php");
safe_require_once(ROOT_PATH . "/app/core/ApiResponse.php");

// Define BASE_URL manually to avoid including constants.php
if (!defined("BASE_URL")) {
    define("BASE_URL", "http://localhost/5s-fashion");
}

// No need to include constants.php as ROOT_PATH and BASE_URL are already defined
// We will just require other files that might be needed

// Get JSON request data
$json = file_get_contents("php://input");
$requestData = json_decode($json, true) ?: [];

// Set response headers
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle OPTIONS request (CORS preflight)
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

// Process only POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Method not allowed"
    ]);
    exit;
}

// Check for message
$message = trim($requestData["message"] ?? "");
if (empty($message)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Tin nhắn không được để trống"
    ]);
    exit;
}

try {
    // Connect to database
    $config = require ROOT_PATH . "/app/config/database.php";
    $db = $config["connections"]["mysql"];
    $pdo = new PDO(
        "mysql:host={$db["host"]};dbname={$db["database"]};charset=utf8mb4",
        $db["username"],
        $db["password"],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Process message
    $response = [];

    // Simple keyword processing
    $message = strtolower($message);

    if (strpos($message, "sản phẩm hot") !== false ||
        strpos($message, "bán chạy") !== false ||
        strpos($message, "phổ biến") !== false) {

        // Get best selling products that have images
        $sql = "SELECT p.*,
                    COALESCE(SUM(oi.quantity), 0) as total_sold,
                    COALESCE(AVG(r.rating), 0) as avg_rating
                FROM products p
                LEFT JOIN order_items oi ON p.id = oi.product_id
                LEFT JOIN reviews r ON p.id = r.product_id
                WHERE p.status = \"active\" AND p.featured_image IS NOT NULL AND p.featured_image != ''
                GROUP BY p.id
                ORDER BY total_sold DESC
                LIMIT 8";

                $stmt = $pdo->query($sql);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Nếu không tìm thấy sản phẩm nào có hình ảnh, lấy các sản phẩm có ID từ 4-9
        // (đã xác nhận có hình ảnh từ truy vấn trước đó)
        if (count($products) == 0) {
            $fallbackSql = "SELECT p.*,
                    COALESCE(SUM(oi.quantity), 0) as total_sold,
                    COALESCE(AVG(r.rating), 0) as avg_rating
                FROM products p
                LEFT JOIN order_items oi ON p.id = oi.product_id
                LEFT JOIN reviews r ON p.id = r.product_id
                WHERE p.id BETWEEN 4 AND 9
                GROUP BY p.id
                LIMIT 8";
            $stmt = $pdo->query($fallbackSql);
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Debug log products
        error_log("Chatbot Products: " . json_encode(array_map(function($p) {
            // Check if image file exists
            $imagePath = "";
            if (isset($p["featured_image"]) && $p["featured_image"]) {
                $uploadPath = ROOT_PATH . "/public/uploads/products/" . $p["featured_image"];
                $imagePath = file_exists($uploadPath) ? $uploadPath : "not_found";
            }

            return [
                "id" => $p["id"],
                "name" => $p["name"],
                "featured_image" => $p["featured_image"] ?? 'no_image',
                "image_exists" => $imagePath
            ];
        }, $products)));

        // Format products
        $formattedProducts = array_map(function($product) {
            // Calculate discount
            $discount = isset($product["discount_percentage"]) ? (float)$product["discount_percentage"] : 0;
            $price = isset($product["price"]) ? (float)$product["price"] : 0;
            $finalPrice = $price;

            if ($discount > 0) {
                $finalPrice = $price * (100 - $discount) / 100;
            } else if (isset($product["sale_price"]) && $product["sale_price"] > 0) {
                $finalPrice = $product["sale_price"];
                if ($price > 0) {
                    $discount = round((($price - (float)$product["sale_price"]) / $price) * 100);
                }
            }

            return [
                "id" => (int)$product["id"],
                "name" => $product["name"] ?? "Sản phẩm",
                "slug" => $product["slug"] ?? "",
                "price" => $price,
                "discount_percentage" => $discount,
                "final_price" => number_format($finalPrice, 2, ".", ""),
                "image" => isset($product["featured_image"]) && !empty($product["featured_image"])
                    ? BASE_URL . "/serve-file.php?file=" . urlencode(ltrim($product["featured_image"], '/'))
                    : BASE_URL . "/serve-file.php?file=products/no-image.jpg",
                "short_description" => isset($product["description"]) ? substr($product["description"], 0, 100) . "..." : "Không có mô tả.",
                "url" => BASE_URL . "/product/" . ($product["slug"] ?? "san-pham")
            ];
        }, $products);

        $response = [
            "message" => "Đây là những sản phẩm bán chạy nhất hiện nay:",
            "products" => $formattedProducts,
            "type" => "best_selling"
        ];
    } else {
        // Default response
        $response = [
            "message" => "Xin chào! Tôi có thể giúp bạn tìm sản phẩm bán chạy, khuyến mãi, hàng mới, hoặc hỗ trợ đơn hàng. Bạn cần hỗ trợ gì?",
            "products" => []
        ];
    }

    // Return success response
    echo json_encode([
        "success" => true,
        "data" => $response,
        "message" => "Phản hồi thành công"
    ]);

} catch (Exception $e) {
    // Log error
    error_log("Chatbot API Error: " . $e->getMessage());

    // Return error response
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Đã xảy ra lỗi trong quá trình xử lý yêu cầu: " . $e->getMessage()
    ]);
}
