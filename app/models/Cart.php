<?php

require_once APP_PATH . '/core/Model.php';
require_once APP_PATH . '/config/database.php';

class Cart extends BaseModel {
    protected $table = 'carts';

    public function __construct() {
        parent::__construct();
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    public function addToCart($data) {
        try {
            // Lấy session_id hoặc user_id
            $sessionId = $this->getSessionId();
            $userId = $this->getCurrentUserId();

            // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
            $existingItem = $this->findExistingCartItem($data, $sessionId, $userId);

            if ($existingItem) {
                // Nếu đã có, cập nhật số lượng
                return $this->updateCartItemQuantity($existingItem['id'], $existingItem['quantity'] + $data['quantity']);
            } else {
                // Nếu chưa có, thêm mới
                $cartData = [
                    'user_id' => $userId,
                    'session_id' => $sessionId,
                    'product_id' => $data['product_id'],
                    'variant_id' => $data['variant_id'] ?? null,
                    'product_name' => $data['product_name'],
                    'product_image' => $data['product_image'] ?? null,
                    'variant_name' => $data['variant_name'] ?? null,
                    'variant_color' => $data['variant_color'] ?? null,
                    'variant_size' => $data['variant_size'] ?? null,
                    'price' => $data['price'],
                    'quantity' => $data['quantity'],
                    'total_price' => $data['price'] * $data['quantity']
                ];

                return $this->create($cartData);
            }
        } catch (Exception $e) {
            error_log("Cart addToCart error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy tất cả items trong giỏ hàng
     */
    public function getCartItems() {
        try {
            $sessionId = $this->getSessionId();
            $userId = $this->getCurrentUserId();

            $sql = "SELECT c.*, p.featured_image as product_original_image
                    FROM {$this->table} c
                    LEFT JOIN products p ON c.product_id = p.id
                    WHERE ";

            $params = [];

            if ($userId) {
                $sql .= "(c.user_id = ? OR c.session_id = ?) ";
                $params = [$userId, $sessionId];
            } else {
                $sql .= "c.session_id = ? ";
                $params = [$sessionId];
            }

            $sql .= "ORDER BY c.created_at DESC";

            return $this->db->query($sql, $params)->fetchAll();
        } catch (Exception $e) {
            error_log("Cart getCartItems error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Cập nhật số lượng sản phẩm trong giỏ hàng
     */
    public function updateCartItemQuantity($cartId, $quantity) {
        try {
            if ($quantity <= 0) {
                return $this->removeCartItem($cartId);
            }

            // Lấy thông tin cart item
            $cartItem = $this->find($cartId);
            if (!$cartItem) {
                return false;
            }

            $totalPrice = $cartItem['price'] * $quantity;

            return $this->update($cartId, [
                'quantity' => $quantity,
                'total_price' => $totalPrice
            ]);
        } catch (Exception $e) {
            error_log("Cart updateCartItemQuantity error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng
     */
    public function removeCartItem($cartId) {
        try {
            return $this->delete($cartId);
        } catch (Exception $e) {
            error_log("Cart removeCartItem error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa toàn bộ giỏ hàng
     */
    public function clearCart() {
        try {
            $sessionId = $this->getSessionId();
            $userId = $this->getCurrentUserId();

            $sql = "DELETE FROM {$this->table} WHERE ";
            $params = [];

            if ($userId) {
                $sql .= "(user_id = ? OR session_id = ?)";
                $params = [$userId, $sessionId];
            } else {
                $sql .= "session_id = ?";
                $params = [$sessionId];
            }

            return $this->db->query($sql, $params);
        } catch (Exception $e) {
            error_log("Cart clearCart error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy tổng số lượng items trong giỏ hàng
     */
    public function getCartCount() {
        try {
            $sessionId = $this->getSessionId();
            $userId = $this->getCurrentUserId();

            $sql = "SELECT SUM(quantity) as total_count FROM {$this->table} WHERE ";
            $params = [];

            if ($userId) {
                $sql .= "(user_id = ? OR session_id = ?)";
                $params = [$userId, $sessionId];
            } else {
                $sql .= "session_id = ?";
                $params = [$sessionId];
            }

            $result = $this->db->query($sql, $params)->fetch();
            return (int)($result['total_count'] ?? 0);
        } catch (Exception $e) {
            error_log("Cart getCartCount error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Lấy tổng giá trị giỏ hàng
     */
    public function getCartTotal() {
        try {
            $sessionId = $this->getSessionId();
            $userId = $this->getCurrentUserId();

            $sql = "SELECT SUM(total_price) as total_amount FROM {$this->table} WHERE ";
            $params = [];

            if ($userId) {
                $sql .= "(user_id = ? OR session_id = ?)";
                $params = [$userId, $sessionId];
            } else {
                $sql .= "session_id = ?";
                $params = [$sessionId];
            }

            $result = $this->db->query($sql, $params)->fetch();
            return (float)($result['total_amount'] ?? 0);
        } catch (Exception $e) {
            error_log("Cart getCartTotal error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Tìm sản phẩm đã có trong giỏ hàng
     */
    private function findExistingCartItem($data, $sessionId, $userId) {
        $sql = "SELECT * FROM {$this->table} WHERE product_id = ? AND ";
        $params = [$data['product_id']];

        // Kiểm tra variant nếu có
        if (!empty($data['variant_id'])) {
            $sql .= "variant_id = ? AND ";
            $params[] = $data['variant_id'];
        } else {
            $sql .= "variant_id IS NULL AND ";
        }

        if ($userId) {
            $sql .= "(user_id = ? OR session_id = ?)";
            $params[] = $userId;
            $params[] = $sessionId;
        } else {
            $sql .= "session_id = ?";
            $params[] = $sessionId;
        }

        return $this->db->query($sql, $params)->fetch();
    }

    /**
     * Lấy session ID
     */
    private function getSessionId() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['cart_session_id'])) {
            $_SESSION['cart_session_id'] = uniqid('cart_', true);
        }

        return $_SESSION['cart_session_id'];
    }

    /**
     * Lấy user ID hiện tại (nếu đã đăng nhập)
     */
    private function getCurrentUserId() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Chuyển giỏ hàng từ session sang user khi đăng nhập
     */
    public function transferSessionCartToUser($userId) {
        try {
            $sessionId = $this->getSessionId();

            $sql = "UPDATE {$this->table} SET user_id = ? WHERE session_id = ? AND user_id IS NULL";
            return $this->db->query($sql, [$userId, $sessionId]);
        } catch (Exception $e) {
            error_log("Cart transferSessionCartToUser error: " . $e->getMessage());
            return false;
        }
    }
}
