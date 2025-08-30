<?php

require_once APP_PATH . '/core/Model.php';

class Cart extends BaseModel {
    protected $table = 'carts';

    public function __construct() {
        parent::__construct();
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     * @param int $productId
     * @param int $quantity
     * @param int|null $variantId
     * @return bool
     */
    public function addToCart($productId, $quantity = 1, $variantId = null) {
        $userId = $this->getCurrentUserId();
        $sessionId = $this->getSessionId();

        // Debug: Log input parameters
        error_log("Cart::addToCart - Product ID: $productId, Quantity: $quantity (type: " . gettype($quantity) . "), Variant ID: $variantId, User ID: $userId");

        // Kiểm tra sản phẩm đã có trong giỏ hàng chưa
        $existingItem = $this->getExistingCartItem($productId, $variantId, $userId, $sessionId);

        if ($existingItem) {
            // Cập nhật số lượng nếu đã có
            $newQuantity = $existingItem['quantity'] + $quantity;
            error_log("Cart::addToCart - Existing item found with quantity: {$existingItem['quantity']}, new total: $newQuantity");
            // Respect available stock (exclude the current cart item when calculating other carts)
            $available = $this->getAvailableStock($productId, $variantId, $existingItem['id']);
            $clamped = false;
            if (is_numeric($available)) {
                if ($available <= 0) {
                    // No stock available
                    return ['success' => false, 'message' => 'Sản phẩm đã hết hàng'];
                }
                if ($newQuantity > $available) {
                    $newQuantity = $available;
                    $clamped = true;
                }
            }

            $res = $this->updateQuantity($existingItem['id'], $newQuantity);
            return ['success' => (bool)$res, 'quantity' => $newQuantity, 'clamped' => $clamped];
        } else {
            // Thêm mới nếu chưa có
            $price = $this->getProductPrice($productId, $variantId);
            error_log("Cart::addToCart - Creating new cart item with quantity: $quantity");

            // Respect available stock for new items
            $available = $this->getAvailableStock($productId, $variantId, null);
            $clamped = false;
            if (is_numeric($available)) {
                if ($available <= 0) {
                    return ['success' => false, 'message' => 'Sản phẩm đã hết hàng'];
                }
                if ($quantity > $available) {
                    $quantity = $available;
                    $clamped = true;
                }
            }

            $created = $this->create([
                'user_id' => $userId,
                'session_id' => $sessionId,
                'product_id' => $productId,
                'variant_id' => $variantId,
                'quantity' => $quantity,
                'price' => $price
            ]);

            return ['success' => (bool)$created, 'quantity' => $quantity, 'clamped' => $clamped];
        }
    }

    /**
     * Lấy danh sách sản phẩm trong giỏ hàng
     * @return array
     */
    public function getCartItems() {
        $userId = $this->getCurrentUserId();
        $sessionId = $this->getSessionId();

        $sql = "
            SELECT
                c.*,
                p.name as product_name,
                p.slug as product_slug,
                p.sku as product_sku,
                p.featured_image as product_image,
                p.stock_quantity as product_stock,
                pv.id as variant_id,
                pv.variant_name,
                pv.sku as variant_sku,
                pv.stock_quantity as variant_stock,
                GROUP_CONCAT(
                    CONCAT(pa.name, ': ', pav.value)
                    ORDER BY pa.sort_order SEPARATOR ', '
                ) as variant_attributes
            FROM {$this->table} c
            JOIN products p ON c.product_id = p.id
            LEFT JOIN product_variants pv ON c.variant_id = pv.id
            LEFT JOIN product_variant_attributes pva ON pv.id = pva.variant_id
            LEFT JOIN product_attribute_values pav ON pva.attribute_value_id = pav.id
            LEFT JOIN product_attributes pa ON pav.attribute_id = pa.id
            WHERE " . $this->getCartCondition($userId, $sessionId) . "
            GROUP BY c.id
            ORDER BY c.created_at DESC
        ";

        return $this->db->query($sql, $this->getCartParams($userId, $sessionId))->fetchAll();
    }

    /**
     * Cập nhật số lượng sản phẩm
     * @param int $cartId
     * @param int $quantity
     * @return bool
     */
    public function updateQuantity($cartId, $quantity) {
        if ($quantity <= 0) {
            return $this->removeItem($cartId);
        }

        return $this->update($cartId, ['quantity' => $quantity]);
    }

    /**
     * Lấy thông tin item trong giỏ hàng theo ID (bao gồm stock của product/variant)
     * @param int $cartId
     * @return array|false
     */
    public function getCartItemById($cartId) {
        $sql = "SELECT c.*, p.stock_quantity as product_stock, pv.id as variant_id, pv.stock_quantity as variant_stock
                FROM {$this->table} c
                JOIN products p ON c.product_id = p.id
                LEFT JOIN product_variants pv ON c.variant_id = pv.id
                WHERE c.id = ? LIMIT 1";

        return $this->db->query($sql, [$cartId])->fetch();
    }

    /**
     * Lấy số lượng tồn kho cho product hoặc variant
     * @param int $productId
     * @param int|null $variantId
     * @return int|null  (null nếu không có thông tin)
     */
    public function getProductStock($productId, $variantId = null) {
        if ($variantId) {
            $sql = "SELECT stock_quantity FROM product_variants WHERE id = ? LIMIT 1";
            $r = $this->db->query($sql, [$variantId])->fetch();
            if ($r && isset($r['stock_quantity'])) {
                return (int)$r['stock_quantity'];
            }
        }

        $sql = "SELECT stock_quantity FROM products WHERE id = ? LIMIT 1";
        $r = $this->db->query($sql, [$productId])->fetch();
        if ($r && isset($r['stock_quantity'])) {
            return (int)$r['stock_quantity'];
        }

        return null;
    }

    /**
     * Tính số lượng thực tế còn có thể bán: stock - reserved - số trong các giỏ hàng khác
     * @param int $productId
     * @param int|null $variantId
     * @param int|null $excludeCartId  // cart id to exclude from counting (usually current cart)
     * @return int|null
     */
    public function getAvailableStock($productId, $variantId = null, $excludeCartId = null) {
        // Get base stock and reserved
        if ($variantId) {
            $sql = "SELECT stock_quantity, reserved_quantity FROM product_variants WHERE id = ? LIMIT 1";
            $r = $this->db->query($sql, [$variantId])->fetch();
            $stock = $r['stock_quantity'] ?? null;
            $reserved = $r['reserved_quantity'] ?? 0;
        } else {
            $sql = "SELECT stock_quantity FROM products WHERE id = ? LIMIT 1";
            $r = $this->db->query($sql, [$productId])->fetch();
            $stock = $r['stock_quantity'] ?? null;
            // products table does not have reserved_quantity; treat reserved as 0
            $reserved = 0;
        }

        if (!is_numeric($stock)) {
            return null; // unknown stock
        }

        $stock = (int)$stock;
        $reserved = (int)$reserved;

        // Sum quantities in other carts for same product/variant
        if ($variantId) {
            if ($excludeCartId) {
                $sql = "SELECT COALESCE(SUM(quantity),0) as total FROM {$this->table} WHERE product_id = ? AND variant_id = ? AND id != ?";
                $params = [$productId, $variantId, $excludeCartId];
            } else {
                $sql = "SELECT COALESCE(SUM(quantity),0) as total FROM {$this->table} WHERE product_id = ? AND variant_id = ?";
                $params = [$productId, $variantId];
            }
        } else {
            if ($excludeCartId) {
                $sql = "SELECT COALESCE(SUM(quantity),0) as total FROM {$this->table} WHERE product_id = ? AND variant_id IS NULL AND id != ?";
                $params = [$productId, $excludeCartId];
            } else {
                $sql = "SELECT COALESCE(SUM(quantity),0) as total FROM {$this->table} WHERE product_id = ? AND variant_id IS NULL";
                $params = [$productId];
            }
        }

        $r = $this->db->query($sql, $params)->fetch();
        $otherCartsQty = (int)($r['total'] ?? 0);

        $available = $stock - $reserved - $otherCartsQty;
        if ($available < 0) $available = 0;

        return $available;
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng
     * @param int $cartId
     * @return bool
     */
    public function removeItem($cartId) {
        return $this->delete($cartId);
    }

    /**
     * Xóa toàn bộ giỏ hàng
     * @return bool
     */
    public function clearCart() {
        $userId = $this->getCurrentUserId();
        $sessionId = $this->getSessionId();

        $sql = "DELETE FROM {$this->table} WHERE " . $this->getCartCondition($userId, $sessionId);

        return $this->db->query($sql, $this->getCartParams($userId, $sessionId));
    }

    /**
     * Lấy số lượng sản phẩm trong giỏ hàng
     * @return int
     */
    public function getCartCount() {
        $userId = $this->getCurrentUserId();
        $sessionId = $this->getSessionId();

        $sql = "SELECT SUM(quantity) as total FROM {$this->table} WHERE " . $this->getCartCondition($userId, $sessionId);
        $result = $this->db->query($sql, $this->getCartParams($userId, $sessionId))->fetch();

        return (int)($result['total'] ?? 0);
    }

    /**
     * Lấy tổng giá trị giỏ hàng
     * @return float
     */
    public function getCartTotal() {
        $userId = $this->getCurrentUserId();
        $sessionId = $this->getSessionId();

        $sql = "SELECT SUM(quantity * price) as total FROM {$this->table} WHERE " . $this->getCartCondition($userId, $sessionId);
        $result = $this->db->query($sql, $this->getCartParams($userId, $sessionId))->fetch();

        return (float)($result['total'] ?? 0);
    }

    /**
     * Chuyển giỏ hàng từ session sang user khi đăng nhập
     * @param int $userId
     * @return bool
     */
    public function transferSessionCartToUser($userId) {
        $sessionId = $this->getSessionId();

        $sql = "UPDATE {$this->table} SET user_id = ? WHERE session_id = ? AND user_id IS NULL";
        return $this->db->query($sql, [$userId, $sessionId]);
    }

    // === PRIVATE METHODS ===

    /**
     * Lấy sản phẩm đã có trong giỏ hàng
     */
    private function getExistingCartItem($productId, $variantId, $userId, $sessionId) {
        $sql = "SELECT * FROM {$this->table} WHERE product_id = ? AND ";
        $params = [$productId];

        if ($variantId) {
            $sql .= "variant_id = ? AND ";
            $params[] = $variantId;
        } else {
            $sql .= "variant_id IS NULL AND ";
        }

        $sql .= $this->getCartCondition($userId, $sessionId);
        $params = array_merge($params, $this->getCartParams($userId, $sessionId));

        return $this->db->query($sql, $params)->fetch();
    }

    /**
     * Lấy giá sản phẩm (có thể từ variant hoặc product)
     */
    private function getProductPrice($productId, $variantId = null) {
        if ($variantId) {
            // Lấy giá từ variant trước
            $sql = "SELECT price, sale_price FROM product_variants WHERE id = ?";
            $variant = $this->db->query($sql, [$variantId])->fetch();

            if ($variant) {
                // Use sale_price if it's set and > 0, otherwise use regular price
                if (!empty($variant['sale_price']) && $variant['sale_price'] > 0) {
                    return $variant['sale_price'];
                } elseif (!empty($variant['price']) && $variant['price'] > 0) {
                    return $variant['price'];
                }
            }
        }

        // Lấy giá từ product
        $sql = "SELECT price, sale_price FROM products WHERE id = ?";
        $product = $this->db->query($sql, [$productId])->fetch();

        if ($product) {
            // Use sale_price if it's set and > 0, otherwise use regular price
            if (!empty($product['sale_price']) && $product['sale_price'] > 0) {
                return $product['sale_price'];
            } elseif (!empty($product['price']) && $product['price'] > 0) {
                return $product['price'];
            }
        }

        return 0;
    }

    /**
     * Lấy điều kiện WHERE cho cart query
     */
    private function getCartCondition($userId, $sessionId) {
        if ($userId) {
            return "(user_id = ? OR session_id = ?)";
        }
        return "session_id = ?";
    }

    /**
     * Lấy parameters cho cart query
     */
    private function getCartParams($userId, $sessionId) {
        if ($userId) {
            return [$userId, $sessionId];
        }
        return [$sessionId];
    }

    /**
     * Lấy User ID hiện tại
     */
    private function getCurrentUserId() {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Lấy hoặc tạo Session ID cho guest
     */
    private function getSessionId() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['cart_session_id'])) {
            $_SESSION['cart_session_id'] = 'cart_' . uniqid() . '_' . time();
        }

        return $_SESSION['cart_session_id'];
    }
}
