<?php
/**
 * Order API Controller
 * Handle order tracking and status updates
 * 5S Fashion E-commerce Platform
 */

require_once dirname(dirname(__DIR__)) . '/core/ApiController.php';
require_once dirname(dirname(__DIR__)) . '/models/Order.php';

class OrderTrackingController extends ApiController
{
    private $orderModel;

    public function __construct()
    {
        parent::__construct();
        $this->orderModel = new Order();
    }

    /**
     * Search order by code
     * GET /api/orders/search?code=ORDER_CODE
     */
    public function search()
    {
        header('Content-Type: application/json');

        try {
            $orderCode = $_GET['code'] ?? '';

            if (empty($orderCode)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Vui lòng nhập mã đơn hàng'
                ]);
                return;
            }

            $order = $this->orderModel->findByOrderCode($orderCode);

            if (!$order) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Không tìm thấy đơn hàng với mã này'
                ]);
                return;
            }

            // Get order details with items
            $orderDetails = $this->orderModel->getFullDetails($order['id']);

            // Parse addresses
            if ($orderDetails['shipping_address']) {
                $orderDetails['shipping_address'] = json_decode($orderDetails['shipping_address'], true);
            }

            if ($orderDetails['billing_address']) {
                $orderDetails['billing_address'] = json_decode($orderDetails['billing_address'], true);
            }

            echo json_encode([
                'success' => true,
                'order' => $orderDetails,
                'message' => 'Tìm thấy đơn hàng'
            ]);

        } catch (Exception $e) {
            error_log('Order search error: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tìm kiếm đơn hàng'
            ]);
        }
    }

    /**
     * Get order status timeline
     * GET /api/orders/{id}/timeline
     */
    public function timeline($id)
    {
        header('Content-Type: application/json');

        try {
            $order = $this->orderModel->find($id);

            if (!$order) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Không tìm thấy đơn hàng'
                ]);
                return;
            }

            // Get order logs
            $db = Database::getInstance();
            $sql = "SELECT ol.*, u.full_name as changed_by_name
                    FROM order_logs ol
                    LEFT JOIN users u ON ol.created_by = u.id
                    WHERE ol.order_id = ?
                    ORDER BY ol.created_at ASC";

            $logs = $db->fetchAll($sql, [$id]);

            // Build timeline
            $timeline = $this->buildOrderTimeline($order['status'], $logs);

            echo json_encode([
                'success' => true,
                'timeline' => $timeline,
                'current_status' => $order['status']
            ]);

        } catch (Exception $e) {
            error_log('Order timeline error: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy thông tin đơn hàng'
            ]);
        }
    }

    /**
     * Cancel order (for customer)
     * POST /api/orders/{id}/cancel
     */
    public function cancel($id)
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
            exit;
        }

        try {
            // Check if user is logged in
            if (!isLoggedIn()) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
                exit;
            }

            $user = getUser();
            $order = $this->orderModel->find($id);

            if (!$order) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Không tìm thấy đơn hàng'
                ]);
                exit;
            }

            // Check if user owns this order
            if ($order['user_id'] != $user['id']) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Bạn không có quyền hủy đơn hàng này'
                ]);
                exit;
            }

            // Check if order can be cancelled
            if (!in_array($order['status'], ['pending', 'processing'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Không thể hủy đơn hàng ở trạng thái hiện tại'
                ]);
                exit;
            }

            // Only allow cancellation for COD orders
            if ($order['payment_method'] !== 'cod') {
                echo json_encode([
                    'success' => false,
                    'message' => 'Chỉ có thể hủy đơn hàng thanh toán khi nhận hàng (COD)'
                ]);
                exit;
            }

            // Get reason from request
            $input = json_decode(file_get_contents('php://input'), true);
            $reason = $input['reason'] ?? 'Khách hàng yêu cầu hủy';

            // Cancel order
            $result = $this->orderModel->cancelOrder($id, $reason);

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Hủy đơn hàng thành công'
                ]);
                exit;
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Không thể hủy đơn hàng'
                ]);
                exit;
            }

        } catch (Exception $e) {
            error_log('Order cancel error: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi hủy đơn hàng'
            ]);
            exit;
        }
    }

    /**
     * Get shipping info
     * GET /api/orders/{id}/shipping
     */
    public function shipping($id)
    {
        header('Content-Type: application/json');

        try {
            $order = $this->orderModel->find($id);

            if (!$order) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Không tìm thấy đơn hàng'
                ]);
                return;
            }

            // Mock shipping info - in real system this would integrate with shipping API
            $shippingInfo = [
                'tracking_code' => 'SHP' . str_pad($id, 6, '0', STR_PAD_LEFT),
                'carrier' => 'Giao Hàng Nhanh',
                'status' => $this->getShippingStatus($order['status']),
                'estimated_delivery' => $this->getEstimatedDelivery($order['created_at']),
                'tracking_url' => 'https://ghn.vn/tracking/' . 'SHP' . str_pad($id, 6, '0', STR_PAD_LEFT),
                'events' => $this->getShippingEvents($order)
            ];

            echo json_encode([
                'success' => true,
                'shipping' => $shippingInfo
            ]);

        } catch (Exception $e) {
            error_log('Shipping info error: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy thông tin vận chuyển'
            ]);
        }
    }

    /**
     * Build order timeline based on status and logs
     */
    private function buildOrderTimeline($currentStatus, $logs)
    {
        $statuses = [
            'pending' => [
                'name' => 'Chờ xử lý',
                'description' => 'Đơn hàng đã được tạo và đang chờ xử lý',
                'icon' => 'fas fa-clock',
                'color' => 'warning'
            ],
            'processing' => [
                'name' => 'Đang xử lý',
                'description' => 'Đơn hàng đang được chuẩn bị và đóng gói',
                'icon' => 'fas fa-cogs',
                'color' => 'info'
            ],
            'shipped' => [
                'name' => 'Đã gửi hàng',
                'description' => 'Đơn hàng đã được gửi đi và đang trên đường giao',
                'icon' => 'fas fa-shipping-fast',
                'color' => 'primary'
            ],
            'delivered' => [
                'name' => 'Đã giao hàng',
                'description' => 'Đơn hàng đã được giao thành công',
                'icon' => 'fas fa-check-circle',
                'color' => 'success'
            ],
            'cancelled' => [
                'name' => 'Đã hủy',
                'description' => 'Đơn hàng đã được hủy',
                'icon' => 'fas fa-times-circle',
                'color' => 'danger'
            ]
        ];

        $timeline = [];
        $statusOrder = ['pending', 'processing', 'shipped', 'delivered'];

        foreach ($statusOrder as $status) {
            $statusInfo = $statuses[$status];
            $isCompleted = false;
            $timestamp = null;

            // Check if this status has been reached
            if ($status === 'pending') {
                $isCompleted = true;
                // Find order creation time from logs or use current order created_at
                $timestamp = date('Y-m-d H:i:s'); // This should come from order created_at
            } else {
                // Check logs for this status
                foreach ($logs as $log) {
                    if ($log['status_to'] === $status) {
                        $isCompleted = true;
                        $timestamp = $log['created_at'];
                        break;
                    }
                }
            }

            $timeline[] = [
                'status' => $status,
                'name' => $statusInfo['name'],
                'description' => $statusInfo['description'],
                'icon' => $statusInfo['icon'],
                'color' => $statusInfo['color'],
                'completed' => $isCompleted,
                'timestamp' => $timestamp,
                'is_current' => $status === $currentStatus
            ];

            // Stop if we hit cancelled status
            if ($currentStatus === 'cancelled') {
                $timeline[] = [
                    'status' => 'cancelled',
                    'name' => $statuses['cancelled']['name'],
                    'description' => $statuses['cancelled']['description'],
                    'icon' => $statuses['cancelled']['icon'],
                    'color' => $statuses['cancelled']['color'],
                    'completed' => true,
                    'timestamp' => $timestamp,
                    'is_current' => true
                ];
                break;
            }
        }

        return $timeline;
    }

    /**
     * Get shipping status based on order status
     */
    private function getShippingStatus($orderStatus)
    {
        $statusMap = [
            'pending' => 'Chờ lấy hàng',
            'processing' => 'Đang chuẩn bị',
            'shipped' => 'Đang vận chuyển',
            'delivered' => 'Đã giao hàng',
            'cancelled' => 'Đã hủy'
        ];

        return $statusMap[$orderStatus] ?? 'Không xác định';
    }

    /**
     * Get estimated delivery date
     */
    private function getEstimatedDelivery($orderDate)
    {
        $deliveryDate = new DateTime($orderDate);
        $deliveryDate->add(new DateInterval('P3D')); // Add 3 days
        return $deliveryDate->format('Y-m-d H:i:s');
    }

    /**
     * Get shipping events
     */
    private function getShippingEvents($order)
    {
        $events = [];

        // Mock shipping events based on order status
        $events[] = [
            'time' => $order['created_at'],
            'status' => 'Đơn hàng đã được tạo',
            'location' => '5S Fashion Store'
        ];

        if (in_array($order['status'], ['processing', 'shipped', 'delivered'])) {
            $events[] = [
                'time' => date('Y-m-d H:i:s', strtotime($order['created_at'] . ' +2 hours')),
                'status' => 'Đang chuẩn bị hàng',
                'location' => '5S Fashion Warehouse'
            ];
        }

        if (in_array($order['status'], ['shipped', 'delivered'])) {
            $events[] = [
                'time' => date('Y-m-d H:i:s', strtotime($order['created_at'] . ' +1 day')),
                'status' => 'Hàng đã được gửi đi',
                'location' => 'Trung tâm phân loại TP.HCM'
            ];
        }

        if ($order['status'] === 'delivered') {
            $events[] = [
                'time' => $order['delivered_at'] ?? date('Y-m-d H:i:s'),
                'status' => 'Đã giao hàng thành công',
                'location' => 'Địa chỉ khách hàng'
            ];
        }

        return array_reverse($events); // Latest first
    }
}
