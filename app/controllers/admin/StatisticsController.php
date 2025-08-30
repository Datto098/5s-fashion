<?php

require_once dirname(__DIR__) . '/BaseController.php';
require_once __DIR__ . '/../../models/Review.php';
require_once __DIR__ . '/../../models/User.php';

class StatisticsController extends BaseController
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
                'title' => 'Thống kê tổng quan - zone Fashion Admin',
                'overview' => $this->getOverviewStats(),
                'reviews' => $this->getReviewStats($period),
                'products' => $this->getProductStats($period),
                'customers' => $this->getCustomerStats($period),
                'orders' => $this->getOrderStats($period),
                'charts' => $this->getChartData($period),
                'period' => $period,
                'breadcrumbs' => [
                    ['name' => 'Dashboard', 'url' => '/zone-fashion/admin'],
                    ['name' => 'Thống kê', 'url' => '']
                ]
            ];

            $this->render('admin/statistics/index', $data, 'admin/layouts/main-inline');
        } catch (Exception $e) {
            error_log('Error in StatisticsController::index: ' . $e->getMessage());
            $this->render('admin/statistics/index', [
                'title' => 'Thống kê tổng quan - zone Fashion Admin',
                'error' => 'Có lỗi xảy ra khi tải dữ liệu thống kê'
            ], 'admin/layouts/main-inline');
        }
    }

    public function reviews()
    {
        try {
            $period = $_GET['period'] ?? '30';

            $data = [
                'title' => 'Thống kê đánh giá - zone Fashion Admin',
                'stats' => $this->getDetailedReviewStats($period),
                'charts' => $this->getReviewChartData($period),
                'topProducts' => $this->getTopRatedProducts(),
                'recentReviews' => $this->getRecentReviews(10),
                'period' => $period,
                'breadcrumbs' => [
                    ['name' => 'Dashboard', 'url' => '/zone-fashion/admin'],
                    ['name' => 'Thống kê', 'url' => '/zone-fashion/admin/statistics'],
                    ['name' => 'Đánh giá', 'url' => '']
                ]
            ];

            $this->render('admin/statistics/reviews', $data, 'admin/layouts/main-inline');
        } catch (Exception $e) {
            error_log('Error in StatisticsController::reviews: ' . $e->getMessage());
            $this->render('admin/statistics/reviews', [
                'title' => 'Thống kê đánh giá - zone Fashion Admin',
                'error' => 'Có lỗi xảy ra khi tải dữ liệu'
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
            $totalCustomers = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'customer'")->fetch()['count'];
            $totalReviews = $pdo->query("SELECT COUNT(*) as count FROM reviews")->fetch()['count'];
            $totalOrders = $pdo->query("SELECT COUNT(*) as count FROM orders")->fetch()['count'] ?? 0;

            // Today's stats
            $today = date('Y-m-d');
            $todayReviews = $pdo->query("SELECT COUNT(*) as count FROM reviews WHERE DATE(created_at) = '$today'")->fetch()['count'];
            $todayCustomers = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'customer' AND DATE(created_at) = '$today'")->fetch()['count'];

            // Average rating
            $avgRating = $pdo->query("SELECT AVG(rating) as avg FROM reviews WHERE status = 'approved'")->fetch()['avg'] ?? 0;

            return [
                'total_products' => $totalProducts,
                'total_customers' => $totalCustomers,
                'total_reviews' => $totalReviews,
                'total_orders' => $totalOrders,
                'today_reviews' => $todayReviews,
                'today_customers' => $todayCustomers,
                'average_rating' => round($avgRating, 1)
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

            return [
                'approved' => $approved,
                'pending' => $pending,
                'rejected' => $rejected,
                'total' => $approved + $pending + $rejected,
                'rating_distribution' => $ratingStats
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

            $dateCondition = "WHERE p.created_at >= DATE_SUB(NOW(), INTERVAL $period DAY)";

            $published = $pdo->query("SELECT COUNT(*) as count FROM products p $dateCondition AND p.status = 'published'")->fetch()['count'];
            $draft = $pdo->query("SELECT COUNT(*) as count FROM products p $dateCondition AND p.status = 'draft'")->fetch()['count'];
            $outOfStock = $pdo->query("SELECT COUNT(*) as count FROM products p $dateCondition AND p.status = 'out_of_stock'")->fetch()['count'];

            return [
                'published' => $published,
                'draft' => $draft,
                'out_of_stock' => $outOfStock,
                'total' => $published + $draft + $outOfStock
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

            return [
                'new_customers' => $newCustomers,
                'active_customers' => $activeCustomers
            ];
        } catch (Exception $e) {
            error_log('Error in getCustomerStats: ' . $e->getMessage());
            return [];
        }
    }

    private function getOrderStats($period)
    {
        // Placeholder for order stats - implement when orders table is available
        return [
            'total_orders' => 0,
            'completed_orders' => 0,
            'pending_orders' => 0,
            'total_revenue' => 0
        ];
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

            // Reviews by day
            $reviewsByDay = $pdo->query("
                SELECT DATE(created_at) as date, COUNT(*) as count
                FROM reviews
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL $period DAY)
                GROUP BY DATE(created_at)
                ORDER BY date ASC
            ")->fetchAll();

            // Customers by day
            $customersByDay = $pdo->query("
                SELECT DATE(created_at) as date, COUNT(*) as count
                FROM users
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL $period DAY) AND role = 'customer'
                GROUP BY DATE(created_at)
                ORDER BY date ASC
            ")->fetchAll();

            return [
                'reviews_by_day' => $reviewsByDay,
                'customers_by_day' => $customersByDay
            ];
        } catch (Exception $e) {
            error_log('Error in getChartData: ' . $e->getMessage());
            return [];
        }
    }

    private function getDetailedReviewStats($period)
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

            $dateCondition = "WHERE r.created_at >= DATE_SUB(NOW(), INTERVAL $period DAY)";

            // Review stats by product
            $productStats = $pdo->query("
                SELECT p.name, COUNT(*) as review_count, AVG(r.rating) as avg_rating
                FROM reviews r
                JOIN products p ON r.product_id = p.id
                $dateCondition
                GROUP BY r.product_id, p.name
                ORDER BY review_count DESC
                LIMIT 10
            ")->fetchAll();

            return [
                'product_stats' => $productStats
            ];
        } catch (Exception $e) {
            error_log('Error in getDetailedReviewStats: ' . $e->getMessage());
            return [];
        }
    }

    private function getReviewChartData($period)
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

            // Rating distribution over time
            $ratingOverTime = $pdo->query("
                SELECT DATE(created_at) as date, rating, COUNT(*) as count
                FROM reviews
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL $period DAY)
                GROUP BY DATE(created_at), rating
                ORDER BY date ASC, rating DESC
            ")->fetchAll();

            return [
                'rating_over_time' => $ratingOverTime
            ];
        } catch (Exception $e) {
            error_log('Error in getReviewChartData: ' . $e->getMessage());
            return [];
        }
    }

    private function getTopRatedProducts()
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

            return $pdo->query("
                SELECT p.name, p.featured_image, COUNT(r.id) as review_count, AVG(r.rating) as avg_rating
                FROM products p
                LEFT JOIN reviews r ON p.id = r.product_id AND r.status = 'approved'
                WHERE p.status = 'published'
                GROUP BY p.id, p.name, p.featured_image
                HAVING review_count > 0
                ORDER BY avg_rating DESC, review_count DESC
                LIMIT 10
            ")->fetchAll();
        } catch (Exception $e) {
            error_log('Error in getTopRatedProducts: ' . $e->getMessage());
            return [];
        }
    }

    private function getRecentReviews($limit = 10)
    {
        try {
            $sql = "SELECT r.*,
                           p.name as product_name,
                           p.featured_image as product_image,
                           u.full_name as customer_name
                    FROM reviews r
                    LEFT JOIN products p ON r.product_id = p.id
                    LEFT JOIN users u ON r.user_id = u.id
                    ORDER BY r.created_at DESC
                    LIMIT $limit";

            return $this->reviewModel->executeQuery($sql);
        } catch (Exception $e) {
            error_log('Error in getRecentReviews: ' . $e->getMessage());
            return [];
        }
    }
}
