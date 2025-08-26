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

            // Debug: Log quantity specifically
            error_log("AjaxController::addToCart - Product ID: $productId, Quantity received: $quantity (type: " . gettype($quantity) . ")");

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

            // Cart::addToCart now returns structured array ['success'=>bool, 'quantity'=>int, 'clamped'=>bool] or ['success'=>false,'message'=>...]
            if (is_array($result)) {
                if (empty($result['success'])) {
                    throw new Exception($result['message'] ?? 'Could not add product to cart');
                }
                $addedQuantity = $result['quantity'] ?? $quantity;
                $wasClamped = !empty($result['clamped']);
            } else {
                // Backwards compatibility: boolean true/false
                if (!$result) {
                    throw new Exception('Could not add product to cart');
                }
                $addedQuantity = $quantity;
                $wasClamped = false;
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
                'item' => $addedItem,
                'added_quantity' => $addedQuantity,
                'clamped' => !empty($wasClamped)
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


            // Server-side stock validation: ensure requested quantity does not exceed available stock
            $clamped = false;
            // Try to get cart item to determine product/variant
            $cartItem = $cartModel->getCartItemById($cartKey);
            if ($cartItem) {
                // Use available stock that subtracts reserved and quantities in other carts
                $availableStock = $cartModel->getAvailableStock($cartItem['product_id'], $cartItem['variant_id'] ?? null, $cartItem['id']);

                // If availableStock is numeric, clamp quantity to it
                if (is_numeric($availableStock)) {
                    $availableStock = (int)$availableStock;
                    if ($availableStock <= 0) {
                        // No stock available: remove item from cart
                        $cartModel->removeItem($cartKey);
                        echo json_encode([
                            'success' => false,
                            'message' => 'Sản phẩm hiện đã hết hàng và đã được xóa khỏi giỏ hàng',
                            'cart_count' => $cartModel->getCartCount(),
                            'cart_total' => $cartModel->getCartTotal()
                        ]);
                        return;
                    }

                    if ($quantity > $availableStock) {
                        $quantity = $availableStock;
                        $clamped = true;
                    }
                }
            }

            // Update quantity using cart ID
            $result = $cartModel->updateQuantity($cartKey, $quantity);

            if (!$result) {
                throw new Exception('Failed to update cart item');
            }

            // Get updated cart info
            $cartTotal = $cartModel->getCartTotal();
            $cartCount = $cartModel->getCartCount();

            $response = [
                'success' => true,
                'message' => 'Giỏ hàng đã được cập nhật!',
                'cart_count' => $cartCount,
                'cart_total' => $cartTotal
            ];

            if (isset($clamped) && $clamped) {
                $response['clamped'] = true;
                $response['message'] = 'Số lượng đã được điều chỉnh về mức tồn kho hiện có';
            }

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

            // Safely obtain product_id from different body formats.
            $productId = $_POST['product_id'] ?? $_POST['productId'] ?? null;

            // If not present in $_POST, attempt to parse raw input (JSON or urlencoded)
            if (empty($productId)) {
                $contentType = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '';
                $raw = file_get_contents('php://input');
                if ($raw) {
                    // Try JSON
                    if (stripos($contentType, 'application/json') !== false) {
                        $json = json_decode($raw, true);
                        if (is_array($json)) {
                            $productId = $json['product_id'] ?? $json['productId'] ?? $productId;
                        }
                    } else {
                        // Parse urlencoded raw body
                        parse_str($raw, $parsed);
                        if (is_array($parsed)) {
                            $productId = $parsed['product_id'] ?? $parsed['productId'] ?? $productId;
                        }
                    }
                }
            }

            // Normalize to int when present
            $productId = $productId !== null ? (int)$productId : null;

            if (!$productId) {
                // Return JSON error instead of throwing (keeps AJAX flow consistent)
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Product ID is required',
                    'productId' => $productId
                ]);
                return;
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
            // $productImages = $this->product->getImages($productId);
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
                // 'images' => $productImages,
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
            $variantsRaw = $this->product->getVariants($productId);
            // Lọc trùng: chỉ giữ 1 variant cho mỗi cặp color-size (ưu tiên còn hàng)
            $variantMap = [];
            foreach ($variantsRaw as $variant) {
                $color = isset($variant['color']) ? $variant['color'] : '';
                $size = isset($variant['size']) ? $variant['size'] : '';
                $key = $color . '|' . $size;
                if (!isset($variantMap[$key])) {
                    $variantMap[$key] = $variant;
                } else {
                    // Nếu variant này còn hàng mà variant trước hết hàng thì thay thế
                    $old = $variantMap[$key];
                    if ((!empty($variant['stock_quantity']) && $variant['stock_quantity'] > 0) && (empty($old['stock_quantity']) || $old['stock_quantity'] <= 0)) {
                        $variantMap[$key] = $variant;
                    }
                }
            }
            $variants = array_values($variantMap);

            // Normalize variants to include available stock (stock - reserved - qty in other carts)
            // Use Cart model helper to compute available stock
            if (!class_exists('Cart')) {
                require_once APP_PATH . '/models/Cart.php';
            }
            $cartModel = new Cart();
            foreach ($variants as &$v) {
                $vid = $v['id'] ?? null;
                $available = null;
                if ($vid) {
                    $available = $cartModel->getAvailableStock($productId, $vid, null);
                }
                // If we couldn't resolve available, fall back to provided stock_quantity
                if (!is_numeric($available)) {
                    $available = isset($v['stock_quantity']) ? (int)$v['stock_quantity'] : (isset($v['stock']) ? (int)$v['stock'] : 0);
                }
                // Expose both fields for compatibility
                $v['available_stock'] = (int)$available;
                $v['stock_quantity'] = (int)$available; // overwrite so client uses available value
            }

            // Get product images (if available)
            // $images = $this->product->getImages($productId) ?? [];

            // Format response
            $response = [
                'id' => $product['id'],
                'name' => $product['name'],
                'description' => $product['description'],
                'price' => $product['price'],
                'sale_price' => $product['sale_price'] ?? null,
                'featured_image' => $product['featured_image'],
                'image' => $product['featured_image'],
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

    /**
     * Like a review
     *
     * @param int $id Review ID
     * @return void
     */
    public function reviewLike($id = null)
    {
        // Debug
        // error_log("AjaxController::reviewLike called with ID: $id");

        if (!isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Bạn cần đăng nhập để thực hiện thao tác này']);
            return;
        }

        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID đánh giá không hợp lệ']);
            return;
        }

        $userId = $_SESSION['user']['id'];

        // Load Review model
        require_once APP_PATH . '/models/Review.php';
        $reviewModel = new Review();

        // Kiểm tra xem đánh giá có tồn tại không
        $review = $reviewModel->findById($id);
        if (!$review) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy đánh giá']);
            return;
        }

        // Kiểm tra xem người dùng đã like đánh giá này chưa
        $hasLiked = $reviewModel->hasUserLikedReview($id, $userId);
        error_log("User has liked review $id: " . ($hasLiked ? 'true' : 'false'));
        if ($hasLiked) {
            // Nếu đã like, thì unlike (xóa like)
            $result = $reviewModel->removeLike($id, $userId);
            $action = 'unliked';
            $message = 'Đã bỏ thích đánh giá';
        } else {
            // Nếu chưa like, thì thêm like
            $result = $reviewModel->addLike($id, $userId);
            $action = 'liked';
            $message = 'Cảm ơn bạn đã đánh giá nội dung này là hữu ích';
        }

        if ($result) {
            // Lấy số lượt thích mới
            $updatedReview = $reviewModel->findById($id);
            $helpfulCount = $updatedReview['helpful_count'] ?? 0;

            echo json_encode([
                'success' => true,
                'message' => $message,
                'helpfulCount' => $helpfulCount,
                'action' => $action,
                'hasLiked' => !$hasLiked  // Toggle the state
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Đã xảy ra lỗi khi xử lý yêu cầu']);
        }
    }

    /**
     * Add a new review
     *
     * @return void
     */
    public function reviewAdd()
    {
        // Debug
        error_log("AjaxController::reviewAdd called");
        error_log("POST data: " . print_r($_POST, true));
        error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
        error_log("Raw input: " . file_get_contents('php://input'));

        // Check if user is logged in
        if (!isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Bạn cần đăng nhập để đánh giá sản phẩm']);
            return;
        }

        $userId = $_SESSION['user']['id'];
        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
        $content = isset($_POST['content']) ? trim($_POST['content']) : '';
        $reviewId = isset($_POST['review_id']) ? (int)$_POST['review_id'] : 0;

        // Validate input
        if (!$productId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID sản phẩm không hợp lệ']);
            return;
        }
        if ($rating < 1 || $rating > 5) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Đánh giá phải từ 1-5 sao']);
            return;
        }
        if (empty($content)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Vui lòng nhập nội dung đánh giá']);
            return;
        }

        // Load required models
        require_once APP_PATH . '/models/Review.php';
        require_once APP_PATH . '/models/Order.php';
        $reviewModel = new Review();
        $orderModel = new Order();

        if ($reviewId) {
            // Sửa review
            $review = $reviewModel->findById($reviewId);
            if (!$review || $review['user_id'] != $userId) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Bạn không có quyền sửa đánh giá này']);
                return;
            }
            // Cập nhật review
            $updateData = [
                'rating' => $rating,
                'title' => $title,
                'content' => $content,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            try {
                $result = $reviewModel->update($reviewId, $updateData);
                if ($result) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Đã cập nhật đánh giá thành công!',
                        'review_id' => $reviewId
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Đã xảy ra lỗi khi cập nhật đánh giá']);
                }
            } catch (Exception $e) {
                error_log("Error updating review: " . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()]);
            }
            return;
        }

        // Thêm mới review (giữ nguyên logic cũ)
        // Check if user has already reviewed this product
        if ($reviewModel->hasUserReviewedProduct($userId, $productId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Bạn đã đánh giá sản phẩm này rồi']);
            return;
        }
        // Check if user has purchased this product
        $hasPurchased = $orderModel->hasUserPurchasedProduct($userId, $productId);
        if (!$hasPurchased) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Bạn chỉ có thể đánh giá sản phẩm mà bạn đã mua']);
            return;
        }
        // Create the review
        $reviewData = [
            'product_id' => $productId,
            'user_id' => $userId,
            'rating' => $rating,
            'title' => $title,
            'content' => $content,
            'status' => 'approved'
        ];
        try {
            $result = $reviewModel->create($reviewData);
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Cảm ơn bạn đã đánh giá sản phẩm!',
                    'review_id' => $result
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Đã xảy ra lỗi khi thêm đánh giá']);
            }
        } catch (Exception $e) {
            error_log("Error adding review: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete a review
     *
     * @param int $id Review ID
     * @return void
     */
    public function reviewDelete($id = null)
    {
        // Debug
        error_log("AjaxController::reviewDelete called with ID: $id");

        if (!isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Bạn cần đăng nhập để thực hiện thao tác này']);
            return;
        }

        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID đánh giá không hợp lệ']);
            return;
        }

        $userId = $_SESSION['user']['id'];

        // Load Review model
        require_once APP_PATH . '/models/Review.php';
        $reviewModel = new Review();

        // Kiểm tra xem đánh giá có tồn tại và thuộc về người dùng hiện tại không
        $review = $reviewModel->findById($id);
        if (!$review) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy đánh giá']);
            return;
        }

        if ($review['user_id'] != $userId) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Bạn không có quyền xóa đánh giá này']);
            return;
        }

        // Xóa đánh giá
        $result = $reviewModel->delete($id);

        // Kiểm tra xem người dùng còn đánh giá nào cho sản phẩm không
        $productId = $review['product_id'];
        $hasMoreReviews = $reviewModel->hasUserReviewedProduct($userId, $productId);

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Đánh giá đã được xóa thành công',
                'canAddReview' => !$hasMoreReviews, // true nếu không còn đánh giá nào
                'productId' => $productId
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Đã xảy ra lỗi khi xóa đánh giá']);
        }
    }
}
?>
