<?php

require_once dirname(__DIR__) . '/BaseController.php';
require_once __DIR__ . '/../../models/Review.php';
require_once __DIR__ . '/../../models/User.php';

class AnalyticsController extends BaseController
{
    private $reviewModel;
    private $userModel;

    public function __construct()
    {
        // Start session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check admin authentication
        if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
            header('Location: /zone-fashion/admin/login');
            exit;
        }

        $this->reviewModel = new Review();
        $this->userModel = new User();
    }

    public function index()
    {
        try {
            // Get time period filter
            $period = $_GET['period'] ?? '30'; // Default 30 days

            $data = [
                'title' => 'Analytics Dashboard - zone Fashion Admin',
                'overview' => $this->getOverviewStats(),
                'reviews' => $this->getReviewStats($period),
                'products' => $this->getProductStats($period),
                'customers' => $this->getCustomerStats($period),
                'sales' => $this->getSalesStats($period),
                'charts' => $this->getChartData($period),
                'trending' => $this->getTrendingData($period),
                'period' => $period,
                'breadcrumbs' => [
                    ['name' => 'Dashboard', 'url' => '/zone-fashion/admin'],
                    ['name' => 'Analytics', 'url' => '']
                ]
            ];

            $this->render('admin/analytics/index', $data, 'admin/layouts/main-inline');
        } catch (Exception $e) {
            error_log('Error in AnalyticsController::index: ' . $e->getMessage());
            $this->render('admin/analytics/index', [
                'title' => 'Analytics Dashboard - zone Fashion Admin',
                'error' => 'Có lỗi xảy ra khi tải dữ liệu analytics'
            ], 'admin/layouts/main-inline');
        }
    }

    public function reports()
    {
        try {
            $period = $_GET['period'] ?? '30';
            $type = $_GET['type'] ?? 'overview';

            $data = [
                'title' => 'Báo cáo chi tiết - zone Fashion Admin',
                'type' => $type,
                'period' => $period,
                'data' => $this->getReportData($type, $period),
                'breadcrumbs' => [
                    ['name' => 'Dashboard', 'url' => '/zone-fashion/admin'],
                    ['name' => 'Analytics', 'url' => '/zone-fashion/admin/analytics'],
                    ['name' => 'Reports', 'url' => '']
                ]
            ];

            $this->render('admin/analytics/reports', $data, 'admin/layouts/main-inline');
        } catch (Exception $e) {
            error_log('Error in AnalyticsController::reports: ' . $e->getMessage());
            $this->render('admin/analytics/reports', [
                'title' => 'Báo cáo chi tiết - zone Fashion Admin',
                'error' => 'Có lỗi xảy ra khi tải báo cáo'
            ], 'admin/layouts/main-inline');
        }
    }

    private function getOverviewStats()
    {
        try {
            $config = require __DIR__ . '/../../config/database.php';
            $db = $config['connections']['mysql'];
            $pdo = new PDO(
                "mysql:host={$db['host']};dbname={$db['database']};charset=utf8mb4",
                $db['username'],
                $db['password'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            // Total counts
            $totalProducts = $pdo->query("SELECT COUNT(*) as count FROM products WHERE status = 'published'")->fetch()['count'];
            $totalCustomers = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'customer' AND status = 'active'")->fetch()['count'];
            $totalReviews = $pdo->query("SELECT COUNT(*) as count FROM reviews WHERE status = 'approved'")->fetch()['count'];

            // Growth rates (vs previous period)
            $yesterday = date('Y-m-d', strtotime('-1 day'));
            $weekAgo = date('Y-m-d', strtotime('-7 days'));

            $todayReviews = $pdo->query("SELECT COUNT(*) as count FROM reviews WHERE DATE(created_at) = CURDATE()")->fetch()['count'];
            $yesterdayReviews = $pdo->query("SELECT COUNT(*) as count FROM reviews WHERE DATE(created_at) = '$yesterday'")->fetch()['count'];

            $thisWeekCustomers = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'customer' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)")->fetch()['count'];
            $lastWeekCustomers = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'customer' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 14 DAY) AND created_at < DATE_SUB(CURDATE(), INTERVAL 7 DAY)")->fetch()['count'];

            // Average rating
            $avgRating = $pdo->query("SELECT AVG(rating) as avg FROM reviews WHERE status = 'approved'")->fetch()['avg'] ?? 0;

            // Sales data
            $totalRevenue = $pdo->query("SELECT SUM(total_amount) as total FROM orders WHERE payment_status = 'paid'")->fetch()['total'] ?? 0;
            $todayRevenue = $pdo->query("SELECT SUM(total_amount) as total FROM orders WHERE DATE(created_at) = CURDATE() AND payment_status = 'paid'")->fetch()['total'] ?? 0;
            $yesterdayRevenue = $pdo->query("SELECT SUM(total_amount) as total FROM orders WHERE DATE(created_at) = '$yesterday' AND payment_status = 'paid'")->fetch()['total'] ?? 0;

            // Calculate growth rates
            $reviewGrowth = $yesterdayReviews > 0 ? (($todayReviews - $yesterdayReviews) / $yesterdayReviews) * 100 : 0;
            $customerGrowth = $lastWeekCustomers > 0 ? (($thisWeekCustomers - $lastWeekCustomers) / $lastWeekCustomers) * 100 : 0;
            $revenueGrowth = $yesterdayRevenue > 0 ? (($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100 : 0;

            return [
                'total_products' => $totalProducts,
                'total_customers' => $totalCustomers,
                'total_reviews' => $totalReviews,
                'total_revenue' => $totalRevenue,
                'average_rating' => round($avgRating, 1),
                'today_reviews' => $todayReviews,
                'today_revenue' => $todayRevenue,
                'review_growth' => round($reviewGrowth, 1),
                'customer_growth' => round($customerGrowth, 1),
                'revenue_growth' => round($revenueGrowth, 1)
            ];
        } catch (Exception $e) {
            error_log('Error in getOverviewStats: ' . $e->getMessage());
            return [];
        }
    }

    private function getReviewStats($period)
    {
        try {
            $config = require __DIR__ . '/../../config/database.php';
            $db = $config['connections']['mysql'];
            $pdo = new PDO(
                "mysql:host={$db['host']};dbname={$db['database']};charset=utf8mb4",
                $db['username'],
                $db['password'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            $dateCondition = "WHERE created_at >= DATE_SUB(NOW(), INTERVAL $period DAY)";

            // Review status counts
            $approved = $pdo->query("SELECT COUNT(*) as count FROM reviews $dateCondition AND status = 'approved'")->fetch()['count'];
            $pending = $pdo->query("SELECT COUNT(*) as count FROM reviews $dateCondition AND status = 'pending'")->fetch()['count'];
            $rejected = $pdo->query("SELECT COUNT(*) as count FROM reviews $dateCondition AND status = 'rejected'")->fetch()['count'];

            // Rating distribution
            $ratingStats = $pdo->query("
                SELECT rating, COUNT(*) as count
                FROM reviews
                $dateCondition
                GROUP BY rating
                ORDER BY rating DESC
            ")->fetchAll();

            // Review sentiment analysis (based on rating)
            $positive = $pdo->query("SELECT COUNT(*) as count FROM reviews $dateCondition AND rating >= 4")->fetch()['count'];
            $neutral = $pdo->query("SELECT COUNT(*) as count FROM reviews $dateCondition AND rating = 3")->fetch()['count'];
            $negative = $pdo->query("SELECT COUNT(*) as count FROM reviews $dateCondition AND rating <= 2")->fetch()['count'];

            return [
                'approved' => $approved,
                'pending' => $pending,
                'rejected' => $rejected,
                'total' => $approved + $pending + $rejected,
                'rating_distribution' => $ratingStats,
                'sentiment' => [
                    'positive' => $positive,
                    'neutral' => $neutral,
                    'negative' => $negative
                ]
            ];
        } catch (Exception $e) {
            error_log('Error in getReviewStats: ' . $e->getMessage());
            return [];
        }
    }

    private function getProductStats($period)
    {
        try {
            $config = require __DIR__ . '/../../config/database.php';
            $db = $config['connections']['mysql'];
            $pdo = new PDO(
                "mysql:host={$db['host']};dbname={$db['database']};charset=utf8mb4",
                $db['username'],
                $db['password'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            // Most reviewed products
            $topReviewed = $pdo->query("
                SELECT p.name, p.featured_image, COUNT(r.id) as review_count, AVG(r.rating) as avg_rating
                FROM products p
                LEFT JOIN reviews r ON p.id = r.product_id AND r.status = 'approved'
                WHERE p.status = 'published'
                GROUP BY p.id
                HAVING review_count > 0
                ORDER BY review_count DESC
                LIMIT 5
            ")->fetchAll();

            // Product views (placeholder - would need actual tracking)
            $mostViewed = $pdo->query("
                SELECT p.name, p.featured_image, p.views as view_count
                FROM products p
                WHERE p.status = 'published'
                ORDER BY p.views DESC
                LIMIT 5
            ")->fetchAll();

            return [
                'top_reviewed' => $topReviewed,
                'most_viewed' => $mostViewed
            ];
        } catch (Exception $e) {
            error_log('Error in getProductStats: ' . $e->getMessage());
            return [];
        }
    }

    private function getCustomerStats($period)
    {
        try {
            $config = require __DIR__ . '/../../config/database.php';
            $db = $config['connections']['mysql'];
            $pdo = new PDO(
                "mysql:host={$db['host']};dbname={$db['database']};charset=utf8mb4",
                $db['username'],
                $db['password'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            $dateCondition = "WHERE created_at >= DATE_SUB(NOW(), INTERVAL $period DAY)";

            $newCustomers = $pdo->query("SELECT COUNT(*) as count FROM users $dateCondition AND role = 'customer'")->fetch()['count'];
            $activeCustomers = $pdo->query("SELECT COUNT(*) as count FROM users $dateCondition AND role = 'customer' AND status = 'active'")->fetch()['count'];

            // Top reviewers
            $topReviewers = $pdo->query("
                SELECT u.full_name, u.email, COUNT(r.id) as review_count, AVG(r.rating) as avg_rating
                FROM users u
                JOIN reviews r ON u.id = r.user_id
                WHERE u.role = 'customer' AND r.status = 'approved'
                GROUP BY u.id
                ORDER BY review_count DESC
                LIMIT 5
            ")->fetchAll();

            return [
                'new_customers' => $newCustomers,
                'active_customers' => $activeCustomers,
                'top_reviewers' => $topReviewers
            ];
        } catch (Exception $e) {
            error_log('Error in getCustomerStats: ' . $e->getMessage());
            return [];
        }
    }

    private function getSalesStats($period)
    {
        try {
            $config = require __DIR__ . '/../../config/database.php';
            $db = $config['connections']['mysql'];
            $pdo = new PDO(
                "mysql:host={$db['host']};dbname={$db['database']};charset=utf8mb4",
                $db['username'],
                $db['password'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            $dateCondition = "WHERE created_at >= DATE_SUB(NOW(), INTERVAL $period DAY)";

            // Total orders and revenue
            $totalOrders = $pdo->query("SELECT COUNT(*) as count FROM orders $dateCondition")->fetch()['count'];
            $totalRevenue = $pdo->query("SELECT SUM(total_amount) as total FROM orders $dateCondition AND payment_status = 'paid'")->fetch()['total'] ?? 0;
            $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

            // Order status distribution
            $completedOrders = $pdo->query("SELECT COUNT(*) as count FROM orders $dateCondition AND status = 'delivered'")->fetch()['count'];
            $pendingOrders = $pdo->query("SELECT COUNT(*) as count FROM orders $dateCondition AND status IN ('pending', 'processing')")->fetch()['count'];
            $cancelledOrders = $pdo->query("SELECT COUNT(*) as count FROM orders $dateCondition AND status = 'cancelled'")->fetch()['count'];

            // Best selling products
            $bestSellers = $pdo->query("
                SELECT p.name, p.featured_image, SUM(oi.quantity) as total_sold, SUM(oi.total) as revenue
                FROM orders o
                JOIN order_items oi ON o.id = oi.order_id
                JOIN products p ON oi.product_id = p.id
                WHERE o.created_at >= DATE_SUB(NOW(), INTERVAL $period DAY)
                AND o.payment_status = 'paid'
                GROUP BY p.id, p.name, p.featured_image
                ORDER BY total_sold DESC
                LIMIT 10
            ")->fetchAll();

            // Revenue by day
            $revenueByDay = $pdo->query("
                SELECT DATE(created_at) as date, SUM(total_amount) as revenue, COUNT(*) as orders
                FROM orders
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL $period DAY)
                AND payment_status = 'paid'
                GROUP BY DATE(created_at)
                ORDER BY date ASC
            ")->fetchAll();

            // Growth comparison
            $previousPeriodRevenue = $pdo->query("
                SELECT SUM(total_amount) as total
                FROM orders
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL " . ($period * 2) . " DAY)
                AND created_at < DATE_SUB(NOW(), INTERVAL $period DAY)
                AND payment_status = 'paid'
            ")->fetch()['total'] ?? 0;

            $revenueGrowth = $previousPeriodRevenue > 0 ? (($totalRevenue - $previousPeriodRevenue) / $previousPeriodRevenue) * 100 : 0;

            return [
                'total_orders' => $totalOrders,
                'total_revenue' => $totalRevenue,
                'avg_order_value' => $avgOrderValue,
                'completed_orders' => $completedOrders,
                'pending_orders' => $pendingOrders,
                'cancelled_orders' => $cancelledOrders,
                'best_sellers' => $bestSellers,
                'revenue_by_day' => $revenueByDay,
                'revenue_growth' => round($revenueGrowth, 1),
                'conversion_rate' => $this->calculateConversionRate($period)
            ];
        } catch (Exception $e) {
            error_log('Error in getSalesStats: ' . $e->getMessage());
            return [
                'total_orders' => 0,
                'total_revenue' => 0,
                'avg_order_value' => 0,
                'conversion_rate' => 0
            ];
        }
    }

    private function getChartData($period)
    {
        try {
            $config = require __DIR__ . '/../../config/database.php';
            $db = $config['connections']['mysql'];
            $pdo = new PDO(
                "mysql:host={$db['host']};dbname={$db['database']};charset=utf8mb4",
                $db['username'],
                $db['password'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            // Reviews trend
            $reviewsTrend = $pdo->query("
                SELECT DATE(created_at) as date, COUNT(*) as count
                FROM reviews
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL $period DAY)
                GROUP BY DATE(created_at)
                ORDER BY date ASC
            ")->fetchAll();

            // Customers trend
            $customersTrend = $pdo->query("
                SELECT DATE(created_at) as date, COUNT(*) as count
                FROM users
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL $period DAY) AND role = 'customer'
                GROUP BY DATE(created_at)
                ORDER BY date ASC
            ")->fetchAll();

            // Rating trends
            $ratingTrend = $pdo->query("
                SELECT DATE(created_at) as date, AVG(rating) as avg_rating
                FROM reviews
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL $period DAY) AND status = 'approved'
                GROUP BY DATE(created_at)
                ORDER BY date ASC
            ")->fetchAll();

            return [
                'reviews_trend' => $reviewsTrend,
                'customers_trend' => $customersTrend,
                'rating_trend' => $ratingTrend
            ];
        } catch (Exception $e) {
            error_log('Error in getChartData: ' . $e->getMessage());
            return [];
        }
    }

    private function getTrendingData($period)
    {
        try {
            $config = require __DIR__ . '/../../config/database.php';
            $db = $config['connections']['mysql'];
            $pdo = new PDO(
                "mysql:host={$db['host']};dbname={$db['database']};charset=utf8mb4",
                $db['username'],
                $db['password'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            // Trending products (based on recent reviews)
            $trendingProducts = $pdo->query("
                SELECT p.name, p.featured_image, COUNT(r.id) as recent_reviews, AVG(r.rating) as avg_rating
                FROM products p
                JOIN reviews r ON p.id = r.product_id
                WHERE r.created_at >= DATE_SUB(NOW(), INTERVAL $period DAY) AND r.status = 'approved'
                GROUP BY p.id
                ORDER BY recent_reviews DESC, avg_rating DESC
                LIMIT 10
            ")->fetchAll();

            // Recent activities
            $recentActivities = $pdo->query("
                SELECT 'review' as type, r.created_at, u.full_name as user_name, p.name as product_name, r.rating
                FROM reviews r
                JOIN users u ON r.user_id = u.id
                JOIN products p ON r.product_id = p.id
                WHERE r.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                ORDER BY r.created_at DESC
                LIMIT 10
            ")->fetchAll();

            return [
                'trending_products' => $trendingProducts,
                'recent_activities' => $recentActivities
            ];
        } catch (Exception $e) {
            error_log('Error in getTrendingData: ' . $e->getMessage());
            return [];
        }
    }

    private function getReportData($type, $period)
    {
        switch ($type) {
            case 'reviews':
                return $this->getDetailedReviewReport($period);
            case 'products':
                return $this->getDetailedProductReport($period);
            case 'customers':
                return $this->getDetailedCustomerReport($period);
            default:
                return $this->getOverviewReport($period);
        }
    }

    private function getDetailedReviewReport($period)
    {
        // Implementation for detailed review report
        return [];
    }

    private function getDetailedProductReport($period)
    {
        // Implementation for detailed product report
        return [];
    }

    private function getDetailedCustomerReport($period)
    {
        // Implementation for detailed customer report
        return [];
    }

    private function getOverviewReport($period)
    {
        // Implementation for overview report
        return [];
    }

    private function calculateConversionRate($period)
    {
        try {
            $config = require __DIR__ . '/../../config/database.php';
            $db = $config['connections']['mysql'];
            $pdo = new PDO(
                "mysql:host={$db['host']};dbname={$db['database']};charset=utf8mb4",
                $db['username'],
                $db['password'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            $dateCondition = "WHERE created_at >= DATE_SUB(NOW(), INTERVAL $period DAY)";

            // Get total visitors (using views as proxy)
            $totalViews = $pdo->query("SELECT SUM(views) as total FROM products")->fetch()['total'] ?? 1;

            // Get total orders
            $totalOrders = $pdo->query("SELECT COUNT(*) as count FROM orders $dateCondition AND payment_status = 'paid'")->fetch()['count'];

            // Calculate conversion rate (orders / visits * 100)
            $conversionRate = ($totalViews > 0) ? ($totalOrders / $totalViews) * 100 : 0;

            return round($conversionRate, 2);
        } catch (Exception $e) {
            error_log('Error in calculateConversionRate: ' . $e->getMessage());
            return 0;
        }
    }
}
