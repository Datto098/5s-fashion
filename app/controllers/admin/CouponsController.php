<?php
/**
 * Admin Coupons Controller
 * zone Fashion E-commerce Platform
 */

require_once dirname(__DIR__) . '/BaseController.php';
require_once dirname(__DIR__) . '/../models/Coupon.php';
require_once dirname(__DIR__) . '/../models/UserCoupon.php';

class CouponsController extends BaseController
{
    private $couponModel;
    private $userCouponModel;

    public function __construct()
    {
        $this->couponModel = new Coupon();
        $this->userCouponModel = new UserCoupon();
    }

    /**
     * List all coupons
     */
    public function index()
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;

        $filters = [
            'status' => $_GET['status'] ?? '',
            'type' => $_GET['type'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];

        $result = $this->couponModel->getCouponsWithFilters($filters, $page, $limit);
        $coupons = $result['coupons'];
        $totalCoupons = $result['total'];
        $totalPages = ceil($totalCoupons / $limit);

        // Get statistics
        $stats = $this->couponModel->getCouponStatistics();

        $this->render('admin/coupons/index', [
            'coupons' => $coupons,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalCoupons' => $totalCoupons,
            'filters' => $filters,
            'stats' => $stats
        ], 'admin/layouts/main-inline');
    }

    /**
     * Show create coupon form
     */
    public function create()
    {
        $this->render('admin/coupons/create', [], 'admin/layouts/main-inline');
    }

    /**
     * Store new coupon
     */
    public function store()
    {
        try {
            $data = [
                'code' => strtoupper(trim($_POST['code'])),
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description'] ?? ''),
                'type' => $_POST['type'],
                'value' => (float)$_POST['value'],
                'minimum_amount' => !empty($_POST['minimum_amount']) ? (float)$_POST['minimum_amount'] : null,
                'maximum_discount' => !empty($_POST['maximum_discount']) ? (float)$_POST['maximum_discount'] : null,
                'usage_limit' => !empty($_POST['usage_limit']) ? (int)$_POST['usage_limit'] : null,
                'user_limit' => !empty($_POST['user_limit']) ? (int)$_POST['user_limit'] : null,
                'valid_from' => !empty($_POST['valid_from']) ? $_POST['valid_from'] : null,
                'valid_until' => !empty($_POST['valid_until']) ? $_POST['valid_until'] : null,
                'status' => $_POST['status'] ?? 'active'
            ];

            // Validate required fields
            if (empty($data['code']) || empty($data['name']) || empty($data['type']) || $data['value'] <= 0) {
                throw new Exception('Vui lòng điền đầy đủ thông tin bắt buộc');
            }

            // Check if code exists
            if ($this->couponModel->codeExists($data['code'])) {
                throw new Exception('Mã voucher đã tồn tại');
            }

            // Validate percentage value
            if ($data['type'] === 'percentage' && $data['value'] > 100) {
                throw new Exception('Giá trị phần trăm không được vượt quá 100%');
            }

            // Create coupon
            if ($this->couponModel->create($data)) {
                header('Location: /zone-fashion/admin/coupons?success=' . urlencode('Tạo voucher thành công'));
                exit;
            } else {
                throw new Exception('Có lỗi xảy ra khi tạo voucher');
            }

        } catch (Exception $e) {
            $this->render('admin/coupons/create', [
                'error' => $e->getMessage(),
                'old_data' => $_POST
            ], 'admin/layouts/main-inline');
        }
    }

    /**
     * Show coupon details
     */
    public function show($id)
    {
        $coupon = $this->couponModel->find($id);

        if (!$coupon) {
            header('Location: /zone-fashion/admin/coupons?error=' . urlencode('Không tìm thấy voucher'));
            exit;
        }

        // Get usage statistics for this coupon
        $usageStats = $this->getUsageStatistics($id);

        $this->render('admin/coupons/show', [
            'coupon' => $coupon,
            'usage_stats' => $usageStats
        ], 'admin/layouts/main-inline');
    }

