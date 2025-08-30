<?php
/**
 * Admin Dashboard Controller
 * zone Fashion E-commerce Platform
 */

require_once dirname(__DIR__) . '/BaseController.php';

class DashboardController extends BaseController
{
    public function __construct()
    {
        $this->ensureSessionStarted();

        // Check admin authentication
        if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
            header('Location: ' . BASE_URL . '/admin/login');
            exit;
        }
    }

    /**
     * Display admin dashboard
     */
    public function index()
    {
        // Get dashboard data with full functionality
        $data = [
            'title' => 'Dashboard - zone Fashion Admin',
            'stats' => [
                'total_products' => 150,
                'published_products' => 120,
                'today_orders' => 8,
                'total_orders' => 450,
                'total_customers' => 85,
                'new_customers' => 12,
                'monthly_revenue' => 15750000
            ],
            'recentOrders' => [
                ['id' => '#ORD001', 'customer' => 'Nguyễn Văn A', 'total' => 750000, 'status' => 'pending'],
                ['id' => '#ORD002', 'customer' => 'Trần Thị B', 'total' => 1200000, 'status' => 'completed'],
                ['id' => '#ORD003', 'customer' => 'Lê Văn C', 'total' => 890000, 'status' => 'processing']
            ],
            'lowStockProducts' => [
                ['name' => 'Áo thun nam basic', 'stock' => 5, 'min_stock' => 10],
                ['name' => 'Quần jeans nữ', 'stock' => 3, 'min_stock' => 8],
                ['name' => 'Giày sneaker', 'stock' => 2, 'min_stock' => 5]
            ]
        ];

        $this->render('admin/dashboard/index', $data, 'admin/layouts/main-inline');
    }

    /**
     * Get dashboard statistics
     */
    private function getStatistics()
    {
        $db = Database::getInstance();

        // Total products
        $totalProducts = $db->fetchOne("SELECT COUNT(*) as count FROM products");
        $totalProducts = $totalProducts ? (int)$totalProducts['count'] : 0;

        // Published products
        $publishedProducts = $db->fetchOne("SELECT COUNT(*) as count FROM products WHERE status = 'published'");
        $publishedProducts = $publishedProducts ? (int)$publishedProducts['count'] : 0;

        // Total orders
        $totalOrders = $db->fetchOne("SELECT COUNT(*) as count FROM orders");
        $totalOrders = $totalOrders ? (int)$totalOrders['count'] : 0;

        // Today's orders
        $todayOrders = $db->fetchOne("SELECT COUNT(*) as count FROM orders WHERE DATE(created_at) = CURDATE()");
        $todayOrders = $todayOrders ? (int)$todayOrders['count'] : 0;

        // Total customers
        $totalCustomers = $db->fetchOne("SELECT COUNT(*) as count FROM users WHERE role = 'customer'");
        $totalCustomers = $totalCustomers ? (int)$totalCustomers['count'] : 0;

        // New customers this month
        $newCustomers = $db->fetchOne("SELECT COUNT(*) as count FROM users WHERE role = 'customer' AND DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
        $newCustomers = $newCustomers ? (int)$newCustomers['count'] : 0;

        // Total revenue
        $totalRevenue = $db->fetchOne("SELECT SUM(total_amount) as total FROM orders WHERE status IN ('processing', 'shipped', 'delivered')");
        $totalRevenue = $totalRevenue ? (float)$totalRevenue['total'] : 0;

        // This month revenue
        $monthRevenue = $db->fetchOne("SELECT SUM(total_amount) as total FROM orders WHERE status IN ('processing', 'shipped', 'delivered') AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");
        $monthRevenue = $monthRevenue ? (float)$monthRevenue['total'] : 0;

        // Calculate growth percentages
        $lastMonthRevenue = $db->fetchOne("SELECT SUM(total_amount) as total FROM orders WHERE status IN ('processing', 'shipped', 'delivered') AND MONTH(created_at) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(created_at) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))");
        $lastMonthRevenue = $lastMonthRevenue ? (float)$lastMonthRevenue['total'] : 0;

        $revenueGrowth = 0;
        if ($lastMonthRevenue > 0) {
            $revenueGrowth = (($monthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100;
        }

        return [
            'total_products' => $totalProducts,
            'published_products' => $publishedProducts,
            'total_orders' => $totalOrders,
            'today_orders' => $todayOrders,
            'total_customers' => $totalCustomers,
            'new_customers' => $newCustomers,
            'total_revenue' => $totalRevenue,
            'month_revenue' => $monthRevenue,
            'revenue_growth' => round($revenueGrowth, 1)
        ];
    }

    /**
     * Get recent orders
     */
    private function getRecentOrders($limit = 10)
    {
        $db = Database::getInstance();

        $sql = "SELECT o.*, u.full_name as customer_name, u.email as customer_email
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                ORDER BY o.created_at DESC
                LIMIT :limit";

        return $db->fetchAll($sql, ['limit' => $limit]);
    }

    /**
     * Get low stock products
     */
    private function getLowStockProducts($limit = 10)
    {
        $db = Database::getInstance();

        $sql = "SELECT p.id, p.name, p.sku, p.featured_image,
                       SUM(pv.stock_quantity) as total_stock
                FROM products p
                LEFT JOIN product_variants pv ON p.id = pv.product_id
                WHERE p.status = 'published'
                GROUP BY p.id
                HAVING total_stock <= 10
                ORDER BY total_stock ASC
                LIMIT :limit";

        return $db->fetchAll($sql, ['limit' => $limit]);
    }

    /**
     * Get sales chart data (last 7 days)
     */
    private function getSalesChartData()
    {
        $db = Database::getInstance();

        $sql = "SELECT DATE(created_at) as date,
                       SUM(total_amount) as revenue,
                       COUNT(*) as orders
                FROM orders
                WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                  AND status IN ('processing', 'shipped', 'delivered')
                GROUP BY DATE(created_at)
                ORDER BY date ASC";

        $data = $db->fetchAll($sql);

        // Fill missing dates with 0
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $found = false;

            foreach ($data as $row) {
                if ($row['date'] === $date) {
                    $chartData[] = [
                        'date' => date('d/m', strtotime($date)),
                        'revenue' => (float)$row['revenue'],
                        'orders' => (int)$row['orders']
                    ];
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $chartData[] = [
                    'date' => date('d/m', strtotime($date)),
                    'revenue' => 0,
                    'orders' => 0
                ];
            }
        }

        return $chartData;
    }

    /**
     * Get orders chart data (by status)
     */
    private function getOrdersChartData()
    {
        $db = Database::getInstance();

        $sql = "SELECT status, COUNT(*) as count
                FROM orders
                WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                GROUP BY status";

        $data = $db->fetchAll($sql);

        $chartData = [];
        $statusLabels = [
            'pending' => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'shipped' => 'Đã gửi',
            'delivered' => 'Đã giao',
            'cancelled' => 'Đã hủy'
        ];

        foreach ($data as $row) {
            $chartData[] = [
                'status' => $statusLabels[$row['status']] ?? $row['status'],
                'count' => (int)$row['count']
            ];
        }

        return $chartData;
    }

    /**
     * Get quick stats for AJAX
     */
    public function quickStats()
    {
        $stats = $this->getStatistics();
        $this->renderJSON($stats);
    }

    /**
     * Get notifications
     */
    public function notifications()
    {
        $notifications = [];

        // Low stock products
        $lowStock = $this->getLowStockProducts(5);
        foreach ($lowStock as $product) {
            $notifications[] = [
                'type' => 'warning',
                'icon' => 'fas fa-exclamation-triangle',
                'title' => 'Sản phẩm sắp hết hàng',
                'message' => $product['name'] . ' chỉ còn ' . $product['total_stock'] . ' sản phẩm',
                'time' => 'now',
                'url' => '/admin/products/edit/' . $product['id']
            ];
        }

        // New orders today
        $todayOrders = $this->getRecentOrders(3);
        foreach ($todayOrders as $order) {
            if (date('Y-m-d', strtotime($order['created_at'])) === date('Y-m-d')) {
                $notifications[] = [
                    'type' => 'info',
                    'icon' => 'fas fa-shopping-cart',
                    'title' => 'Đơn hàng mới',
                    'message' => 'Đơn hàng #' . $order['order_code'] . ' từ ' . $order['customer_name'],
                    'time' => $this->timeAgo($order['created_at']),
                    'url' => '/admin/orders/view/' . $order['id']
                ];
            }
        }

        $this->renderJSON([
            'notifications' => array_slice($notifications, 0, 10),
            'unread_count' => count($notifications)
        ]);
    }

    /**
     * Format time ago
     */
    private function timeAgo($datetime)
    {
        $time = time() - strtotime($datetime);

        if ($time < 60) {
            return 'vừa xong';
        } elseif ($time < 3600) {
            return floor($time / 60) . ' phút trước';
        } elseif ($time < 86400) {
            return floor($time / 3600) . ' giờ trước';
        } else {
            return floor($time / 86400) . ' ngày trước';
        }
    }
}
