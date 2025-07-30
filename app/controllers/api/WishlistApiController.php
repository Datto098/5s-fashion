<?php

require_once __DIR__ . '/../../core/ApiController.php';
require_once __DIR__ . '/../../core/Database.php';

/**
 * Wishlist API Controller
 * Handles user wishlist functionality
 */
class WishlistApiController extends ApiController
{
    private $sessionKey = 'wishlist';

    public function __construct()
    {
        parent::__construct();
        // Database is already initialized in parent
    }

    /**
     * Get wishlist items
     * GET /api/wishlist
     */
    public function index()
    {
        $this->checkMethod(['GET']);

        try {
            $wishlist = $this->getWishlist();
            $wishlistData = $this->formatWishlistResponse($wishlist);

            ApiResponse::success($wishlistData, 'Wishlist retrieved successfully');

        } catch (Exception $e) {
            error_log('Wishlist API Error: ' . $e->getMessage());
            ApiResponse::serverError('Failed to retrieve wishlist');
        }
    }

    /**
     * Add item to wishlist
     * POST /api/wishlist/add
     */
    public function add()
    {
        $this->checkMethod(['POST']);

        // Validate required fields
        $required = ['product_id'];
        $errors = $this->validateRequired($required);

        if ($errors) {
            ApiResponse::validationError($errors);
        }

        try {
            $productId = (int)$this->requestData['product_id'];

            // Check if product exists
            $product = $this->getProduct($productId);
            if (!$product) {
                ApiResponse::notFound('Product not found');
            }

            // Check product status
            if ($product['status'] !== 'published') {
                ApiResponse::error('Product is not available', 400);
            }

            $wishlist = $this->getWishlist();

            // Check if already in wishlist
            if (in_array($productId, $wishlist)) {
                ApiResponse::error('Product is already in wishlist', 400);
            }

            // Add to wishlist
            $wishlist[] = $productId;
            $this->saveWishlist($wishlist);

            $wishlistData = $this->formatWishlistResponse($wishlist);

            ApiResponse::success($wishlistData, 'Item added to wishlist successfully');

        } catch (Exception $e) {
            error_log('Wishlist Add Error: ' . $e->getMessage());
            ApiResponse::serverError('Failed to add item to wishlist');
        }
    }

    /**
     * Remove item from wishlist
     * DELETE /api/wishlist/{productId}
     */
    public function remove($params = [])
    {
        $this->checkMethod(['DELETE']);

        $productId = isset($params['id']) ? (int)$params['id'] : null;
        if (!$productId) {
            ApiResponse::error('Product ID is required', 400);
        }

        try {
            $wishlist = $this->getWishlist();

            $key = array_search($productId, $wishlist);
            if ($key === false) {
                ApiResponse::notFound('Product not found in wishlist');
            }

            unset($wishlist[$key]);
            $wishlist = array_values($wishlist); // Reindex array

            $this->saveWishlist($wishlist);

            $wishlistData = $this->formatWishlistResponse($wishlist);

            ApiResponse::success($wishlistData, 'Item removed from wishlist successfully');

        } catch (Exception $e) {
            error_log('Wishlist Remove Error: ' . $e->getMessage());
            ApiResponse::serverError('Failed to remove item from wishlist');
        }
    }

    /**
     * Clear entire wishlist
     * DELETE /api/wishlist/clear
     */
    public function clear()
    {
        $this->checkMethod(['DELETE']);

        try {
            $this->saveWishlist([]);

            ApiResponse::success([
                'items' => [],
                'count' => 0
            ], 'Wishlist cleared successfully');

        } catch (Exception $e) {
            error_log('Wishlist Clear Error: ' . $e->getMessage());
            ApiResponse::serverError('Failed to clear wishlist');
        }
    }

