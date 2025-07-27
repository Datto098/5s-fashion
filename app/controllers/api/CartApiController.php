<?php

require_once __DIR__ . '/../../core/ApiController.php';

/**
 * Cart API Controller
 * Handles shopping cart functionality
 */
class CartApiController extends ApiController
{
    private $sessionKey = 'shopping_cart';

    /**
     * Get cart contents
     * GET /api/cart
     */
    public function index()
    {
        $this->checkMethod(['GET']);

        try {
            $cart = $this->getCart();
            $cartData = $this->formatCartResponse($cart);

            ApiResponse::success($cartData, 'Cart retrieved successfully');

        } catch (Exception $e) {
            error_log('Cart API Error: ' . $e->getMessage());
            ApiResponse::serverError('Failed to retrieve cart');
        }
    }

    /**
     * Add item to cart
     * POST /api/cart/add
     */
    public function add()
    {
        $this->checkMethod(['POST']);

        // Validate required fields
        $required = ['product_id', 'quantity'];
        $errors = $this->validateRequired($required);

        if ($errors) {
            ApiResponse::validationError($errors);
        }

        try {
            $productId = (int)$this->requestData['product_id'];
            $quantity = (int)$this->requestData['quantity'];
            $variantId = isset($this->requestData['variant_id']) ? (int)$this->requestData['variant_id'] : null;

            // Validate quantity
            if ($quantity <= 0) {
                ApiResponse::error('Quantity must be greater than 0', 400);
            }

            // Check if product exists
            $product = $this->getProduct($productId);
            if (!$product) {
                ApiResponse::notFound('Product not found');
            }

            // Check product status
            if ($product['status'] !== 'published') {
                ApiResponse::error('Product is not available', 400);
            }

            // Check stock availability
            $availableStock = $this->getAvailableStock($productId, $variantId);
            if ($availableStock < $quantity) {
                ApiResponse::error("Only {$availableStock} items available in stock", 400);
            }

            // Add to cart
            $cart = $this->getCart();
            $cartItemKey = $this->generateCartItemKey($productId, $variantId);

            if (isset($cart[$cartItemKey])) {
                // Update existing item
                $newQuantity = $cart[$cartItemKey]['quantity'] + $quantity;

                // Check total quantity against stock
                if ($availableStock < $newQuantity) {
                    ApiResponse::error("Cannot add {$quantity} more items. Only {$availableStock} available in stock", 400);
                }

                $cart[$cartItemKey]['quantity'] = $newQuantity;
                $cart[$cartItemKey]['updated_at'] = date('Y-m-d H:i:s');
            } else {
                // Add new item
                $cart[$cartItemKey] = [
                    'product_id' => $productId,
                    'variant_id' => $variantId,
                    'quantity' => $quantity,
                    'added_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
            }

            $this->saveCart($cart);
            $cartData = $this->formatCartResponse($cart);

            ApiResponse::success($cartData, 'Item added to cart successfully');

        } catch (Exception $e) {
            error_log('Cart Add Error: ' . $e->getMessage());
            ApiResponse::serverError('Failed to add item to cart');
        }
    }

    /**
     * Update cart item quantity
     * PUT /api/cart/{cartItemKey}
     */
    public function update($params = [])
    {
        $this->checkMethod(['PUT']);

        $cartItemKey = $params['id'] ?? null;
        if (!$cartItemKey) {
            ApiResponse::error('Cart item key is required', 400);
        }

        // Validate quantity
        if (!isset($this->requestData['quantity'])) {
            ApiResponse::error('Quantity is required', 400);
        }

        $quantity = (int)$this->requestData['quantity'];
        if ($quantity <= 0) {
            ApiResponse::error('Quantity must be greater than 0', 400);
        }

        try {
            $cart = $this->getCart();

            if (!isset($cart[$cartItemKey])) {
                ApiResponse::notFound('Cart item not found');
            }

            $cartItem = $cart[$cartItemKey];

            // Check stock availability
            $availableStock = $this->getAvailableStock($cartItem['product_id'], $cartItem['variant_id']);
            if ($availableStock < $quantity) {
                ApiResponse::error("Only {$availableStock} items available in stock", 400);
            }

            // Update quantity
            $cart[$cartItemKey]['quantity'] = $quantity;
            $cart[$cartItemKey]['updated_at'] = date('Y-m-d H:i:s');

            $this->saveCart($cart);
            $cartData = $this->formatCartResponse($cart);

            ApiResponse::success($cartData, 'Cart item updated successfully');

        } catch (Exception $e) {
            error_log('Cart Update Error: ' . $e->getMessage());
            ApiResponse::serverError('Failed to update cart item');
        }
    }

    /**
     * Remove item from cart
     * DELETE /api/cart/{cartItemKey}
     */
    public function remove($params = [])
    {
        $this->checkMethod(['DELETE']);

        $cartItemKey = $params['id'] ?? null;
        if (!$cartItemKey) {
            ApiResponse::error('Cart item key is required', 400);
        }

        try {
            $cart = $this->getCart();

            if (!isset($cart[$cartItemKey])) {
                ApiResponse::notFound('Cart item not found');
            }

            unset($cart[$cartItemKey]);
            $this->saveCart($cart);

            $cartData = $this->formatCartResponse($cart);

            ApiResponse::success($cartData, 'Item removed from cart successfully');

        } catch (Exception $e) {
            error_log('Cart Remove Error: ' . $e->getMessage());
            ApiResponse::serverError('Failed to remove cart item');
        }
    }

    /**
     * Clear entire cart
     * DELETE /api/cart/clear
     */
    public function clear()
    {
        $this->checkMethod(['DELETE']);

        try {
            $this->saveCart([]);

            ApiResponse::success([
                'items' => [],
                'totals' => [
                    'subtotal' => 0,
                    'tax' => 0,
                    'shipping' => 0,
                    'total' => 0
                ],
                'item_count' => 0
            ], 'Cart cleared successfully');

        } catch (Exception $e) {
            error_log('Cart Clear Error: ' . $e->getMessage());
            ApiResponse::serverError('Failed to clear cart');
        }
    }

    /**
     * Get cart from session
     */
    private function getCart()
    {
        return $_SESSION[$this->sessionKey] ?? [];
    }

    /**
     * Save cart to session
     */
    private function saveCart($cart)
    {
        $_SESSION[$this->sessionKey] = $cart;
    }

    /**
     * Generate unique cart item key
     */
    private function generateCartItemKey($productId, $variantId = null)
    {
        return $variantId ? "p{$productId}_v{$variantId}" : "p{$productId}";
    }

    /**
     * Get product details
     */
    private function getProduct($productId)
    {
        $query = "SELECT * FROM products WHERE id = ? AND status = 'published'";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$productId]);
        return $stmt->fetch();
    }

    /**
     * Get available stock for product/variant
     */
    private function getAvailableStock($productId, $variantId = null)
    {
        if ($variantId) {
            $query = "SELECT stock_quantity FROM product_variants WHERE id = ? AND product_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$variantId, $productId]);
            $variant = $stmt->fetch();
            return $variant ? (int)$variant['stock_quantity'] : 0;
        } else {
            $query = "SELECT stock_quantity FROM products WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$productId]);
            $product = $stmt->fetch();
            return $product ? (int)$product['stock_quantity'] : 0;
        }
    }

    /**
     * Format cart response with product details
     */
    private function formatCartResponse($cart)
    {
        $items = [];
        $subtotal = 0;
        $totalQuantity = 0;

        foreach ($cart as $key => $cartItem) {
            // Get product details
            $product = $this->getProductDetails($cartItem['product_id']);
            if (!$product) {
                continue; // Skip if product no longer exists
            }

            // Get variant details if applicable
            $variant = null;
            if ($cartItem['variant_id']) {
                $variant = $this->getVariantDetails($cartItem['variant_id']);
            }

            // Calculate price
            $price = $variant ? (float)$variant['price'] : (float)$product['price'];
            $salePrice = $variant ?
                ($variant['sale_price'] ? (float)$variant['sale_price'] : null) :
                ($product['sale_price'] ? (float)$product['sale_price'] : null);

            $finalPrice = $salePrice ?? $price;
            $lineTotal = $finalPrice * $cartItem['quantity'];

            $subtotal += $lineTotal;
            $totalQuantity += $cartItem['quantity'];

            $items[] = [
                'key' => $key,
                'product' => [
                    'id' => (int)$product['id'],
                    'name' => $product['name'],
                    'slug' => $product['slug'],
                    'sku' => $product['sku'],
                    'featured_image' => $product['featured_image'] ? [
                        'url' => '/uploads/products/' . $product['featured_image'],
                        'alt' => $product['name']
                    ] : null
                ],
                'variant' => $variant ? [
                    'id' => (int)$variant['id'],
                    'name' => $variant['name'],
                    'sku' => $variant['sku'],
                    'color' => $variant['color'],
                    'size' => $variant['size']
                ] : null,
                'price' => [
                    'regular' => $price,
                    'sale' => $salePrice,
                    'final' => $finalPrice,
                    'currency' => 'VND'
                ],
                'quantity' => $cartItem['quantity'],
                'line_total' => $lineTotal,
                'stock_status' => $this->getStockStatus($cartItem['product_id'], $cartItem['variant_id']),
                'dates' => [
                    'added_at' => $cartItem['added_at'],
                    'updated_at' => $cartItem['updated_at']
                ]
            ];
        }

        // Calculate totals
        $tax = $subtotal * 0.1; // 10% VAT
        $shipping = $subtotal > 500000 ? 0 : 30000; // Free shipping over 500k VND
        $total = $subtotal + $tax + $shipping;

        return [
            'items' => $items,
            'item_count' => $totalQuantity,
            'totals' => [
                'subtotal' => $subtotal,
                'tax' => $tax,
                'tax_rate' => 0.1,
                'shipping' => $shipping,
                'total' => $total,
                'currency' => 'VND'
            ],
            'shipping_info' => [
                'free_shipping_threshold' => 500000,
                'is_free_shipping' => $subtotal > 500000
            ]
        ];
    }

    /**
     * Get product details for cart
     */
    private function getProductDetails($productId)
    {
        $query = "SELECT id, name, slug, sku, price, sale_price, featured_image, stock_quantity
                 FROM products WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$productId]);
        return $stmt->fetch();
    }

    /**
     * Get variant details for cart
     */
    private function getVariantDetails($variantId)
    {
        $query = "SELECT id, name, sku, price, sale_price, color, size, stock_quantity
                 FROM product_variants WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$variantId]);
        return $stmt->fetch();
    }

    /**
     * Get stock status for cart item
     */
    private function getStockStatus($productId, $variantId = null)
    {
        $stock = $this->getAvailableStock($productId, $variantId);

        if ($stock <= 0) {
            return 'out_of_stock';
        } elseif ($stock <= 5) {
            return 'low_stock';
        } else {
            return 'in_stock';
        }
    }
}
