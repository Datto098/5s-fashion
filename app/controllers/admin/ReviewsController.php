<?php

require_once dirname(__DIR__) . '/BaseController.php';
require_once __DIR__ . '/../../models/Review.php';

class ReviewsController extends BaseController
{
    private $reviewModel;

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
    }

    public function index()
    {
        try {
            // Get search and filter parameters
            $search = $_GET['search'] ?? '';
            $filters = [
                'status' => $_GET['status'] ?? '',
                'rating' => $_GET['rating'] ?? '',
                'product_id' => $_GET['product_id'] ?? '',
                'date_from' => $_GET['date_from'] ?? '',
                'date_to' => $_GET['date_to'] ?? '',
                'sort' => $_GET['sort'] ?? 'created_at',
                'order' => $_GET['order'] ?? 'DESC',
                'limit' => $_GET['limit'] ?? 20
            ];

            // Get reviews using your exact SQL query
            $reviews = $this->getReviewsWithDetails($search, $filters);

            // Get review statistics
            $stats = $this->reviewModel->getStatistics();

            $data = [
                'title' => 'Quản lý đánh giá - zone Fashion Admin',
                'reviews' => $reviews,
                'stats' => $stats,
                'search' => $search,
                'filters' => $filters,
                'breadcrumbs' => [
                    ['name' => 'Dashboard', 'url' => '/zone-fashion/admin'],
                    ['name' => 'Đánh giá', 'url' => '']
                ]
            ];

            $this->render('admin/reviews/index', $data, 'admin/layouts/main-inline');
        } catch (Exception $e) {
            error_log('Error in ReviewsController::index: ' . $e->getMessage());
            $this->render('admin/reviews/index', [
                'title' => 'Quản lý đánh giá - zone Fashion Admin',
                'reviews' => [],
                'stats' => [],
                'error' => 'Có lỗi xảy ra khi tải dữ liệu'
            ], 'admin/layouts/main-inline');
        }
    }

    public function show($id)
    {
        try {
            $review = $this->getReviewWithDetails($id);
            if (!$review) {
                header('Location: /zone-fashion/admin/reviews?error=' . urlencode('Không tìm thấy đánh giá'));
                exit;
            }

            $data = [
                'title' => 'Chi tiết đánh giá - zone Fashion Admin',
                'review' => $review,
                'breadcrumbs' => [
                    ['name' => 'Dashboard', 'url' => '/zone-fashion/admin'],
                    ['name' => 'Đánh giá', 'url' => '/zone-fashion/admin/reviews'],
                    ['name' => 'Chi tiết', 'url' => '']
                ]
            ];

            $this->render('admin/reviews/show', $data, 'admin/layouts/main-inline');
        } catch (Exception $e) {
            error_log('Error in ReviewsController::show: ' . $e->getMessage());
            header('Location: /zone-fashion/admin/reviews?error=' . urlencode('Có lỗi xảy ra'));
            exit;
        }
    }

    public function updateStatus()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $reviewId = $input['review_id'] ?? null;
            $status = $input['status'] ?? null;

            if (!$reviewId || !$status) {
                throw new Exception('Thiếu thông tin cần thiết');
            }

            $validStatuses = ['pending', 'approved', 'rejected'];
            if (!in_array($status, $validStatuses)) {
                throw new Exception('Trạng thái không hợp lệ');
            }

            $result = $this->reviewModel->updateStatus($reviewId, $status);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Cập nhật trạng thái thành công']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể cập nhật trạng thái']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function delete()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $reviewId = $input['review_id'] ?? null;

            if (!$reviewId) {
                throw new Exception('Thiếu ID đánh giá');
            }

            $result = $this->reviewModel->delete($reviewId);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Xóa đánh giá thành công']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể xóa đánh giá']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function bulkAction()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $action = $input['action'] ?? null;
            $reviewIds = $input['review_ids'] ?? [];

            if (!$action || empty($reviewIds)) {
                throw new Exception('Thiếu thông tin cần thiết');
            }

            $result = false;
            switch ($action) {
                case 'approve':
                    $result = $this->reviewModel->bulkUpdateStatus($reviewIds, 'approved');
                    break;
                case 'reject':
                    $result = $this->reviewModel->bulkUpdateStatus($reviewIds, 'rejected');
                    break;
                case 'delete':
                    $result = $this->reviewModel->bulkDelete($reviewIds);
                    break;
                default:
                    throw new Exception('Hành động không hợp lệ');
            }

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Thực hiện thành công']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể thực hiện hành động']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    private function getReviewsWithDetails($search = '', $filters = [])
    {
        try {
            // Use the exact SQL query you provided (updated with correct column name)
            $sql = "SELECT r.*,
                           p.name as product_name,
                           p.featured_image as product_image,
                           u.full_name as customer_name,
                           u.email as customer_email
                    FROM reviews r
                    LEFT JOIN products p ON r.product_id = p.id
                    LEFT JOIN users u ON r.user_id = u.id";

            $conditions = [];
            $params = [];

            // Apply search
            if (!empty($search)) {
                $conditions[] = "(p.name LIKE ? OR u.full_name LIKE ? OR r.content LIKE ? OR r.title LIKE ?)";
                $searchTerm = "%{$search}%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }

            // Apply filters
            if (!empty($filters['status'])) {
                $conditions[] = "r.status = ?";
                $params[] = $filters['status'];
            }

            if (!empty($filters['rating'])) {
                $conditions[] = "r.rating = ?";
                $params[] = $filters['rating'];
            }

            if (!empty($filters['product_id'])) {
                $conditions[] = "r.product_id = ?";
                $params[] = $filters['product_id'];
            }

            if (!empty($filters['date_from'])) {
                $conditions[] = "DATE(r.created_at) >= ?";
                $params[] = $filters['date_from'];
            }

            if (!empty($filters['date_to'])) {
                $conditions[] = "DATE(r.created_at) <= ?";
                $params[] = $filters['date_to'];
            }

            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(" AND ", $conditions);
            }

            // Add ORDER BY from your original query
            $orderBy = $filters['sort'] ?? 'created_at';
            $orderDirection = $filters['order'] ?? 'DESC';
            $sql .= " ORDER BY r.{$orderBy} {$orderDirection}";

            // Add LIMIT from your original query
            $limit = intval($filters['limit'] ?? 20);
            $sql .= " LIMIT {$limit}";

            return $this->reviewModel->executeQuery($sql, $params);
        } catch (Exception $e) {
            error_log('Error in getReviewsWithDetails: ' . $e->getMessage());
            return [];
        }
    }

    private function getReviewWithDetails($id)
    {
        try {
            $sql = "SELECT r.*,
                           p.name as product_name,
                           p.featured_image as product_image,
                           p.slug as product_slug,
                           p.price as product_price,
                           u.full_name as customer_name,
                           u.email as customer_email,
                           u.avatar as customer_avatar,
                           u.phone as customer_phone
                    FROM reviews r
                    LEFT JOIN products p ON r.product_id = p.id
                    LEFT JOIN users u ON r.user_id = u.id
                    WHERE r.id = ?";

            $result = $this->reviewModel->executeQuery($sql, [$id]);
            return $result[0] ?? null;
        } catch (Exception $e) {
            error_log('Error in getReviewWithDetails: ' . $e->getMessage());
            return null;
        }
    }
}
