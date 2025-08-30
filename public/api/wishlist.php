<?php
/**
 * Wishlist API Endpoint
 * zone Fashion E-commerce Platform
 */

// Set headers for API response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Include necessary files
require_once '../app/config/constants.php';
require_once '../app/core/Database.php';
require_once '../app/core/Model.php';
require_once '../app/models/Wishlist.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Vui lòng đăng nhập để thực hiện chức năng này'
    ]);
    exit;
}

// Get user ID
$userId = $_SESSION['user']['id'] ?? null;
if (!$userId) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Không tìm thấy thông tin người dùng'
    ]);
    exit;
}

// Initialize Wishlist model
$wishlistModel = new Wishlist();

// Get request method and action
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// Handle different actions
switch ($action) {
    case 'remove':
        if ($method === 'POST') {
            removeFromWishlist($wishlistModel, $userId);
        } else {
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Method not allowed'
            ]);
        }
        break;

    case 'add':
        if ($method === 'POST') {
            addToWishlist($wishlistModel, $userId);
        } else {
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Method not allowed'
            ]);
        }
        break;

    case 'toggle':
        if ($method === 'POST') {
            toggleWishlist($wishlistModel, $userId);
        } else {
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Method not allowed'
            ]);
        }
        break;

    default:
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Endpoint not found'
        ]);
        break;
}

/**
 * Remove product from wishlist
 */
function removeFromWishlist($wishlistModel, $userId)
{
    try {
        // Get JSON input
        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['product_id']) || empty($input['product_id'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Product ID is required'
            ]);
            return;
        }

        $productId = (int)$input['product_id'];

        // Remove from wishlist using the model
        $success = $wishlistModel->removeFromWishlist($userId, $productId);

        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Đã xóa sản phẩm khỏi danh sách yêu thích'
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Không thể xóa sản phẩm khỏi danh sách yêu thích'
            ]);
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
        ]);
    }
}

/**
 * Add product to wishlist
 */
function addToWishlist($wishlistModel, $userId)
{
    try {
        // Get JSON input
        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['product_id']) || empty($input['product_id'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Product ID is required'
            ]);
            return;
        }

        $productId = (int)$input['product_id'];

        // Add to wishlist using the model
        $success = $wishlistModel->addToWishlist($userId, $productId);

        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Đã thêm sản phẩm vào danh sách yêu thích'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Sản phẩm đã có trong danh sách yêu thích'
            ]);
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
        ]);
    }
}

/**
 * Toggle product in wishlist
 */
function toggleWishlist($wishlistModel, $userId)
{
    try {
        // Get JSON input
        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['product_id']) || empty($input['product_id'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Product ID is required'
            ]);
            return;
        }

        $productId = (int)$input['product_id'];

        // Check if product is in wishlist
        $isInWishlist = $wishlistModel->isInWishlist($userId, $productId);

        if ($isInWishlist) {
            // Remove from wishlist
            $success = $wishlistModel->removeFromWishlist($userId, $productId);
            $message = 'Đã xóa khỏi danh sách yêu thích';
            $inWishlist = false;
        } else {
            // Add to wishlist
            $success = $wishlistModel->addToWishlist($userId, $productId);
            $message = 'Đã thêm vào danh sách yêu thích';
            $inWishlist = true;
        }

        // Simulate checking if product is in wishlist
        $isInWishlist = false; // This would be the result of checking the database

        if ($isInWishlist) {
            // Remove from wishlist
            $success = true; // Database remove operation result
            $message = 'Đã xóa khỏi danh sách yêu thích';
            $inWishlist = false;
        } else {
            // Add to wishlist
            $success = true; // Database add operation result
            $message = 'Đã thêm vào danh sách yêu thích';
            $inWishlist = true;
        }

        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => $message,
                'in_wishlist' => $inWishlist
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Không thể cập nhật danh sách yêu thích'
            ]);
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
        ]);
    }
}
?>
