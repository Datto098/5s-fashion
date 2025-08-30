<?php
/**
 * Coupon Model
 * zone Fashion E-commerce Platform
 */

require_once dirname(__DIR__) . '/core/Model.php';
require_once dirname(__DIR__) . '/core/Database.php';

class Coupon extends BaseModel
{
    protected $table = 'coupons';
    protected $primaryKey = 'id';
    protected $fillable = [
        'code', 'name', 'description', 'type', 'value', 'minimum_amount',
        'maximum_discount', 'usage_limit', 'used_count', 'user_limit',
        'valid_from', 'valid_until', 'status'
    ];

    /**
     * Get coupon by code
     */
    public function findByCode($code)
    {
        return $this->findBy('code', $code);
    }

    /**
     * Get all active coupons
     */
    public function getActiveCoupons()
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE status = 'active'
                AND (valid_from IS NULL OR valid_from <= NOW())
                AND (valid_until IS NULL OR valid_until >= NOW())
                ORDER BY created_at DESC";

        return $this->db->fetchAll($sql);
    }

    /**
     * Get coupons with pagination and filters
     */
    public function getCouponsWithFilters($filters = [], $page = 1, $limit = 10)
    {
        $offset = ($page - 1) * $limit;
        $whereConditions = ['1=1'];
        $params = [];

        // Status filter
        if (!empty($filters['status'])) {
            $whereConditions[] = "status = ?";
            $params[] = $filters['status'];
        }

        // Type filter
        if (!empty($filters['type'])) {
            $whereConditions[] = "type = ?";
            $params[] = $filters['type'];
        }

        // Search by code or name
        if (!empty($filters['search'])) {
            $whereConditions[] = "(code LIKE ? OR name LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $whereClause = implode(' AND ', $whereConditions);

        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM {$this->table} WHERE {$whereClause}";
        $totalResult = $this->db->fetchOne($countSql, $params);
        $total = $totalResult['total'];

        // Get coupons
        $sql = "SELECT * FROM {$this->table} WHERE {$whereClause}
                ORDER BY created_at DESC
                LIMIT ? OFFSET ?";

        $params[] = $limit;
        $params[] = $offset;

        $coupons = $this->db->fetchAll($sql, $params);

        return [
            'coupons' => $coupons,
            'total' => $total
        ];
    }

    /**
     * Validate coupon for use
     */
    public function validateCoupon($code, $orderAmount, $userId = null)
    {
        $coupon = $this->findByCode($code);

        if (!$coupon) {
            return ['valid' => false, 'message' => 'Mã giảm giá không tồn tại'];
        }

        // Check if coupon is active
        if ($coupon['status'] !== 'active') {
            return ['valid' => false, 'message' => 'Mã giảm giá không khả dụng'];
        }

        // Check valid dates
        $now = date('Y-m-d H:i:s');

        if ($coupon['valid_from'] && $coupon['valid_from'] > $now) {
            return ['valid' => false, 'message' => 'Mã giảm giá chưa có hiệu lực'];
        }

        if ($coupon['valid_until'] && $coupon['valid_until'] < $now) {
            return ['valid' => false, 'message' => 'Mã giảm giá đã hết hạn'];
        }

        // Check minimum order amount
        if ($coupon['minimum_amount'] && $orderAmount < $coupon['minimum_amount']) {
            return [
                'valid' => false,
                'message' => 'Đơn hàng phải có giá trị tối thiểu ' . number_format($coupon['minimum_amount']) . 'đ'
            ];
        }

        // Check usage limit
        if ($coupon['usage_limit'] && $coupon['used_count'] >= $coupon['usage_limit']) {
            return ['valid' => false, 'message' => 'Mã giảm giá đã hết lượt sử dụng'];
        }

        // Check user usage limit if user is logged in
        if ($userId && $coupon['user_limit']) {
            $userUsageCount = $this->getUserUsageCount($coupon['id'], $userId);
            if ($userUsageCount >= $coupon['user_limit']) {
                return ['valid' => false, 'message' => 'Bạn đã sử dụng hết lượt cho mã giảm giá này'];
            }
        }

        return ['valid' => true, 'coupon' => $coupon];
    }

    /**
     * Calculate discount amount
     */
    public function calculateDiscount($coupon, $orderAmount)
    {
        if ($coupon['type'] === 'percentage') {
            $discount = $orderAmount * ($coupon['value'] / 100);

            // Apply maximum discount limit if set
            if ($coupon['maximum_discount'] && $discount > $coupon['maximum_discount']) {
                $discount = $coupon['maximum_discount'];
            }
        } else {
            $discount = $coupon['value'];
        }

        // Discount cannot exceed order amount
        return min($discount, $orderAmount);
    }

    /**
     * Apply coupon to order
     */
    public function applyCoupon($couponId, $orderId, $userId = null, $discountAmount = 0)
    {
        try {
            // Record coupon usage
            $usageSql = "INSERT INTO coupon_usage (coupon_id, user_id, order_id, discount_amount)
                         VALUES (?, ?, ?, ?)";
            $this->db->execute($usageSql, [$couponId, $userId, $orderId, $discountAmount]);

            // Update coupon used count
            $updateSql = "UPDATE {$this->table} SET used_count = used_count + 1 WHERE id = ?";
            $this->db->execute($updateSql, [$couponId]);

            // Mark user coupon as used if user is logged in
            if ($userId) {
                $userCouponSql = "UPDATE user_coupons SET status = 'used', used_at = NOW()
                                  WHERE user_id = ? AND coupon_id = ? AND status = 'saved'";
                $this->db->execute($userCouponSql, [$userId, $couponId]);
            }

            return true;
        } catch (Exception $e) {
            error_log("Error applying coupon: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user usage count for a coupon
     */
    private function getUserUsageCount($couponId, $userId)
    {
        $sql = "SELECT COUNT(*) as count FROM coupon_usage
                WHERE coupon_id = ? AND user_id = ?";
        $result = $this->db->fetchOne($sql, [$couponId, $userId]);
        return $result ? $result['count'] : 0;
    }

    /**
     * Get coupon statistics
     */
    public function getCouponStatistics()
    {
        $sql = "SELECT
                    COUNT(*) as total_coupons,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_coupons,
                    SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive_coupons,
                    SUM(CASE WHEN status = 'expired' THEN 1 ELSE 0 END) as expired_coupons,
                    SUM(used_count) as total_usage,
                    AVG(value) as average_value
                FROM {$this->table}";

        $stats = $this->db->fetchOne($sql);

        // Get usage statistics
        $usageSql = "SELECT
                        COUNT(*) as total_redemptions,
                        SUM(discount_amount) as total_savings
                     FROM coupon_usage";
        $usageStats = $this->db->fetchOne($usageSql);

        return array_merge($stats ?: [], $usageStats ?: []);
    }

    /**
     * Get expired coupons
     */
    public function getExpiredCoupons()
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE valid_until < NOW() AND status = 'active'
                ORDER BY valid_until ASC";

        return $this->db->fetchAll($sql);
    }

    /**
     * Mark expired coupons
     */
    public function markExpiredCoupons()
    {
        $sql = "UPDATE {$this->table} SET status = 'expired'
                WHERE valid_until < NOW() AND status = 'active'";

        return $this->db->execute($sql);
    }

    /**
     * Get top performing coupons
     */
    public function getTopPerformingCoupons($limit = 10)
    {
        $sql = "SELECT c.*,
                       c.used_count,
                       COALESCE(SUM(cu.discount_amount), 0) as total_savings
                FROM {$this->table} c
                LEFT JOIN coupon_usage cu ON c.id = cu.coupon_id
                GROUP BY c.id
                ORDER BY c.used_count DESC, total_savings DESC
                LIMIT ?";

        return $this->db->fetchAll($sql, [$limit]);
    }

    /**
     * Generate unique coupon code
     */
    public function generateCouponCode($prefix = '', $length = 8)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        do {
            $code = $prefix . substr(str_shuffle($characters), 0, $length);
            $exists = $this->findByCode($code);
        } while ($exists);

        return $code;
    }

    /**
     * Check if coupon code exists
     */
    public function codeExists($code, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE code = ?";
        $params = [$code];

        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        $result = $this->db->fetchOne($sql, $params);
        return $result && $result['count'] > 0;
    }

    /**
     * Export coupons for download
     */
    public function exportCoupons($filters = [])
    {
        $result = $this->getCouponsWithFilters($filters, 1, 1000); // Get up to 1000 coupons
        $coupons = $result['coupons'];

        $export = [];
        foreach ($coupons as $coupon) {
            $export[] = [
                'ID' => $coupon['id'],
                'Code' => $coupon['code'],
                'Tên' => $coupon['name'],
                'Mô tả' => $coupon['description'],
                'Loại' => $coupon['type'] === 'percentage' ? 'Phần trăm' : 'Số tiền cố định',
                'Giá trị' => $coupon['type'] === 'percentage' ? $coupon['value'] . '%' : number_format($coupon['value']) . 'đ',
                'Đơn tối thiểu' => $coupon['minimum_amount'] ? number_format($coupon['minimum_amount']) . 'đ' : '',
                'Giảm tối đa' => $coupon['maximum_discount'] ? number_format($coupon['maximum_discount']) . 'đ' : '',
                'Giới hạn sử dụng' => $coupon['usage_limit'] ?: 'Không giới hạn',
                'Đã sử dụng' => $coupon['used_count'],
                'Hiệu lực từ' => $coupon['valid_from'],
                'Hiệu lực đến' => $coupon['valid_until'],
                'Trạng thái' => ucfirst($coupon['status']),
                'Tạo lúc' => $coupon['created_at']
            ];
        }

        return $export;
    }

    /**
     * Get featured vouchers for homepage display
     */
    public function getFeaturedVouchers($limit = 2)
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE status = 'active'
                AND (valid_until IS NULL OR valid_until > NOW())
                AND (valid_from IS NULL OR valid_from <= NOW())
                AND is_featured = 1
                ORDER BY created_at DESC
                LIMIT ?";

        return $this->db->fetchAll($sql, [$limit]);
    }
}
