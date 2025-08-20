<?php
// Direct API endpoint for voucher save
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Handle OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only allow POST method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed',
        'errors' => null,
        'timestamp' => date('c'),
        'status_code' => 405
    ]);
    exit;
}

// Start session
session_start();

// Include required files
require_once __DIR__ . '/../../app/helpers/functions.php';
require_once __DIR__ . '/../../app/core/Database.php';
require_once __DIR__ . '/../../app/models/Coupon.php';

try {
    // Check if user is authenticated
    if (!isLoggedIn()) {
        $response = [
            'success' => false,
            'message' => 'Vui lòng đăng nhập để lưu voucher!',
            'errors' => null,
            'timestamp' => date('c'),
            'status_code' => 401
        ];
        http_response_code(401);
        echo json_encode($response);
        exit;
    }

    // Get request data
    $input = json_decode(file_get_contents('php://input'), true);
    $couponId = $input['coupon_id'] ?? null;
    $userId = $_SESSION['user']['id'];

    if (!$couponId) {
        $response = [
            'success' => false,
            'message' => 'Thiếu thông tin voucher!',
            'errors' => null,
            'timestamp' => date('c'),
            'status_code' => 422
        ];
        http_response_code(422);
        echo json_encode($response);
        exit;
    }

    // Initialize database and models
    $couponModel = new Coupon();
    $pdo = Database::getInstance()->getConnection();

    // Check if coupon exists and is valid
    $coupon = $couponModel->find($couponId);
    if (!$coupon) {
        $response = [
            'success' => false,
            'message' => 'Voucher không tồn tại!',
            'errors' => null,
            'timestamp' => date('c'),
            'status_code' => 404
        ];
        http_response_code(404);
        echo json_encode($response);
        exit;
    }

    // Check if coupon is still valid
    $now = date('Y-m-d H:i:s');
    if ($coupon['valid_until'] && $coupon['valid_until'] < $now) {
        $response = [
            'success' => false,
            'message' => 'Voucher đã hết hạn sử dụng!',
            'errors' => null,
            'timestamp' => date('c'),
            'status_code' => 422
        ];
        http_response_code(422);
        echo json_encode($response);
        exit;
    }

    if ($coupon['status'] !== 'active') {
        $response = [
            'success' => false,
            'message' => 'Voucher không còn hiệu lực!',
            'errors' => null,
            'timestamp' => date('c'),
            'status_code' => 422
        ];
        http_response_code(422);
        echo json_encode($response);
        exit;
    }

    // Check if user already has this voucher
    $stmt = $pdo->prepare("
        SELECT id FROM user_coupons
        WHERE user_id = ? AND coupon_id = ?
    ");
    $stmt->execute([$userId, $couponId]);

    if ($stmt->fetch()) {
        $response = [
            'success' => false,
            'message' => 'Bạn đã lưu voucher này rồi!',
            'errors' => null,
            'timestamp' => date('c'),
            'status_code' => 422
        ];
        http_response_code(422);
        echo json_encode($response);
        exit;
    }

    // Save voucher to user account
    $stmt = $pdo->prepare("
        INSERT INTO user_coupons (user_id, coupon_id, saved_at)
        VALUES (?, ?, NOW())
    ");

    if ($stmt->execute([$userId, $couponId])) {
        $response = [
            'success' => true,
            'data' => [
                'voucher' => $coupon,
                'saved_at' => date('Y-m-d H:i:s')
            ],
            'message' => 'Lưu voucher thành công!',
            'timestamp' => date('c'),
            'status_code' => 200
        ];
        echo json_encode($response);
    } else {
        $response = [
            'success' => false,
            'message' => 'Lưu voucher thất bại. Vui lòng thử lại!',
            'errors' => null,
            'timestamp' => date('c'),
            'status_code' => 500
        ];
        http_response_code(500);
        echo json_encode($response);
    }

} catch (Exception $e) {
    $response = [
        'success' => false,
    'message' => 'Có lỗi xảy ra khi lưu voucher!',
        'errors' => null,
        'timestamp' => date('c'),
        'status_code' => 500
    ];
    http_response_code(500);
    echo json_encode($response);
}
?>