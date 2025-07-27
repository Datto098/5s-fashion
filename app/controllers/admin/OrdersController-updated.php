<?php

require_once __DIR__ . '/../../models/Order.php';
require_once __DIR__ . '/../../models/Customer.php';

class OrdersController extends BaseController
{
    private $orderModel;
    private $customerModel;

    public function __construct()
    {
        // Check admin authentication
        if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
            header('Location: /5s-fashion/admin/login');
            exit;
        }

        $this->orderModel = new Order();
        $this->customerModel = new Customer();
    }

    public function index()
    {
        try {
            // Get search and filter parameters
            $search = $_GET['search'] ?? '';
            $filters = [
                'status' => $_GET['status'] ?? '',
                'payment_status' => $_GET['payment_status'] ?? '',
                'payment_method' => $_GET['payment_method'] ?? '',
                'date_from' => $_GET['date_from'] ?? '',
                'date_to' => $_GET['date_to'] ?? '',
                'amount_min' => $_GET['amount_min'] ?? '',
                'amount_max' => $_GET['amount_max'] ?? '',
                'sort' => $_GET['sort'] ?? 'created_at',
                'order' => $_GET['order'] ?? 'DESC',
                'limit' => $_GET['limit'] ?? 20
            ];

            // Get orders with search and filters
            $orders = $this->orderModel->search($search, $filters);

            // Get order statistics
            $stats = $this->orderModel->getStatistics();

            // Get orders needing attention
            $needsAttention = $this->orderModel->getOrdersNeedingAttention();

            $data = [
                'title' => 'Quản lý đơn hàng - 5S Fashion Admin',
                'orders' => $orders,
                'stats' => $stats,
                'needsAttention' => $needsAttention,
                'search' => $search,
                'filters' => $filters,
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'url' => '/5s-fashion/admin'],
                    ['title' => 'Đơn hàng']
                ]
            ];

            $this->render('admin/orders/index', $data, 'admin/layouts/main-inline');

        } catch (Exception $e) {
            $data = [
                'title' => 'Quản lý đơn hàng - 5S Fashion Admin',
                'error' => 'Lỗi khi tải danh sách đơn hàng: ' . $e->getMessage(),
                'orders' => [],
                'stats' => [],
                'needsAttention' => [],
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'url' => '/5s-fashion/admin'],
                    ['title' => 'Đơn hàng']
                ]
            ];

            $this->render('admin/orders/index', $data, 'admin/layouts/main-inline');
        }
    }

    public function view($id)
    {
        try {
            // Get order details
            $order = $this->orderModel->getFullDetails($id);

            if (!$order) {
                throw new Exception('Không tìm thấy đơn hàng');
            }

            $data = [
                'title' => 'Chi tiết đơn hàng #' . $order['order_code'] . ' - 5S Fashion Admin',
                'order' => $order,
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'url' => '/5s-fashion/admin'],
                    ['title' => 'Đơn hàng', 'url' => '/5s-fashion/admin/orders'],
                    ['title' => 'Chi tiết #' . $order['order_code']]
                ]
            ];

            $this->render('admin/orders/view', $data, 'admin/layouts/main-inline');

        } catch (Exception $e) {
            header('Location: /5s-fashion/admin/orders?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function updateStatus()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: /5s-fashion/admin/orders');
                exit;
            }

            $orderId = $_POST['order_id'] ?? null;
            $status = $_POST['status'] ?? null;
            $adminNotes = $_POST['admin_notes'] ?? null;

            if (!$orderId || !$status) {
                throw new Exception('Thiếu thông tin cần thiết');
            }

            // Check if order exists
            $order = $this->orderModel->findById($orderId);
            if (!$order) {
                throw new Exception('Không tìm thấy đơn hàng');
            }

            // Update order status
            $result = $this->orderModel->updateStatus($orderId, $status, $adminNotes);

            if (!$result) {
                throw new Exception('Không thể cập nhật trạng thái đơn hàng');
            }

            // Send notification email to customer if needed
            if (in_array($status, ['processing', 'shipped', 'delivered'])) {
                $this->sendOrderStatusNotification($order, $status);
            }

            if (isset($_POST['ajax']) && $_POST['ajax']) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Cập nhật trạng thái đơn hàng thành công'
                ]);
            } else {
                header('Location: /5s-fashion/admin/orders?success=' . urlencode('Cập nhật trạng thái đơn hàng thành công'));
            }

        } catch (Exception $e) {
            if (isset($_POST['ajax']) && $_POST['ajax']) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'error' => $e->getMessage()
                ]);
            } else {
                header('Location: /5s-fashion/admin/orders?error=' . urlencode($e->getMessage()));
            }
        }
    }

    public function updatePaymentStatus()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: /5s-fashion/admin/orders');
                exit;
            }

            $orderId = $_POST['order_id'] ?? null;
            $paymentStatus = $_POST['payment_status'] ?? null;

            if (!$orderId || !$paymentStatus) {
                throw new Exception('Thiếu thông tin cần thiết');
            }

            // Check if order exists
            $order = $this->orderModel->findById($orderId);
            if (!$order) {
                throw new Exception('Không tìm thấy đơn hàng');
            }

            // Update payment status
            $result = $this->orderModel->updatePaymentStatus($orderId, $paymentStatus);

            if (!$result) {
                throw new Exception('Không thể cập nhật trạng thái thanh toán');
            }

            if (isset($_POST['ajax']) && $_POST['ajax']) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Cập nhật trạng thái thanh toán thành công'
                ]);
            } else {
                header('Location: /5s-fashion/admin/orders?success=' . urlencode('Cập nhật trạng thái thanh toán thành công'));
            }

        } catch (Exception $e) {
            if (isset($_POST['ajax']) && $_POST['ajax']) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'error' => $e->getMessage()
                ]);
            } else {
                header('Location: /5s-fashion/admin/orders?error=' . urlencode($e->getMessage()));
            }
        }
    }

    public function cancel()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: /5s-fashion/admin/orders');
                exit;
            }

            $orderId = $_POST['order_id'] ?? null;
            $reason = $_POST['reason'] ?? null;

            if (!$orderId) {
                throw new Exception('Thiếu ID đơn hàng');
            }

            // Check if order exists
            $order = $this->orderModel->findById($orderId);
            if (!$order) {
                throw new Exception('Không tìm thấy đơn hàng');
            }

            // Check if order can be cancelled
            if (in_array($order['status'], ['shipped', 'delivered', 'cancelled'])) {
                throw new Exception('Không thể hủy đơn hàng với trạng thái hiện tại');
            }

            // Cancel order
            $result = $this->orderModel->cancelOrder($orderId, $reason);

            if (!$result) {
                throw new Exception('Không thể hủy đơn hàng');
            }

            // Send cancellation notification to customer
            $this->sendOrderCancellationNotification($order, $reason);

            header('Location: /5s-fashion/admin/orders?success=' . urlencode('Hủy đơn hàng thành công'));
            exit;

        } catch (Exception $e) {
            header('Location: /5s-fashion/admin/orders?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    /**
     * Bulk actions
     */
    public function bulkAction()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: /5s-fashion/admin/orders');
                exit;
            }

            $action = $_POST['bulk_action'] ?? '';
            $orderIds = $_POST['order_ids'] ?? [];

            if (empty($action) || empty($orderIds)) {
                throw new Exception('Vui lòng chọn thao tác và đơn hàng');
            }

            $count = 0;
            foreach ($orderIds as $orderId) {
                $order = $this->orderModel->findById($orderId);
                if (!$order) continue;

                switch ($action) {
                    case 'mark_processing':
                        if ($order['status'] === 'pending') {
                            if ($this->orderModel->updateStatus($orderId, 'processing')) {
                                $count++;
                                $this->sendOrderStatusNotification($order, 'processing');
                            }
                        }
                        break;

                    case 'mark_shipped':
                        if (in_array($order['status'], ['pending', 'processing'])) {
                            if ($this->orderModel->updateStatus($orderId, 'shipped')) {
                                $count++;
                                $this->sendOrderStatusNotification($order, 'shipped');
                            }
                        }
                        break;

                    case 'mark_delivered':
                        if ($order['status'] === 'shipped') {
                            if ($this->orderModel->updateStatus($orderId, 'delivered')) {
                                $count++;
                                $this->sendOrderStatusNotification($order, 'delivered');
                            }
                        }
                        break;

                    case 'mark_paid':
                        if ($order['payment_status'] !== 'paid') {
                            if ($this->orderModel->updatePaymentStatus($orderId, 'paid')) {
                                $count++;
                            }
                        }
                        break;
                }
            }

            header('Location: /5s-fashion/admin/orders?success=' . urlencode("Đã thực hiện thao tác cho {$count} đơn hàng"));
            exit;

        } catch (Exception $e) {
            header('Location: /5s-fashion/admin/orders?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    /**
     * Export orders
     */
    public function export()
    {
        try {
            $format = $_GET['format'] ?? 'csv';
            $search = $_GET['search'] ?? '';
            $filters = [
                'status' => $_GET['status'] ?? '',
                'payment_status' => $_GET['payment_status'] ?? '',
                'date_from' => $_GET['date_from'] ?? '',
                'date_to' => $_GET['date_to'] ?? ''
            ];

            $orders = $this->orderModel->search($search, $filters);

            // Format data for export
            $exportData = [];
            foreach ($orders as $order) {
                $exportData[] = [
                    'Mã đơn hàng' => $order['order_code'],
                    'Khách hàng' => $order['customer_name'],
                    'Email' => $order['customer_email'],
                    'Điện thoại' => $order['customer_phone'],
                    'Tổng tiền' => number_format($order['total_amount']),
                    'Trạng thái' => $this->getStatusText($order['status']),
                    'Thanh toán' => $this->getPaymentStatusText($order['payment_status']),
                    'Phương thức TT' => $this->getPaymentMethodText($order['payment_method']),
                    'Ngày đặt' => $order['created_at'],
                    'Ghi chú' => $order['notes']
                ];
            }

            if ($format === 'csv') {
                $this->exportToCsv($exportData, 'orders_' . date('Y-m-d_H-i-s') . '.csv');
            } else {
                $this->exportToExcel($exportData, 'orders_' . date('Y-m-d_H-i-s') . '.xlsx');
            }

        } catch (Exception $e) {
            header('Location: /5s-fashion/admin/orders?error=' . urlencode('Lỗi khi xuất dữ liệu: ' . $e->getMessage()));
            exit;
        }
    }

    /**
     * Print order
     */
    public function print($id)
    {
        try {
            $order = $this->orderModel->getFullDetails($id);

            if (!$order) {
                throw new Exception('Không tìm thấy đơn hàng');
            }

            $data = [
                'title' => 'In đơn hàng #' . $order['order_code'],
                'order' => $order
            ];

            $this->render('admin/orders/print', $data, 'admin/layouts/print');

        } catch (Exception $e) {
            header('Location: /5s-fashion/admin/orders?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    /**
     * Get order statistics via AJAX
     */
    public function getStatistics()
    {
        try {
            $dateFrom = $_GET['date_from'] ?? null;
            $dateTo = $_GET['date_to'] ?? null;

            $stats = $this->orderModel->getStatistics($dateFrom, $dateTo);
            $revenue = $this->orderModel->getRevenueByDateRange($dateFrom, $dateTo);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => [
                    'stats' => $stats,
                    'revenue' => $revenue
                ]
            ]);

        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send order status notification email
     */
    private function sendOrderStatusNotification($order, $status)
    {
        // Implementation for sending email notification
        // This would integrate with your email service

        $statusMessages = [
            'processing' => 'Đơn hàng của bạn đang được xử lý',
            'shipped' => 'Đơn hàng của bạn đã được gửi đi',
            'delivered' => 'Đơn hàng của bạn đã được giao thành công'
        ];

        $subject = "Cập nhật đơn hàng #{$order['order_code']} - 5S Fashion";
        $message = $statusMessages[$status] ?? 'Trạng thái đơn hàng đã được cập nhật';

        // TODO: Implement actual email sending
        // mail($order['customer_email'], $subject, $message);
    }

    /**
     * Send order cancellation notification email
     */
    private function sendOrderCancellationNotification($order, $reason)
    {
        $subject = "Đơn hàng #{$order['order_code']} đã bị hủy - 5S Fashion";
        $message = "Đơn hàng của bạn đã bị hủy. Lý do: " . ($reason ?: 'Không có lý do cụ thể');

        // TODO: Implement actual email sending
        // mail($order['customer_email'], $subject, $message);
    }

    /**
     * Get status text
     */
    private function getStatusText($status)
    {
        $statuses = [
            'pending' => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'shipped' => 'Đã gửi',
            'delivered' => 'Đã giao',
            'cancelled' => 'Đã hủy'
        ];

        return $statuses[$status] ?? $status;
    }

    /**
     * Get payment status text
     */
    private function getPaymentStatusText($status)
    {
        $statuses = [
            'pending' => 'Chờ thanh toán',
            'paid' => 'Đã thanh toán',
            'failed' => 'Thanh toán thất bại',
            'refunded' => 'Đã hoàn tiền'
        ];

        return $statuses[$status] ?? $status;
    }

    /**
     * Get payment method text
     */
    private function getPaymentMethodText($method)
    {
        $methods = [
            'cod' => 'Thanh toán khi nhận hàng',
            'bank_transfer' => 'Chuyển khoản ngân hàng',
            'credit_card' => 'Thẻ tín dụng',
            'e_wallet' => 'Ví điện tử'
        ];

        return $methods[$method] ?? $method;
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