    /**
     * Show edit coupon form
     */
    public function edit($id)
    {
        $coupon = $this->couponModel->find($id);

        if (!$coupon) {
            header('Location: /zone-fashion/admin/coupons?error=' . urlencode('Không tìm thấy voucher'));
            exit;
        }

        $this->render('admin/coupons/edit', [
            'coupon' => $coupon
        ], 'admin/layouts/main-inline');
    }

    /**
     * Update coupon
     */
    public function update($id)
    {
        try {
            $coupon = $this->couponModel->find($id);

            if (!$coupon) {
                throw new Exception('Không tìm thấy voucher');
            }

            $data = [
                'code' => strtoupper(trim($_POST['code'])),
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description'] ?? ''),
                'type' => $_POST['type'],
                'value' => (float)$_POST['value'],
                'minimum_amount' => !empty($_POST['minimum_amount']) ? (float)$_POST['minimum_amount'] : null,
                'maximum_discount' => !empty($_POST['maximum_discount']) ? (float)$_POST['maximum_discount'] : null,
                'usage_limit' => !empty($_POST['usage_limit']) ? (int)$_POST['usage_limit'] : null,
                'user_limit' => !empty($_POST['user_limit']) ? (int)$_POST['user_limit'] : null,
                'valid_from' => !empty($_POST['valid_from']) ? $_POST['valid_from'] : null,
                'valid_until' => !empty($_POST['valid_until']) ? $_POST['valid_until'] : null,
                'status' => $_POST['status'] ?? 'active'
            ];

            // Validate required fields
            if (empty($data['code']) || empty($data['name']) || empty($data['type']) || $data['value'] <= 0) {
                throw new Exception('Vui lòng điền đầy đủ thông tin bắt buộc');
            }

            // Check if code exists (exclude current coupon)
            if ($this->couponModel->codeExists($data['code'], $id)) {
                throw new Exception('Mã voucher đã tồn tại');
            }

            // Validate percentage value
            if ($data['type'] === 'percentage' && $data['value'] > 100) {
                throw new Exception('Giá trị phần trăm không được vượt quá 100%');
            }

            // Update coupon
            if ($this->couponModel->update($id, $data)) {
                header('Location: /zone-fashion/admin/coupons?success=' . urlencode('Cập nhật voucher thành công'));
                exit;
            } else {
                throw new Exception('Có lỗi xảy ra khi cập nhật voucher');
            }

        } catch (Exception $e) {
            $this->render('admin/coupons/edit', [
                'coupon' => $coupon,
                'error' => $e->getMessage(),
                'old_data' => $_POST
            ], 'admin/layouts/main-inline');
        }
    }

