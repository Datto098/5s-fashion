<?php

require_once MODEL_PATH . '/Cart.php';
require_once MODEL_PATH . '/Product.php';
require_once MODEL_PATH . '/ProductVariant.php';

class CartController extends BaseController {
    private $cartModel;
    private $productModel;

    public function __construct() {
        $this->cartModel = new Cart();
        $this->productModel = new Product();
    }

    /**
     * Hiển thị trang giỏ hàng
     */
    public function index() {
        $cartItems = $this->cartModel->getCartItems();
        $cartTotal = $this->cartModel->getCartTotal();
        $cartCount = $this->cartModel->getCartCount();

        $data = [
            'title' => 'Giỏ hàng - 5S Fashion',
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
            'cartCount' => $cartCount
        ];

        $this->render('client/cart/index', $data, 'client/layouts/app');
    }

    /**
     * Thêm sản phẩm vào giỏ hàng (API)
     */
    public function add() {
        header('Content-Type: application/json');

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input) {
                throw new Exception('Invalid JSON data');
            }

            // Validate required fields
            $productId = (int)($input['product_id'] ?? 0);
            $quantity = (int)($input['quantity'] ?? 1);
            $variantId = !empty($input['variant_id']) ? (int)$input['variant_id'] : null;

            if ($productId <= 0) {
                throw new Exception('Product ID is required');
            }

            if ($quantity <= 0) {
                throw new Exception('Quantity must be greater than 0');
            }

            // Kiểm tra sản phẩm có tồn tại không
            $product = $this->productModel->find($productId);
            if (!$product) {
                throw new Exception('Product not found');
            }

            // Kiểm tra variant nếu có
            if ($variantId) {
                $variant = $this->productModel->getVariantById($variantId);
                if (!$variant || $variant['product_id'] != $productId) {
                    throw new Exception('Invalid product variant');
                }
            }

            // Thêm vào giỏ hàng
            $result = $this->cartModel->addToCart($productId, $quantity, $variantId);

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Đã thêm sản phẩm vào giỏ hàng!',
                    'cart_count' => $this->cartModel->getCartCount()
                ]);
            } else {
                throw new Exception('Failed to add product to cart');
            }

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Cập nhật số lượng sản phẩm (API)
     */
    public function update() {
        header('Content-Type: application/json');

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $input = json_decode(file_get_contents('php://input'), true);

            $cartId = (int)($input['cart_id'] ?? 0);
            $quantity = (int)($input['quantity'] ?? 1);

            if ($cartId <= 0) {
                throw new Exception('Cart ID is required');
            }

            if ($quantity < 0) {
                throw new Exception('Quantity cannot be negative');
            }

            $result = $this->cartModel->updateQuantity($cartId, $quantity);

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => $quantity > 0 ? 'Đã cập nhật số lượng!' : 'Đã xóa sản phẩm!',
                    'cart_count' => $this->cartModel->getCartCount(),
                    'cart_total' => $this->cartModel->getCartTotal()
                ]);
            } else {
                throw new Exception('Failed to update cart');
            }

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng (API)
     */
    public function remove() {
        header('Content-Type: application/json');

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $cartId = (int)($input['cart_id'] ?? 0);

            if ($cartId <= 0) {
                throw new Exception('Cart ID is required');
            }

            $result = $this->cartModel->removeItem($cartId);

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Đã xóa sản phẩm khỏi giỏ hàng!',
                    'cart_count' => $this->cartModel->getCartCount(),
                    'cart_total' => $this->cartModel->getCartTotal()
                ]);
            } else {
                throw new Exception('Failed to remove item from cart');
            }

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Xóa toàn bộ giỏ hàng (API)
     */
    public function clear() {
        header('Content-Type: application/json');

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $result = $this->cartModel->clearCart();

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Đã xóa toàn bộ giỏ hàng!',
                    'cart_count' => 0,
                    'cart_total' => 0
                ]);
            } else {
                throw new Exception('Failed to clear cart');
            }

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Lấy thông tin giỏ hàng (API)
     */
    public function get() {
        header('Content-Type: application/json');

        try {
            $cartItems = $this->cartModel->getCartItems();
            $cartTotal = $this->cartModel->getCartTotal();
            $cartCount = $this->cartModel->getCartCount();

            echo json_encode([
                'success' => true,
                'data' => [
                    'items' => $cartItems,
                    'total' => $cartTotal,
                    'count' => $cartCount
                ]
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Lấy số lượng sản phẩm trong giỏ hàng (API)
     */
    public function count() {
        header('Content-Type: application/json');

        try {
            $count = $this->cartModel->getCartCount();

            echo json_encode([
                'success' => true,
                'count' => $count
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage(),
                'count' => 0
            ]);
        }
    }
}
