<?php

require_once __DIR__ . '/../../core/ApiController.php';

/**
 * Product API Controller
 * Handles all product-related API endpoints
 */
class ProductApiController extends ApiController
{
    /**
     * Get all products with filtering and pagination
     * GET /api/products
     */
    public function index()
    {
        $this->checkMethod(['GET']);

        try {
            // Get pagination parameters
            $pagination = $this->getPaginationParams();

            // Get filter parameters
            $filters = $this->getFilterParams([
                'category_id', 'brand_id', 'status', 'featured',
                'min_price', 'max_price', 'search'
            ]);

            // Get sort parameters
            $sort = $this->getSortParams([
                'id', 'name', 'price', 'created_at', 'updated_at'
            ], 'created_at');

            // Build query
            $query = "SELECT p.*, c.name as category_name, b.name as brand_name
                     FROM products p
                     LEFT JOIN categories c ON p.category_id = c.id
                     LEFT JOIN brands b ON p.brand_id = b.id
                     WHERE 1=1";

            $params = [];

            // Apply filters
            if (!empty($filters['category_id'])) {
                $query .= " AND p.category_id = ?";
                $params[] = $filters['category_id'];
            }

            if (!empty($filters['brand_id'])) {
                $query .= " AND p.brand_id = ?";
                $params[] = $filters['brand_id'];
            }

            if (!empty($filters['status'])) {
                $query .= " AND p.status = ?";
                $params[] = $filters['status'];
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

            // Get total count for pagination
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

            // Format product data
            $formattedProducts = array_map([$this, 'formatProduct'], $products);

            // Return paginated response
            ApiResponse::paginated(
                $formattedProducts,
                $pagination['page'],
                $totalPages,
                $totalItems,
                $pagination['limit']
            );

        } catch (Exception $e) {
            error_log('Products API Error: ' . $e->getMessage());
            ApiResponse::serverError('Failed to retrieve products');
        }
    }

    /**
     * Get single product by ID
     * GET /api/products/{id}
     */
    public function show($params = [])
    {
        $this->checkMethod(['GET']);

        $productId = $params['id'] ?? null;

        if (!$productId) {
            ApiResponse::error('Product ID is required', 400);
        }

        try {
            $query = "SELECT p.*, c.name as category_name, b.name as brand_name
                     FROM products p
                     LEFT JOIN categories c ON p.category_id = c.id
                     LEFT JOIN brands b ON p.brand_id = b.id
                     WHERE p.id = ?";

            $stmt = $this->db->prepare($query);
            $stmt->execute([$productId]);
            $product = $stmt->fetch();

            if (!$product) {
                ApiResponse::notFound('Product not found');
            }

            // Get product images
            $imagesQuery = "SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order";
            $imagesStmt = $this->db->prepare($imagesQuery);
            $imagesStmt->execute([$productId]);
            $images = $imagesStmt->fetchAll();

            // Get product variants
            $variantsQuery = "SELECT * FROM product_variants WHERE product_id = ?";
            $variantsStmt = $this->db->prepare($variantsQuery);
            $variantsStmt->execute([$productId]);
            $variants = $variantsStmt->fetchAll();

            // Get recent reviews
            $reviewsQuery = "SELECT r.*, u.full_name as customer_name
                           FROM reviews r
                           LEFT JOIN users u ON r.user_id = u.id
                           WHERE r.product_id = ? AND r.status = 'approved'
                           ORDER BY r.created_at DESC LIMIT 5";
            $reviewsStmt = $this->db->prepare($reviewsQuery);
            $reviewsStmt->execute([$productId]);
            $reviews = $reviewsStmt->fetchAll();

            // Format product data
            $formattedProduct = $this->formatProduct($product);
            $formattedProduct['images'] = $images;
            $formattedProduct['variants'] = $variants;
            $formattedProduct['reviews'] = $reviews;

            ApiResponse::success($formattedProduct, 'Product retrieved successfully');

        } catch (Exception $e) {
            error_log('Product API Error: ' . $e->getMessage());
            ApiResponse::serverError('Failed to retrieve product');
        }
    }

    /**
     * Create new product (Admin only)
     * POST /api/products
     */
    public function store()
    {
        $this->checkMethod(['POST']);

        // Basic validation
        $required = ['name', 'description', 'price', 'category_id'];
        $errors = $this->validateRequired($required);

        if ($errors) {
            ApiResponse::validationError($errors);
        }

        try {
            $data = $this->sanitize($this->requestData);

            $query = "INSERT INTO products (name, description, price, sale_price, category_id, brand_id,
                     sku, stock_quantity, status, featured, meta_title, meta_description, created_at)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

            $stmt = $this->db->prepare($query);
            $result = $stmt->execute([
                $data['name'],
                $data['description'],
                $data['price'],
                $data['sale_price'] ?? null,
                $data['category_id'],
                $data['brand_id'] ?? null,
                $data['sku'] ?? $this->generateSku(),
                $data['stock_quantity'] ?? 0,
                $data['status'] ?? 'published',
                $data['featured'] ?? 0,
                $data['meta_title'] ?? $data['name'],
                $data['meta_description'] ?? substr($data['description'], 0, 160)
            ]);

            if ($result) {
                $productId = $this->db->lastInsertId();

                // Get created product
                $createdProduct = $this->getProductById($productId);

                ApiResponse::success(
                    $this->formatProduct($createdProduct),
                    'Product created successfully',
                    201
                );
            } else {
                ApiResponse::serverError('Failed to create product');
            }

        } catch (Exception $e) {
            error_log('Product Creation Error: ' . $e->getMessage());
            ApiResponse::serverError('Failed to create product');
        }
    }

