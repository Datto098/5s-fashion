<?php
/**
 * UserCoupon Model
 * 5S Fashion E-commerce Platform
 */

require_once dirname(__DIR__) . '/core/Model.php';
require_once dirname(__DIR__) . '/core/Database.php';

class UserCoupon extends BaseModel

{

        protected $table = 'user_coupons';
        protected $primaryKey = 'id';
        protected $fillable = [
            'user_id', 'coupon_id', 'status'
        ];

    
    /**
     * Get user's saved coupons
     */
    public function getUserCoupons($userId, $status = 'saved')
    {
        $sql = "SELECT uc.*, c.code, c.name, c.description, c.type, c.value,
                       c.minimum_amount, c.maximum_discount, c.valid_from, c.valid_until,
                       c.status as coupon_status
                FROM {$this->table} uc
                INNER JOIN coupons c ON uc.coupon_id = c.id
                WHERE uc.user_id = ?";

        $params = [$userId];

        if ($status) {
            $sql .= " AND uc.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY uc.saved_at DESC";

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Get available coupons for user (not saved yet)
     */
    public function getAvailableCoupons($userId)
    {
        $sql = "SELECT c.* FROM coupons c
                WHERE c.status = 'active'
                AND (c.valid_from IS NULL OR c.valid_from <= NOW())
                AND (c.valid_until IS NULL OR c.valid_until >= NOW())
                AND c.id NOT IN (
                    SELECT coupon_id FROM {$this->table}
                    WHERE user_id = ? AND status IN ('saved', 'used')
                )
                ORDER BY c.created_at DESC";

        return $this->db->fetchAll($sql, [$userId]);
    }
        /**
     * Tìm user_coupons theo user_id và code (dùng cho kiểm tra quyền áp dụng mã)
     */
    public function findByUserAndCode($userId, $code)
    {
        $sql = "SELECT uc.*, c.* FROM {$this->table} uc
                INNER JOIN coupons c ON uc.coupon_id = c.id
                WHERE uc.user_id = ? AND c.code = ? AND uc.status = 'saved'";
        return $this->db->fetchOne($sql, [$userId, $code]);
    }

    /**
     * Save coupon for user
     */
    public function saveCoupon($userId, $couponId)
    {
        // Check if coupon exists and is active
        $couponModel = new Coupon();
        $coupon = $couponModel->find($couponId);

        if (!$coupon || $coupon['status'] !== 'active') {
            return ['success' => false, 'message' => 'Voucher không khả dụng'];
        }

        // Check if already saved
        $existingSql = "SELECT * FROM {$this->table} WHERE user_id = ? AND coupon_id = ?";
        $existing = $this->db->fetchOne($existingSql, [$userId, $couponId]);

        if ($existing) {
            return ['success' => false, 'message' => 'Bạn đã lưu voucher này rồi'];
        }

        // Save coupon
        $data = [
            'user_id' => $userId,
            'coupon_id' => $couponId,
            'status' => 'saved'
        ];

        if ($this->create($data)) {
            return ['success' => true, 'message' => 'Đã lưu voucher thành công'];
        }

        return ['success' => false, 'message' => 'Có lỗi xảy ra khi lưu voucher'];
    }

    /**
     * Remove saved coupon
     */
    public function removeCoupon($userId, $couponId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ? AND coupon_id = ? AND status = 'saved'";
        $userCoupon = $this->db->fetchOne($sql, [$userId, $couponId]);

        if ($userCoupon && $this->delete($userCoupon['id'])) {
            return ['success' => true, 'message' => 'Đã xóa voucher khỏi danh sách'];
        }

        return ['success' => false, 'message' => 'Không thể xóa voucher'];
    }

    /**
     * Check if user has saved a coupon
     */
    public function userHasCoupon($userId, $couponId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ? AND coupon_id = ?";
        $userCoupon = $this->db->fetchOne($sql, [$userId, $couponId]);

        return $userCoupon !== null;
    }

    /**
     * Get user's valid coupons for checkout
     */
    public function getValidCouponsForCheckout($userId, $orderAmount)
    {
        $sql = "SELECT uc.*, c.code, c.name, c.description, c.type, c.value,
                       c.minimum_amount, c.maximum_discount, c.valid_from, c.valid_until
                FROM {$this->table} uc
                INNER JOIN coupons c ON uc.coupon_id = c.id
                WHERE uc.user_id = ?
                AND uc.status = 'saved'
                AND c.status = 'active'
                AND (c.valid_from IS NULL OR c.valid_from <= NOW())
                AND (c.valid_until IS NULL OR c.valid_until >= NOW())
                AND (c.minimum_amount IS NULL OR c.minimum_amount <= ?)
                ORDER BY c.value DESC";

        return $this->db->fetchAll($sql, [$userId, $orderAmount]);
    }

    /**
     * Mark user coupon as expired
     */
    public function markExpiredCoupons($userId = null)
    {
        $sql = "UPDATE {$this->table} uc
                INNER JOIN coupons c ON uc.coupon_id = c.id
                SET uc.status = 'expired'
                WHERE uc.status = 'saved'
                AND c.valid_until < NOW()";

        $params = [];
        if ($userId) {
            $sql .= " AND uc.user_id = ?";
            $params[] = $userId;
        }

        return $this->db->execute($sql, $params);
    }

    /**
     * Get user coupon statistics
     */
    public function getUserCouponStats($userId)
    {
        $sql = "SELECT
                    COUNT(*) as total_saved,
                    SUM(CASE WHEN uc.status = 'saved' THEN 1 ELSE 0 END) as saved_count,
                    SUM(CASE WHEN uc.status = 'used' THEN 1 ELSE 0 END) as used_count,
                    SUM(CASE WHEN uc.status = 'expired' THEN 1 ELSE 0 END) as expired_count
                FROM {$this->table} uc
                WHERE uc.user_id = ?";

        $stats = $this->db->fetchOne($sql, [$userId]);

        // Get total savings
        $savingsSql = "SELECT COALESCE(SUM(cu.discount_amount), 0) as total_savings
                       FROM coupon_usage cu
                       WHERE cu.user_id = ?";
        $savingsResult = $this->db->fetchOne($savingsSql, [$userId]);

        return array_merge($stats ?: [], $savingsResult ?: []);
    }

    /**
     * Get trending coupons (most saved by users)
     */
    public function getTrendingCoupons($limit = 10)
    {
        $sql = "SELECT c.*, COUNT(uc.id) as save_count
                FROM coupons c
                INNER JOIN {$this->table} uc ON c.id = uc.coupon_id
                WHERE c.status = 'active'
                AND (c.valid_from IS NULL OR c.valid_from <= NOW())
                AND (c.valid_until IS NULL OR c.valid_until >= NOW())
                GROUP BY c.id
                ORDER BY save_count DESC
                LIMIT ?";

        return $this->db->fetchAll($sql, [$limit]);
    }

    /**
     * Notify users about expiring coupons
     */
    public function getExpiringCoupons($days = 7)
    {
        $sql = "SELECT uc.user_id, u.email, u.name as user_name,
                       c.code, c.name as coupon_name, c.valid_until
                FROM {$this->table} uc
                INNER JOIN coupons c ON uc.coupon_id = c.id
                INNER JOIN users u ON uc.user_id = u.id
                WHERE uc.status = 'saved'
                AND c.status = 'active'
                AND c.valid_until BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL ? DAY)
                ORDER BY c.valid_until ASC";

        return $this->db->fetchAll($sql, [$days]);
    }

    /**
     * Auto-save popular coupons for new users
     */
    public function autoSavePopularCoupons($userId, $limit = 3)
    {
        $popularCoupons = $this->getTrendingCoupons($limit);
        $savedCount = 0;

        foreach ($popularCoupons as $coupon) {
            $result = $this->saveCoupon($userId, $coupon['id']);
            if ($result['success']) {
                $savedCount++;
            }
        }

        return $savedCount;
    }

    /**
     * Validate and apply coupon code for checkout
     */
    public function applyCouponCode($userId, $orderAmount, $couponCode)
    {
        // Tìm coupon theo code
        $couponModel = new Coupon();
        $coupon = $couponModel->findByCode($couponCode);

        if (!$coupon || $coupon['status'] !== 'active') {
            return ['success' => false, 'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn'];
        }

        // Kiểm tra thời gian hiệu lực
        $now = date('Y-m-d H:i:s');
        if (($coupon['valid_from'] && $coupon['valid_from'] > $now) ||
            ($coupon['valid_until'] && $coupon['valid_until'] < $now)) {
            return ['success' => false, 'message' => 'Mã giảm giá chưa đến hạn hoặc đã hết hạn'];
        }

        // Kiểm tra điều kiện đơn hàng tối thiểu
        if ($coupon['minimum_amount'] && $orderAmount < $coupon['minimum_amount']) {
            return ['success' => false, 'message' => 'Đơn hàng chưa đủ điều kiện áp dụng mã giảm giá'];
        }

        // Tính số tiền giảm
        $discount = 0;
        if ($coupon['type'] === 'percent') {
            $discount = $orderAmount * ($coupon['value'] / 100);
            if ($coupon['maximum_discount'] && $discount > $coupon['maximum_discount']) {
                $discount = $coupon['maximum_discount'];
            }
        } else {
            $discount = $coupon['value'];
        }
        if ($discount > $orderAmount) $discount = $orderAmount;

        return [
            'success' => true,
            'discount' => $discount,
            'coupon' => $coupon,
            'message' => 'Áp dụng mã giảm giá thành công'
        ];
    }

    /**
     * Đánh dấu coupon đã dùng cho đơn hàng
     */
    public function updateCouponUsed($userId, $couponId, $orderId)
    {
        $now = date('Y-m-d H:i:s');
        $sql = "UPDATE {$this->table} SET order_id = ?, used_at = ?, status = 'used' WHERE user_id = ? AND coupon_id = ? AND status = 'saved' LIMIT 1";
        return $this->db->execute($sql, [$orderId, $now, $userId, $couponId]);
    }
}
