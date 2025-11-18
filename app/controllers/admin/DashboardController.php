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
        try {
            // Get real statistics from database
            $stats = $this->getStatistics();
            $recentOrders = $this->getRecentOrders(5);
            $lowStockProducts = $this->getLowStockProducts(5);
            $salesChartData = $this->getSalesChartData();
            $ordersChartData = $this->getOrdersChartData();

            // Get dashboard data with real functionality
            $data = [
                'title' => 'Dashboard - zone Fashion Admin',
                'stats' => [
                    'total_products' => $stats['total_products'],
                    'published_products' => $stats['published_products'],
                    'today_orders' => $stats['today_orders'],
                    'total_orders' => $stats['total_orders'],
                    'total_customers' => $stats['total_customers'],
                    'new_customers' => $stats['new_customers'],
                    'monthly_revenue' => $stats['month_revenue'],
                    'revenue_growth' => $stats['revenue_growth']
                ],
                'recentOrders' => $recentOrders,
                'lowStockProducts' => $lowStockProducts,
                'salesChartData' => $salesChartData,
                'ordersChartData' => $ordersChartData
            ];

            $this->render('admin/dashboard/index', $data, 'admin/layouts/main-inline');
        } catch (Exception $e) {
            error_log('Dashboard error: ' . $e->getMessage());
            
            // Fallback to basic data if database fails
            $data = [
                'title' => 'Dashboard - zone Fashion Admin',
                'stats' => [
                    'total_products' => 0,
                    'published_products' => 0,
                    'today_orders' => 0,
                    'total_orders' => 0,
                    'total_customers' => 0,
                    'new_customers' => 0,
                    'monthly_revenue' => 0,
                    'revenue_growth' => 0
                ],
                'recentOrders' => [],
                'lowStockProducts' => [],
                'salesChartData' => [],
                'ordersChartData' => [],
                'error' => 'Không thể tải dữ liệu thống kê'
            ];
            
            $this->render('admin/dashboard/index', $data, 'admin/layouts/main-inline');
        }
    }

    /**
     * Get dashboard statistics
     */
    private function getStatistics()
    {
        try {
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

            // Total customers (active users only)
            $totalCustomers = $db->fetchOne("SELECT COUNT(*) as count FROM users WHERE role = 'customer' AND status = 'active'");
            $totalCustomers = $totalCustomers ? (int)$totalCustomers['count'] : 0;

            // New customers this month
            $newCustomers = $db->fetchOne("SELECT COUNT(*) as count FROM users WHERE role = 'customer' AND status = 'active' AND DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
            $newCustomers = $newCustomers ? (int)$newCustomers['count'] : 0;

            // Total revenue (only completed orders)
            $totalRevenue = $db->fetchOne("SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE payment_status = 'paid' AND status IN ('processing', 'shipped', 'delivered')");
            $totalRevenue = $totalRevenue ? (float)$totalRevenue['total'] : 0;

            // This month revenue
            $monthRevenue = $db->fetchOne("SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE payment_status = 'paid' AND status IN ('processing', 'shipped', 'delivered') AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");
            $monthRevenue = $monthRevenue ? (float)$monthRevenue['total'] : 0;

            // Last month revenue for growth calculation
            $lastMonthRevenue = $db->fetchOne("SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE payment_status = 'paid' AND status IN ('processing', 'shipped', 'delivered') AND MONTH(created_at) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(created_at) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))");
            $lastMonthRevenue = $lastMonthRevenue ? (float)$lastMonthRevenue['total'] : 0;

            // Calculate growth percentages
            $revenueGrowth = 0;
            if ($lastMonthRevenue > 0) {
                $revenueGrowth = (($monthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100;
            } elseif ($monthRevenue > 0) {
                $revenueGrowth = 100; // First month with revenue
            }

            // Customer growth
            $lastMonthCustomers = $db->fetchOne("SELECT COUNT(*) as count FROM users WHERE role = 'customer' AND status = 'active' AND DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 60 DAY) AND DATE(created_at) < DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
            $lastMonthCustomers = $lastMonthCustomers ? (int)$lastMonthCustomers['count'] : 0;
            
            $customerGrowth = 0;
            if ($lastMonthCustomers > 0) {
                $customerGrowth = (($newCustomers - $lastMonthCustomers) / $lastMonthCustomers) * 100;
            } elseif ($newCustomers > 0) {
                $customerGrowth = 100;
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
                'revenue_growth' => round($revenueGrowth, 1),
                'customer_growth' => round($customerGrowth, 1),
                'last_updated' => date('Y-m-d H:i:s')
            ];
            
        } catch (Exception $e) {
            error_log('Error getting dashboard statistics: ' . $e->getMessage());
            
            // Return default values if database error
            return [
                'total_products' => 0,
                'published_products' => 0,
                'total_orders' => 0,
                'today_orders' => 0,
                'total_customers' => 0,
                'new_customers' => 0,
                'total_revenue' => 0,
                'month_revenue' => 0,
                'revenue_growth' => 0,
                'customer_growth' => 0,
                'last_updated' => date('Y-m-d H:i:s')
            ];
        }
    }

    /**
     * Get recent orders
     */
    private function getRecentOrders($limit = 10)
    {
        try {
            $db = Database::getInstance();

            $sql = "SELECT o.*, 
                           COALESCE(u.full_name, 'Khách vãng lai') as customer_name, 
                           u.email as customer_email,
                           o.order_code,
                           o.total_amount,
                           o.status,
                           o.payment_status,
                           o.created_at
                    FROM orders o
                    LEFT JOIN users u ON o.user_id = u.id
                    ORDER BY o.created_at DESC
                    LIMIT :limit";

            $orders = $db->fetchAll($sql, ['limit' => $limit]);
            
            // Format orders for display
            $formattedOrders = [];
            foreach ($orders as $order) {
                $formattedOrders[] = [
                    'id' => $order['id'],
                    'order_code' => $order['order_code'] ?? '#ORD' . str_pad($order['id'], 4, '0', STR_PAD_LEFT),
                    'customer' => $order['customer_name'],
                    'customer_email' => $order['customer_email'],
                    'total' => (float)$order['total_amount'],
                    'total_formatted' => number_format($order['total_amount']) . 'đ',
                    'status' => $order['status'],
                    'payment_status' => $order['payment_status'] ?? 'pending',
                    'created_at' => $order['created_at'],
                    'time_ago' => $this->timeAgo($order['created_at'])
                ];
            }
            
            return $formattedOrders;
            
        } catch (Exception $e) {
            error_log('Error getting recent orders: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get low stock products
     */
    private function getLowStockProducts($limit = 10)
    {
        try {
            $db = Database::getInstance();

            $sql = "SELECT p.id, 
                           p.name, 
                           p.sku, 
                           p.featured_image,
                           p.price,
                           COALESCE(SUM(pv.stock_quantity), 0) as total_stock,
                           CASE 
                               WHEN COALESCE(SUM(pv.stock_quantity), 0) = 0 THEN 'out_of_stock'
                               WHEN COALESCE(SUM(pv.stock_quantity), 0) <= 10 THEN 'low_stock'
                               ELSE 'normal'
                           END as stock_status
                    FROM products p
                    LEFT JOIN product_variants pv ON p.id = pv.product_id
                    WHERE p.status = 'published'
                    GROUP BY p.id
                    HAVING total_stock <= 10
                    ORDER BY total_stock ASC, p.name ASC
                    LIMIT :limit";

            $products = $db->fetchAll($sql, ['limit' => $limit]);
            
            // Format products for display
            $formattedProducts = [];
            foreach ($products as $product) {
                $formattedProducts[] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'sku' => $product['sku'],
                    'featured_image' => $product['featured_image'],
                    'price' => (float)$product['price'],
                    'price_formatted' => number_format($product['price']) . 'đ',
                    'stock' => (int)$product['total_stock'],
                    'min_stock' => 10, // Default minimum stock level
                    'stock_status' => $product['stock_status'],
                    'stock_percentage' => $product['total_stock'] > 0 ? 
                        round(($product['total_stock'] / 10) * 100, 1) : 0
                ];
            }
            
            return $formattedProducts;
            
        } catch (Exception $e) {
            error_log('Error getting low stock products: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get sales chart data (last 7 days)
     */
    private function getSalesChartData()
    {
        try {
            $db = Database::getInstance();

            $sql = "SELECT DATE(created_at) as date,
                           COALESCE(SUM(total_amount), 0) as revenue,
                           COUNT(*) as orders
                    FROM orders
                    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                      AND payment_status = 'paid'
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
                            'full_date' => $date,
                            'revenue' => (float)$row['revenue'],
                            'revenue_formatted' => number_format($row['revenue']) . 'đ',
                            'orders' => (int)$row['orders']
                        ];
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $chartData[] = [
                        'date' => date('d/m', strtotime($date)),
                        'full_date' => $date,
                        'revenue' => 0,
                        'revenue_formatted' => '0đ',
                        'orders' => 0
                    ];
                }
            }

            return $chartData;
            
        } catch (Exception $e) {
            error_log('Error getting sales chart data: ' . $e->getMessage());
            return [];
        }
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
