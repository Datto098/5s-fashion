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

// Include database connection and API response
safe_require_once(ROOT_PATH . "/app/core/Database.php");
safe_require_once(ROOT_PATH . "/app/core/ApiResponse.php");
safe_require_once(ROOT_PATH . "/app/core/ApiController.php"); // Add this line to include the ApiController
safe_require_once(ROOT_PATH . "/app/controllers/api/ChatbotApiController.php");

// Define BASE_URL manually to avoid including constants.php
if (!defined("BASE_URL")) {
    define("BASE_URL", "http://localhost/5s-fashion");
}

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
    // Initialize ChatbotApiController
    $chatbotController = new ChatbotApiController();

    // Extract previous context from session
    $previousContext = isset($_SESSION['chatbot_context']) ? $_SESSION['chatbot_context'] : [];

    // Process message
    $response = $chatbotController->processMessage($message, $previousContext);

    // Save context to session
    if (isset($response['context'])) {
        $_SESSION['chatbot_context'] = $response['context'];
        unset($response['context']); // Don't send context to frontend
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
