<?php

require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../../core/ApiController.php';
require_once __DIR__ . '/../../core/ApiResponse.php';
require_once __DIR__ . '/../../models/Coupon.php';

class VoucherApiController extends ApiController
{
    private $pdo;
    private $couponModel;

    public function __construct()
    {
        parent::__construct();
        $this->pdo = Database::getInstance()->getConnection();
        $this->couponModel = new Coupon();
    }

    /**
     * Get all available vouchers
     */
    public function index()
    {
        try {
            $vouchers = $this->couponModel->getActiveCoupons();

            ApiResponse::success($vouchers, 'Vouchers retrieved successfully');
        } catch (Exception $e) {
            ApiResponse::error('Failed to retrieve vouchers: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get featured vouchers for homepage
     */
    public function featured()
    {
        try {
            $limit = $_GET['limit'] ?? 2;
            $vouchers = $this->couponModel->getFeaturedVouchers($limit);

            ApiResponse::success($vouchers, 'Featured vouchers retrieved successfully');
        } catch (Exception $e) {
            ApiResponse::error('Failed to retrieve featured vouchers: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Save voucher to user account
     */
    public function save()
    {
        try {
            // Check if user is authenticated
            if (!isLoggedIn()) {
                ApiResponse::error('Authentication required', 401);
                return;
            }

            $couponId = $this->requestData['coupon_id'] ?? null;
            $userId = $_SESSION['user']['id'];

            if (!$couponId) {
                ApiResponse::error('Coupon ID is required', 422);
                return;
            }

            // Check if coupon exists and is valid
            $coupon = $this->couponModel->find($couponId);
            if (!$coupon) {
                ApiResponse::error('Voucher not found', 404);
                return;
            }

            // Check if coupon is still valid
            $now = date('Y-m-d H:i:s');
            if ($coupon['valid_until'] && $coupon['valid_until'] < $now) {
                ApiResponse::error('Voucher has expired', 422);
                return;
            }

            if ($coupon['status'] !== 'active') {
                ApiResponse::error('Voucher is not active', 422);
                return;
            }

            // Check if user already has this voucher
            $stmt = $this->pdo->prepare("
                SELECT id FROM user_coupons
                WHERE user_id = ? AND coupon_id = ?
            ");
            $stmt->execute([$userId, $couponId]);

            if ($stmt->fetch()) {
                ApiResponse::error('You already have this voucher', 422);
                return;
            }

            // Save voucher to user account
            $stmt = $this->pdo->prepare("
                INSERT INTO user_coupons (user_id, coupon_id, saved_at)
                VALUES (?, ?, NOW())
            ");

            if ($stmt->execute([$userId, $couponId])) {
                ApiResponse::success([
                    'voucher' => $coupon,
                    'saved_at' => date('Y-m-d H:i:s')
                ], 'Voucher saved successfully');
            } else {
                ApiResponse::error('Failed to save voucher', 500);
            }

        } catch (Exception $e) {
            ApiResponse::error('Failed to save voucher: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Check voucher validity
     */
    public function check()
    {
        try {
            $code = $_GET['code'] ?? null;

            if (!$code) {
                ApiResponse::error('Voucher code is required', 422);
                return;
            }

            $coupon = $this->couponModel->findByCode($code);

            if (!$coupon) {
                ApiResponse::error('Invalid voucher code', 404);
                return;
            }

            // Check validity
            $now = date('Y-m-d H:i:s');
            $isValid = $coupon['status'] === 'active' &&
                      (!$coupon['valid_until'] || $coupon['valid_until'] > $now) &&
                      ($coupon['usage_limit'] === null || $coupon['used_count'] < $coupon['usage_limit']);

            ApiResponse::success([
                'coupon' => $coupon,
                'is_valid' => $isValid,
                'reason' => !$isValid ? $this->getInvalidReason($coupon, $now) : null
            ], 'Voucher check completed');

        } catch (Exception $e) {
            ApiResponse::error('Failed to check voucher: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get reason why voucher is invalid
     */
    private function getInvalidReason($coupon, $now)
    {
        if ($coupon['status'] !== 'active') {
            return 'Voucher is not active';
        }

        if ($coupon['valid_until'] && $coupon['valid_until'] <= $now) {
            return 'Voucher has expired';
        }

        if ($coupon['usage_limit'] && $coupon['used_count'] >= $coupon['usage_limit']) {
            return 'Voucher usage limit reached';
        }

        return 'Unknown reason';
    }
}
