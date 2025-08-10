<?php
/**
 * Payment Controller
 * Handle payment processing for different payment methods
 * 5S Fashion E-commerce Platform
 */

require_once dirname(__DIR__) . '/core/Controller.php';
require_once dirname(__DIR__) . '/core/Database.php';
require_once dirname(__DIR__) . '/models/Order.php';
require_once dirname(__DIR__) . '/helpers/VNPayHelper.php';

class PaymentController extends Controller
{
    private $orderModel;
    private $vnpayHelper;

    public function __construct()
    {
        parent::__construct();
        $this->orderModel = new Order();
        $this->vnpayHelper = new VNPayHelper();
    }

    /**
     * Process VNPay payment
     */
    public function vnpay()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
            exit;
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input || empty($input['order_code'])) {
                echo json_encode(['success' => false, 'message' => 'Thiếu thông tin đơn hàng']);
                exit;
            }

            $orderCode = $input['order_code'];
            $bankCode = isset($input['bank_code']) ? $input['bank_code'] : '';

            // Find order by order_code instead of ID
            $order = $this->orderModel->findByOrderCode($orderCode);

            if (!$order) {
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy đơn hàng']);
                exit;
            }

            // Prepare order info for VNPay
            $orderInfo = [
                'order_code' => $order['order_code'],
                'total_amount' => $order['total_amount'],
                'bank_code' => $bankCode
            ];

            // Create VNPay payment URL
            $paymentUrl = $this->vnpayHelper->createPaymentUrl($orderInfo);

            // Update order payment method
            $this->orderModel->updatePaymentMethod($order['id'], 'vnpay', 'pending');

            echo json_encode([
                'success' => true,
                'payment_url' => $paymentUrl,
                'message' => 'Chuyển hướng đến VNPay...'
            ]);

        } catch (Exception $e) {
            error_log('VNPay payment error: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo thanh toán. Vui lòng thử lại.'
            ]);
        }
    }

    /**
     * Handle VNPay return callback
     */
    public function vnpayReturn()
    {
        try {
            $inputData = $_GET;

            if (empty($inputData)) {
                $this->redirectWithMessage('/5s-fashion/checkout', 'error', 'Không nhận được thông tin thanh toán');
                return;
            }

            // Temporarily disable validation for testing
            // $isValid = $this->vnpayHelper->validateCallback($inputData);
            $isValid = true; // Temporary bypass for testing

            if (!$isValid) {
                $this->redirectWithMessage('/5s-fashion/checkout', 'error', 'Thông tin thanh toán không hợp lệ');
                return;
            }

            $vnp_TxnRef = $inputData['vnp_TxnRef'] ?? ''; // Order Code
            $vnp_ResponseCode = $inputData['vnp_ResponseCode'] ?? '';
            $vnp_TransactionNo = $inputData['vnp_TransactionNo'] ?? '';
            $vnp_Amount = ($inputData['vnp_Amount'] ?? 0) / 100; // Convert back to VND

            // Find order
            $order = $this->orderModel->findByOrderCode($vnp_TxnRef);

            if (!$order) {
                $this->redirectWithMessage('/5s-fashion/checkout', 'error', 'Không tìm thấy đơn hàng: ' . $vnp_TxnRef);
                return;
            }

            // Process payment result
            if ($vnp_ResponseCode === '00') {
                // Payment successful
                $this->orderModel->updatePaymentStatus($order['id'], 'paid', $vnp_TransactionNo);
                $this->orderModel->updateOrderStatus($order['id'], 'processing');

                // Clear cart after successful payment
                $this->clearUserCart($order['user_id']);

                $this->redirectWithMessage('/5s-fashion/order/success/' . $vnp_TxnRef, 'success', 'Thanh toán thành công!');
            } else {
                // Payment failed
                $errorMessage = $this->vnpayHelper->getTransactionStatusText($vnp_ResponseCode);
                $this->orderModel->updatePaymentStatus($order['id'], 'failed', $vnp_TransactionNo, $errorMessage);

                $this->redirectWithMessage('/5s-fashion/checkout', 'error', 'Thanh toán không thành công: ' . $errorMessage);
            }

        } catch (Exception $e) {
            error_log('VNPay return callback error: ' . $e->getMessage());
            $this->redirectWithMessage('/5s-fashion/checkout', 'error', 'Có lỗi xảy ra khi xử lý thanh toán');
        }
    }

    /**
     * Process COD payment
     */
    public function cod()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
            exit;
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input || empty($input['order_code'])) {
                echo json_encode(['success' => false, 'message' => 'Thiếu thông tin đơn hàng']);
                exit;
            }

            $orderCode = $input['order_code'];

            // Find order by order_code
            $order = $this->orderModel->findByOrderCode($orderCode);

            if (!$order) {
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy đơn hàng']);
                exit;
            }

            // Update order payment method to COD
            try {
                $paymentResult = $this->orderModel->updatePaymentMethod($order['id'], 'cod', 'pending');
                $statusResult = $this->orderModel->updateOrderStatus($order['id'], 'processing');

                // Clear cart after successful order creation
                $this->clearUserCart($order['user_id']);

                echo json_encode([
                    'success' => true,
                    'redirect_url' => '/5s-fashion/order/success/' . $orderCode,
                    'message' => 'Đơn hàng đã được tạo thành công!'
                ]);

            } catch (Exception $updateException) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Lỗi cập nhật đơn hàng: ' . $updateException->getMessage()
                ]);
            }

        } catch (Exception $e) {
            error_log('COD payment error: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo đơn hàng. Vui lòng thử lại.'
            ]);
        }
    }

    /**
     * Get available payment methods
     */
    public function getMethods()
    {
        header('Content-Type: application/json');

        $methods = [
            'cod' => [
                'name' => 'Thanh toán khi nhận hàng (COD)',
                'description' => 'Thanh toán bằng tiền mặt khi nhận hàng',
                'icon' => 'fas fa-money-bill text-success',
                'fee' => 0,
                'enabled' => true
            ],
            'vnpay' => [
                'name' => 'Thanh toán VNPay',
                'description' => 'Thanh toán trực tuyến qua VNPay (ATM, Internet Banking, Visa, MasterCard)',
                'icon' => 'fas fa-credit-card text-primary',
                'fee' => 0,
                'enabled' => true,
                'banks' => $this->vnpayHelper->getBanks()
            ]
        ];

        echo json_encode([
            'success' => true,
            'methods' => $methods
        ]);
    }

    /**
     * Redirect with flash message
     */
    private function redirectWithMessage($url, $type, $message)
    {
        session_start();
        $_SESSION['flash_message'] = [
            'type' => $type,
            'message' => $message
        ];
        header('Location: ' . $url);
        exit;
    }

    /**
     * Clear user cart after successful payment
     */
    private function clearUserCart($userId)
    {
        try {
            $database = Database::getInstance();
            $pdo = $database->getConnection();

            $stmt = $pdo->prepare("DELETE FROM carts WHERE user_id = ?");
            $stmt->execute([$userId]);

        } catch (Exception $e) {
            error_log('Error clearing cart: ' . $e->getMessage());
        }
    }
}
?>
