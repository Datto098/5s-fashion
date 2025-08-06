<?php
// app/models/Post.php
/**
 * Post Model
 * 5S Fashion E-commerce Platform
 */

class Post extends BaseModel
{
    protected $table = 'posts';
    protected $primaryKey = 'id';
    protected $fillable = [
        'title', 'content', 'thumbnail', 'author_id', 'created_at', 'updated_at', 'status'
    ];

    // Lấy X bài viết mới nhất cho client
    public static function getLatest($limit = 5)
    {
        $instance = new static();
        $sql = "SELECT * FROM {$instance->table} WHERE status = 1 ORDER BY created_at DESC LIMIT :limit";
        return $instance->db->fetchAll($sql, ['limit' => $limit]);
    }

    // Lấy tất cả bài viết cho client
    public static function getAllForClient() {
        $instance = new static();
        $stmt = $instance->db->query("
            SELECT posts.*, users.full_name AS author_name
            FROM posts
            LEFT JOIN users ON posts.author_id = users.id
            WHERE posts.status = 1
            ORDER BY posts.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy tất cả bài viết
    public static function getAll() {
        $instance = new static();
        $stmt = $instance->db->query("
            SELECT posts.*, users.full_name AS author_name
            FROM posts
            LEFT JOIN users ON posts.author_id = users.id
            ORDER BY posts.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy chi tiết 1 bài viết theo id
   
}
