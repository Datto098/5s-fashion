<?php
/**
 * Client Voucher Controller
 * 5S Fashion E-commerce Platform
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Coupon.php';
require_once __DIR__ . '/../models/UserCoupon.php';

class VoucherController extends BaseController
{
    private $couponModel;
    private $userCouponModel;

    public function __construct()
    {
        $this->couponModel = new Coupon();
        $this->userCouponModel = new UserCoupon();
    }

    /**
     * Show all available vouchers for users
     */
    public function index()
    {
        $userId = $_SESSION['user_id'] ?? null;

        // Get available coupons (not saved by user yet)
        $availableCoupons = [];
        $savedCoupons = [];


        if ($userId) {
            // Lấy tất cả coupon còn hạn, active
            $availableCoupons = $this->couponModel->getActiveCoupons();
            // Lấy tất cả trạng thái user đã lưu (saved, used)
            $savedCoupons = $this->userCouponModel->getUserCoupons($userId, null); // null để lấy tất cả trạng thái
        } else {
            $availableCoupons = $this->couponModel->getActiveCoupons();
        }

        // Get trending coupons
        $trendingCoupons = $this->userCouponModel->getTrendingCoupons(6);

        $this->render('client/vouchers/index', [
            'availableCoupons' => $availableCoupons,
            'savedCoupons' => $savedCoupons,
            'trendingCoupons' => $trendingCoupons,
            'userId' => $userId
        ], 'client/layouts/app');
    }

    /**
     * Show user's saved vouchers
     */
    public function myVouchers()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /5s-fashion/login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
            exit;
        }

        $userId = $_SESSION['user_id'];

        // Get user's coupons by status
        $savedCoupons = $this->userCouponModel->getUserCoupons($userId, 'saved');
        $usedCoupons = $this->userCouponModel->getUserCoupons($userId, 'used');
        $expiredCoupons = $this->userCouponModel->getUserCoupons($userId, 'expired');

        // Get user stats
        $stats = $this->userCouponModel->getUserCouponStats($userId);

        $this->render('client/vouchers/my-vouchers', [
            'savedCoupons' => $savedCoupons,
            'usedCoupons' => $usedCoupons,
            'expiredCoupons' => $expiredCoupons,
            'stats' => $stats
        ], 'client/layouts/app');
    }

    /**
     * Save voucher for user
     */
    public function save()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để lưu voucher']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
            exit;
        }

        $userId = $_SESSION['user_id'];
        $couponId = (int)($_POST['coupon_id'] ?? 0);

        if (!$couponId) {
            echo json_encode(['success' => false, 'message' => 'Thông tin voucher không hợp lệ']);
            exit;
        }

        $result = $this->userCouponModel->saveCoupon($userId, $couponId);
        echo json_encode($result);
        exit;
    }

    /**
     * Remove saved voucher
     */
    public function remove()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
            exit;
        }

        $userId = $_SESSION['user_id'];
        $couponId = (int)($_POST['coupon_id'] ?? 0);

        if (!$couponId) {
            echo json_encode(['success' => false, 'message' => 'Thông tin voucher không hợp lệ']);
            exit;
        }

        $result = $this->userCouponModel->removeCoupon($userId, $couponId);
        echo json_encode($result);
        exit;
    }

    /**
     * Validate voucher code (AJAX)
     */
    public function validate()
    {
        header('Content-Type: application/json');

        $code = $_GET['code'] ?? '';
        $orderAmount = (float)($_GET['amount'] ?? 0);
        $userId = $_SESSION['user_id'] ?? null;

        if (!$code || !$orderAmount) {
            echo json_encode([
                'valid' => false,
                'message' => 'Vui lòng nhập mã voucher và số tiền đơn hàng'
            ]);
            exit;
        }

        $result = $this->couponModel->validateCoupon($code, $orderAmount, $userId);

        if ($result['valid']) {
            $discount = $this->couponModel->calculateDiscount($result['coupon'], $orderAmount);
            $result['discount'] = $discount;
            $result['formatted_discount'] = number_format($discount) . 'đ';
            $result['final_amount'] = $orderAmount - $discount;
            $result['formatted_final_amount'] = number_format($orderAmount - $discount) . 'đ';
        }

        echo json_encode($result);
        exit;
    }

    /**
     * Get user's valid vouchers for checkout
     */
    public function getValidVouchers()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
            exit;
        }

        $userId = $_SESSION['user_id'];
        $orderAmount = (float)($_GET['amount'] ?? 0);

        if (!$orderAmount) {
            echo json_encode(['success' => false, 'message' => 'Số tiền đơn hàng không hợp lệ']);
            exit;
        }

        $validCoupons = $this->userCouponModel->getValidCouponsForCheckout($userId, $orderAmount);

        // Calculate discount for each coupon
        foreach ($validCoupons as &$coupon) {
            $discount = $this->couponModel->calculateDiscount($coupon, $orderAmount);
            $coupon['discount_amount'] = $discount;
            $coupon['formatted_discount'] = number_format($discount) . 'đ';
        }

        echo json_encode([
            'success' => true,
            'coupons' => $validCoupons
        ]);
        exit;
    }

    /**
     * Apply voucher in checkout (called from cart/checkout)
     */
    public function apply()
    {
        header('Content-Type: application/json');

        if (empty($_POST['code']) || empty($_POST['amount'])) {
            echo json_encode(['success' => false, 'message' => 'Thiếu mã hoặc số tiền']);
            exit;
        }

        $code = $_POST['code'] ?? '';
        $orderAmount = (float)($_POST['amount'] ?? 0);
        $userId = $_SESSION['user_id'] ?? null;

        if (!$code || !$orderAmount) {
            echo json_encode([
                'success' => false,
                'message' => 'Thông tin không hợp lệ'
            ]);
            exit;
        }

        // Chỉ cho phép áp dụng nếu user đã lưu mã này (user_coupons)
        if (!$userId) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để sử dụng voucher']);
            exit;
        }

        // Kiểm tra user đã lưu mã này chưa
        $userCoupon = $this->userCouponModel->findByUserAndCode($userId, $code);
        if (!$userCoupon) {
            echo json_encode([
                'success' => false,
                'message' => 'Bạn chưa lưu mã này vào ví voucher của mình!'
            ]);
            exit;
        }

        // Validate coupon (kiểm tra hạn, điều kiện...)
        $validation = $this->couponModel->validateCoupon($code, $orderAmount, $userId);
        if (!$validation['valid']) {
            echo json_encode([
                'success' => false,
                'message' => $validation['message']
            ]);
            exit;
        }

        $coupon = $validation['coupon'];
        $discount = $this->couponModel->calculateDiscount($coupon, $orderAmount);
        $finalAmount = $orderAmount - $discount;

        // Store in session for checkout process
        $_SESSION['applied_coupon'] = [
            'id' => $coupon['id'],
            'code' => $coupon['code'],
            'name' => $coupon['name'],
            'type' => $coupon['type'],
            'value' => $coupon['value'],
            'discount_amount' => $discount
        ];

        echo json_encode([
            'success' => true,
            'message' => 'Áp dụng voucher thành công',
            'coupon' => $coupon,
            'discount' => $discount,
            'formatted_discount' => number_format($discount) . 'đ',
            'final_amount' => $finalAmount,
            'formatted_final_amount' => number_format($finalAmount) . 'đ'
        ]);
        exit;
    }

    /**
     * Remove applied voucher
     */
    public function removeApplied()
    {
        header('Content-Type: application/json');

        unset($_SESSION['applied_coupon']);

        echo json_encode([
            'success' => true,
            'message' => 'Đã hủy áp dụng voucher'
        ]);
        exit;
    }

    /**
     * Share voucher via social media
     */
    public function share($couponId)
    {
        $coupon = $this->couponModel->find($couponId);

        if (!$coupon || $coupon['status'] !== 'active') {
            header('Location: /5s-fashion/vouchers?error=' . urlencode('Voucher không tồn tại'));
            exit;
        }

        $shareUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/5s-fashion/vouchers';
        $shareText = "Nhận ngay voucher {$coupon['name']} - Mã: {$coupon['code']}";

        if ($coupon['type'] === 'percentage') {
            $shareText .= " giảm {$coupon['value']}%";
        } else {
            $shareText .= " giảm " . number_format($coupon['value']) . "đ";
        }

        $shareText .= " tại 5S Fashion!";

        $this->render('client/vouchers/share', [
            'coupon' => $coupon,
            'shareUrl' => $shareUrl,
            'shareText' => $shareText
        ]);
    }
}
