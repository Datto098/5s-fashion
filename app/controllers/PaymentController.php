<?php
/**
 * Payment Controller
 * Handle payment processing for different payment methods
 * 5S Fashion E-commerce Platform
 */

require_once dirname(__DIR__) . '/core/Controller.php';
require_once dirname(__DIR__) . '/models/Order.php';

class PaymentController extends Controller
{
    private $orderModel;

    public function __construct()
    {
        parent::__construct();
        $this->orderModel = new Order();
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

            if (!$input || empty($input['order_id'])) {
                echo json_encode(['success' => false, 'message' => 'Thiếu thông tin đơn hàng']);
                exit;
            }

            $orderId = $input['order_id'];
            $order = $this->orderModel->find($orderId);

            if (!$order) {
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy đơn hàng']);
                exit;
            }

            // VNPay configuration
            $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
            $vnp_Returnurl = url("payment/vnpay/return");
            $vnp_TmnCode = "VNPAY_TMN_CODE"; // Get from config
            $vnp_HashSecret = "VNPAY_HASH_SECRET"; // Get from config

            $vnp_TxnRef = $order['order_code'];
            $vnp_OrderInfo = 'Thanh toán đơn hàng ' . $order['order_code'];
            $vnp_OrderType = 'other';
            $vnp_Amount = $order['total_amount'] * 100; // VNPay requires amount in VND * 100
            $vnp_Locale = 'vn';
            $vnp_BankCode = '';
            $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

            $inputData = array(
                "vnp_Version" => "2.1.0",
                "vnp_TmnCode" => $vnp_TmnCode,
                "vnp_Amount" => $vnp_Amount,
                "vnp_Command" => "pay",
                "vnp_CreateDate" => date('YmdHis'),
                "vnp_CurrCode" => "VND",
                "vnp_IpAddr" => $vnp_IpAddr,
                "vnp_Locale" => $vnp_Locale,
                "vnp_OrderInfo" => $vnp_OrderInfo,
                "vnp_OrderType" => $vnp_OrderType,
                "vnp_ReturnUrl" => $vnp_Returnurl,
                "vnp_TxnRef" => $vnp_TxnRef,
            );

            if (isset($vnp_BankCode) && $vnp_BankCode != "") {
                $inputData['vnp_BankCode'] = $vnp_BankCode;
            }

            ksort($inputData);
            $query = "";
            $i = 0;
            $hashdata = "";
            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashdata .= urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
                $query .= urlencode($key) . "=" . urlencode($value) . '&';
            }

            $vnp_Url = $vnp_Url . "?" . $query;
            if (isset($vnp_HashSecret)) {
                $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
                $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
            }

            echo json_encode([
                'success' => true,
                'redirect_url' => $vnp_Url
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi khởi tạo thanh toán: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Handle VNPay return
     */
    public function vnpayReturn()
    {
        try {
            $vnp_HashSecret = "VNPAY_HASH_SECRET"; // Get from config

            $vnp_SecureHash = $_GET['vnp_SecureHash'];
            $inputData = array();

            foreach ($_GET as $key => $value) {
                if (substr($key, 0, 4) == "vnp_") {
                    $inputData[$key] = $value;
                }
            }

            unset($inputData['vnp_SecureHash']);
            ksort($inputData);

            $hashData = "";
            $i = 0;
            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
            }

            $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

            if ($secureHash == $vnp_SecureHash) {
                $orderCode = $_GET['vnp_TxnRef'];
                $responseCode = $_GET['vnp_ResponseCode'];

                $order = $this->orderModel->findByOrderCode($orderCode);

                if ($order) {
                    if ($responseCode == '00') {
                        // Payment successful
                        $this->orderModel->updatePaymentStatus($order['id'], 'paid');
                        $this->orderModel->updateStatus($order['id'], 'processing', 'Thanh toán VNPay thành công');

                        redirect('order/success?id=' . $order['id']);
                    } else {
                        // Payment failed
                        $this->orderModel->updatePaymentStatus($order['id'], 'failed');
                        redirect('checkout?error=payment_failed');
                    }
                } else {
                    redirect('checkout?error=order_not_found');
                }
            } else {
                redirect('checkout?error=invalid_signature');
            }
        } catch (Exception $e) {
            error_log('VNPay return error: ' . $e->getMessage());
            redirect('checkout?error=payment_error');
        }
    }

    /**
     * Process MoMo payment
     */
    public function momo()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
            exit;
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input || empty($input['order_id'])) {
                echo json_encode(['success' => false, 'message' => 'Thiếu thông tin đơn hàng']);
                exit;
            }

            $orderId = $input['order_id'];
            $order = $this->orderModel->find($orderId);

            if (!$order) {
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy đơn hàng']);
                exit;
            }