    /**
     * Delete coupon
     */
    public function destroy($id)
    {
        try {
            $coupon = $this->couponModel->find($id);

            if (!$coupon) {
                throw new Exception('Không tìm thấy voucher');
            }

            // Check if coupon has been used
            if ($coupon['used_count'] > 0) {
                throw new Exception('Không thể xóa voucher đã được sử dụng');
            }

            if ($this->couponModel->delete($id)) {
                header('Location: /zone-fashion/admin/coupons?success=' . urlencode('Xóa voucher thành công'));
                exit;
            } else {
                throw new Exception('Có lỗi xảy ra khi xóa voucher');
            }

        } catch (Exception $e) {
            header('Location: /zone-fashion/admin/coupons?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    /**
     * Bulk actions
     */
    public function bulkAction()
    {
        try {
            $action = $_POST['bulk_action'] ?? '';
            $couponIds = $_POST['coupon_ids'] ?? [];

            if (empty($action) || empty($couponIds)) {
                throw new Exception('Vui lòng chọn hành động và ít nhất một voucher');
            }

            $count = 0;
            foreach ($couponIds as $id) {
                switch ($action) {
                    case 'activate':
                        if ($this->couponModel->update($id, ['status' => 'active'])) {
                            $count++;
                        }
                        break;
                    case 'deactivate':
                        if ($this->couponModel->update($id, ['status' => 'inactive'])) {
                            $count++;
                        }
                        break;
                    case 'delete':
                        $coupon = $this->couponModel->find($id);
                        if ($coupon && $coupon['used_count'] == 0 && $this->couponModel->delete($id)) {
                            $count++;
                        }
                        break;
                }
            }

            $message = '';
            switch ($action) {
                case 'activate':
                    $message = "Đã kích hoạt {$count} voucher";
                    break;
                case 'deactivate':
                    $message = "Đã tắt {$count} voucher";
                    break;
                case 'delete':
                    $message = "Đã xóa {$count} voucher";
                    break;
            }

            header('Location: /zone-fashion/admin/coupons?success=' . urlencode($message));
            exit;

        } catch (Exception $e) {
            header('Location: /zone-fashion/admin/coupons?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    /**
     * Export coupons
     */
    public function export()
    {
        try {
            $format = $_GET['format'] ?? 'csv';

            $filters = [
                'status' => $_GET['status'] ?? '',
                'type' => $_GET['type'] ?? '',
                'search' => $_GET['search'] ?? ''
            ];

            $coupons = $this->couponModel->exportCoupons($filters);

            if ($format === 'csv') {
                $this->exportToCsv($coupons, 'coupons_' . date('Y-m-d_H-i-s') . '.csv');
            }

        } catch (Exception $e) {
            header('Location: /zone-fashion/admin/coupons?error=' . urlencode('Lỗi khi xuất dữ liệu: ' . $e->getMessage()));
            exit;
        }
    }

    /**
     * Generate coupon code
     */
    public function generateCode()
    {
        header('Content-Type: application/json');

        $prefix = $_GET['prefix'] ?? '';
        $length = (int)($_GET['length'] ?? 8);

        $code = $this->couponModel->generateCouponCode($prefix, $length);

        echo json_encode(['code' => $code]);
        exit;
    }

    /**
     * Validate coupon code
     */
    public function validateCode()
    {
        header('Content-Type: application/json');

        $code = $_GET['code'] ?? '';
        $orderAmount = (float)($_GET['amount'] ?? 0);
        $userId = $_SESSION['user_id'] ?? null;

        $result = $this->couponModel->validateCoupon($code, $orderAmount, $userId);

        if ($result['valid']) {
            $discount = $this->couponModel->calculateDiscount($result['coupon'], $orderAmount);
            $result['discount'] = $discount;
            $result['formatted_discount'] = number_format($discount);
        }

        echo json_encode($result);
        exit;
    }

    /**
     * Mark expired coupons
     */
    public function markExpired()
    {
        try {
            $count = $this->couponModel->markExpiredCoupons();
            header('Location: /zone-fashion/admin/coupons?success=' . urlencode("Đã đánh dấu {$count} voucher hết hạn"));
            exit;
        } catch (Exception $e) {
            header('Location: /zone-fashion/admin/coupons?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    /**
     * Get usage statistics for a coupon
     */
    private function getUsageStatistics($couponId)
    {
        $db = Database::getInstance();

        $sql = "SELECT
                    COUNT(*) as total_usage,
                    SUM(discount_amount) as total_savings,
                    COUNT(DISTINCT user_id) as unique_users,
                    AVG(discount_amount) as avg_discount
                FROM coupon_usage
                WHERE coupon_id = ?";

        $stats = $db->fetchOne($sql, [$couponId]);

        // Get usage by month
        $monthlyUsageSql = "SELECT
                                DATE_FORMAT(created_at, '%Y-%m') as month,
                                COUNT(*) as usage_count,
                                SUM(discount_amount) as total_discount
                            FROM coupon_usage
                            WHERE coupon_id = ?
                            AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                            ORDER BY month ASC";

        $monthlyStats = $db->fetchAll($monthlyUsageSql, [$couponId]);

        return [
            'overall' => $stats,
            'monthly' => $monthlyStats
        ];
    }

    /**
     * Export to CSV
     */
    private function exportToCsv($data, $filename)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');

        // UTF-8 BOM for proper Excel display
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        if (!empty($data)) {
            // Write headers
            fputcsv($output, array_keys($data[0]));

            // Write data
            foreach ($data as $row) {
                fputcsv($output, $row);
            }
        }

        fclose($output);
        exit;
    }
}
