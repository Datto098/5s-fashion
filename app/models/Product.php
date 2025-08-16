<?php
/**
 * Product Model
 * 5S Fashion E-commerce Platform
 */

class Product extends BaseModel
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name', 'slug', 'sku', 'short_description', 'description', 'price', 'sale_price',
        'cost_price', 'category_id', 'brand_id', 'featured_image', 'gallery', 'status',
        'featured', 'weight', 'dimensions', 'material', 'care_instructions',
        'gender', 'season', 'style', 'meta_title', 'meta_description', 'views',
        'stock_quantity', 'manage_stock', 'low_stock_threshold', 'has_variants'
    ];

    /**
     * Get product by slug
     */
    public function findBySlug($slug)
    {
        return $this->findBy('slug', $slug);
    }

    /**
     * Get product by SKU
     */
    public function findBySKU($sku)
    {
        return $this->findBy('sku', $sku);
    }

    /**
     * Get all published products
     */
    public function getPublished()
    {
        return $this->where(['status' => 'published'], 'created_at', 'DESC');
    }

    /**
     * Get featured products for homepage
     */
    public function getFeaturedProducts($limit = 8)
    {
        try {
            $db = Database::getInstance();

            $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug,
                           p.stock_quantity as stock
                    FROM products p
                    LEFT JOIN categories c ON p.category_id = c.id
                    WHERE p.status = 'published'
                    AND p.featured = 1
                    ORDER BY p.created_at DESC
                    LIMIT ?";

            $products = $db->fetchAll($sql, [$limit]);

            // If no featured products, get latest published products
            if (empty($products)) {
                $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug,
                               p.stock_quantity as stock
                        FROM products p
                        LEFT JOIN categories c ON p.category_id = c.id
                        WHERE p.status = 'published'
                        ORDER BY p.created_at DESC
                        LIMIT ?";
                $products = $db->fetchAll($sql, [$limit]);
            }

            // Load variants for each product
            foreach ($products as &$product) {
                $product['variants'] = $this->getVariants($product['id']);
            }

            return $products;

        } catch (Exception $e) {
            error_log("Error in getFeaturedProducts: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get new arrivals
     */
    public function getNewArrivals($limit = 8)
    {
        try {
            $db = Database::getInstance();

            $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug,
                           p.stock_quantity as stock
                    FROM products p
                    LEFT JOIN categories c ON p.category_id = c.id
                    WHERE p.status = 'published'
                    ORDER BY p.created_at DESC
                    LIMIT ?";

            $products = $db->fetchAll($sql, [$limit]);

            // Load variants for each product
            foreach ($products as &$product) {
                $product['variants'] = $this->getVariants($product['id']);
            }

            return $products;

        } catch (Exception $e) {
            error_log("Error in getNewArrivals: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get best sellers (placeholder - would need order stats)
     */
    public function getBestSellers($limit = 8)
    {
        try {
            $db = Database::getInstance();

            // Get products ordered by sales count (simplified for existing schema)
            $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug
                    FROM products p
                    LEFT JOIN categories c ON p.category_id = c.id
                    WHERE p.status = 'published'
                    ORDER BY p.created_at DESC
                    LIMIT ?";

            $products = $db->fetchAll($sql, [$limit]);

            // If no products, fallback to featured products
            if (empty($products)) {
                return $this->getFeaturedProducts($limit);
            }

            return $products;

        } catch (Exception $e) {
            error_log("Error in getBestSellers: " . $e->getMessage());
            // Fallback to featured products
            return $this->getFeaturedProducts($limit);
        }
    }

    /**
     * Get sale products
     */
    public function getSaleProducts($limit = 8)
    {
        try {
            $db = Database::getInstance();

            $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug,
                           AVG(r.rating) as rating,
                           COUNT(r.id) as reviews_count
                    FROM products p
                    LEFT JOIN categories c ON p.category_id = c.id
                    LEFT JOIN reviews r ON p.id = r.product_id
                    WHERE p.status = 'published'
                    AND p.sale_price IS NOT NULL
                    AND p.sale_price > 0
                    AND p.sale_price < p.price
                    GROUP BY p.id
                    ORDER BY ((p.price - p.sale_price) / p.price) DESC, p.created_at DESC
                    LIMIT ?";

            $products = $db->fetchAll($sql, [$limit]);

            // Format the results
            foreach ($products as &$product) {
                $product['rating'] = $product['rating'] ? round($product['rating'], 1) : 0;
                $product['reviews_count'] = (int)$product['reviews_count'];
                $product['discount_percent'] = round(((float)$product['price'] - (float)$product['sale_price']) / (float)$product['price'] * 100);
            }

            return $products;

        } catch (Exception $e) {
            error_log("Error in getSaleProducts: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get products with filters and pagination
     */
    public function getProductsWithFilters($filters = [], $page = 1, $limit = 12)
    {
        $offset = ($page - 1) * $limit;
        $whereConditions = ["p.status = 'published'"];
        $params = [];

        // Apply filters
        if (!empty($filters['category'])) {
            $whereConditions[] = "c.slug = ?";
            $params[] = $filters['category'];
        }

        if (!empty($filters['brand'])) {
            $whereConditions[] = "b.slug = ?";
            $params[] = $filters['brand'];
        }

        if (!empty($filters['search'])) {
            $whereConditions[] = "(p.name LIKE ? OR p.description LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($filters['min_price'])) {
            $whereConditions[] = "COALESCE(p.sale_price, p.price) >= ?";
            $params[] = $filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $whereConditions[] = "COALESCE(p.sale_price, p.price) <= ?";
            $params[] = $filters['max_price'];
        }

        // Order by
        $orderBy = "p.created_at DESC";
        switch ($filters['sort'] ?? 'latest') {
            case 'price_asc':
                $orderBy = "COALESCE(p.sale_price, p.price) ASC";
                break;
            case 'price_desc':
                $orderBy = "COALESCE(p.sale_price, p.price) DESC";
                break;
            case 'name':
                $orderBy = "p.name ASC";
                break;
            case 'featured':
                $orderBy = "p.featured DESC, p.created_at DESC";
                break;
        }

        $whereClause = implode(' AND ', $whereConditions);

        // Get total count
        $countSql = "SELECT COUNT(*) as total
                     FROM {$this->table} p
                     LEFT JOIN categories c ON p.category_id = c.id
                     LEFT JOIN brands b ON p.brand_id = b.id
                     WHERE {$whereClause}";

        $totalResult = $this->db->fetchOne($countSql, $params);
        $total = $totalResult['total'];

        // Get products
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug, b.name as brand_name
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE {$whereClause}
                ORDER BY {$orderBy}
                LIMIT ? OFFSET ?";

        $params[] = $limit;
        $params[] = $offset;

        $products = $this->db->fetchAll($sql, $params);

        return [
            'products' => $products,
            'total' => $total
        ];
    }

    /**
     * Get product variants (placeholder)
     */
    public function getProductVariants($productId)
    {
        // For now, return empty array - will implement when variants table is ready
        return [];
    }

    /**
     * Get all brands for filter dropdown
     */
    public function getAllBrands()
    {
        $sql = "SELECT DISTINCT b.id, b.name, b.slug, COUNT(p.id) as product_count
                FROM brands b
                LEFT JOIN products p ON b.id = p.brand_id AND p.status = 'published'
                GROUP BY b.id, b.name, b.slug
                HAVING product_count > 0
                ORDER BY b.name";

        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Get product by slug for detail page
     */
    public function getProductBySlug($slug)
    {
        $sql = "SELECT p.*, c.name as category_name, b.name as brand_name,
                COALESCE(p.sale_price, p.price) as current_price,
                CASE WHEN p.sale_price IS NOT NULL THEN 1 ELSE 0 END as is_sale,
                CASE WHEN DATEDIFF(NOW(), p.created_at) <= 30 THEN 1 ELSE 0 END as is_new,
                p.featured as is_featured,
                4.5 as rating,
                50 as review_count
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE p.slug = ? AND p.status = 'published'";

        return $this->db->query($sql, [$slug])->fetch();
    }

    /**
     * Get related products for product detail page
     */
    public function getRelatedProducts($productId, $categoryId, $limit = 8)
    {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug, b.name as brand_name,
                COALESCE(p.sale_price, p.price) as current_price,
                CASE WHEN p.sale_price IS NOT NULL THEN 1 ELSE 0 END as is_sale,
                CASE WHEN DATEDIFF(NOW(), p.created_at) <= 30 THEN 1 ELSE 0 END as is_new,
                p.featured as is_featured,
                4.5 as rating
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE p.category_id = ? AND p.id != ? AND p.status = 'published'
                ORDER BY RAND()
                LIMIT ?";

        return $this->db->query($sql, [$categoryId, $productId, $limit])->fetchAll();
    }

    /**
     * Get featured products
     */
    public function getFeatured($limit = 10)
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE status = 'published' AND featured = 1
                ORDER BY created_at DESC
                LIMIT ?";
        return $this->db->fetchAll($sql, [$limit]);
    }

    /**
     * Get products by category
     */
    public function getByCategory($categoryId, $limit = null)
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE category_id = ? AND status = 'published'
                ORDER BY created_at DESC";

        if ($limit) {
            $sql .= " LIMIT ?";
            return $this->db->fetchAll($sql, [$categoryId, $limit]);
        }

        return $this->db->fetchAll($sql, [$categoryId]);
    }

    /**
     * Get products by brand
     */
    public function getByBrand($brandId, $limit = null)
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE brand_id = ? AND status = 'published'
                ORDER BY created_at DESC";

        if ($limit) {
            $sql .= " LIMIT ?";
            return $this->db->fetchAll($sql, [$brandId, $limit]);
        }

        return $this->db->fetchAll($sql, [$brandId]);
    }

    /**
     * Search products
     */
    public function search($query, $filters = [])
    {
        $sql = "SELECT p.*, c.name as category_name, b.name as brand_name,
                       CASE
                           WHEN p.has_variants = 1 THEN COALESCE(SUM(pv.stock_quantity), 0)
                           ELSE p.stock_quantity
                       END as total_stock
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN brands b ON p.brand_id = b.id
                LEFT JOIN product_variants pv ON p.id = pv.product_id
                WHERE 1=1";

        $params = [];

        // Status filter - allow filtering by different statuses for admin
        if (!empty($filters['status'])) {
            $sql .= " AND p.status = ?";
            $params[] = $filters['status'];
        } else {
            // Default to show all statuses for admin view
            $sql .= " AND p.status IN ('draft', 'published', 'out_of_stock')";
        }

        // Text search
        if (!empty($query)) {
            $sql .= " AND (p.name LIKE ? OR p.description LIKE ? OR p.sku LIKE ?)";
            $searchTerm = "%{$query}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        // Category filter
        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = ?";
            $params[] = $filters['category_id'];
        }

        // Brand filter
        if (!empty($filters['brand_id'])) {
            $sql .= " AND p.brand_id = ?";
            $params[] = $filters['brand_id'];
        }

        // Price range
        if (!empty($filters['price_min'])) {
            $sql .= " AND p.price >= ?";
            $params[] = $filters['price_min'];
        }

        if (!empty($filters['price_max'])) {
            $sql .= " AND p.price <= ?";
            $params[] = $filters['price_max'];
        }

        // Stock status filter
        if (!empty($filters['stock_status'])) {
            if ($filters['stock_status'] === 'in_stock') {
                $sql .= " AND (
                    (p.has_variants = 1 AND EXISTS (SELECT 1 FROM product_variants pv2 WHERE pv2.product_id = p.id AND pv2.stock_quantity > 0))
                    OR (p.has_variants = 0 AND p.stock_quantity > 0)
                )";
            } elseif ($filters['stock_status'] === 'low_stock') {
                $sql .= " AND (
                    (p.has_variants = 1 AND EXISTS (SELECT 1 FROM product_variants pv2 WHERE pv2.product_id = p.id AND pv2.stock_quantity > 0 AND pv2.stock_quantity <= 10))
                    OR (p.has_variants = 0 AND p.stock_quantity > 0 AND p.stock_quantity <= 10)
                )";
            } elseif ($filters['stock_status'] === 'out_of_stock') {
                $sql .= " AND (
                    (p.has_variants = 1 AND NOT EXISTS (SELECT 1 FROM product_variants pv2 WHERE pv2.product_id = p.id AND pv2.stock_quantity > 0))
                    OR (p.has_variants = 0 AND p.stock_quantity <= 0)
                )";
            }
        }

        // Gender filter
        if (!empty($filters['gender'])) {
            $sql .= " AND p.gender = ?";
            $params[] = $filters['gender'];
        }

        $sql .= " GROUP BY p.id";

        // Sort
        $orderBy = $filters['sort'] ?? 'created_at';
        $orderDir = $filters['order'] ?? 'DESC';

        $allowedSorts = ['name', 'price', 'created_at', 'views', 'total_stock', 'status'];
        if (in_array($orderBy, $allowedSorts)) {
            if ($orderBy === 'total_stock') {
                $sql .= " ORDER BY total_stock {$orderDir}";
            } else {
                $sql .= " ORDER BY p.{$orderBy} {$orderDir}";
            }
        } else {
            $sql .= " ORDER BY p.created_at DESC";
        }

        // Limit
        if (!empty($filters['limit'])) {
            $sql .= " LIMIT ?";
            $params[] = $filters['limit'];
        }

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Get product with full details (category, brand, variants, images)
     */
    public function getFullDetails($id)
    {
        $sql = "SELECT p.*,
                       c.name as category_name, c.slug as category_slug
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.id = ?";

        $product = $this->db->fetchOne($sql, [$id]);

        if ($product) {
            // Get variants
            $product['variants'] = $this->getVariants($id);

            // Get images
            $product['images'] = $this->getImages($id);

            // Get attributes (commented out as getAttributes method may not exist)
            // $product['attributes'] = $this->getAttributes($id);
        }

        return $product;
    }

    /**
     * Get product variants
     */
    public function getVariants($productId)
    {
        $sql = "SELECT * FROM product_variants
                WHERE product_id = ?
                ORDER BY id ASC";

        $variants = $this->db->fetchAll($sql, [$productId]);

        // Parse variant_name to extract color and size
        foreach ($variants as &$variant) {
            if (!empty($variant['variant_name'])) {
                // Variant name format: "Product Name - Color - Size"
                $parts = explode(' - ', $variant['variant_name']);
                if (count($parts) >= 3) {
                    $variant['color'] = $parts[count($parts) - 2]; // Second to last part is color
                    $variant['size'] = $parts[count($parts) - 1];  // Last part is size
                } else {
                    $variant['color'] = 'Default';
                    $variant['size'] = 'One Size';
                }
            }

            // Use product price if variant price is null or 0
            if (is_null($variant['price']) || floatval($variant['price']) == 0) {
                // Get product price
                $productSql = "SELECT price, sale_price FROM products WHERE id = ?";
                $product = $this->db->fetchOne($productSql, [$productId]);
                if ($product) {
                    $variant['price'] = floatval($product['sale_price']) > 0 ? $product['sale_price'] : $product['price'];
                }
            }
        }

        return $variants;
    }

    /**
     * Get product images
     */
    public function getImages($productId)
    {
        $sql = "SELECT * FROM product_images
                WHERE product_id = ?
                ORDER BY id ASC";

        return $this->db->fetchAll($sql, [$productId]);
    }

    /**
     * Get product attributes
     */
    public function getAttributes($productId)
    {
        $sql = "SELECT * FROM product_attributes
                WHERE product_id = ?
                ORDER BY attribute_name ASC";

        return $this->db->fetchAll($sql, [$productId]);
    }

    /**
     * Get related products
     */
    public function getRelated($productId, $limit = 8)
    {
        // First try to get products from same category
        $product = $this->find($productId);
        if (!$product) return [];

        $sql = "SELECT * FROM {$this->table}
                WHERE category_id = ? AND id != ? AND status = 'published'
                ORDER BY RAND()
                LIMIT ?";

        $related = $this->db->fetchAll($sql, [$product['category_id'], $productId, $limit]);

        // If not enough, get from other categories
        if (count($related) < $limit) {
            $remaining = $limit - count($related);
            $excludeIds = array_column($related, 'id');
            $excludeIds[] = $productId;

            $sql = "SELECT * FROM {$this->table}
                    WHERE id NOT IN (" . implode(',', array_fill(0, count($excludeIds), '?')) . ")
                    AND status = 'published'
                    ORDER BY RAND()
                    LIMIT ?";

            $params = array_merge($excludeIds, [$remaining]);
            $additional = $this->db->fetchAll($sql, $params);

            $related = array_merge($related, $additional);
        }

        return $related;
    }

    /**
     * Update product views
     */
    public function updateViews($id)
    {
        $sql = "UPDATE {$this->table} SET views = views + 1 WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }

    /**
     * Get low stock products
     */
    public function getLowStock($threshold = 10)
    {
        $sql = "SELECT p.*,
                       SUM(pv.stock_quantity) as total_stock,
                       c.name as category_name
                FROM {$this->table} p
                LEFT JOIN product_variants pv ON p.id = pv.product_id
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'published'
                GROUP BY p.id
                HAVING total_stock <= ?
                ORDER BY total_stock ASC";

        return $this->db->fetchAll($sql, [$threshold]);
    }

    /**
     * Get best selling products
     */
    public function getBestSelling($limit = 10)
    {
        $sql = "SELECT p.*,
                       SUM(oi.quantity) as total_sold,
                       c.name as category_name
                FROM {$this->table} p
                LEFT JOIN order_items oi ON p.id = oi.product_id
                LEFT JOIN orders o ON oi.order_id = o.id
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'published'
                AND o.status IN ('processing', 'shipped', 'delivered')
                GROUP BY p.id
                ORDER BY total_sold DESC
                LIMIT ?";

        return $this->db->fetchAll($sql, [$limit]);
    }

    /**
     * Get products on sale
     */
    public function getOnSale($limit = 10)
    {
        $sql = "SELECT p.*, c.name as category_name,
                       ((p.price - p.sale_price) / p.price * 100) as discount_percent
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'published'
                AND p.sale_price IS NOT NULL
                AND p.sale_price > 0
                AND p.sale_price < p.price
                ORDER BY discount_percent DESC
                LIMIT ?";

        return $this->db->fetchAll($sql, [$limit]);
    }

    /**
     * Generate unique SKU
     */
    public function generateSKU($categoryId = null)
    {
        $prefix = 'PRD';

        if ($categoryId) {
            $category = $this->db->fetchOne("SELECT name FROM categories WHERE id = ?", [$categoryId]);
            if ($category) {
                $prefix = strtoupper(substr($category['name'], 0, 3));
            }
        }

        do {
            $sku = $prefix . date('ymd') . rand(100, 999);
            $exists = $this->findBySKU($sku);
        } while ($exists);

        return $sku;
    }

    /**
     * Calculate total stock for a product
     */
    public function getTotalStock($productId)
    {
        $sql = "SELECT SUM(stock_quantity) as total_stock
                FROM product_variants
                WHERE product_id = ? AND status = 'active'";

        $result = $this->db->fetchOne($sql, [$productId]);
        return $result ? (int)$result['total_stock'] : 0;
    }

    /**
     * Check if product is in stock
     */
    public function isInStock($productId, $variantId = null)
    {
        if ($variantId) {
            $sql = "SELECT stock_quantity FROM product_variants WHERE id = ? AND product_id = ?";
            $result = $this->db->fetchOne($sql, [$variantId, $productId]);
            return $result && $result['stock_quantity'] > 0;
        }

        return $this->getTotalStock($productId) > 0;
    }

    /**
     * Update stock quantity
     */
    public function updateStock($productId, $variantId, $quantity, $operation = 'set')
    {
        if ($operation === 'set') {
            $sql = "UPDATE product_variants SET stock_quantity = ? WHERE id = ? AND product_id = ?";
            $params = [$quantity, $variantId, $productId];
        } elseif ($operation === 'add') {
            $sql = "UPDATE product_variants SET stock_quantity = stock_quantity + ? WHERE id = ? AND product_id = ?";
            $params = [$quantity, $variantId, $productId];
        } elseif ($operation === 'subtract') {
            $sql = "UPDATE product_variants SET stock_quantity = stock_quantity - ? WHERE id = ? AND product_id = ? AND stock_quantity >= ?";
            $params = [$quantity, $variantId, $productId, $quantity];
        }

        return $this->db->execute($sql, $params);
    }

    /**
     * Get price range for filters
     */
    public function getPriceRange()
    {
        $sql = "SELECT MIN(price) as min_price, MAX(price) as max_price
                FROM {$this->table}
                WHERE status = 'published'";

        return $this->db->fetchOne($sql);
    }

    /**
     * Get available filters
     */
    public function getAvailableFilters()
    {
        return [
            'categories' => $this->getActiveCategories(),
            'brands' => $this->getActiveBrands(),
            'price_range' => $this->getPriceRange(),
            'genders' => $this->getAvailableGenders(),
            'seasons' => $this->getAvailableSeasons(),
            'styles' => $this->getAvailableStyles()
        ];
    }

    private function getActiveCategories()
    {
        $sql = "SELECT DISTINCT c.id, c.name
                FROM categories c
                INNER JOIN {$this->table} p ON c.id = p.category_id
                WHERE c.status = 'active' AND p.status = 'published'
                ORDER BY c.name";

        return $this->db->fetchAll($sql);
    }

    private function getActiveBrands()
    {
        $sql = "SELECT DISTINCT b.id, b.name
                FROM brands b
                INNER JOIN {$this->table} p ON b.id = p.brand_id
                WHERE b.status = 'active' AND p.status = 'published'
                ORDER BY b.name";

        return $this->db->fetchAll($sql);
    }

    private function getAvailableGenders()
    {
        $sql = "SELECT DISTINCT gender
                FROM {$this->table}
                WHERE status = 'published' AND gender IS NOT NULL
                ORDER BY gender";

        return array_column($this->db->fetchAll($sql), 'gender');
    }

    private function getAvailableSeasons()
    {
        $sql = "SELECT DISTINCT season
                FROM {$this->table}
                WHERE status = 'published' AND season IS NOT NULL
                ORDER BY season";

        return array_column($this->db->fetchAll($sql), 'season');
    }

    private function getAvailableStyles()
    {
        $sql = "SELECT DISTINCT style
                FROM {$this->table}
                WHERE status = 'published' AND style IS NOT NULL
                ORDER BY style";

        return array_column($this->db->fetchAll($sql), 'style');
    }

    /**
     * Get product statistics
     */
    public function getProductStatistics()
    {
        $sql = "SELECT
                    COUNT(*) as total_products,
                    SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) as active_products,
                    SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft_products,
                    SUM(CASE WHEN status = 'out_of_stock' THEN 1 ELSE 0 END) as out_of_stock_products,
                    SUM(CASE WHEN featured = 1 THEN 1 ELSE 0 END) as featured_products,
                    AVG(price) as average_price,
                    MIN(price) as min_price,
                    MAX(price) as max_price
                FROM {$this->table}";        $stats = $this->db->fetchOne($sql);

        // Get stock statistics
        $stockSql = "SELECT
                        COUNT(DISTINCT p.id) as products_with_stock,
                        SUM(pv.stock_quantity) as total_stock_quantity
                     FROM {$this->table} p
                     INNER JOIN product_variants pv ON p.id = pv.product_id
                     WHERE pv.stock_quantity > 0";        $stockStats = $this->db->fetchOne($stockSql);

        return array_merge($stats ?: [], $stockStats ?: []);
    }

    /**
     * Export products for download
     */
    public function exportProducts($filters = [])
    {
        $products = $this->search('', $filters);

        $export = [];
        foreach ($products as $product) {
            $export[] = [
                'ID' => $product['id'],
                'Tên sản phẩm' => $product['name'],
                'SKU' => $product['sku'],
                'Danh mục' => $product['category_name'],
                'Giá' => number_format($product['price']),
                'Giá sale' => $product['sale_price'] ? number_format($product['sale_price']) : '',
                'Tồn kho' => $product['total_stock'],
                'Trạng thái' => $product['status'] === 'published' ? 'Hoạt động' : 'Không hoạt động',
                'Nổi bật' => $product['featured'] ? 'Có' : 'Không',
                'Ngày tạo' => $product['created_at'],
                'Cập nhật cuối' => $product['updated_at']
            ];
        }

        return $export;
    }

    // ==================== VARIANT SUPPORT METHODS ====================

    /**
     * Check if product has variants
     */
    public function hasVariants()
    {
        if (isset($this->has_variants)) {
            return (bool) $this->has_variants;
        }

        $sql = "SELECT has_variants FROM products WHERE id = :id";
        $result = $this->db->fetchOne($sql, ['id' => $this->id ?? 0]);
        return $result ? (bool) $result['has_variants'] : false;
    }

    /**
     * Get product variants by status
     */
    public function getVariantsByStatus($activeOnly = true)
    {
        $whereClause = $activeOnly ? "AND status = 'active'" : '';
        $sql = "SELECT * FROM product_variants WHERE product_id = :product_id {$whereClause} ORDER BY sort_order";
        return $this->db->fetchAll($sql, ['product_id' => $this->id ?? 0]);
    }

    /**
     * Get variants with attributes
     */
    public function getVariantsWithAttributes()
    {
        return ProductVariant::getProductVariantsWithDetails($this->id ?? 0);
    }

    /**
     * Get available attributes for this product (from existing variants)
     */
    public function getAvailableAttributes()
    {
        $sql = "
            SELECT DISTINCT pa.id, pa.name, pa.type, pa.slug,
                   pav.id as value_id, pav.value, pav.color_code, pav.image as attribute_image
            FROM product_variants pv
            JOIN product_variant_attributes pva ON pv.id = pva.variant_id
            JOIN product_attribute_values pav ON pva.attribute_value_id = pav.id
            JOIN product_attributes pa ON pav.attribute_id = pa.id
            WHERE pv.product_id = :product_id AND pv.status = 'active'
            ORDER BY pa.sort_order, pav.sort_order
        ";

        $attributes = $this->db->fetchAll($sql, ['product_id' => $this->id ?? 0]);

        // Group by attribute type
        $grouped = [];
        foreach ($attributes as $attr) {
            if (!isset($grouped[$attr['type']])) {
                $grouped[$attr['type']] = [
                    'id' => $attr['id'],
                    'name' => $attr['name'],
                    'type' => $attr['type'],
                    'slug' => $attr['slug'],
                    'values' => []
                ];
            }

            $grouped[$attr['type']]['values'][] = [
                'id' => $attr['value_id'],
                'value' => $attr['value'],
                'color_code' => $attr['color_code'],
                'image' => $attr['attribute_image']
            ];
        }

        return array_values($grouped);
    }

    /**
     * Get total stock from all variants for current product instance
     */
    public function getCurrentTotalStock()
    {
        if ($this->hasVariants()) {
            $sql = "SELECT SUM(stock_quantity) as total_stock FROM product_variants WHERE product_id = :product_id AND status = 'active'";
            $result = $this->db->fetchOne($sql, ['product_id' => $this->id ?? 0]);
            return $result ? (int) $result['total_stock'] : 0;
        }

        return (int) ($this->stock_quantity ?? 0);
    }

    /**
     * Get price range for variants of this product
     */
    public function getVariantPriceRange()
    {
        if (!$this->hasVariants()) {
            return [
                'min_price' => $this->sale_price ?: $this->price,
                'max_price' => $this->sale_price ?: $this->price,
                'has_range' => false
            ];
        }

        $sql = "
            SELECT
                MIN(COALESCE(sale_price, price)) as min_price,
                MAX(COALESCE(sale_price, price)) as max_price
            FROM product_variants
            WHERE product_id = :product_id AND status = 'active'
        ";

        $result = $this->db->fetchOne($sql, ['product_id' => $this->id ?? 0]);

        if (!$result) {
            return [
                'min_price' => $this->sale_price ?: $this->price,
                'max_price' => $this->sale_price ?: $this->price,
                'has_range' => false
            ];
        }

        return [
            'min_price' => $result['min_price'] ?: ($this->sale_price ?: $this->price),
            'max_price' => $result['max_price'] ?: ($this->sale_price ?: $this->price),
            'has_range' => $result['min_price'] != $result['max_price']
        ];
    }

    /**
     * Find variant by attribute combination
     */
    public function findVariantByAttributes($attributeValueIds)
    {
        if (empty($attributeValueIds)) {
            return null;
        }

        // Sort the attribute value IDs for consistent comparison
        sort($attributeValueIds);
        $attributeValueIdsStr = implode(',', $attributeValueIds);

        $sql = "
            SELECT pv.*,
                   GROUP_CONCAT(pva.attribute_value_id ORDER BY pva.attribute_value_id) as variant_attributes
            FROM product_variants pv
            JOIN product_variant_attributes pva ON pv.id = pva.variant_id
            WHERE pv.product_id = :product_id AND pv.status = 'active'
            GROUP BY pv.id
            HAVING variant_attributes = :attribute_values
        ";

        return $this->db->fetchOne($sql, [
            'product_id' => $this->id ?? 0,
            'attribute_values' => $attributeValueIdsStr
        ]);
    }

    /**
     * Enable variants for this product
     */
    public function enableVariants()
    {
        $sql = "UPDATE products SET has_variants = 1 WHERE id = :id";
        return $this->db->execute($sql, ['id' => $this->id ?? 0]);
    }

    /**
     * Disable variants for this product
     */
    public function disableVariants()
    {
        $sql = "UPDATE products SET has_variants = 0 WHERE id = :id";
        return $this->db->execute($sql, ['id' => $this->id ?? 0]);
    }

    /**
     * Get variant by ID
     * @param int $variantId
     * @return array|null
     */
    public function getVariantById($variantId)
    {
        $sql = "SELECT * FROM product_variants WHERE id = ?";
        return $this->db->query($sql, [$variantId])->fetch();
    }
}
