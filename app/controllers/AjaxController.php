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

        // Start session first
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Set content type to JSON for all AJAX responses
        header('Content-Type: application/json');
    }

    /**
     * Check if user is authenticated
     * @return bool
     */
    private function isUserAuthenticated()
    {
        // Use helper function for consistency
        if (!isLoggedIn()) {
            return false;
        }

        // Also check if user is not admin (admins shouldn't use client cart)
        $user = getUser();
        if ($user && $user['role'] === 'admin') {
            return false;
        }

        return true;
    }

    /**
     * Initialize session for AJAX operations
     */
    public function initSession()
    {
        echo json_encode([
            'success' => true,
            'message' => 'Session initialized',
            'session_id' => session_id()
        ]);
    }

    /**
     * Add product to cart - Requires user authentication
     */
    public function addToCart()
    {
        try {
            // Check user authentication first
            if (!$this->isUserAuthenticated()) {
                // Check if user is admin
                $user = getUser();
                if ($user && $user['role'] === 'admin') {
                    http_response_code(403);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Tài khoản admin không thể sử dụng giỏ hàng khách hàng',
                        'error_code' => 'ADMIN_ACCOUNT_RESTRICTION',
                        'redirect_url' => '/5s-fashion/admin/dashboard'
                    ]);
                } else {
                    http_response_code(401);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Bạn cần đăng nhập để thêm sản phẩm vào giỏ hàng',
                        'error_code' => 'AUTHENTICATION_REQUIRED',
                        'redirect_url' => '/5s-fashion/login'
                    ]);
                }
                return;
            }

            // Get JSON input
            $input = json_decode(file_get_contents('php://input'), true);

            // Debug: Log input data
            error_log("AjaxController::addToCart - Input data: " . print_r($input, true));

            if (!$input) {
                throw new Exception('Invalid JSON data');
            }

            // Validate required fields
            $productId = $input['product_id'] ?? null;
            $quantity = $input['quantity'] ?? 1;
            $clientPrice = $input['price'] ?? null; // Price sent from client

            // Extract variant information - handle both formats
            $variantId = $input['variant_id'] ?? null;
            $variantData = $input['variant'] ?? null;

            // Initialize variant fields
            $variantColor = '';
            $variantSize = '';
            $variantName = '';
            $variantPrice = null;

            // Extract from direct fields (legacy format)
            if (!$variantData) {
                $variantColor = $input['variant_color'] ?? '';
                $variantSize = $input['variant_size'] ?? '';
                $variantName = $input['variant_name'] ?? '';
                $variantPrice = $input['variant_price'] ?? null;
            } else {
                // Extract from variant object (new QuickView format)
                if (is_array($variantData)) {
                    $variantColor = $variantData['color'] ?? '';
                    $variantSize = $variantData['size'] ?? '';
                    $variantName = $variantData['name'] ?? '';
                    $variantPrice = $variantData['price'] ?? null;
                    // Also get variant ID from variant object if not provided separately
                    if (!$variantId && isset($variantData['id'])) {
                        $variantId = $variantData['id'];
                    }
                } else {
                    // Handle string format (like "Xanh dương - S")
                    $variantName = $variantData;
                    $parts = explode(' - ', $variantData);
                    if (count($parts) == 2) {
                        $variantColor = trim($parts[0]);
                        $variantSize = trim($parts[1]);
                    }
                }
            }

            // Debug: Log variant data
            error_log("Variant data - ID: $variantId, Color: $variantColor, Size: $variantSize, Price: $variantPrice");

            // Create variant object for storage
            $variant = null;
            if ($variantId || $variantColor || $variantSize) {
                $variant = [
                    'id' => $variantId,
                    'color' => $variantColor,
                    'size' => $variantSize,
                    'name' => $variantName ?: ($variantColor && $variantSize ? "$variantColor - $variantSize" : ''),
                    'price' => $variantPrice
                ];
                error_log("Created variant object: " . print_r($variant, true));
            }

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

            // Determine price to use
            $priceToUse = $product['sale_price'] ?: $product['price']; // Default price

            // If variant has price, use it
            if ($variantPrice && is_numeric($variantPrice) && $variantPrice > 0) {
                $priceToUse = $variantPrice;
            }
            // Otherwise, if client sent a valid price and it's reasonable, use it
            elseif ($clientPrice !== null && is_numeric($clientPrice) && $clientPrice > 0) {
                // Validate that the client price is within reasonable bounds of product price
                $basePrice = $product['sale_price'] ?: $product['price'];
                $maxReasonablePrice = $basePrice * 2; // Allow up to 2x base price for variants

                if ($clientPrice <= $maxReasonablePrice) {
                    $priceToUse = $clientPrice;
                }
            }

            // Initialize Cart model
            $cartModel = $this->model('Cart');

            // Add to cart using Cart model
            $result = $cartModel->addToCart($productId, $quantity, $variantId);

            if (!$result) {
                throw new Exception('Could not add product to cart');
            }

            // Get updated cart data
            $cartItems = $cartModel->getCartItems();
            $cartTotal = $cartModel->getCartTotal();
            $cartCount = $cartModel->getCartCount();

            // Find the added item for response
            $addedItem = null;
            foreach ($cartItems as $item) {
                if ($item['product_id'] == $productId &&
                    ($variantId ? $item['variant_id'] == $variantId : true)) {
                    $addedItem = $item;
                    break;
                }
            }

            $response = [
                'success' => true,
                'message' => 'Sản phẩm đã được thêm vào giỏ hàng!',
                'cart_count' => $cartCount,
                'cart_total' => $cartTotal,
                'item' => $addedItem
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
     * Update cart item quantity - Requires user authentication
     */
    public function updateCart()
    {
        try {
            // Check user authentication first
            if (!$this->isUserAuthenticated()) {
                // Check if user is admin
                $user = getUser();
                if ($user && $user['role'] === 'admin') {
                    http_response_code(403);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Tài khoản admin không thể sử dụng giỏ hàng khách hàng',
                        'error_code' => 'ADMIN_ACCOUNT_RESTRICTION',
                        'redirect_url' => '/5s-fashion/admin/dashboard'
                    ]);
                } else {
                    http_response_code(401);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Bạn cần đăng nhập để cập nhật giỏ hàng',
                        'error_code' => 'AUTHENTICATION_REQUIRED',
                        'redirect_url' => '/5s-fashion/login'
                    ]);
                }
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input) {
                throw new Exception('Invalid JSON data');
            }

            $cartKey = $input['cart_key'] ?? null;
            $quantity = $input['quantity'] ?? 1;

            if (!$cartKey) {
                throw new Exception('Cart key is required');
            }

            // Load Cart model
            if (!class_exists('Cart')) {
                require_once APP_PATH . '/models/Cart.php';
            }
            $cartModel = new Cart();

            // Update quantity using cart ID
            $result = $cartModel->updateQuantity($cartKey, $quantity);

            if (!$result) {
                throw new Exception('Failed to update cart item');
            }

            // Get updated cart info
            $cartTotal = $cartModel->getCartTotal();
            $cartCount = $cartModel->getCartCount();

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
     * Remove item from cart - Requires user authentication
     */
    public function removeFromCart()
    {
        try {
            // Check user authentication first
            if (!$this->isUserAuthenticated()) {
                // Check if user is admin
                $user = getUser();
                if ($user && $user['role'] === 'admin') {
                    http_response_code(403);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Tài khoản admin không thể sử dụng giỏ hàng khách hàng',
                        'error_code' => 'ADMIN_ACCOUNT_RESTRICTION',
                        'redirect_url' => '/5s-fashion/admin/dashboard'
                    ]);
                } else {
                    http_response_code(401);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Bạn cần đăng nhập để xóa sản phẩm khỏi giỏ hàng',
                        'error_code' => 'AUTHENTICATION_REQUIRED',
                        'redirect_url' => '/5s-fashion/login'
                    ]);
                }
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input) {
                throw new Exception('Invalid JSON data');
            }

            $cartKey = $input['cart_key'] ?? null;

            if (!$cartKey) {
                throw new Exception('Cart key is required');
            }

            // Load Cart model
            if (!class_exists('Cart')) {
                require_once APP_PATH . '/models/Cart.php';
            }
            $cartModel = new Cart();

            // Remove item using cart ID
            $result = $cartModel->removeItem($cartKey);

            if (!$result) {
                throw new Exception('Failed to remove item from cart');
            }

            // Get updated cart info
            $cartTotal = $cartModel->getCartTotal();
            $cartCount = $cartModel->getCartCount();

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
            // Load Cart model
            $cartModel = $this->model('Cart');

            // Get cart items from database
            $cartItems = $cartModel->getCartItems();
            $cartTotal = $cartModel->getCartTotal();
            $cartCount = $cartModel->getCartCount();

            // Format items for JavaScript
            $formattedItems = [];
            foreach ($cartItems as $item) {
                $formattedItems[] = [
                    'cart_key' => $item['id'], // Use cart ID as key
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'product_image' => $item['product_image'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'variant' => $item['variant'] ?? null
                ];
            }

            echo json_encode([
                'success' => true,
                'items' => $formattedItems,
                'cart_count' => $cartCount,
                'cart_total' => $cartTotal
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage(),
                'items' => [] // Ensure items is always an array
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

    /**
     * Get wishlist items for current user
     */
    public function getWishlistList()
    {
        header('Content-Type: application/json');

        try {
            // Check if user is logged in
            $user = getUser();
            if (!$user) {
                echo json_encode([
                    'success' => true,
                    'data' => []
                ]);
                return;
            }

            // Load wishlist model
            $wishlistModel = $this->model('Wishlist');

            // Get wishlist items for user
            $wishlistItems = $wishlistModel->getUserWishlist($user['id']);

            echo json_encode([
                'success' => true,
                'data' => $wishlistItems
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ]);
        }
    }

    /**
     * Get product data for quick view
     */
    public function getProductData()
    {
        try {
            $productId = $_GET['id'] ?? $_POST['product_id'] ?? null;

            if (!$productId) {
                throw new Exception('Product ID is required');
            }

            // Get product data
            $product = $this->product->find($productId);
            if (!$product) {
                throw new Exception('Sản phẩm không tồn tại');
            }

            // Get additional product data (images, variants, etc.)
            $productImages = $this->product->getImages($productId);
            $productVariants = $this->product->getVariants($productId);

            // Format product data for quick view
            $formattedProduct = [
                'id' => $product['id'],
                'name' => $product['name'],
                'slug' => $product['slug'],
                'description' => $product['description'],
                'price' => $product['price'],
                'sale_price' => $product['sale_price'],
                'status' => $product['status'],
                'featured_image' => $product['featured_image'],
                'images' => $productImages,
                'variants' => $productVariants,
                'category_name' => $product['category_name'] ?? '',
                'in_stock' => $product['status'] !== 'out_of_stock'
            ];

            echo json_encode([
                'success' => true,
                'product' => $formattedProduct
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get product details for quick view modal
     */
    public function getProductForQuickView()
    {
        try {
            $productId = $_GET['id'] ?? null;

            if (!$productId) {
                throw new Exception('Product ID is required');
            }

            // Get product details
            $product = $this->product->find($productId);
            if (!$product) {
                throw new Exception('Product not found');
            }

            // Get product variants
            $variants = $this->product->getVariants($productId);

            // Get product images (if available)
            $images = $this->product->getImages($productId) ?? [];

            // Format response
            $response = [
                'id' => $product['id'],
                'name' => $product['name'],
                'description' => $product['description'],
                'price' => $product['price'],
                'sale_price' => $product['sale_price'] ?? null,
                'featured_image' => $product['featured_image'],
                'image' => $product['featured_image'], // Backward compatibility
                'images' => $images,
                'variants' => $variants,
                'in_stock' => $product['status'] !== 'out_of_stock',
                'category_name' => $product['category_name'] ?? null,
                'rating' => $product['rating'] ?? 0,
                'review_count' => $product['review_count'] ?? 0
            ];

            echo json_encode([
                'success' => true,
                'product' => $response // Changed back to 'product' for client.js compatibility
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
?>
