 
<?php

class Review extends BaseModel
{
    protected $table = 'reviews';
    protected $primaryKey = 'id';
    protected $fillable = [
        'product_id', 'user_id', 'rating', 'title', 'content', 'status'
    ];

    /**
     * Get total number of reviews for a product
     * @param int $productId
     * @return int
     */
    public function getProductReviewCount($productId)
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE product_id = ?";
        $result = $this->db->fetchOne($sql, [$productId]);
        return (int)($result['count'] ?? 0);
    }
       /**
     * Get average rating and per-star breakdown for a product
     * @param int $productId
     * @return array ['average' => float, 'counts' => [1=>int,2=>int,3=>int,4=>int,5=>int]]
     */
    public function getProductRatingStats($productId)
    {
        $sql = "SELECT rating, COUNT(*) as count FROM {$this->table} WHERE product_id = ? GROUP BY rating";
        $rows = $this->db->fetchAll($sql, [$productId]);
        $counts = [1=>0,2=>0,3=>0,4=>0,5=>0];
        $total = 0;
        $sum = 0;
        foreach ($rows as $row) {
            $r = (int)$row['rating'];
            if ($r >= 1 && $r <= 5) {
                $counts[$r] = (int)$row['count'];
                $total += (int)$row['count'];
                $sum += $r * (int)$row['count'];
            }
        }
        $average = $total > 0 ? round($sum / $total, 1) : 0;
        return ['average' => $average, 'counts' => $counts];
    }
    
    /**
     * Check if user has already reviewed a product
     * 
     * @param int $userId User ID
     * @param int $productId Product ID
     * @return bool True if user has already reviewed the product
     */
    public function hasUserReviewedProduct($userId, $productId)
    {
        $sql = "SELECT COUNT(*) as count 
                FROM {$this->table}
                WHERE user_id = ? AND product_id = ?";
                
        $result = $this->db->fetchOne($sql, [$userId, $productId]);
        return $result['count'] > 0;
    }
    
    /**
     * Get user's reviews for a product (count)
     * @param int $userId User ID
     * @param int $productId Product ID
     * @return int Number of reviews
     */
    public function getUserReviewCount($userId, $productId)
    {
        $sql = "SELECT COUNT(*) as count 
                FROM {$this->table}
                WHERE user_id = ? AND product_id = ?";
                
        $result = $this->db->fetchOne($sql, [$userId, $productId]);
        return $result['count'];
    }

    /**
     * Search reviews with filters
     */
    public function search($search = '', $filters = [])
    {
        $conditions = [];
        $params = [];

        if (!empty($search)) {
            $conditions[] = "(r.title LIKE ? OR r.content LIKE ? OR p.name LIKE ? OR u.full_name LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

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

        $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $sort = $filters['sort'] ?? 'created_at';
        $order = $filters['order'] ?? 'DESC';
        $limit = $filters['limit'] ?? 20;

        $sql = "SELECT r.*,
                       p.name as product_name,
                       p.image as product_image,
                       u.full_name as customer_name,
                       u.email as customer_email
                FROM {$this->table} r
                LEFT JOIN products p ON r.product_id = p.id
                LEFT JOIN users u ON r.user_id = u.id
                $whereClause
                ORDER BY r.$sort $order
                LIMIT $limit";

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Get review with full details
     */
    public function getFullDetails($id)
    {
        $sql = "SELECT r.*,
                       p.name as product_name,
                       p.slug as product_slug,
                       p.image as product_image,
                       p.price as product_price,
                       u.full_name as customer_name,
                       u.email as customer_email,
                       u.avatar as customer_avatar
                FROM {$this->table} r
                LEFT JOIN products p ON r.product_id = p.id
                LEFT JOIN users u ON r.user_id = u.id
                WHERE r.id = ?";

        return $this->db->fetchOne($sql, [$id]);
    }

    /**
     * Get review statistics
     */
    public function getStatistics()
    {
        $sql = "SELECT
                    COUNT(*) as total_reviews,
                    AVG(rating) as average_rating,
                    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_reviews,
                    COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved_reviews,
                    COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected_reviews,
                    COUNT(CASE WHEN rating = 5 THEN 1 END) as five_star,
                    COUNT(CASE WHEN rating = 4 THEN 1 END) as four_star,
                    COUNT(CASE WHEN rating = 3 THEN 1 END) as three_star,
                    COUNT(CASE WHEN rating = 2 THEN 1 END) as two_star,
                    COUNT(CASE WHEN rating = 1 THEN 1 END) as one_star
                FROM {$this->table}";

        return $this->db->fetchOne($sql);
    }

    /**
     * Update review status
     */
    public function updateStatus($id, $status)
    {
        $sql = "UPDATE {$this->table} SET status = ?, updated_at = NOW() WHERE id = ?";
        return $this->db->execute($sql, [$status, $id]);
    }

    /**
     * Bulk update status
     */
    public function bulkUpdateStatus($ids, $status)
    {
        if (empty($ids)) {
            return false;
        }

        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $sql = "UPDATE {$this->table} SET status = ?, updated_at = NOW() WHERE id IN ($placeholders)";

        $params = array_merge([$status], $ids);
        return $this->db->execute($sql, $params);
    }

    /**
     * Bulk delete reviews
     */
    public function bulkDelete($ids)
    {
        if (empty($ids)) {
            return false;
        }

        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $sql = "DELETE FROM {$this->table} WHERE id IN ($placeholders)";

        return $this->db->execute($sql, $ids);
    }

    /**
     * Get product reviews
     */
    public function getProductReviews($productId, $limit = 10, $userId = null)
    {
        // Xây dựng truy vấn cơ bản trước
        $params = [$productId];
        
        // Truy vấn SQL cơ bản
        $baseSql = "
            SELECT r.*, u.full_name as customer_name, u.avatar as customer_avatar,
                   0 as is_verified_purchase 
            FROM {$this->table} r
            LEFT JOIN users u ON r.user_id = u.id
            WHERE r.product_id = ?
        ";
        
        // Lấy kết quả truy vấn cơ bản
        $sql = $baseSql . " ORDER BY r.created_at DESC LIMIT ?";
        $params[] = $limit;
        
        $result = $this->db->fetchAll($sql, $params);
        
        // Nếu có userId, thêm thông tin like cho từng review
        if ($userId && !empty($result)) {
            // Tạo mảng các review id để lấy thông tin like trong 1 lần truy vấn
            $reviewIds = array_column($result, 'id');
            $placeholders = implode(',', array_fill(0, count($reviewIds), '?'));

            // Lấy danh sách các review mà user đã like
            $likesSql = "
                SELECT review_id 
                FROM review_likes 
                WHERE review_id IN ($placeholders) AND user_id = ?
            ";

            // Tạo params cho truy vấn likes
            $likeParams = array_merge($reviewIds, [$userId]);
            $likedReviews = $this->db->fetchAll($likesSql, $likeParams);

            // Chuyển kết quả thành mảng đơn giản các ID review đã like
            $likedReviewIds = array_column($likedReviews, 'review_id');

            // Đánh dấu các review đã like trong kết quả (đổi tên trường thành liked_by_user)
            foreach ($result as &$review) {
                $review['liked_by_user'] = in_array($review['id'], $likedReviewIds);
            }
        } else {
            // Nếu không có userId, đánh dấu tất cả là chưa like
            foreach ($result as &$review) {
                $review['liked_by_user'] = false;
            }
        }

        return $result;
    }

    /**
     * Get customer reviews
     */
    public function getCustomerReviews($userId, $limit = 10)
    {
        $sql = "SELECT r.*, p.name as product_name, p.image as product_image
                FROM {$this->table} r
                LEFT JOIN products p ON r.product_id = p.id
                WHERE r.user_id = ?
                ORDER BY r.created_at DESC
                LIMIT ?";

        return $this->db->fetchAll($sql, [$userId, $limit]);
    }

    /**
     * Execute custom SQL query - for admin reviews with details
     */
    public function executeQuery($sql, $params = [])
    {
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Lấy thông tin đánh giá theo ID
     * 
     * @param int $id Review ID
     * @return array|false Thông tin đánh giá hoặc false nếu không tìm thấy
     */
    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }
    
    /**
     * Kiểm tra xem người dùng đã like đánh giá chưa
     * 
     * @param int $reviewId ID đánh giá
     * @param int $userId ID người dùng
     * @return bool True nếu người dùng đã like
     */
    public function hasUserLikedReview($reviewId, $userId)
    {
        $sql = "SELECT COUNT(*) as count FROM review_likes 
                WHERE review_id = ? AND user_id = ?";
        $result = $this->db->fetchOne($sql, [$reviewId, $userId]);
        return $result['count'] > 0;
    }
    
    /**
     * Thêm like cho đánh giá
     * 
     * @param int $reviewId ID đánh giá
     * @param int $userId ID người dùng
     * @return bool Kết quả của thao tác
     */
    public function addLike($reviewId, $userId)
    {
        try {
            // Bắt đầu transaction
            $this->db->beginTransaction();
            
            // Thêm record vào bảng review_likes
            $sqlInsert = "INSERT INTO review_likes (review_id, user_id) VALUES (?, ?)";
            $this->db->execute($sqlInsert, [$reviewId, $userId]);
            
            // Tăng helpful_count trong bảng reviews
            $sqlUpdate = "UPDATE {$this->table} SET helpful_count = helpful_count + 1 WHERE id = ?";
            $this->db->execute($sqlUpdate, [$reviewId]);
            
            // Commit transaction
            $this->db->commit();
            
            return true;
        } catch (Exception $e) {
            // Rollback nếu có lỗi
            $this->db->rollback();
            error_log("Error adding like: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Tăng số lượt thích cho một đánh giá
     * 
     * @param int $id Review ID
     * @return bool Kết quả của thao tác
     */
    public function incrementHelpfulCount($id)
    {
        $sql = "UPDATE {$this->table} SET helpful_count = helpful_count + 1 WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }
    
    /**
     * Remove a like from a review
     * 
     * @param int $reviewId Review ID
     * @param int $userId User ID
     * @return bool Success status
     */
    public function removeLike($reviewId, $userId)
    {
        try {
            // Remove the like
            $sqlDelete = "DELETE FROM review_likes WHERE review_id = ? AND user_id = ?";
            $result = $this->db->execute($sqlDelete, [$reviewId, $userId]);
            
            if ($result) {
                // Update helpful_count
                $sqlUpdate = "UPDATE {$this->table} 
                             SET helpful_count = (
                                 SELECT COUNT(*) FROM review_likes WHERE review_id = ?
                             ) 
                             WHERE id = ?";
                $this->db->execute($sqlUpdate, [$reviewId, $reviewId]);
                
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Error removing like: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Xóa đánh giá theo ID
     * 
     * @param int $id Review ID
     * @return bool Kết quả của thao tác
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }
}