    /**
     * Format product data for API response
     */
    private function formatProduct($product)
    {
        return [
            'id' => (int)$product['id'],
            'name' => $product['name'],
            'slug' => $product['slug'] ?? null,
            'description' => $product['description'],
            'short_description' => $product['short_description'] ?? null,
            'price' => (float)$product['price'],
            'sale_price' => $product['sale_price'] ? (float)$product['sale_price'] : null,
            'currency' => 'VND',
            'sku' => $product['sku'],
            'stock_quantity' => (int)$product['stock_quantity'],
            'stock_status' => $product['stock_quantity'] > 0 ? 'in_stock' : 'out_of_stock',
            'status' => $product['status'],
            'featured' => (bool)$product['featured'],
            'category' => [
                'id' => (int)$product['category_id'],
                'name' => $product['category_name'] ?? null
            ],
            'brand' => $product['brand_id'] ? [
                'id' => (int)$product['brand_id'],
                'name' => $product['brand_name'] ?? null
            ] : null,
            'featured_image' => $product['featured_image'] ? [
                'url' => '/uploads/products/' . $product['featured_image'],
                'alt' => $product['name']
            ] : null,
            'rating' => [
                'average' => $product['average_rating'] ? round((float)$product['average_rating'], 1) : 0,
                'count' => (int)($product['review_count'] ?? 0)
            ],
            'seo' => [
                'meta_title' => $product['meta_title'],
                'meta_description' => $product['meta_description']
            ],
            'dates' => [
                'created_at' => $product['created_at'],
                'updated_at' => $product['updated_at']
            ]
        ];
    }

    /**
     * Generate unique SKU
     */
    private function generateSku()
    {
        return 'PRD-' . strtoupper(uniqid());
    }

    /**
     * Get product by ID
     */
    private function getProductById($id)
    {
        $query = "SELECT p.*, c.name as category_name, b.name as brand_name
                 FROM products p
                 LEFT JOIN categories c ON p.category_id = c.id
                 LEFT JOIN brands b ON p.brand_id = b.id
                 WHERE p.id = ?";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
