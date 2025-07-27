<?php

require_once __DIR__ . '/../../core/ApiController.php';

/**
 * Category API Controller
 * Handles all category-related API endpoints
 */
class CategoryApiController extends ApiController
{
    /**
     * Get all categories
     * GET /api/categories
     */
    public function index()
    {
        $this->checkMethod(['GET']);

        try {
            // Get parameters
            $includeProducts = $this->requestData['include_products'] ?? false;
            $parentId = $this->requestData['parent_id'] ?? null;

            $query = "SELECT * FROM categories WHERE 1=1";
            $params = [];

            // Filter by parent ID if specified
            if ($parentId !== null) {
                if ($parentId === '0' || $parentId === 0) {
                    $query .= " AND parent_id IS NULL";
                } else {
                    $query .= " AND parent_id = ?";
                    $params[] = $parentId;
                }
            }

            $query .= " ORDER BY sort_order ASC, name ASC";

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $categories = $stmt->fetchAll();

            // Format categories
            $formattedCategories = [];
            foreach ($categories as $category) {
                $formattedCategory = $this->formatCategory($category);

                // Include products count if requested
                if ($includeProducts) {
                    $formattedCategory['products_count'] = $this->getProductsCount($category['id']);
                }

                // Get children categories
                $formattedCategory['children'] = $this->getChildrenCategories($category['id']);

                $formattedCategories[] = $formattedCategory;
            }

            ApiResponse::success($formattedCategories, 'Categories retrieved successfully');

        } catch (Exception $e) {
            error_log('Categories API Error: ' . $e->getMessage());
            ApiResponse::serverError('Failed to retrieve categories');
        }
    }

    /**
     * Get single category by ID
     * GET /api/categories/{id}
     */
    public function show($params = [])
    {
        $this->checkMethod(['GET']);

        $categoryId = $params['id'] ?? null;

        if (!$categoryId) {
            ApiResponse::error('Category ID is required', 400);
        }

        try {
            $query = "SELECT * FROM categories WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$categoryId]);
            $category = $stmt->fetch();

            if (!$category) {
                ApiResponse::notFound('Category not found');
            }

            // Format category
            $formattedCategory = $this->formatCategory($category);

            // Get children categories
            $formattedCategory['children'] = $this->getChildrenCategories($categoryId);

            // Get products count
            $formattedCategory['products_count'] = $this->getProductsCount($categoryId);

            // Get parent category if exists
            if ($category['parent_id']) {
                $parentQuery = "SELECT * FROM categories WHERE id = ?";
                $parentStmt = $this->db->prepare($parentQuery);
                $parentStmt->execute([$category['parent_id']]);
                $parent = $parentStmt->fetch();

                if ($parent) {
                    $formattedCategory['parent'] = $this->formatCategory($parent);
                }
            }

            ApiResponse::success($formattedCategory, 'Category retrieved successfully');

        } catch (Exception $e) {
            error_log('Category API Error: ' . $e->getMessage());
            ApiResponse::serverError('Failed to retrieve category');
        }
    }

