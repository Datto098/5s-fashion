<?php

/**
 * Order Controller
 * Handle order and address management for checkout
 * 5S Fashion E-commerce Platform
 */

class OrderController extends Controller
{
    private $userModel;
    private $customerModel;

/**
 * Hiển thị trang checkout
 */
public function checkout()
{
    $user = getUser();
    $addresses = $this->customerModel->getCustomerAddresses($user['id']);        // Lấy danh sách voucher đã lưu còn hạn
        require_once dirname(__DIR__) . '/models/UserCoupon.php';
        $userCouponModel = new UserCoupon();

        // Tính tổng tiền đơn hàng từ session cart (mảng sản phẩm)
        $orderAmount = 0;
        if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $orderAmount += (isset($item['price']) ? $item['price'] : 0) * (isset($item['quantity']) ? $item['quantity'] : 1);
            }
        }
        $savedVouchers = $userCouponModel->getValidCouponsForCheckout($user['id'], $orderAmount);

        require dirname(__DIR__) . '/views/client/checkout/index.php';
    }

    public function __construct()
    {
        $this->userModel = $this->model('User');
        $this->customerModel = $this->model('Customer');

        // Check if user is logged in for address operations
        if (!isLoggedIn()) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
                exit;
            }
            redirect('login');
        }
    }

    /**
     * Get user addresses for AJAX
     */
    public function getAddresses()
    {
        header('Content-Type: application/json');

        try {
            $user = getUser();
            $addresses = $this->customerModel->getCustomerAddresses($user['id']);

            echo json_encode([
                'success' => true,
                'addresses' => $addresses ?: []
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Không thể tải địa chỉ: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Add new address
     */
    public function addAddress()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Sai phương thức']);
            exit;
        }

        try {
            // Get JSON input
            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input) {
                // Fallback to POST data
                $input = $_POST;
            }

            $user = getUser();

            $name = trim($input['name'] ?? $input['customerName'] ?? '');
            $phone = trim($input['phone'] ?? $input['customerPhone'] ?? '');
            $address = trim($input['address'] ?? '');
            $note = trim($input['note'] ?? $input['notes'] ?? '');
            $is_default = isset($input['is_default']) || isset($input['setDefault']) ? 1 : 0;

            // Validate required fields
            if (empty($name) || empty($address) || empty($phone)) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng nhập đầy đủ họ tên, số điện thoại và địa chỉ']);
                exit;
            }

            $addressData = [
                'user_id' => $user['id'],
                'name' => $name,
                'phone' => $phone,
                'address' => $address,
                'note' => $note,
                'is_default' => $is_default
            ];

            $result = $this->customerModel->addCustomerAddress($addressData);

            if ($result) {
                // Get updated addresses list
                $addresses = $this->customerModel->getCustomerAddresses($user['id']);

                echo json_encode([
                    'success' => true,
                    'message' => 'Thêm địa chỉ thành công!',
                    'addresses' => $addresses ?: []
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi thêm địa chỉ'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update address
     */
    public function updateAddress($id)
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Sai phương thức']);
            exit;
        }

        try {
            // Get JSON input for PUT requests
            if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
                $input = json_decode(file_get_contents('php://input'), true);
            } else {
                $input = $_POST;
            }

            if (!$input) {
                echo json_encode(['success' => false, 'message' => 'Không có dữ liệu']);
                exit;
            }

            $user = getUser();

            $name = trim($input['name'] ?? '');
            $phone = trim($input['phone'] ?? '');
            $address = trim($input['address'] ?? '');
            $note = trim($input['note'] ?? '');
            $is_default = isset($input['is_default']) ? 1 : 0;

            // Validate required fields
            if (empty($name) || empty($address) || empty($phone)) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng nhập đầy đủ họ tên, số điện thoại và địa chỉ']);
                exit;
            }

            $addressData = [
                'user_id' => $user['id'],
                'name' => $name,
                'phone' => $phone,
                'address' => $address,
                'note' => $note,
                'is_default' => $is_default
            ];

            $result = $this->customerModel->updateCustomerAddress($id, $user['id'], $addressData);

            if ($result) {
                // Get updated addresses list
                $addresses = $this->customerModel->getCustomerAddresses($user['id']);

                echo json_encode([
                    'success' => true,
                    'message' => 'Cập nhật địa chỉ thành công!',
                    'addresses' => $addresses ?: []
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi cập nhật địa chỉ'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete address
     */
    public function deleteAddress($id)
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Sai phương thức']);
            exit;
        }

        try {
            $user = getUser();
            $result = $this->customerModel->deleteCustomerAddress($id, $user['id']);

            if ($result) {
                // Get updated addresses list
                $addresses = $this->customerModel->getCustomerAddresses($user['id']);

                echo json_encode([
                    'success' => true,
                    'message' => 'Đã xóa địa chỉ thành công!',
                    'addresses' => $addresses ?: []
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể xóa địa chỉ!']);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Edit address - called from frontend JS
     */
    public function editAddress($id)
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Sai phương thức']);
            exit;
        }

        try {
            // Get JSON input for PUT requests
            if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
                $input = json_decode(file_get_contents('php://input'), true);
            } else {
                $input = $_POST;
            }

            if (!$input) {
                echo json_encode(['success' => false, 'message' => 'Không có dữ liệu']);
                exit;
            }

            $user = getUser();

            $name = trim($input['name'] ?? '');
            $phone = trim($input['phone'] ?? '');
            $address = trim($input['address'] ?? '');
            $note = trim($input['note'] ?? '');

            $is_default = isset($input['is_default']) ? 1 : 0;

            // Validate required fields
            if (empty($name) || empty($address) || empty($phone)) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng nhập đầy đủ họ tên, số điện thoại và địa chỉ']);
                exit;
            }

            $addressData = [
                'user_id' => $user['id'],
                'name' => $name,
                'phone' => $phone,
                'address' => $address,
                'note' => $note,
                'is_default' => $is_default
            ];

            $result = $this->customerModel->updateCustomerAddress($id, $user['id'], $addressData);

            if ($result) {
                // Get updated addresses list
                $addresses = $this->customerModel->getCustomerAddresses($user['id']);

                echo json_encode([
                    'success' => true,
                    'message' => 'Cập nhật địa chỉ thành công!',
                    'addresses' => $addresses ?: []
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi cập nhật địa chỉ'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Set default address
     */
    public function setDefaultAddress($id)
    {
        header('Content-Type: application/json');

        try {
            $user = getUser();
            $result = $this->customerModel->setDefaultAddress($id, $user['id']);

            if ($result) {
                // Get updated addresses list
                $addresses = $this->customerModel->getCustomerAddresses($user['id']);

                echo json_encode([
                    'success' => true,
                    'message' => 'Đã đặt địa chỉ mặc định!',
                    'addresses' => $addresses ?: []
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể đặt địa chỉ mặc định!']);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Place order
     */
    public function placeOrder()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Sai phương thức']);
            exit;
        }

        try {
            // Get JSON input
            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input) {
                echo json_encode(['success' => false, 'message' => 'Không có dữ liệu đơn hàng']);
                exit;
            }
            
            // Kiểm tra và lấy thông tin mã giảm giá từ session nếu có
            if (isset($_SESSION['applied_coupon'])) {
                // Lưu vào input data để sử dụng
                $input['discount'] = $_SESSION['applied_coupon']['discount_amount'];
                $input['coupon_id'] = $_SESSION['applied_coupon']['id'];
                $input['coupon_code'] = $_SESSION['applied_coupon']['code'];
            }

            // DEBUG: Log input data to see if status is being sent
            error_log('=== ORDER PLACE DEBUG ===');
            error_log('Raw input keys: ' . implode(', ', array_keys($input)));
            error_log('Input data: ' . json_encode($input));
            if (isset($input['status'])) {
                error_log('STATUS FOUND in input: "' . $input['status'] . '" (length: ' . strlen($input['status']) . ')');
            } else {
                error_log('STATUS NOT FOUND in input - GOOD!');
            }

            $user = getUser();

            // Validate required fields
            if (empty($input['customer']['name']) || empty($input['customer']['phone'])) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin khách hàng']);
                exit;
            }

            if (empty($input['shipping']['address'])) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng nhập địa chỉ giao hàng']);
                exit;
            }

            if (empty($input['payment']['method'])) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng chọn phương thức thanh toán']);
                exit;
            }

            // Get cart items from request or database cart
            $cartItems = [];

            if (!empty($input['items']) && is_array($input['items'])) {
                // Use items from request (preferred)
                $cartItems = $input['items'];
            } else {
                // Fallback to session cart for compatibility
                if (empty($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
                    // Try to get from database cart
                    require_once APP_PATH . '/models/Cart.php';
                    $cartModel = new Cart();
                    $dbCartItems = $cartModel->getCartItems();

                    if (empty($dbCartItems)) {
                        echo json_encode(['success' => false, 'message' => 'Giỏ hàng trống']);
                        exit;
                    }

                    // Convert DB cart format to expected format
                    foreach ($dbCartItems as $dbItem) {
                        $cartItems[] = [
                            'product_id' => $dbItem['product_id'],
                            'variant_id' => $dbItem['variant_id'],
                            'product_name' => $dbItem['product_name'],
                            'sku' => $dbItem['product_slug'] ?? '',
                            'variant' => $dbItem['variant_name'],
                            'quantity' => $dbItem['quantity'],
                            'price' => $dbItem['price']
                        ];
                    }
                } else {
                    $cartItems = $_SESSION['cart'];
                }
            }

            // Validate stock and prepare order items
            $orderItems = [];
            $subtotal = 0;

            foreach ($cartItems as $item) {
                // Validate item structure
                if (empty($item['product_id']) || empty($item['quantity']) || empty($item['price'])) {
                    echo json_encode(['success' => false, 'message' => 'Dữ liệu sản phẩm không hợp lệ']);
                    exit;
                }

                // Validate stock (optional, would require product model)
                $itemTotal = $item['price'] * $item['quantity'];
                $subtotal += $itemTotal;

                $orderItems[] = [
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'] ?? null,
                    'product_name' => $item['product_name'] ?? $item['name'] ?? '',
                    'product_sku' => $item['sku'] ?? '',
                    'variant_info' => isset($item['variant']) ? $item['variant'] : null,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $itemTotal
                ];
            }

            // Calculate order totals
            $shippingFee = (float)($input['shipping']['fee'] ?? 30000);
            $discountAmount = isset($input['discount_amount']) ? (float)$input['discount_amount'] : (float)($input['discount'] ?? 0);
            $totalAmount = $subtotal + $shippingFee - $discountAmount;

            // Prepare order data
            $orderData = [
                'user_id' => $user['id'],
                'customer_name' => $input['customer']['name'],
                'customer_email' => $user['email'] ?? $input['customer']['email'] ?? '',
                'customer_phone' => $input['customer']['phone'],
                'subtotal' => $subtotal,
                'tax_amount' => 0,
                'shipping_amount' => $shippingFee,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                // No status - let database default to 'pending'
                'payment_method' => $input['payment']['method'],
                'payment_status' => 'pending',
                'shipping_address' => json_encode([
                    'name' => $input['customer']['name'],
                    'phone' => $input['customer']['phone'],
                    'address' => $input['shipping']['address']
                ]),
                'notes' => $input['order_notes'] ?? ''
            ];

            // Create order using Order model
            require_once dirname(__DIR__) . '/models/Order.php';
            $orderModel = new Order();

            $orderId = $orderModel->createOrder($orderData, $orderItems);

            // Nếu có mã giảm giá đã áp dụng thì cập nhật user_coupons
            if ($orderId && isset($_SESSION['applied_coupon']) && !empty($_SESSION['applied_coupon']['id'])) {
                require_once dirname(__DIR__) . '/models/UserCoupon.php';
                $userCouponModel = new UserCoupon();
                $couponId = $_SESSION['applied_coupon']['id'];
                $userId = $user['id'];
                $userCouponModel->updateCouponUsed($userId, $couponId, $orderId);
            }

            if ($orderId) {
                // Get the created order for response
                $order = $orderModel->find($orderId);

                // Handle different payment methods
                $paymentMethod = $input['payment']['method'];

                if ($paymentMethod === 'cod') {
                    // COD - Direct success
                    unset($_SESSION['cart']);

                    echo json_encode([
                        'success' => true,
                        'message' => 'Đặt hàng thành công!',
                        'order_id' => $orderId,
                        'order_code' => $order['order_code'] ?? 'ORD-' . $orderId,
                        'total_amount' => $totalAmount,
                        'payment_method' => 'cod',
                        'redirect_url' => "/5s-fashion/order/success/{$orderId}"
                    ]);
                } elseif (in_array($paymentMethod, ['vnpay', 'momo'])) {
                    // Online payment - need redirect to payment gateway
                    echo json_encode([
                        'success' => true,
                        'message' => 'Đơn hàng đã được tạo. Đang chuyển hướng đến cổng thanh toán...',
                        'order_id' => $orderId,
                        'order_code' => $order['order_code'] ?? 'ORD-' . $orderId,
                        'total_amount' => $totalAmount,
                        'payment_method' => $paymentMethod,
                        'requires_payment' => true,
                        'payment_url' => "/5s-fashion/public/payment/{$paymentMethod}"
                    ]);
                } elseif ($paymentMethod === 'bank_transfer') {
                    // Bank transfer - show bank info
                    unset($_SESSION['cart']);

                    echo json_encode([
                        'success' => true,
                        'message' => 'Đặt hàng thành công! Vui lòng chuyển khoản theo thông tin bên dưới.',
                        'order_id' => $orderId,
                        'order_code' => $order['order_code'] ?? 'ORD-' . $orderId,
                        'total_amount' => $totalAmount,
                        'payment_method' => 'bank_transfer',
                        'bank_info' => [
                            'bank_name' => 'Vietcombank',
                            'account_number' => '1234567890',
                            'account_name' => '5S Fashion Co., Ltd',
                            'amount' => $totalAmount,
                            'content' => 'Thanh toan don hang ' . ($order['order_code'] ?? 'ORD-' . $orderId)
                        ],
                        'redirect_url' => "/5s-fashion/public/order/success?id={$orderId}"
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Phương thức thanh toán không hợp lệ'
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Không thể tạo đơn hàng. Vui lòng thử lại.'
                ]);
            }

        } catch (Exception $e) {
            error_log('Place Order Error: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi đặt hàng: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get address by ID
     */
    public function getAddress($id)
    {
        // Lấy model Address (hoặc UserAddress)
        $addressModel = $this->model('Customer');
        $address = $this->customerModel->getCustomerAddressById($id);

        if ($address && $address['user_id'] == $_SESSION['user_id']) {
            echo json_encode([
                'success' => true,
                'address' => $address
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Không tìm thấy địa chỉ'
            ]);
        }
        exit;
    }

    /**
     * Show order success page
     */
    public function success($orderCodeOrId = null)
    {
        require_once dirname(__DIR__) . '/models/Order.php';
        $orderModel = new Order();
        $order = null;

        // Check if we have a parameter from URL
        if ($orderCodeOrId) {
            // Check if it's an order code (starts with letters) or numeric ID
            if (is_numeric($orderCodeOrId)) {
                // It's a numeric ID
                $order = $orderModel->getFullDetails($orderCodeOrId);
            } else {
                // It's an order code (like ORD2508104453)
                $order = $orderModel->getOrderWithItems($orderCodeOrId);
            }
        }

        // Also check GET parameter for backwards compatibility
        $orderCode = $_GET['order'] ?? null;
        if (!$order && $orderCode) {
            $order = $orderModel->getOrderWithItems($orderCode);
        }

        // Verify order belongs to current user (if logged in)
        if ($order && isLoggedIn()) {
            $user = getUser();
            if ($order['user_id'] != $user['id']) {
                $order = null; // Don't show other user's orders
            }
        }

        if (!$order) {
            header('Location: /5s-fashion/');
            exit;
        }

        $data = [
            'title' => 'Đặt hàng thành công - 5S Fashion',
            'order' => $order,
            'orderCode' => $orderCodeOrId
        ];

        require dirname(__DIR__) . '/views/client/order/success.php';
    }

    /**
     * Show order tracking page
     */
    public function tracking()
    {
        $data = [
            'title' => 'Theo dõi đơn hàng - 5S Fashion'
        ];

        // If user is logged in, get their orders
        $orders = [];
        if (isLoggedIn()) {
            $user = getUser();
            require_once dirname(__DIR__) . '/models/Order.php';
            $orderModel = new Order();
            $orders = $orderModel->getByUser($user['id'], 10);
        }

        $data['orders'] = $orders;

        require dirname(__DIR__) . '/views/client/order/tracking.php';
    }

    /**
     * Place a new order
     */
    public function place()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
            exit;
        }

        // Check if user is logged in
        session_start();
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để đặt hàng']);
            exit;
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $userId = $_SESSION['user_id'];

            // Get user's cart items from database
            $sql = "SELECT c.*, p.name as product_name, p.price as product_price
                    FROM carts c
                    LEFT JOIN products p ON c.product_id = p.id
                    WHERE c.user_id = ?";

            require_once dirname(__DIR__) . '/core/Database.php';
            $database = new Database();
            $pdo = $database->getConnection();
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$userId]);
            $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($cartItems)) {
                echo json_encode(['success' => false, 'message' => 'Giỏ hàng trống']);
                exit;
            }

            // Calculate totals
            $subtotal = 0;
            foreach ($cartItems as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }

            $shippingCost = $input['totals']['shipping'] ?? 30000;
            $discount = $input['totals']['discount'] ?? 0;
            $total = $subtotal + $shippingCost - $discount;

            // Generate order code
            $orderCode = 'ORD' . date('Ymd') . rand(1000, 9999);

            // Create order data
            $orderData = [
                'user_id' => $userId,
                'order_code' => $orderCode,
                'customer_name' => $input['customer']['name'] ?? '',
                'customer_phone' => $input['customer']['phone'] ?? '',
                'subtotal' => $subtotal,
                'shipping_amount' => $shippingCost,
                'discount_amount' => $discount,
                'total_amount' => $total,
                // No status - let database default to 'pending'
                'payment_method' => $input['payment']['method'] ?? 'cod',
                'payment_status' => 'pending',
                'shipping_address' => json_encode($input['shipping'] ?? []),
                'notes' => $input['order_notes'] ?? ''
            ];

            // Create order
            require_once dirname(__DIR__) . '/models/Order.php';
            $orderModel = new Order();
            $orderId = $orderModel->create($orderData);

            if ($orderId) {
                // Create order items
                $this->createOrderItems($orderId, $cartItems);

                echo json_encode([
                    'success' => true,
                    'order_id' => $orderId,
                    'order_code' => $orderCode,
                    'message' => 'Đơn hàng đã được tạo thành công'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể tạo đơn hàng']);
            }

        } catch (Exception $e) {
            error_log('Order creation error: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo đơn hàng. Vui lòng thử lại.'
            ]);
        }
    }

    /**
     * Create order items
     */
    private function createOrderItems($orderId, $cartItems)
    {
        try {
            require_once dirname(__DIR__) . '/core/Database.php';
            $database = new Database();
            $pdo = $database->getConnection();

            $sql = "INSERT INTO order_items (order_id, product_id, product_variant_id, quantity, price, subtotal)
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);

            foreach ($cartItems as $item) {
                $subtotal = $item['price'] * $item['quantity'];
                $stmt->execute([
                    $orderId,
                    $item['product_id'],
                    $item['product_variant_id'] ?? null,
                    $item['quantity'],
                    $item['price'],
                    $subtotal
                ]);
            }

        } catch (Exception $e) {
            error_log('Error creating order items: ' . $e->getMessage());
            throw $e;
        }
    }
}
?>