            // MoMo configuration
            $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
            $partnerCode = 'MOMO_PARTNER_CODE'; // Get from config
            $accessKey = 'MOMO_ACCESS_KEY'; // Get from config
            $secretKey = 'MOMO_SECRET_KEY'; // Get from config

            $orderInfo = 'Thanh toán đơn hàng ' . $order['order_code'];
            $amount = (string)$order['total_amount'];
            $orderId = $order['order_code'];
            $redirectUrl = url('payment/momo/return');
            $ipnUrl = url('payment/momo/ipn');
            $extraData = "";
            $requestType = "captureWallet";
            $requestId = time() . "";

            // Create signature
            $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
            $signature = hash_hmac("sha256", $rawHash, $secretKey);

            $data = array(
                'partnerCode' => $partnerCode,
                'partnerName' => "5S Fashion",
                'storeId' => "5SFashionStore",
                'requestId' => $requestId,
                'amount' => $amount,
                'orderId' => $orderId,
                'orderInfo' => $orderInfo,
                'redirectUrl' => $redirectUrl,
                'ipnUrl' => $ipnUrl,
                'lang' => 'vi',
                'extraData' => $extraData,
                'requestType' => $requestType,
                'signature' => $signature
            );

            $result = $this->execPostRequest($endpoint, json_encode($data));
            $jsonResult = json_decode($result, true);

            if ($jsonResult['resultCode'] == 0) {
                echo json_encode([
                    'success' => true,
                    'redirect_url' => $jsonResult['payUrl']
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Lỗi khởi tạo thanh toán MoMo'
                ]);
            }

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi khởi tạo thanh toán: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Handle MoMo return
     */
    public function momoReturn()
    {
        try {
            $orderCode = $_GET['orderId'];
            $resultCode = $_GET['resultCode'];

            $order = $this->orderModel->findByOrderCode($orderCode);

            if ($order) {
                if ($resultCode == '0') {
                    // Payment successful
                    $this->orderModel->updatePaymentStatus($order['id'], 'paid');
                    $this->orderModel->updateStatus($order['id'], 'processing', 'Thanh toán MoMo thành công');

                    redirect('order/success?id=' . $order['id']);
                } else {
                    // Payment failed
                    $this->orderModel->updatePaymentStatus($order['id'], 'failed');
                    redirect('checkout?error=payment_failed');
                }
            } else {
                redirect('checkout?error=order_not_found');
            }
        } catch (Exception $e) {
            error_log('MoMo return error: ' . $e->getMessage());
            redirect('checkout?error=payment_error');
        }
    }

    /**
     * Execute POST request for MoMo
     */
    private function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * Handle bank transfer
     */
    public function bankTransfer()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
            exit;
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input || empty($input['order_id'])) {
                echo json_encode(['success' => false, 'message' => 'Thiếu thông tin đơn hàng']);
                exit;
            }

            $orderId = $input['order_id'];
            $order = $this->orderModel->find($orderId);

            if (!$order) {
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy đơn hàng']);
                exit;
            }

            // For bank transfer, we just update status to pending and show bank info
            $this->orderModel->updatePaymentStatus($orderId, 'pending');

            echo json_encode([
                'success' => true,
                'message' => 'Đơn hàng đã được tạo. Vui lòng chuyển khoản theo thông tin bên dưới.',
                'bank_info' => [
                    'bank_name' => 'Vietcombank',
                    'account_number' => '1234567890',
                    'account_name' => '5S Fashion Co., Ltd',
                    'amount' => $order['total_amount'],
                    'content' => 'Thanh toan don hang ' . $order['order_code']
                ]
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi xử lý chuyển khoản: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get payment methods
     */
    public function getMethods()
    {
        header('Content-Type: application/json');

        $methods = [
            'cod' => [
                'name' => 'Thanh toán khi nhận hàng',
                'description' => 'Thanh toán bằng tiền mặt khi nhận hàng',
                'icon' => 'fas fa-money-bill',
                'fee' => 0,
                'enabled' => true
            ],
            'vnpay' => [
                'name' => 'VNPay',
                'description' => 'Thanh toán qua VNPay (ATM, Visa, MasterCard)',
                'icon' => 'fab fa-cc-visa',
                'fee' => 0,
                'enabled' => true
            ],
            'momo' => [
                'name' => 'MoMo',
                'description' => 'Thanh toán qua ví điện tử MoMo',
                'icon' => 'fas fa-mobile-alt',
                'fee' => 0,
                'enabled' => true
            ],
            'bank_transfer' => [
                'name' => 'Chuyển khoản ngân hàng',
                'description' => 'Chuyển khoản trực tiếp qua ngân hàng',
                'icon' => 'fas fa-university',
                'fee' => 0,
                'enabled' => true
            ]
        ];

        echo json_encode([
            'success' => true,
            'methods' => $methods
        ]);
    }
}
