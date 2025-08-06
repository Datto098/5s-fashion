<?php

require_once MODEL_PATH . '/SimpleCart.php';

class SimpleCartController {
    private $cart;

    public function __construct() {
        $this->cart = new SimpleCart();
    }

    /**
     * Hiển thị trang giỏ hàng
     */
    public function index() {
        // Include helper functions for asset() and other helpers
        require_once APP_PATH . '/helpers/functions.php';

        $cartItems = $this->cart->getCartItems();
        $cartTotal = $this->cart->getCartTotal();
        $cartCount = $this->cart->getCartCount();

        require_once APP_PATH . '/views/client/cart/index.php';
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    public function add() {
        header('Content-Type: application/json');

        try {
            error_log("SimpleCartController::add() called");

            $input = json_decode(file_get_contents('php://input'), true);
            error_log("Input data: " . print_r($input, true));

            if (!$input) {
                throw new Exception('Dữ liệu không hợp lệ');
            }

            // Validate required fields - chỉ cần product_id và quantity
            if (!isset($input['product_id']) || !isset($input['quantity'])) {
                throw new Exception('Thiếu thông tin product_id hoặc quantity');
            }

            if (!is_numeric($input['product_id']) || $input['product_id'] <= 0) {
                throw new Exception('product_id không hợp lệ');
            }

            if (!is_numeric($input['quantity']) || $input['quantity'] <= 0) {
                throw new Exception('quantity không hợp lệ');
            }

            // Chuẩn bị variant data nếu có
            $variant_data = [];
            if (isset($input['variant_color'])) $variant_data['color'] = $input['variant_color'];
            if (isset($input['variant_size'])) $variant_data['size'] = $input['variant_size'];
            if (isset($input['variant_price'])) $variant_data['price'] = $input['variant_price'];
            if (isset($input['variant_id'])) $variant_data['variant_id'] = $input['variant_id'];
            if (isset($input['variant_name'])) $variant_data['variant_name'] = $input['variant_name'];

            $result = $this->cart->addToCart($input['product_id'], $input['quantity'], $variant_data);

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Đã thêm vào giỏ hàng',
                    'cart_count' => $this->cart->getCartCount()
                ]);
            } else {
                throw new Exception('Không thể thêm vào giỏ hàng');
            }
        } catch (Exception $e) {
            error_log("SimpleCartController::add() error: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Cập nhật số lượng sản phẩm trong giỏ hàng
     */
    public function update() {
        header('Content-Type: application/json');

        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (!isset($input['cart_id']) || !isset($input['quantity'])) {
                throw new Exception('Thiếu thông tin cart_id hoặc quantity');
            }

            $result = $this->cart->updateQuantity($input['cart_id'], $input['quantity']);

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Đã cập nhật giỏ hàng',
                    'cart_count' => $this->cart->getCartCount(),
                    'cart_total' => $this->cart->getCartTotal()
                ]);
            } else {
                throw new Exception('Không thể cập nhật giỏ hàng');
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng
     */
    public function remove() {
        header('Content-Type: application/json');

        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (!isset($input['cart_id'])) {
                throw new Exception('Thiếu thông tin cart_id');
            }

            $result = $this->cart->removeFromCart($input['cart_id']);

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Đã xóa khỏi giỏ hàng',
                    'cart_count' => $this->cart->getCartCount(),
                    'cart_total' => $this->cart->getCartTotal()
                ]);
            } else {
                throw new Exception('Không thể xóa khỏi giỏ hàng');
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Xóa toàn bộ giỏ hàng
     */
    public function clear() {
        header('Content-Type: application/json');

        try {
            $result = $this->cart->clearCart();

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Đã xóa toàn bộ giỏ hàng',
                    'cart_count' => 0,
                    'cart_total' => 0
                ]);
            } else {
                throw new Exception('Không thể xóa giỏ hàng');
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Lấy số lượng sản phẩm trong giỏ hàng
     */
    public function getCount() {
        header('Content-Type: application/json');

        try {
            $count = $this->cart->getCartCount();
            echo json_encode([
                'success' => true,
                'cart_count' => $count
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Lấy dữ liệu giỏ hàng
     */
    public function getCartData() {
        header('Content-Type: application/json');

        try {
            error_log("SimpleCartController::getCartData() called");
            error_log("Cookies: " . print_r($_COOKIE, true));
            error_log("Session ID from cookie: " . ($_COOKIE['cart_session_id'] ?? 'NULL'));

            $cartItems = $this->cart->getCartItems();
            $cartTotal = $this->cart->getCartTotal();
            $cartCount = $this->cart->getCartCount();

            error_log("Cart items count: " . count($cartItems));
            error_log("Cart total: $cartTotal");
            error_log("Cart count: $cartCount");

            echo json_encode([
                'success' => true,
                'cart_items' => $cartItems,
                'cart_total' => $cartTotal,
                'cart_count' => $cartCount
            ]);
        } catch (Exception $e) {
            error_log("SimpleCartController::getCartData() error: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