    /**
     * Toggle item in wishlist
     * POST /api/wishlist/toggle
     */
    public function toggle()
    {
        $this->checkMethod(['POST']);

        // Validate required fields
        $required = ['product_id'];
        $errors = $this->validateRequired($required);

        if ($errors) {
            ApiResponse::validationError($errors);
        }

        try {
            $productId = (int)$this->requestData['product_id'];

            // Check if product exists
            $product = $this->getProduct($productId);
            if (!$product) {
                ApiResponse::notFound('Product not found');
            }

            $wishlist = $this->getWishlist();
            $key = array_search($productId, $wishlist);

            if ($key !== false) {
                // Remove from wishlist
                unset($wishlist[$key]);
                $wishlist = array_values($wishlist);
                $action = 'removed';
            } else {
                // Add to wishlist
                $wishlist[] = $productId;
                $action = 'added';
            }

            $this->saveWishlist($wishlist);

            $wishlistData = $this->formatWishlistResponse($wishlist);
            $wishlistData['action'] = $action;
            $wishlistData['is_in_wishlist'] = ($action === 'added');

            ApiResponse::success($wishlistData, "Product {$action} to/from wishlist successfully");

        } catch (Exception $e) {
            error_log('Wishlist Toggle Error: ' . $e->getMessage());
            ApiResponse::serverError('Failed to toggle wishlist item');
        }
    }

    /**
     * Check if product is in wishlist
     * GET /api/wishlist/check/{productId}
     */
    public function check($params = [])
    {
        $this->checkMethod(['GET']);

        $productId = isset($params['id']) ? (int)$params['id'] : null;
        if (!$productId) {
            ApiResponse::error('Product ID is required', 400);
        }

        try {
            $wishlist = $this->getWishlist();
            $isInWishlist = in_array($productId, $wishlist);

            ApiResponse::success([
                'product_id' => $productId,
                'is_in_wishlist' => $isInWishlist,
                'wishlist_count' => count($wishlist)
            ], 'Wishlist status checked successfully');

        } catch (Exception $e) {
            error_log('Wishlist Check Error: ' . $e->getMessage());
            ApiResponse::serverError('Failed to check wishlist status');
        }
    }

