<?php
/**
 * Category Model
 * zone Fashion E-commerce Platform
 */

class Category extends BaseModel
{
    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name', 'slug', 'description', 'image', 'parent_id',
        'sort_order', 'status', 'meta_title', 'meta_description'
    ];

    /**
     * Get category by slug
     */
    public function findBySlug($slug)
    {
        return $this->findBy('slug', $slug);
    }

    /**
     * Get featured categories for homepage
     */
    public function getFeaturedCategories($limit = 6)
    {
        try {
            $db = Database::getInstance();

            // Get active categories with images, ordered by sort_order
            $sql = "SELECT * FROM categories
                    WHERE status = 'active'
                    AND image IS NOT NULL
                    AND image != ''
                    ORDER BY sort_order ASC, created_at DESC
                    LIMIT ?";

            $categories = $db->fetchAll($sql, [$limit]);

            // If no categories with images found, get any active categories
            if (empty($categories)) {
                $sql = "SELECT * FROM categories
                        WHERE status = 'active'
                        ORDER BY sort_order ASC, created_at DESC
                        LIMIT ?";
                $categories = $db->fetchAll($sql, [$limit]);
            }

            return $categories;

        } catch (Exception $e) {
            error_log("Error in getFeaturedCategories: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all active categories
     */
    public function getActiveCategories()
    {
        return $this->where(['status' => 'active'], 'sort_order', 'ASC');
    }

    /**
     * Get all active categories
     */
    public function getActive()
    {
        return $this->where(['status' => 'active'], 'sort_order', 'ASC');
    }

    /**
     * Get parent categories (top level)
     */
    public function getParents()
    {
        $sql = "SELECT * FROM {$this->table} WHERE parent_id IS NULL ORDER BY sort_order ASC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get children of a category
     */
    public function getChildren($parentId)
    {
        return $this->where(['parent_id' => $parentId], 'sort_order', 'ASC');
    }

    /**
     * Get category tree (hierarchical structure)
     */
    public function getTree()
    {
        $categories = $this->all('sort_order', 'ASC');
        return $this->buildTree($categories);
    }

    /**
     * Get active category tree
     */
    public function getActiveTree()
    {
        $categories = $this->getActive();
        return $this->buildTree($categories);
    }

    /**
     * Get categories for navigation menu
     * Returns only active parent categories with their children
     */
    public function getNavigationCategories()
    {
        $db = Database::getInstance();

        // Get active parent categories
        $sql = "SELECT * FROM {$this->table}
                WHERE parent_id IS NULL
                AND status = 'active'
                ORDER BY sort_order ASC";
        $parents = $db->fetchAll($sql);

        // Get all active categories for building the tree
        $allCategories = $this->getActive();

        // For each parent, attach its children
        foreach ($parents as &$parent) {
            $parent['children'] = $this->buildTree($allCategories, $parent['id']);
        }

        return $parents;
    }

    /**
     * Build hierarchical tree from flat array
     */
    private function buildTree($categories, $parentId = null)
    {
        $tree = [];

        foreach ($categories as $category) {
            if ($category['parent_id'] == $parentId) {
                $category['children'] = $this->buildTree($categories, $category['id']);
                $tree[] = $category;
            }
        }

        return $tree;
    }

    /**
     * Get category with its parent chain
     */
    public function getCategoryWithParents($categoryId)
    {
        $category = $this->find($categoryId);
        if (!$category) {
            return null;
        }

        $parents = [];
        $currentId = $category['parent_id'];

        while ($currentId) {
            $parent = $this->find($currentId);
            if ($parent) {
                array_unshift($parents, $parent);
                $currentId = $parent['parent_id'];
            } else {
                break;
            }
        }

        return [
            'category' => $category,
            'parents' => $parents
        ];
    }

    /**
     * Get breadcrumb for category
     */
    public function getBreadcrumb($categoryId)
    {
        $data = $this->getCategoryWithParents($categoryId);
        if (!$data) {
            return [];
        }

        $breadcrumb = [];

        // Add parents
        foreach ($data['parents'] as $parent) {
            $breadcrumb[] = [
                'name' => $parent['name'],
                'slug' => $parent['slug'],
                'url' => '/category/' . $parent['slug']
            ];
        }

        // Add current category
        $breadcrumb[] = [
            'name' => $data['category']['name'],
            'slug' => $data['category']['slug'],
            'url' => '/category/' . $data['category']['slug'],
            'current' => true
        ];

        return $breadcrumb;
    }

    /**
     * Create new category
     */
    public function createCategory($data)
    {
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['name']);
        }

        // Set sort order if not provided
        if (!isset($data['sort_order'])) {
            $data['sort_order'] = $this->getNextSortOrder($data['parent_id'] ?? null);
        }

        return $this->create($data);
    }

    /**
     * Update category
     */
    public function updateCategory($id, $data)
    {
        // Generate new slug if name changed
        if (isset($data['name'])) {
            $current = $this->find($id);
            if ($current && $current['name'] !== $data['name']) {
                $data['slug'] = $this->generateSlug($data['name'], $this->table, 'slug', $id);
            }
        }

        return $this->update($id, $data);
    }

    /**
     * Get next sort order for a parent
     */
    private function getNextSortOrder($parentId = null)
    {
        $sql = "SELECT MAX(sort_order) as max_order FROM {$this->table} WHERE ";
        $params = [];

        if ($parentId) {
            $sql .= "parent_id = :parent_id";
            $params['parent_id'] = $parentId;
        } else {
            $sql .= "parent_id IS NULL";
        }

        $result = $this->db->fetchOne($sql, $params);
        return $result ? (int)$result['max_order'] + 1 : 1;
    }

    /**
     * Get products count for category
     */
    public function getProductsCount($categoryId)
    {
        $sql = "SELECT COUNT(*) as count FROM products WHERE category_id = :category_id AND status = 'published'";
        $result = $this->db->fetchOne($sql, ['category_id' => $categoryId]);
        return $result ? (int)$result['count'] : 0;
    }

    /**
     * Get all descendant category IDs
     */
    public function getDescendantIds($categoryId)
    {
        $descendants = [$categoryId];
        $children = $this->getChildren($categoryId);

        foreach ($children as $child) {
            $descendants = array_merge($descendants, $this->getDescendantIds($child['id']));
        }

        return $descendants;
    }

    /**
     * Delete category and handle children
     */
    public function deleteCategory($id)
    {
        $category = $this->find($id);
        if (!$category) {
            return false;
        }

        // Check if category has products
        if ($this->getProductsCount($id) > 0) {
            return ['error' => 'Không thể xóa danh mục có sản phẩm'];
        }

        // Move children to parent level
        $children = $this->getChildren($id);
        foreach ($children as $child) {
            $this->update($child['id'], ['parent_id' => $category['parent_id']]);
        }

        // Delete category image if exists
        if ($category['image']) {
            $imagePath = PUBLIC_PATH . $category['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        return $this->delete($id);
    }

    /**
     * Generate unique slug
     */
    protected function generateSlug($string, $table = null, $column = 'slug', $excludeId = null)
    {
        $table = $table ?: $this->table;

        // Convert to lowercase and replace spaces/special chars
        $slug = strtolower(trim($string));
        $slug = preg_replace('/[^a-z0-9\-_]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');

        // Check if slug exists
        $originalSlug = $slug;
        $counter = 1;

        while ($this->slugExists($slug, $table, $column, $excludeId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Check if slug exists
     */
    protected function slugExists($slug, $table, $column, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) as count FROM {$table} WHERE {$column} = :slug";
        $params = ['slug' => $slug];

        if ($excludeId) {
            $sql .= " AND id != :id";
            $params['id'] = $excludeId;
        }

        $result = $this->db->fetchOne($sql, $params);
        return $result && $result['count'] > 0;
    }

    /**
     * Validate category data
     */
    protected function validate($data)
    {
        $errors = [];

        // Name validation
        if (empty($data['name'])) {
            $errors['name'] = 'Tên danh mục là trường bắt buộc.';
        } elseif (strlen($data['name']) > 100) {
            $errors['name'] = 'Tên danh mục không được vượt quá 100 ký tự.';
        }

        // Parent validation
        if (isset($data['parent_id']) && !empty($data['parent_id'])) {
            $parent = $this->find($data['parent_id']);
            if (!$parent) {
                $errors['parent_id'] = 'Danh mục cha không tồn tại.';
            }
        }

        // Status validation
        if (isset($data['status']) && !in_array($data['status'], ['active', 'inactive'])) {
            $errors['status'] = 'Trạng thái không hợp lệ.';
        }

        return $errors;
    }

    /**
     * Search categories
     */
    public function search($query, $filters = [], $page = 1, $limit = 25)
    {
        $whereConditions = [];
        $params = [];

        // Search in name and description
        if (!empty($query)) {
            $whereConditions[] = "(name LIKE :query OR description LIKE :query)";
            $params['query'] = "%{$query}%";
        }

        // Status filter
        if (!empty($filters['status'])) {
            $whereConditions[] = "status = :status";
            $params['status'] = $filters['status'];
        }

        // Parent filter
        if (isset($filters['parent_id'])) {
            if ($filters['parent_id'] === 'null' || $filters['parent_id'] === '') {
                $whereConditions[] = "parent_id IS NULL";
            } else {
                $whereConditions[] = "parent_id = :parent_id";
                $params['parent_id'] = $filters['parent_id'];
            }
        }

        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM {$this->table} {$whereClause}";
        $totalResult = $this->db->fetchOne($countSql, $params);
        $total = $totalResult ? (int)$totalResult['total'] : 0;

        // Get paginated data
        $offset = ($page - 1) * $limit;
        $params['limit'] = $limit;
        $params['offset'] = $offset;

        $sql = "SELECT * FROM {$this->table} {$whereClause} ORDER BY sort_order ASC LIMIT :limit OFFSET :offset";
        $data = $this->db->fetchAll($sql, $params);

        return [
            'data' => $data,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $total,
                'last_page' => ceil($total / $limit),
                'from' => $offset + 1,
                'to' => min($offset + $limit, $total)
            ]
        ];
    }

    /**
     * Get category statistics
     */
    public function getStatistics()
    {
        $total = $this->count();
        $active = $this->count(['status' => 'active']);
        $inactive = $this->count(['status' => 'inactive']);
        $parents = count($this->getParents());

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'parents' => $parents,
            'children' => $total - $parents
        ];
    }

    /**
     * Reorder categories
     */
    public function reorder($categoryIds)
    {
        foreach ($categoryIds as $index => $categoryId) {
            $this->update($categoryId, ['sort_order' => $index + 1]);
        }
        return true;
    }
}
