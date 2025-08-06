<?php

require_once MODEL_PATH . '/Cart.php';
require_once MODEL_PATH . '/Product.php';
require_once MODEL_PATH . '/ProductVariant.php';

class CartController extends BaseController {
    private $cartModel;
    private $productModel;
    private $variantModel;

    public function __construct() {
        parent::__construct();
        $this->cartModel = new Cart();
        $this->productModel = new Product();
        $this->variantModel = new ProductVariant();
    }

    /**
     * Hiển thị trang giỏ hàng
     */
    public function index() {
        try {
            $cartItems = $this->cartModel->getCartItems();
            $cartTotal = $this->cartModel->getCartTotal();
            $cartCount = $this->cartModel->getCartCount();

            // Chuẩn bị dữ liệu cho view
            $data = [
                'cartItems' => $cartItems,
                'cartTotal' => $cartTotal,
                'cartCount' => $cartCount,
                'title' => 'Giỏ hàng - 5S Fashion'
            ];

            $this->view('client/cart/index', $data);
        } catch (Exception $e) {
            error_log("CartController index error: " . $e->getMessage());
            $this->redirect('/');
        }
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    public function add() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->jsonResponse(['success' => false, 'message' => 'Invalid request method']);
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);

            // Validate input
            $productId = (int)($input['product_id'] ?? 0);
            $quantity = (int)($input['quantity'] ?? 1);
            $variantId = !empty($input['variant_id']) ? (int)$input['variant_id'] : null;

            if ($productId <= 0 || $quantity <= 0) {
                $this->jsonResponse(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
                return;
            }

            // Lấy thông tin sản phẩm
            $product = $this->productModel->find($productId);
            if (!$product) {
                $this->jsonResponse(['success' => false, 'message' => 'Sản phẩm không tồn tại']);
                return;
            }

            // Chuẩn bị dữ liệu cart
            $cartData = [
                'product_id' => $productId,
                'product_name' => $product['name'],
                'product_image' => $product['featured_image'],
                'quantity' => $quantity,
                'price' => $product['price']
            ];

            // Xử lý variant nếu có
            if ($variantId) {
                $variant = $this->variantModel->find($variantId);
                if ($variant) {
                    $cartData['variant_id'] = $variantId;
                    $cartData['variant_name'] = $input['variant_name'] ?? '';
                    $cartData['variant_color'] = $input['variant_color'] ?? '';
                    $cartData['variant_size'] = $input['variant_size'] ?? '';
                    $cartData['price'] = $variant['price'] ?? $product['price'];
                }
            }

            // Thêm vào giỏ hàng
            $result = $this->cartModel->addToCart($cartData);

            if ($result) {
                $cartCount = $this->cartModel->getCartCount();
                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Đã thêm sản phẩm vào giỏ hàng!',
                    'cart_count' => $cartCount
                ]);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Có lỗi xảy ra khi thêm vào giỏ hàng']);
            }

        } catch (Exception $e) {
            error_log("CartController add error: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Có lỗi xảy ra khi thêm vào giỏ hàng']);
        }
    }

    /**
     * Cập nhật số lượng sản phẩm trong giỏ hàng
     */
    public function update() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->jsonResponse(['success' => false, 'message' => 'Invalid request method']);
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);

            $cartId = (int)($input['cart_id'] ?? 0);
            $quantity = (int)($input['quantity'] ?? 1);

            if ($cartId <= 0) {
                $this->jsonResponse(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
                return;
            }

            $result = $this->cartModel->updateCartItemQuantity($cartId, $quantity);

            if ($result) {
                $cartCount = $this->cartModel->getCartCount();
                $cartTotal = $this->cartModel->getCartTotal();

                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Đã cập nhật số lượng!',
                    'cart_count' => $cartCount,
                    'cart_total' => $cartTotal
                ]);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Có lỗi xảy ra khi cập nhật']);
            }

        } catch (Exception $e) {
            error_log("CartController update error: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Có lỗi xảy ra khi cập nhật']);
        }
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng
     */
    public function remove() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->jsonResponse(['success' => false, 'message' => 'Invalid request method']);
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $cartId = (int)($input['cart_id'] ?? 0);

            if ($cartId <= 0) {
                $this->jsonResponse(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
                return;
            }

            $result = $this->cartModel->removeCartItem($cartId);

            if ($result) {
                $cartCount = $this->cartModel->getCartCount();
                $cartTotal = $this->cartModel->getCartTotal();

                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Đã xóa sản phẩm khỏi giỏ hàng!',
                    'cart_count' => $cartCount,
                    'cart_total' => $cartTotal
                ]);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Có lỗi xảy ra khi xóa sản phẩm']);
            }

        } catch (Exception $e) {
            error_log("CartController remove error: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Có lỗi xảy ra khi xóa sản phẩm']);
        }
    }

    /**
     * Xóa toàn bộ giỏ hàng
     */
    public function clear() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->jsonResponse(['success' => false, 'message' => 'Invalid request method']);
                return;
            }

            $result = $this->cartModel->clearCart();

            if ($result) {
                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Đã xóa toàn bộ giỏ hàng!',
                    'cart_count' => 0,
                    'cart_total' => 0
                ]);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Có lỗi xảy ra khi xóa giỏ hàng']);
            }

        } catch (Exception $e) {
            error_log("CartController clear error: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Có lỗi xảy ra khi xóa giỏ hàng']);
        }
    }

    /**
     * Lấy số lượng sản phẩm trong giỏ hàng (AJAX)
     */
    public function getCount() {
        try {
            $cartCount = $this->cartModel->getCartCount();
            $this->jsonResponse(['success' => true, 'count' => $cartCount]);
        } catch (Exception $e) {
            error_log("CartController getCount error: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'count' => 0]);
        }
    }

    /**
     * Lấy thông tin giỏ hàng (AJAX)
     */
    public function getCartData() {
        try {
            $cartItems = $this->cartModel->getCartItems();
            $cartTotal = $this->cartModel->getCartTotal();
            $cartCount = $this->cartModel->getCartCount();

            $this->jsonResponse([
                'success' => true,
                'items' => $cartItems,
                'total' => $cartTotal,
                'count' => $cartCount
            ]);
        } catch (Exception $e) {
            error_log("CartController getCartData error: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Có lỗi xảy ra']);
        }
    }
}
