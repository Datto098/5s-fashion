<?php
/**
 * Wishlist Model
 * 5S Fashion E-commerce Platform
 */

class Wishlist extends BaseModel
{
    protected $table = 'wishlist';

    /**
     * Get user's wishlist with product details
     */
    public function getUserWishlist($userId)
    {
        $sql = "SELECT w.*, p.name, p.slug, p.price, p.sale_price, p.featured_image as image, p.description,
                       COALESCE(AVG(r.rating), 0) as rating,
                       COUNT(r.id) as reviews_count
                FROM {$this->table} w
                LEFT JOIN products p ON w.product_id = p.id
                LEFT JOIN reviews r ON p.id = r.product_id
                WHERE w.user_id = :user_id AND p.status = 'published'
                GROUP BY w.id, p.id
                ORDER BY w.created_at DESC";

        return $this->db->fetchAll($sql, ['user_id' => $userId]);
    }

    /**
     * Add product to wishlist
     */
    public function addToWishlist($userId, $productId)
    {
        // Check if already exists
        if ($this->isInWishlist($userId, $productId)) {
            return false;
        }

        $sql = "INSERT INTO {$this->table} (user_id, product_id) VALUES (:user_id, :product_id)";
        return $this->db->execute($sql, [
            'user_id' => $userId,
            'product_id' => $productId
        ]);
    }

    /**
     * Remove product from wishlist
     */
    public function removeFromWishlist($userId, $productId)
    {
        $sql = "DELETE FROM {$this->table} WHERE user_id = :user_id AND product_id = :product_id";
        return $this->db->execute($sql, [
            'user_id' => $userId,
            'product_id' => $productId
        ]);
    }

    /**
     * Check if product is in wishlist
     */
    public function isInWishlist($userId, $productId)
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = :user_id AND product_id = :product_id";
        $result = $this->db->fetchOne($sql, [
            'user_id' => $userId,
            'product_id' => $productId
        ]);

        return isset($result['count']) && $result['count'] > 0;
    }

    /**
     * Get wishlist count for user
     */
    public function getWishlistCount($userId)
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} w
                LEFT JOIN products p ON w.product_id = p.id
                WHERE w.user_id = :user_id AND p.status = 'published'";

        $result = $this->db->fetchOne($sql, ['user_id' => $userId]);
        return isset($result['count']) ? (int)$result['count'] : 0;
    }

    /**
     * Toggle product in wishlist (add if not exists, remove if exists)
     */
    public function toggleWishlist($userId, $productId)
    {
        if ($this->isInWishlist($userId, $productId)) {
            return $this->removeFromWishlist($userId, $productId);
        } else {
            return $this->addToWishlist($userId, $productId);
        }
    }

    /**
     * Clear all wishlist for user
     */
    public function clearWishlist($userId)
    {
        $sql = "DELETE FROM {$this->table} WHERE user_id = :user_id";
        return $this->db->execute($sql, ['user_id' => $userId]);
    }
}