    /**
     * Move item from wishlist to cart
     * POST /api/wishlist/move-to-cart
     */
    public function moveToCart()
    {
        $this->checkMethod(['POST']);

        // Validate required fields
        $required = ['product_id'];
        $errors = $this->validateRequired($required);

        if ($errors) {
            ApiResponse::validationError($errors);
        }

        try {
            $productId = (int)$this->requestData['product_id'];
            $quantity = isset($this->requestData['quantity']) ? (int)$this->requestData['quantity'] : 1;

            // Check if product is in wishlist
            $wishlist = $this->getWishlist();
            $key = array_search($productId, $wishlist);

            if ($key === false) {
                ApiResponse::notFound('Product not found in wishlist');
            }

            // Check product availability and stock
            $product = $this->getProduct($productId);
            if (!$product) {
                ApiResponse::notFound('Product not found');
            }

            if ($product['status'] !== 'published') {
                ApiResponse::error('Product is not available', 400);
            }

            $availableStock = (int)$product['stock_quantity'];
            if ($availableStock < $quantity) {
                ApiResponse::error("Only {$availableStock} items available in stock", 400);
            }

            // Add to cart (using session)
            $cartSessionKey = 'shopping_cart';
            $cart = $_SESSION[$cartSessionKey] ?? [];
            $cartItemKey = "p{$productId}";

            if (isset($cart[$cartItemKey])) {
                $newQuantity = $cart[$cartItemKey]['quantity'] + $quantity;
                if ($availableStock < $newQuantity) {
                    ApiResponse::error("Cannot add {$quantity} more items. Only {$availableStock} available in stock", 400);
                }
                $cart[$cartItemKey]['quantity'] = $newQuantity;
                $cart[$cartItemKey]['updated_at'] = date('Y-m-d H:i:s');
            } else {
                $cart[$cartItemKey] = [
                    'product_id' => $productId,
                    'variant_id' => null,
                    'quantity' => $quantity,
                    'added_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
            }

            $_SESSION[$cartSessionKey] = $cart;

            // Remove from wishlist
            unset($wishlist[$key]);
            $wishlist = array_values($wishlist);
            $this->saveWishlist($wishlist);

            ApiResponse::success([
                'moved_product' => [
                    'id' => $productId,
                    'name' => $product['name'],
                    'quantity' => $quantity
                ],
                'wishlist_count' => count($wishlist),
                'cart_updated' => true
            ], 'Product moved from wishlist to cart successfully');

        } catch (Exception $e) {
            error_log('Wishlist Move to Cart Error: ' . $e->getMessage());
            ApiResponse::serverError('Failed to move item from wishlist to cart');
        }
    }

    /**
     * Get wishlist from session
     */
    private function getWishlist()
    {
        return $_SESSION[$this->sessionKey] ?? [];
    }

    /**
     * Save wishlist to session
     */
    private function saveWishlist($wishlist)
    {
        $_SESSION[$this->sessionKey] = $wishlist;
    }

    /**
     * Get product details
     */
    private function getProduct($productId)
    {
        $query = "SELECT * FROM products WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$productId]);
        return $stmt->fetch();
    }

    /**
     * Format wishlist response with product details
     */
    private function formatWishlistResponse($wishlist)
    {
        $items = [];

        foreach ($wishlist as $productId) {
            $product = $this->getProductDetails($productId);
            if (!$product) {
                continue; // Skip if product no longer exists
            }

            // Get category info
            $category = null;
            if ($product['category_id']) {
                $categoryQuery = "SELECT id, name, slug FROM categories WHERE id = ?";
                $stmt = $this->db->prepare($categoryQuery);
                $stmt->execute([$product['category_id']]);
                $category = $stmt->fetch();
            }

            // Get brand info
            $brand = null;
            if ($product['brand_id']) {
                $brandQuery = "SELECT id, name, slug FROM brands WHERE id = ?";
                $stmt = $this->db->prepare($brandQuery);
                $stmt->execute([$product['brand_id']]);
                $brand = $stmt->fetch();
            }

            // Calculate discount if sale price exists
            $discount = null;
            if ($product['sale_price'] && $product['price'] > $product['sale_price']) {
                $discount = [
                    'amount' => $product['price'] - $product['sale_price'],
                    'percentage' => round((($product['price'] - $product['sale_price']) / $product['price']) * 100, 2)
                ];
            }

            $items[] = [
                'product' => [
                    'id' => (int)$product['id'],
                    'name' => $product['name'],
                    'slug' => $product['slug'],
                    'sku' => $product['sku'],
                    'description' => $product['description'],
                    'short_description' => $product['short_description'],
                    'featured_image' => $product['featured_image'] ? [
                        'url' => '/uploads/products/' . $product['featured_image'],
                        'alt' => $product['name']
                    ] : null,
                    'price' => [
                        'regular' => (float)$product['price'],
                        'sale' => $product['sale_price'] ? (float)$product['sale_price'] : null,
                        'final' => $product['sale_price'] ? (float)$product['sale_price'] : (float)$product['price'],
                        'currency' => 'VND'
                    ],
                    'discount' => $discount,
                    'stock' => [
                        'quantity' => (int)$product['stock_quantity'],
                        'status' => $this->getStockStatus($product['stock_quantity'])
                    ],
                    'category' => $category ? [
                        'id' => (int)$category['id'],
                        'name' => $category['name'],
                        'slug' => $category['slug']
                    ] : null,
                    'brand' => $brand ? [
                        'id' => (int)$brand['id'],
                        'name' => $brand['name'],
                        'slug' => $brand['slug']
                    ] : null,
                    'status' => $product['status'],
                    'is_featured' => (bool)$product['is_featured']
                ],
                'added_at' => date('Y-m-d H:i:s') // Could be stored separately in the future
            ];
        }

        return [
            'items' => $items,
            'count' => count($items)
        ];
    }

    /**
     * Get product details for wishlist
     */
    private function getProductDetails($productId)
    {
        $query = "SELECT id, name, slug, sku, description, short_description, price, sale_price,
                        featured_image, stock_quantity, category_id, brand_id, status, is_featured,
                        created_at, updated_at
                 FROM products WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$productId]);
        return $stmt->fetch();
    }

    /**
     * Get stock status
     */
    private function getStockStatus($stockQuantity)
    {
        $stock = (int)$stockQuantity;

        if ($stock <= 0) {
            return 'out_of_stock';
        } elseif ($stock <= 5) {
            return 'low_stock';
        } else {
            return 'in_stock';
        }
    }
}