    /**
     * Get products in category
     * GET /api/categories/{id}/products
     */
    public function products($params = [])
    {
        $this->checkMethod(['GET']);

        $categoryId = $params['id'] ?? null;

        if (!$categoryId) {
            ApiResponse::error('Category ID is required', 400);
        }

        try {
            // Check if category exists
            $categoryQuery = "SELECT * FROM categories WHERE id = ?";
            $categoryStmt = $this->db->prepare($categoryQuery);
            $categoryStmt->execute([$categoryId]);
            $category = $categoryStmt->fetch();

            if (!$category) {
                ApiResponse::notFound('Category not found');
            }

            // Get pagination parameters
            $pagination = $this->getPaginationParams();

            // Get filter parameters
            $filters = $this->getFilterParams([
                'brand_id', 'status', 'featured',
                'min_price', 'max_price', 'search'
            ]);

            // Get sort parameters
            $sort = $this->getSortParams([
                'id', 'name', 'price', 'created_at', 'updated_at'
            ], 'created_at');

            // Build query - include subcategories
            $query = "SELECT p.*, c.name as category_name, b.name as brand_name
                     FROM products p
                     LEFT JOIN categories c ON p.category_id = c.id
                     LEFT JOIN brands b ON p.brand_id = b.id
                     WHERE p.category_id IN (
                         SELECT id FROM categories
                         WHERE id = ? OR parent_id = ?
                     )";

            $params = [$categoryId, $categoryId];

            // Apply additional filters
            if (!empty($filters['brand_id'])) {
                $query .= " AND p.brand_id = ?";
                $params[] = $filters['brand_id'];
            }

            if (!empty($filters['status'])) {
                $query .= " AND p.status = ?";
                $params[] = $filters['status'];
            } else {
                $query .= " AND p.status = 'published'";
            }

            if (!empty($filters['featured'])) {
                $query .= " AND p.featured = ?";
                $params[] = $filters['featured'];
            }

            if (!empty($filters['min_price'])) {
                $query .= " AND p.price >= ?";
                $params[] = $filters['min_price'];
            }

            if (!empty($filters['max_price'])) {
                $query .= " AND p.price <= ?";
                $params[] = $filters['max_price'];
            }

            if (!empty($filters['search'])) {
                $query .= " AND (p.name LIKE ? OR p.description LIKE ?)";
                $searchTerm = '%' . $filters['search'] . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }

            // Get total count
            $countQuery = str_replace(
                "SELECT p.*, c.name as category_name, b.name as brand_name",
                "SELECT COUNT(*) as total",
                $query
            );

            $countStmt = $this->db->prepare($countQuery);
            $countStmt->execute($params);
            $totalItems = $countStmt->fetch()['total'];
            $totalPages = ceil($totalItems / $pagination['limit']);

            // Apply sorting and pagination
            $query .= " ORDER BY p.{$sort['sort_by']} {$sort['sort_order']}";
            $query .= " LIMIT {$pagination['limit']} OFFSET {$pagination['offset']}";

            // Execute query
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $products = $stmt->fetchAll();

            // Format products (reuse from ProductApiController)
            $formattedProducts = array_map([$this, 'formatProduct'], $products);

            // Return response with category info
            $responseData = [
                'category' => $this->formatCategory($category),
                'products' => $formattedProducts,
            ];

            ApiResponse::paginated(
                $responseData,
                $pagination['page'],
                $totalPages,
                $totalItems,
                $pagination['limit']
            );

        } catch (Exception $e) {
            error_log('Category Products API Error: ' . $e->getMessage());
            ApiResponse::serverError('Failed to retrieve category products');
        }
    }

    /**
     * Format category data for API response
     */
    private function formatCategory($category)
    {
        return [
            'id' => (int)$category['id'],
            'name' => $category['name'],
            'slug' => $category['slug'] ?? null,
            'description' => $category['description'] ?? null,
            'parent_id' => $category['parent_id'] ? (int)$category['parent_id'] : null,
            'sort_order' => (int)$category['sort_order'],
            'status' => $category['status'] ?? 'active',
            'image' => $category['image'] ? [
                'url' => '/uploads/categories/' . $category['image'],
                'alt' => $category['name']
            ] : null,
            'seo' => [
                'meta_title' => $category['meta_title'],
                'meta_description' => $category['meta_description']
            ],
            'dates' => [
                'created_at' => $category['created_at'],
                'updated_at' => $category['updated_at']
            ]
        ];
    }

    /**
     * Get children categories
     */
    private function getChildrenCategories($parentId)
    {
        $query = "SELECT * FROM categories WHERE parent_id = ? ORDER BY sort_order ASC, name ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$parentId]);
        $children = $stmt->fetchAll();

        return array_map([$this, 'formatCategory'], $children);
    }

    /**
     * Get products count for category
     */
    private function getProductsCount($categoryId)
    {
        $query = "SELECT COUNT(*) as count FROM products
                 WHERE category_id IN (
                     SELECT id FROM categories
                     WHERE id = ? OR parent_id = ?
                 ) AND status = 'published'";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$categoryId, $categoryId]);
        $result = $stmt->fetch();

        return (int)$result['count'];
    }

    /**
     * Format product data (simplified version)
     */
    private function formatProduct($product)
    {
        return [
            'id' => (int)$product['id'],
            'name' => $product['name'],
            'slug' => $product['slug'] ?? null,
            'price' => (float)$product['price'],
            'sale_price' => $product['sale_price'] ? (float)$product['sale_price'] : null,
            'currency' => 'VND',
            'stock_quantity' => (int)$product['stock_quantity'],
            'stock_status' => $product['stock_quantity'] > 0 ? 'in_stock' : 'out_of_stock',
            'featured' => (bool)$product['featured'],
            'featured_image' => $product['featured_image'] ? [
                'url' => '/uploads/products/' . $product['featured_image'],
                'alt' => $product['name']
            ] : null,
            'category' => [
                'id' => (int)$product['category_id'],
                'name' => $product['category_name'] ?? null
            ],
            'brand' => $product['brand_id'] ? [
                'id' => (int)$product['brand_id'],
                'name' => $product['brand_name'] ?? null
            ] : null
        ];
    }
}
