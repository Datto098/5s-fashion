<?php

class SimpleCart
{
    private $db;
    private $user_id;
    private $session_id;

    public function __construct()
    {
        // Kết nối database
        $this->db = new PDO('mysql:host=localhost;dbname=5s_fashion', 'root', '');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Xác định user
        if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
            // User đã đăng nhập - dùng user_id
            $this->user_id = $_SESSION['user_id'];
            $this->session_id = null;
        } else {
            // Guest user - dùng cookie thay vì session
            $this->user_id = null;
            if (!isset($_COOKIE['cart_session_id'])) {
                $session_id = 'cart_' . uniqid() . '.' . microtime(true);
                setcookie('cart_session_id', $session_id, time() + (86400 * 30), '/'); // 30 days
                $_COOKIE['cart_session_id'] = $session_id; // Set immediately for current request
            }
            $this->session_id = $_COOKIE['cart_session_id'];
        }

        error_log("Cart initialized - user_id: " . $this->user_id . ", session_id: " . $this->session_id);
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    public function addToCart($product_id, $quantity = 1, $variant_data = [])
    {
        try {
            // Lấy thông tin sản phẩm
            $stmt = $this->db->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                throw new Exception("Sản phẩm không tồn tại");
            }

            // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
            $existing_item = $this->getCartItem($product_id, $variant_data);

            if ($existing_item) {
                // Cập nhật số lượng
                $new_quantity = $existing_item['quantity'] + $quantity;
                return $this->updateCartItem($existing_item['id'], $new_quantity);
            } else {
                // Thêm mới
                return $this->insertCartItem($product, $quantity, $variant_data);
            }

        } catch (Exception $e) {
            error_log("Error adding to cart: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kiểm tra sản phẩm đã có trong giỏ hàng chưa
     */
    private function getCartItem($product_id, $variant_data = [])
    {
        $sql = "SELECT * FROM carts WHERE product_id = ?";
        $params = [$product_id];

        // Thêm điều kiện user hoặc session
        if ($this->user_id) {
            $sql .= " AND user_id = ?";
            $params[] = $this->user_id;
        } else {
            $sql .= " AND session_id = ?";
            $params[] = $this->session_id;
        }

        // Nếu có variant, kiểm tra thêm
        if (!empty($variant_data['color'])) {
            $sql .= " AND variant_color = ?";
            $params[] = $variant_data['color'];
        }
        if (!empty($variant_data['size'])) {
            $sql .= " AND variant_size = ?";
            $params[] = $variant_data['size'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Thêm item mới vào cart
     */
    private function insertCartItem($product, $quantity, $variant_data = [])
    {
        $sql = "INSERT INTO carts (
            user_id, session_id, product_id, variant_id,
            product_name, product_image, variant_name,
            variant_color, variant_size, price, quantity, total_price,
            created_at, updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

        $price = $variant_data['price'] ?? $product['price'];
        $total_price = $price * $quantity;

        $params = [
            $this->user_id,
            $this->session_id,
            $product['id'],
            $variant_data['variant_id'] ?? null,
            $product['name'],
            $product['image'] ?? '',
            $variant_data['variant_name'] ?? '',
            $variant_data['color'] ?? '',
            $variant_data['size'] ?? '',
            $price,
            $quantity,
            $total_price
        ];

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Cập nhật số lượng item trong cart
     */
    private function updateCartItem($cart_id, $new_quantity)
    {
        $sql = "UPDATE carts SET
                quantity = ?,
                total_price = price * ?,
                updated_at = NOW()
                WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$new_quantity, $new_quantity, $cart_id]);
    }

    /**
     * Lấy tất cả items trong giỏ hàng của user/session hiện tại
     */
    public function getCartItems()
    {
        $sql = "SELECT * FROM carts WHERE ";
        $params = [];

        if ($this->user_id) {
            $sql .= "user_id = ?";
            $params[] = $this->user_id;
        } else {
            $sql .= "session_id = ?";
            $params[] = $this->session_id;
        }

        $sql .= " ORDER BY created_at DESC";

        error_log("Getting cart items - SQL: $sql, Params: " . json_encode($params));

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Found " . count($items) . " cart items");

        return $items;
    }

    /**
     * Cập nhật số lượng sản phẩm trong giỏ hàng
     */
    public function updateQuantity($cart_id, $quantity)
    {
        try {
            if ($quantity <= 0) {
                return $this->removeFromCart($cart_id);
            }

            $sql = "UPDATE carts SET
                    quantity = ?,
                    total_price = price * ?,
                    updated_at = NOW()
                    WHERE id = ?";

            // Thêm điều kiện user/session để bảo mật
            if ($this->user_id) {
                $sql .= " AND user_id = ?";
                $params = [$quantity, $quantity, $cart_id, $this->user_id];
            } else {
                $sql .= " AND session_id = ?";
                $params = [$quantity, $quantity, $cart_id, $this->session_id];
            }

            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);

        } catch (Exception $e) {
            error_log("Error updating cart quantity: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng
     */
    public function removeFromCart($cart_id)
    {
        try {
            $sql = "DELETE FROM carts WHERE id = ?";

            // Thêm điều kiện user/session để bảo mật
            if ($this->user_id) {
                $sql .= " AND user_id = ?";
                $params = [$cart_id, $this->user_id];
            } else {
                $sql .= " AND session_id = ?";
                $params = [$cart_id, $this->session_id];
            }

            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);

        } catch (Exception $e) {
            error_log("Error removing from cart: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy tổng số lượng items trong giỏ hàng
     */
    public function getCartCount()
    {
        $sql = "SELECT SUM(quantity) as total FROM carts WHERE ";
        $params = [];

        if ($this->user_id) {
            $sql .= "user_id = ?";
            $params[] = $this->user_id;
        } else {
            $sql .= "session_id = ?";
            $params[] = $this->session_id;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['total'] ?? 0);
    }

    /**
     * Lấy tổng giá trị giỏ hàng
     */
    public function getCartTotal()
    {
        $sql = "SELECT SUM(total_price) as total FROM carts WHERE ";
        $params = [];

        if ($this->user_id) {
            $sql .= "user_id = ?";
            $params[] = $this->user_id;
        } else {
            $sql .= "session_id = ?";
            $params[] = $this->session_id;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float)($result['total'] ?? 0);
    }

    /**
     * Xóa toàn bộ giỏ hàng
     */
    public function clearCart()
    {
        try {
            $sql = "DELETE FROM carts WHERE ";
            $params = [];

            if ($this->user_id) {
                $sql .= "user_id = ?";
                $params[] = $this->user_id;
            } else {
                $sql .= "session_id = ?";
                $params[] = $this->session_id;
            }

            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);

        } catch (Exception $e) {
            error_log("Error clearing cart: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Chuyển đổi cart từ session sang user khi đăng nhập
     */
    public function migrateSessionCartToUser($user_id)
    {
        try {
            if (!$this->session_id) {
                return true; // Không có session cart để migrate
            }

            $sql = "UPDATE carts SET
                    user_id = ?,
                    session_id = NULL,
                    updated_at = NOW()
                    WHERE session_id = ?";

            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([$user_id, $this->session_id]);

            if ($result) {
                // Cập nhật lại thuộc tính
                $this->user_id = $user_id;
                $this->session_id = null;
                unset($_SESSION['cart_session_id']);
            }

            return $result;

        } catch (Exception $e) {
            error_log("Error migrating cart: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Debug info
     */
    public function getDebugInfo()
    {
        return [
            'user_id' => $this->user_id,
            'session_id' => $this->session_id,
            'cart_count' => $this->getCartCount(),
            'cart_total' => $this->getCartTotal()
        ];
    }
}
