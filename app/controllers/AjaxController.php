<?php
/**
 * AJAX Controller
 * Handle AJAX requests for cart operations
 * 5S Fashion E-commerce Platform
 */

require_once APP_PATH . '/core/Controller.php';

// Product model will be loaded as needed
if (!class_exists('Product')) {
    require_once APP_PATH . '/models/Product.php';
}

class AjaxController extends Controller
{
    private $product;

    public function __construct()
    {
        parent::__construct();
        $this->product = new Product();

        // Set content type to JSON for all AJAX responses
        header('Content-Type: application/json');
    }

    /**
     * Add product to cart
     */
    public function addToCart()
    {
        try {
            // Get JSON input
            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input) {
                throw new Exception('Invalid JSON data');
            }

            // Validate required fields
            $productId = $input['product_id'] ?? null;
            $quantity = $input['quantity'] ?? 1;
            $variant = $input['variant'] ?? null;

            if (!$productId) {
                throw new Exception('Product ID is required');
            }

            // Validate product exists
            $product = $this->product->find($productId);
            if (!$product) {
                throw new Exception('Product not found');
            }

            // Check if product is published
            if ($product['status'] !== 'published') {
                throw new Exception('Product is not available');
            }

            // Start session if not started
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Initialize cart if not exists
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            // Create cart item key
            $cartKey = $productId . '_' . ($variant ? md5($variant) : 'default');

            // Check if item already exists in cart
            if (isset($_SESSION['cart'][$cartKey])) {
                $_SESSION['cart'][$cartKey]['quantity'] += $quantity;
            } else {
                // Process image path properly - use same logic as product-card.php
                $imagePath = $product['featured_image'];
                $imageUrl = '/5s-fashion/assets/images/no-image.jpg'; // default

                if (!empty($imagePath)) {
                    // Handle image path for file server - same logic as in product-card.php
                    if (strpos($imagePath, '/uploads/') === 0) {
                        $cleanPath = substr($imagePath, 9); // Remove '/uploads/'
                    } elseif (strpos($imagePath, 'uploads/') === 0) {
                        $cleanPath = substr($imagePath, 8); // Remove 'uploads/'
                    } else {
                        $cleanPath = ltrim($imagePath, '/'); // Remove leading '/'
                    }
                    $imageUrl = '/5s-fashion/serve-file.php?file=' . urlencode($cleanPath);
                }

                $_SESSION['cart'][$cartKey] = [
                    'product_id' => $productId,
                    'product_name' => $product['name'],
                    'product_image' => $imageUrl,
                    'price' => $product['sale_price'] ?: $product['price'],
                    'quantity' => $quantity,
                    'variant' => $variant,
                    'added_at' => date('Y-m-d H:i:s')
                ];
            }

            // Calculate cart totals
            $cartTotal = $this->calculateCartTotal();
            $cartCount = $this->getCartItemCount();

            $response = [
                'success' => true,
                'message' => 'Sản phẩm đã được thêm vào giỏ hàng!',
                'cart_count' => $cartCount,
                'cart_total' => $cartTotal,
                'item' => $_SESSION['cart'][$cartKey]
            ];

            echo json_encode($response);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update cart item quantity
     */
    public function updateCart()
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input) {
                throw new Exception('Invalid JSON data');
            }

            $productId = $input['product_id'] ?? null;
            $variant = $input['variant'] ?? null;
            $cartKey = $input['cart_key'] ?? null;
            $quantity = $input['quantity'] ?? 1;

            // If cart_key is not provided, create it from product_id and variant
            if (!$cartKey) {
                if (!$productId) {
                    throw new Exception('Product ID or cart key is required');
                }
                $cartKey = $productId . '_' . ($variant ? md5($variant) : 'default');
            }

            // Start session if not started
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['cart'][$cartKey])) {
                throw new Exception('Item not found in cart');
            }

            if ($quantity <= 0) {
                unset($_SESSION['cart'][$cartKey]);
            } else {
                $_SESSION['cart'][$cartKey]['quantity'] = $quantity;
            }

            $cartTotal = $this->calculateCartTotal();
            $cartCount = $this->getCartItemCount();

            echo json_encode([
                'success' => true,
                'message' => 'Giỏ hàng đã được cập nhật!',
                'cart_count' => $cartCount,
                'cart_total' => $cartTotal
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove item from cart
     */
    public function removeFromCart()
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input) {
                throw new Exception('Invalid JSON data');
            }

            $productId = $input['product_id'] ?? null;
            $variant = $input['variant'] ?? null;
            $cartKey = $input['cart_key'] ?? null;

            // If cart_key is not provided, create it from product_id and variant
            if (!$cartKey) {
                if (!$productId) {
                    throw new Exception('Product ID or cart key is required');
                }
                $cartKey = $productId . '_' . ($variant ? md5($variant) : 'default');
            }

            // Start session if not started
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (isset($_SESSION['cart'][$cartKey])) {
                unset($_SESSION['cart'][$cartKey]);
            }

            $cartTotal = $this->calculateCartTotal();
            $cartCount = $this->getCartItemCount();

            echo json_encode([
                'success' => true,
                'message' => 'Sản phẩm đã được xóa khỏi giỏ hàng!',
                'cart_count' => $cartCount,
                'cart_total' => $cartTotal
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get cart items
     */
    public function getCartItems()
    {
        try {
            // Start session if not started
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $cartItems = $_SESSION['cart'] ?? [];
            $cartTotal = $this->calculateCartTotal();
            $cartCount = $this->getCartItemCount();

            echo json_encode([
                'success' => true,
                'items' => $cartItems,
                'cart_count' => $cartCount,
                'cart_total' => $cartTotal
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Calculate cart total
     */
    private function calculateCartTotal()
    {
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            return 0;
        }

        $total = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return $total;
    }

    /**
     * Get cart item count
     */
    private function getCartItemCount()
    {
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            return 0;
        }

        $count = 0;
        foreach ($_SESSION['cart'] as $item) {
            $count += $item['quantity'];
        }

        return $count;
    }

    /**
     * Toggle product in wishlist
     */
    public function toggleWishlist()
    {
        try {
            // Check if user is logged in
            if (!isLoggedIn()) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Vui lòng đăng nhập để sử dụng danh sách yêu thích'
                ]);
                return;
            }

            $user = getUser();
            $productId = $_POST['product_id'] ?? null;

            if (!$productId) {
                throw new Exception('Product ID is required');
            }

            // Validate product exists
            $product = $this->product->find($productId);
            if (!$product) {
                throw new Exception('Sản phẩm không tồn tại');
            }

            // Load wishlist model
            $wishlistModel = $this->model('Wishlist');

            // Toggle wishlist
            $result = $wishlistModel->toggleWishlist($user['id'], $productId);
            $isInWishlist = $wishlistModel->isInWishlist($user['id'], $productId);

            if ($result) {
                $message = $isInWishlist ? 'Đã thêm vào danh sách yêu thích' : 'Đã xóa khỏi danh sách yêu thích';
                $action = $isInWishlist ? 'added' : 'removed';

                echo json_encode([
                    'success' => true,
                    'message' => $message,
                    'action' => $action,
                    'in_wishlist' => $isInWishlist
                ]);
            } else {
                throw new Exception('Không thể thực hiện thao tác');
            }

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
?>
