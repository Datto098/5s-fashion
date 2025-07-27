<?php

class Review extends BaseModel
{
    protected $table = 'reviews';
    protected $primaryKey = 'id';
    protected $fillable = [
        'product_id', 'user_id', 'rating', 'title', 'content',
        'status', 'is_verified_purchase'
    ];

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
    public function getProductReviews($productId, $limit = 10)
    {
        $sql = "SELECT r.*, u.full_name as customer_name, u.avatar as customer_avatar
                FROM {$this->table} r
                LEFT JOIN users u ON r.user_id = u.id
                WHERE r.product_id = ? AND r.status = 'approved'
                ORDER BY r.created_at DESC
                LIMIT ?";

        return $this->db->fetchAll($sql, [$productId, $limit]);
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
}
