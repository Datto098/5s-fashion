<?php

require_once __DIR__ . '/../../core/ApiController.php';
require_once __DIR__ . '/../../models/Review.php';
require_once __DIR__ . '/../../models/Order.php';

class ReviewApiController extends ApiController
{
    private $reviewModel;
    private $orderModel;

    public function __construct()
    {
        parent::__construct();
        $this->reviewModel = new Review();
        $this->orderModel = new Order();
    }

    /**
     * Get all reviews
     */
    public function index()
    {
        try {
            $page = (int) ($_GET['page'] ?? 1);
            $limit = (int) ($_GET['limit'] ?? 20);
            $productId = $_GET['product_id'] ?? null;

            if ($productId) {
                $reviews = $this->reviewModel->getProductReviews($productId, $limit);
            } else {
                // Get all reviews (you may need to implement this method in Review model)
                $reviews = $this->reviewModel->getProductReviews(null, $limit);
            }

            ApiResponse::success($reviews, 'Reviews retrieved successfully');
        } catch (Exception $e) {
            ApiResponse::error('Failed to retrieve reviews: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get reviews for a specific product
     */
    public function productReviews()
    {
        try {
            $productId = $this->getRouteParam('id');
            $limit = (int) ($_GET['limit'] ?? 20);
            $userId = $this->getCurrentUserId();

            $reviews = $this->reviewModel->getProductReviews($productId, $limit, $userId);

            ApiResponse::success($reviews, 'Product reviews retrieved successfully');
        } catch (Exception $e) {
            ApiResponse::error('Failed to retrieve product reviews: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Create a new review
     */
    public function store()
    {
        try {
            // Check if user is authenticated
            if (!$this->isAuthenticated()) {
                ApiResponse::unauthorized('Please login to submit a review');
                return;
            }

            $userId = $this->getCurrentUserId();
            $productId = $_POST['product_id'] ?? null;
            $rating = (int) ($_POST['rating'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');

            // Validate input
            if (!$productId || !$rating || !$title || !$content) {
                ApiResponse::error('Missing required fields: product_id, rating, title, content', 400);
                return;
            }

            if ($rating < 1 || $rating > 5) {
                ApiResponse::error('Rating must be between 1 and 5', 400);
                return;
            }

            // Check if user has purchased and received the product
            $hasCompletedOrders = $this->orderModel->hasUserPurchasedProduct(
                $userId,
                $productId,
                ['delivered', 'completed']
            );

            if (!$hasCompletedOrders) {
                ApiResponse::forbidden('You must purchase and receive the product before reviewing');
                return;
            }

            // Check if user has already reviewed this product
            $hasReviewed = $this->reviewModel->hasUserReviewedProduct($userId, $productId);
            if ($hasReviewed) {
                ApiResponse::error('You have already reviewed this product', 409);
                return;
            }

            // Create review
            $reviewData = [
                'product_id' => $productId,
                'user_id' => $userId,
                'rating' => $rating,
                'title' => $title,
                'content' => $content,
                'status' => 'approved' // Auto-approve for now
            ];

            $reviewId = $this->reviewModel->create($reviewData);

            if ($reviewId) {
                // Get the created review with user info
                $review = $this->reviewModel->find($reviewId);
                ApiResponse::success($review, 'Review submitted successfully');
            } else {
                ApiResponse::error('Failed to create review', 500);
            }

        } catch (Exception $e) {
            error_log("Review creation error: " . $e->getMessage());
            ApiResponse::error('Failed to submit review: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update a review
     */
    public function update()
    {
        try {
            // Check if user is authenticated
            if (!$this->isAuthenticated()) {
                ApiResponse::unauthorized('Please login to update review');
                return;
            }

            $reviewId = $this->getRouteParam('id');
            $userId = $this->getCurrentUserId();

            // Check if review exists and belongs to user
            $review = $this->reviewModel->find($reviewId);
            if (!$review) {
                ApiResponse::notFound('Review not found');
                return;
            }

            if ($review['user_id'] != $userId) {
                ApiResponse::forbidden('You can only update your own reviews');
                return;
            }

            $rating = (int) ($_POST['rating'] ?? $review['rating']);
            $title = trim($_POST['title'] ?? $review['title']);
            $content = trim($_POST['content'] ?? $review['content']);

            // Validate input
            if ($rating < 1 || $rating > 5) {
                ApiResponse::error('Rating must be between 1 and 5', 400);
                return;
            }

            // Update review
            $updateData = [
                'rating' => $rating,
                'title' => $title,
                'content' => $content
            ];

            $success = $this->reviewModel->update($reviewId, $updateData);

            if ($success) {
                $updatedReview = $this->reviewModel->find($reviewId);
                ApiResponse::success($updatedReview, 'Review updated successfully');
            } else {
                ApiResponse::error('Failed to update review', 500);
            }

        } catch (Exception $e) {
            ApiResponse::error('Failed to update review: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Delete a review
     */
    public function destroy()
    {
        try {
            // Check if user is authenticated
            if (!$this->isAuthenticated()) {
                ApiResponse::unauthorized('Please login to delete review');
                return;
            }

            $reviewId = $this->getRouteParam('id');
            $userId = $this->getCurrentUserId();

            // Check if review exists and belongs to user
            $review = $this->reviewModel->find($reviewId);
            if (!$review) {
                ApiResponse::notFound('Review not found');
                return;
            }

            if ($review['user_id'] != $userId) {
                ApiResponse::forbidden('You can only delete your own reviews');
                return;
            }

            $success = $this->reviewModel->delete($reviewId);

            if ($success) {
                ApiResponse::success(null, 'Review deleted successfully');
            } else {
                ApiResponse::error('Failed to delete review', 500);
            }

        } catch (Exception $e) {
            ApiResponse::error('Failed to delete review: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Check if current user is authenticated
     */
    private function isAuthenticated()
    {
        return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
    }

    /**
     * Get current user ID
     */
    private function getCurrentUserId()
    {
        return $_SESSION['user']['id'] ?? null;
    }

    /**
     * Get route parameter
     */
    private function getRouteParam($key)
    {
        // This should be implemented based on your router
        // For now, get from URL path
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $segments = explode('/', trim($path, '/'));

        if ($key === 'id') {
            // Find the segment after 'products' or 'reviews'
            $index = array_search('products', $segments);
            if ($index !== false && isset($segments[$index + 1])) {
                return $segments[$index + 1];
            }

            $index = array_search('reviews', $segments);
            if ($index !== false && isset($segments[$index + 1])) {
                return $segments[$index + 1];
            }
        }

        return null;
    }
}
