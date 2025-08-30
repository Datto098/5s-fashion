<?php

require_once dirname(__DIR__) . '/BaseController.php';
require_once __DIR__ . '/../../models/Order.php';
require_once __DIR__ . '/../../models/Customer.php';

class OrdersController extends BaseController
{
    private $orderModel;
    private $customerModel;

    public function __construct()
    {
        // Start session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check admin authentication
        if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
            error_log("Admin not authenticated for updatePaymentStatus");
            header('Location: /zone-fashion/admin/login');
            exit;
        }

        $this->orderModel = new Order();
        $this->customerModel = new Customer();

        // Handle hyphenated URLs
        $this->handleSpecialRoutes();
    }

    private function handleSpecialRoutes()
    {
        $uri = $_SERVER['REQUEST_URI'];

        // Handle /admin/orders/update-status/{id}
        if (preg_match('/\/admin\/orders\/update-status\/(\d+)/', $uri, $matches)) {
            $orderId = $matches[1];
            $this->updateStatus();
            exit;
        }

        // Handle /admin/orders/update-payment-status/{id}
        if (preg_match('/\/admin\/orders\/update-payment-status\/(\d+)/', $uri, $matches)) {
            error_log("Matched update-payment-status route with order ID: " . $matches[1]);
            $orderId = $matches[1];
            $_GET['order_id'] = $orderId; // Pass order ID via $_GET for updatePaymentStatus method
            $this->updatePaymentStatus();
            exit;
        }
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
                'title' => 'Quản lý đơn hàng - zone Fashion Admin',
                'orders' => $orders,
                'stats' => $stats,
                'needsAttention' => $needsAttention,
                'search' => $search,
                'filters' => $filters,
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'url' => '/zone-fashion/admin'],
                    ['title' => 'Đơn hàng']
                ]
            ];

            $this->render('admin/orders/index', $data, 'admin/layouts/main-inline');

        } catch (Exception $e) {
            $data = [
                'title' => 'Quản lý đơn hàng - zone Fashion Admin',
                'error' => 'Lỗi khi tải danh sách đơn hàng: ' . $e->getMessage(),
                'orders' => [],
                'stats' => [],
                'needsAttention' => [],
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'url' => '/zone-fashion/admin'],
                    ['title' => 'Đơn hàng']
                ]
            ];

            $this->render('admin/orders/index', $data, 'admin/layouts/main-inline');
        }
    }

    public function pending()
    {
        try {
            // Get pending orders only
            $filters = [
                'status' => 'pending',
                'sort' => 'created_at',
                'order' => 'ASC' // Oldest first for processing priority
            ];

            $pendingOrders = $this->orderModel->search('', $filters);

            // Get pending statistics
            $stats = $this->orderModel->getPendingStatistics();

            $data = [
                'title' => 'Đơn hàng chờ xử lý - zone Fashion Admin',
                'orders' => $pendingOrders,
                'stats' => $stats,
                'breadcrumbs' => [
                    ['name' => 'Dashboard', 'url' => '/zone-fashion/admin'],
                    ['name' => 'Đơn hàng chờ xử lý', 'url' => '']
                ]
            ];

            $this->render('admin/orders/pending', $data, 'admin/layouts/main-inline');
        } catch (Exception $e) {
            error_log('Error in OrdersController::pending: ' . $e->getMessage());
            header('Location: /zone-fashion/admin/orders?error=' . urlencode('Có lỗi xảy ra'));
            exit;
        }
    }

    // Signature kept compatible with BaseController::view($view, $data = [], $layout = 'client/layouts/app')
    // We support calling this method with a numeric first argument (order id) for existing routes.
    public function view($view, $data = [], $layout = 'admin/layouts/main-inline')
    {
        // If $view is numeric, treat it as the order ID (backwards-compatible route calling)
        if (is_numeric($view)) {
            $id = (int)$view;
        } else {
            // If first arg isn't numeric, try to extract order id from provided data
            $id = is_array($data) && isset($data['id']) ? (int)$data['id'] : null;
        }

        try {
            if (!$id) {
                throw new Exception('Thiếu ID đơn hàng');
            }

            // Get order details
            $order = $this->orderModel->getFullDetails($id);

            if (!$order) {
                throw new Exception('Không tìm thấy đơn hàng');
            }

            $viewData = [
                'title' => 'Chi tiết đơn hàng #' . $order['order_code'] . ' - zone Fashion Admin',
                'order' => $order,
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'url' => '/zone-fashion/admin'],
                    ['title' => 'Đơn hàng', 'url' => '/zone-fashion/admin/orders'],
                    ['title' => 'Chi tiết #' . $order['order_code']]
                ]
            ];

            // Merge any additional $data passed through
            if (is_array($data)) {
                $viewData = array_merge($viewData, $data);
            }

            $this->render('admin/orders/view', $viewData, $layout);

        } catch (Exception $e) {
            header('Location: /zone-fashion/admin/orders?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function updateStatus()
    {
        try {
            // Allow both GET and POST for admin interface
            if (!in_array($_SERVER['REQUEST_METHOD'], ['POST', 'GET'])) {
                header('Location: /zone-fashion/admin/orders');
                exit;
            }

            // Extract order ID from URL
            $orderId = null;
            if (preg_match('/\/admin\/orders\/update-status\/(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
                $orderId = $matches[1];
            }

            // Fallback to GET/POST data
            if (!$orderId) {
                $orderId = $_POST['order_id'] ?? $_GET['order_id'] ?? null;
            }

            // For GET requests, show update form
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                if (!$orderId) {
                    header('Location: /zone-fashion/admin/orders');
                    exit;
                }

                $order = $this->orderModel->find($orderId);
                if (!$order) {
                    header('Location: /zone-fashion/admin/orders');
                    exit;
                }

                // Show form to update status
                $data = [
                    'title' => 'Cập nhật trạng thái đơn hàng',
                    'order' => $order,
                    // Admin allowed statuses (match admin UI)
                    'validStatuses' => ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'confirmed']
                ];

                require VIEW_PATH . '/admin/orders/update-status.php';
                return;
            }

            // Handle JSON and form data for POST requests
            $jsonInput = json_decode(file_get_contents('php://input'), true);
            $status = null;
            $adminNotes = null;
            $isAjax = false;

            if ($jsonInput && is_array($jsonInput)) {
                // JSON request
                $status = trim($jsonInput['status'] ?? '');
                $adminNotes = $jsonInput['admin_notes'] ?? 'Cập nhật từ admin interface';
                $isAjax = true;
            } else {
                // Form data
                $status = trim($_POST['status'] ?? '');
                $adminNotes = $_POST['admin_notes'] ?? null;
                $isAjax = !empty($_POST['ajax']);
            }

            if (!$orderId || !$status) {
                throw new Exception('Thiếu thông tin cần thiết');
            }

            // Validate status
            // Accept statuses used in the admin UI
            $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'confirmed'];
            if (!in_array($status, $validStatuses)) {
                throw new Exception('Trạng thái không hợp lệ');
            }

            // Check if order exists
            $order = $this->orderModel->find($orderId);
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
                // Skip email for now to avoid errors
                // $this->sendOrderStatusNotification($order, $status);
            }

            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Cập nhật trạng thái đơn hàng thành công'
                ]);
                exit; // Make sure we don't continue execution
            } else {
                header('Location: /zone-fashion/admin/orders/show/' . $orderId . '?success=' . urlencode('Cập nhật trạng thái đơn hàng thành công'));
                exit;
            }

        } catch (Exception $e) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
                exit;
            } else {
                // Use orderId if available, otherwise redirect to orders list
                $redirectUrl = ($orderId)
                    ? "/zone-fashion/admin/orders/show/$orderId?error=" . urlencode($e->getMessage())
                    : "/zone-fashion/admin/orders?error=" . urlencode($e->getMessage());
                header('Location: ' . $redirectUrl);
                exit;
            }
        }
    }

    public function updatePaymentStatus()
    {
        error_log("updatePaymentStatus called");
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                error_log("Not a POST request");
                header('Location: /zone-fashion/admin/orders');
                exit;
            }

            // Handle both JSON and form data
            $jsonInput = json_decode(file_get_contents('php://input'), true);
            error_log("JSON Input: " . print_r($jsonInput, true));
            $orderId = null;
            $paymentStatus = null;
            $ajaxFlag = null;

            // Check if it's JSON request
            if ($jsonInput && is_array($jsonInput)) {
                $paymentStatus = trim($jsonInput['payment_status'] ?? '');
                $ajaxFlag = '1'; // JSON requests are always AJAX

                // Get order ID from $_GET (set by handleSpecialRoutes)
                $orderId = $_GET['order_id'] ?? null;
            } else {
                // Form data
                $orderId = $_POST['order_id'] ?? null;
                $paymentStatus = $_POST['payment_status'] ?? null;
                $ajaxFlag = $_POST['ajax'] ?? null;
            }

            if (!$orderId || !$paymentStatus) {
                error_log("Missing data - Order ID: $orderId, Payment Status: $paymentStatus");
                throw new Exception('Thiếu thông tin cần thiết');
            }

            error_log("Processing update - Order ID: $orderId, Payment Status: $paymentStatus");

            // Check if order exists
            $order = $this->orderModel->find($orderId);
            if (!$order) {
                throw new Exception('Không tìm thấy đơn hàng');
            }

            // Update payment status
            $result = $this->orderModel->updatePaymentStatus($orderId, $paymentStatus);

            if (!$result) {
                throw new Exception('Không thể cập nhật trạng thái thanh toán');
            }

            if ($ajaxFlag) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Cập nhật trạng thái thanh toán thành công'
                ]);
            } else {
                header('Location: /zone-fashion/admin/orders?success=' . urlencode('Cập nhật trạng thái thanh toán thành công'));
            }

        } catch (Exception $e) {
            if ($ajaxFlag) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            } else {
                header('Location: /zone-fashion/admin/orders?error=' . urlencode($e->getMessage()));
            }
        }
    }

    public function cancel()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: /zone-fashion/admin/orders');
                exit;
            }

            $orderId = $_POST['order_id'] ?? null;
            $reason = $_POST['reason'] ?? null;

            if (!$orderId) {
                throw new Exception('Thiếu ID đơn hàng');
            }

            // Check if order exists
            $order = $this->orderModel->find($orderId);
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

            header('Location: /zone-fashion/admin/orders?success=' . urlencode('Hủy đơn hàng thành công'));
            exit;

        } catch (Exception $e) {
            header('Location: /zone-fashion/admin/orders?error=' . urlencode($e->getMessage()));
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
                header('Location: /zone-fashion/admin/orders');
                exit;
            }

            $action = $_POST['bulk_action'] ?? '';
            $orderIds = $_POST['order_ids'] ?? [];

            if (empty($action) || empty($orderIds)) {
                throw new Exception('Vui lòng chọn thao tác và đơn hàng');
            }

            $count = 0;
            foreach ($orderIds as $orderId) {
                $order = $this->orderModel->find($orderId);
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

            header('Location: /zone-fashion/admin/orders?success=' . urlencode("Đã thực hiện thao tác cho {$count} đơn hàng"));
            exit;

        } catch (Exception $e) {
            header('Location: /zone-fashion/admin/orders?error=' . urlencode($e->getMessage()));
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
                // Excel export not implemented yet
                header('Location: /zone-fashion/admin/orders?error=' . urlencode('Excel export not implemented'));
                exit;
            }

        } catch (Exception $e) {
            header('Location: /zone-fashion/admin/orders?error=' . urlencode('Lỗi khi xuất dữ liệu: ' . $e->getMessage()));
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
            header('Location: /zone-fashion/admin/orders?error=' . urlencode($e->getMessage()));
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
                'message' => $e->getMessage()
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

        $subject = "Cập nhật đơn hàng #{$order['order_code']} - zone Fashion";
        $message = $statusMessages[$status] ?? 'Trạng thái đơn hàng đã được cập nhật';

        // TODO: Implement actual email sending
        // mail($order['customer_email'], $subject, $message);
    }

    /**
     * Send order cancellation notification email
     */
    private function sendOrderCancellationNotification($order, $reason)
    {
        $subject = "Đơn hàng #{$order['order_code']} đã bị hủy - zone Fashion";
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

    public function create()
    {
        try {
            // Get customers for dropdown
            $customers = $this->customerModel->all();

            // Get available products
            $db = Database::getInstance();
            $products = $db->fetchAll("SELECT id, name, price, stock_quantity FROM products WHERE status = 'active' ORDER BY name");

            $this->render('admin/orders/create', [
                'customers' => $customers,
                'products' => $products
            ]);
        } catch (Exception $e) {
            error_log('Error in OrdersController::create: ' . $e->getMessage());
            header('Location: /zone-fashion/admin/orders?error=' . urlencode('Có lỗi xảy ra khi tải trang tạo đơn hàng'));
            exit;
        }
    }

    public function show($id)
    {
        try {
            // Get order details - use getFullDetails instead of findById
            $order = $this->orderModel->getFullDetails($id);
            if (!$order) {
                header('Location: /zone-fashion/admin/orders?error=' . urlencode('Không tìm thấy đơn hàng'));
                exit;
            }

            // Get order items
            $db = Database::getInstance();
            $orderItems = $db->fetchAll("
                SELECT oi.*, p.name as product_name, p.featured_image as product_image, p.slug as product_slug
                FROM order_items oi
                LEFT JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?
                ORDER BY oi.id
            ", [$id]);

            // Get customer details
            $customer = null;
            if (isset($order['user_id']) && $order['user_id']) {
                $customer = $this->customerModel->find($order['user_id']);
            }

            // Get order history/logs
            $orderLogs = [];
            try {
                $orderLogs = $db->fetchAll("
                    SELECT ol.*, u.full_name as created_by_name
                    FROM order_logs ol
                    LEFT JOIN users u ON ol.created_by = u.id
                    WHERE ol.order_id = ?
                    ORDER BY ol.created_at DESC
                ", [$id]);
            } catch (Exception $e) {
                error_log("Order logs error: " . $e->getMessage());
                $orderLogs = [];
            }

            $this->render('admin/orders/show', [
                'order' => $order,
                'orderItems' => $orderItems,
                'customer' => $customer,
                'orderLogs' => $orderLogs
            ], 'admin/layouts/main-inline');
        } catch (Exception $e) {
            error_log('Error in OrdersController::show: ' . $e->getMessage());
            header('Location: /zone-fashion/admin/orders?error=' . urlencode('Có lỗi xảy ra khi tải chi tiết đơn hàng'));
            exit;
        }
    }

    public function edit($id)
    {
        try {
            // Get order details
            $order = $this->orderModel->find($id);
            if (!$order) {
                header('Location: /zone-fashion/admin/orders?error=' . urlencode('Không tìm thấy đơn hàng'));
                exit;
            }

            // Get order items
            $db = Database::getInstance();
            $orderItems = $db->fetchAll("
                SELECT oi.*, p.name as product_name, p.price as product_price, p.stock_quantity
                FROM order_items oi
                LEFT JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?
                ORDER BY oi.id
            ", [$id]);

            // Get customers for dropdown
            $customers = $this->customerModel->all();

            // Get available products
            $products = $db->fetchAll("SELECT id, name, price, stock_quantity FROM products WHERE status = 'active' ORDER BY name");

            // Get customer details
            $customer = null;
            if (isset($order['user_id']) && $order['user_id']) {
                $customer = $this->customerModel->find($order['user_id']);
            }

            $this->render('admin/orders/edit', [
                'order' => $order,
                'orderItems' => $orderItems,
                'customer' => $customer,
                'customers' => $customers,
                'products' => $products
            ]);
        } catch (Exception $e) {
            error_log('Error in OrdersController::edit: ' . $e->getMessage());
            header('Location: /zone-fashion/admin/orders?error=' . urlencode('Có lỗi xảy ra khi tải trang chỉnh sửa đơn hàng'));
            exit;
        }
    }
}
