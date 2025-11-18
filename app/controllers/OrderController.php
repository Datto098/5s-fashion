<?php


/**
 * Order Controller
 * Handle order and address management for checkout
 * zone Fashion E-commerce Platform
 */

class OrderController extends BaseController
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

            if ($result !== false) {
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

            if ($result !== false) {
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

                // Calculate totals
                $itemTotal = $item['price'] * $item['quantity'];
                $subtotal += $itemTotal;

                // Resolve variant_id when it's missing but variant info exists (session cart often stores 'variant' string)
                $resolvedVariantId = $item['variant_id'] ?? null;
                if (empty($resolvedVariantId) && !empty($item['variant'])) {
                    // If variant value is numeric, assume it's an ID
                    if (is_numeric($item['variant'])) {
                        $resolvedVariantId = (int)$item['variant'];
                    } else {
                        // Try to resolve by SKU first, then by variant_name for the product
                        require_once dirname(__DIR__) . '/models/ProductVariant.php';
                        $found = ProductVariant::getBySku($item['variant']);
                        if ($found && isset($found['id'])) {
                            $resolvedVariantId = $found['id'];
                        } else {
                            // Fallback: search variants for this product and match variant_name
                            $variants = ProductVariant::getByProduct($item['product_id'], false);
                            foreach ($variants as $v) {
                                if (isset($v['variant_name']) && mb_strtolower(trim($v['variant_name'])) === mb_strtolower(trim($item['variant']))) {
                                    $resolvedVariantId = $v['id'];
                                    break;
                                }
                            }
                        }
                    }
                }

                // If variant_id still not resolved, try extra heuristics: variant_sku field, part before '|' or matching substring
                if (empty($resolvedVariantId) && !empty($item['variant'])) {
                    // Try explicit variant_sku first if provided
                    if (!empty($item['variant_sku'])) {
                        require_once dirname(__DIR__) . '/models/ProductVariant.php';
                        $foundBySku = ProductVariant::getBySku($item['variant_sku']);
                        if ($foundBySku && isset($foundBySku['id'])) {
                            $resolvedVariantId = $foundBySku['id'];
                        }
                    }

                    // If still not found, try trimming the part before a pipe '|' which some clients append
                    if (empty($resolvedVariantId) && mb_strpos($item['variant'], '|') !== false) {
                        $parts = explode('|', $item['variant']);
                        $candidate = trim($parts[0]);
                        require_once dirname(__DIR__) . '/models/ProductVariant.php';
                        $found = ProductVariant::getBySku($candidate);
                        if ($found && isset($found['id'])) {
                            $resolvedVariantId = $found['id'];
                        } else {
                            // try matching by variant_name substring
                            $variants = ProductVariant::getByProduct($item['product_id'], false);
                            foreach ($variants as $v) {
                                if (isset($v['variant_name']) && mb_stripos($v['variant_name'], $candidate) !== false) {
                                    $resolvedVariantId = $v['id'];
                                    break;
                                }
                            }
                        }
                    }

                    // Final heuristic: try to match by last two segments (color/size) separated by ' - '
                    if (empty($resolvedVariantId)) {
                        $candidateFull = $item['variant'];
                        $segments = preg_split('/\s*-\s*/u', $candidateFull);
                        if (count($segments) >= 2) {
                            // try match using last two segments joined
                            $tail = trim(implode(' - ', array_slice($segments, -2)));
                            require_once dirname(__DIR__) . '/models/ProductVariant.php';
                            $variants = ProductVariant::getByProduct($item['product_id'], false);
                            foreach ($variants as $v) {
                                if (isset($v['variant_name']) && mb_stripos($v['variant_name'], $tail) !== false) {
                                    $resolvedVariantId = $v['id'];
                                    break;
                                }
                            }
                        }
                    }
                }

                $orderItems[] = [
                    'product_id' => $item['product_id'],
                    'variant_id' => $resolvedVariantId,
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
                'status' => 'pending',
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

            // Nếu có mã giảm giá đã áp dụng thì ghi nhận usage vào coupon_usage và tăng used_count
            if ($orderId && isset($_SESSION['applied_coupon']) && !empty($_SESSION['applied_coupon']['id'])) {
                require_once dirname(__DIR__) . '/models/Coupon.php';
                $couponModel = new Coupon();
                $couponId = $_SESSION['applied_coupon']['id'];
                $userId = $user['id'];
                // discount amount persisted in order data
                $discountAmount = isset($orderData['discount_amount']) ? (float)$orderData['discount_amount'] : 0.0;

                // applyCoupon will insert into coupon_usage, increment coupons.used_count
                // and mark user_coupons as used when $userId is provided
                $applied = $couponModel->applyCoupon($couponId, $orderId, $userId, $discountAmount);
                if (!$applied) {
                    error_log("[COUPON] Failed to record coupon usage for coupon_id={$couponId}, order_id={$orderId}");
                }
            }

            // Clear applied coupon from session so it won't be reused for future orders
            if (isset($_SESSION['applied_coupon'])) {
                unset($_SESSION['applied_coupon']);
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
                        'redirect_url' => "/zone-fashion/order/success/{$orderId}"
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
                        'payment_url' => "/zone-fashion/public/payment/{$paymentMethod}"
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
                            'account_name' => 'zone Fashion Co., Ltd',
                            'amount' => $totalAmount,
                            'content' => 'Thanh toan don hang ' . ($order['order_code'] ?? 'ORD-' . $orderId)
                        ],
                        'redirect_url' => "/zone-fashion/public/order/success?id={$orderId}"
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
            header('Location: /zone-fashion/');
            exit;
        }

        $data = [
            'title' => 'Đặt hàng thành công - zone Fashion',
            'order' => $order,
            'orderCode' => $orderCodeOrId
        ];

        // Clear applied coupon from session after viewing success page
        if (isset($_SESSION['applied_coupon'])) {
            unset($_SESSION['applied_coupon']);
        }

    // Render success page using controller helper to apply layout
    $this->render('client/order/success', $data, 'client/layouts/app');
    }

    /**
     * Show order tracking page
     */
    public function tracking()
    {
        $data = [
            'title' => 'Theo dõi đơn hàng - zone Fashion'
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

    /**
     * Download invoice for an order - Simple HTML version
     */
    public function downloadInvoice()
    {
        try {
            // Get order ID from URL
            $orderId = $_GET['order_id'] ?? null;
            
            if (!$orderId) {
                echo "Mã đơn hàng không hợp lệ";
                return;
            }

            $user = getUser();
            if (!$user) {
                echo "Vui lòng đăng nhập";
                return;
            }
            
            // Get order details
            $order = $this->getSimpleOrderDetails($orderId, $user['id']);
            
            if (!$order) {
                echo "Không tìm thấy đơn hàng";
                return;
            }

            // Output HTML invoice directly
            $this->outputInvoiceHtml($order);
            
        } catch (Exception $e) {
            echo "Có lỗi xảy ra: " . $e->getMessage();
        }
    }

    /**
     * Get simple order details
     */
    private function getSimpleOrderDetails($orderId, $userId)
    {
        try {
            $db = Database::getInstance()->getConnection();
            
            // Get order
            $stmt = $db->prepare("
                SELECT o.*, 
                       COALESCE(o.customer_name, u.username) as customer_name,
                       COALESCE(o.customer_email, u.email) as customer_email,
                       COALESCE(o.customer_phone, u.phone) as customer_phone
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                WHERE o.id = ? AND o.user_id = ?
            ");
            $stmt->execute([$orderId, $userId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$order) return null;
            
            // Get order items
            $stmt = $db->prepare("
                SELECT oi.*, 
                       p.name as product_name
                FROM order_items oi
                LEFT JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?
            ");
            $stmt->execute([$orderId]);
            $order['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $order;
            
        } catch (Exception $e) {
            error_log('Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Output simple HTML invoice
     */
    private function outputInvoiceHtml($order)
    {
        $orderDate = date('d/m/Y H:i', strtotime($order['created_at']));
        $shippingData = json_decode($order['shipping_address'], true);
        $customerAddress = $shippingData['address'] ?? '';

        header('Content-Type: text/html; charset=utf-8');
        
        echo "<!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Hóa đơn {$order['order_code']}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
                .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px; }
                .company-name { font-size: 24px; font-weight: bold; color: #333; margin-bottom: 10px; }
                .invoice-title { font-size: 20px; margin: 20px 0; font-weight: bold; }
                .info-row { display: flex; justify-content: space-between; margin-bottom: 30px; }
                .info-col { width: 48%; }
                .info-title { font-weight: bold; margin-bottom: 10px; font-size: 16px; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
                .info-item { margin-bottom: 8px; }
                table { width: 100%; border-collapse: collapse; margin: 30px 0; }
                th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
                th { background-color: #f5f5f5; font-weight: bold; }
                .text-center { text-align: center; }
                .text-right { text-align: right; }
                .total-row { font-weight: bold; background-color: #f9f9f9; font-size: 16px; }
                .footer { margin-top: 40px; text-align: center; color: #666; }
                .print-btn { 
                    background: #007bff; color: white; border: none; padding: 15px 30px; 
                    font-size: 16px; border-radius: 5px; cursor: pointer; margin: 20px;
                }
                @media print { .print-btn { display: none; } }
            </style>
        </head>
        <body>
            <div class='header'>
                <div class='company-name'>Zone Fashion</div>
                <div>Địa chỉ: 123 Đường ABC, Quận XYZ, TP.HCM</div>
                <div>Hotline: 1900 1900 | Email: info@zone-fashion.com</div>
                <div class='invoice-title'>HÓA ĐƠN BÁN HÀNG</div>
            </div>
            
            <div class='info-row'>
                <div class='info-col'>
                    <div class='info-title'>Thông tin đơn hàng</div>
                    <div class='info-item'><strong>Mã đơn hàng:</strong> {$order['order_code']}</div>
                    <div class='info-item'><strong>Ngày đặt:</strong> {$orderDate}</div>
                    <div class='info-item'><strong>Thanh toán:</strong> Thanh toán khi nhận hàng</div>
                </div>
                <div class='info-col'>
                    <div class='info-title'>Thông tin khách hàng</div>
                    <div class='info-item'><strong>Họ tên:</strong> {$order['customer_name']}</div>
                    <div class='info-item'><strong>Điện thoại:</strong> {$order['customer_phone']}</div>
                    <div class='info-item'><strong>Email:</strong> {$order['customer_email']}</div>
                    <div class='info-item'><strong>Địa chỉ:</strong> {$customerAddress}</div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th class='text-center'>STT</th>
                        <th>Tên sản phẩm</th>
                        <th class='text-center'>Số lượng</th>
                        <th class='text-right'>Đơn giá</th>
                        <th class='text-right'>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>";

        $stt = 1;
        foreach ($order['items'] as $item) {
            $subtotal = $item['price'] * $item['quantity'];
            echo "<tr>
                <td class='text-center'>{$stt}</td>
                <td>{$item['product_name']}</td>
                <td class='text-center'>{$item['quantity']}</td>
                <td class='text-right'>" . number_format($item['price'], 0, ',', '.') . "đ</td>
                <td class='text-right'>" . number_format($subtotal, 0, ',', '.') . "đ</td>
            </tr>";
            $stt++;
        }

        echo "<tr class='total-row'>
                <td colspan='4' class='text-right'>TỔNG CỘNG:</td>
                <td class='text-right'>" . number_format($order['total_amount'], 0, ',', '.') . "đ</td>
            </tr>
            </tbody>
            </table>

            <div class='footer'>
                <p><strong>Cảm ơn quý khách đã mua sắm tại Zone Fashion!</strong></p>
                <p>Đây là hóa đơn điện tử, vui lòng lưu giữ để tra cứu và bảo hành sản phẩm.</p>
            </div>
            
            <div style='text-align: center;'>
                <button class='print-btn' onclick='window.print()'>🖨️ In hóa đơn</button>
            </div>
        </body>
        </html>";
    }

    /**
     * Get order details with items
     */
    private function getOrderDetails($orderId, $userId)
    {
        try {
            $db = Database::getInstance()->getConnection();
            
            // Get order basic info
            $stmt = $db->prepare("
                SELECT o.*, 
                       COALESCE(o.customer_name, u.username) as customer_name,
                       COALESCE(o.customer_email, u.email) as customer_email,
                       COALESCE(o.customer_phone, u.phone) as customer_phone
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                WHERE o.id = ? AND o.user_id = ?
            ");
            $stmt->execute([$orderId, $userId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$order) return null;
            
            // Get order items
            $stmt = $db->prepare("
                SELECT oi.*, 
                       p.name as product_name,
                       p.sku as product_sku,
                       pv.variant_name,
                       pv.color,
                       pv.size
                FROM order_items oi
                LEFT JOIN products p ON oi.product_id = p.id
                LEFT JOIN product_variants pv ON oi.product_variant_id = pv.id
                WHERE oi.order_id = ?
                ORDER BY oi.id
            ");
            $stmt->execute([$orderId]);
            $order['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $order;
            
        } catch (Exception $e) {
            error_log('Error getting order details: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate and output PDF invoice
     */
    private function generateInvoicePdf($order)
    {
        // Require DomPDF
        require_once dirname(__DIR__, 2) . '/vendor/autoload.php';
        
        // Configure DomPDF options
        $options = new \Dompdf\Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        
        // Create DomPDF instance
        $dompdf = new \Dompdf\Dompdf($options);
        
        // Generate HTML for PDF
        $html = $this->generateInvoiceHtml($order, true); // true for PDF format
        
        // Load HTML to DomPDF
        $dompdf->loadHtml($html);
        
        // Set paper size and orientation
        $dompdf->setPaper('A4', 'portrait');
        
        // Render PDF
        $dompdf->render();
        
        // Set headers for PDF download
        $filename = 'hoa-don-' . $order['order_code'] . '.pdf';
        
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        
        // Output PDF
        echo $dompdf->output();
        exit;
    }

    /**
     * Generate invoice HTML
     */
    private function generateInvoiceHtml($order, $forPdf = false)
    {
        $orderDate = date('d/m/Y H:i', strtotime($order['created_at']));
        $orderCode = $order['order_code'];
        $customerName = $order['customer_name'] ?: 'Khách hàng';
        $customerPhone = $order['customer_phone'] ?: '';
        $customerEmail = $order['customer_email'] ?: '';
        $shippingAddress = $order['shipping_address'] ?: '';
        $paymentMethod = $this->getPaymentMethodName($order['payment_method']);
        $totalAmount = number_format($order['total_amount'], 0, ',', '.');
        
        $itemsHtml = '';
        $itemNumber = 1;
        
        foreach ($order['items'] as $item) {
            $productName = $item['product_name'];
            $variant = '';
            if ($item['variant_name']) {
                $variant = ' (' . $item['variant_name'] . ')';
            } elseif ($item['color'] || $item['size']) {
                $variant = ' (' . ($item['color'] ?: '') . ($item['color'] && $item['size'] ? ', ' : '') . ($item['size'] ?: '') . ')';
            }
            
            $subtotal = number_format($item['price'] * $item['quantity'], 0, ',', '.');
            
            $itemsHtml .= "
                <tr>
                    <td style='border: 1px solid #ddd; padding: 8px; text-align: center;'>{$itemNumber}</td>
                    <td style='border: 1px solid #ddd; padding: 8px;'>{$productName}{$variant}</td>
                    <td style='border: 1px solid #ddd; padding: 8px; text-align: center;'>{$item['quantity']}</td>
                    <td style='border: 1px solid #ddd; padding: 8px; text-align: right;'>" . number_format($item['price'], 0, ',', '.') . "đ</td>
                    <td style='border: 1px solid #ddd; padding: 8px; text-align: right;'>{$subtotal}đ</td>
                </tr>
            ";
            $itemNumber++;
        }

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Hóa đơn {$orderCode}</title>
            <style>
                body { 
                    font-family: 'DejaVu Sans', Arial, sans-serif; 
                    margin: 0; 
                    padding: 20px; 
                    font-size: " . ($forPdf ? '12px' : '14px') . "; 
                    line-height: 1.4;
                }
                .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px; }
                .company-name { font-size: " . ($forPdf ? '20px' : '24px') . "; font-weight: bold; color: #333; margin-bottom: 10px; }
                .company-info { font-size: " . ($forPdf ? '11px' : '13px') . "; color: #666; margin-bottom: 5px; }
                .invoice-title { font-size: " . ($forPdf ? '16px' : '20px') . "; margin: 20px 0; font-weight: bold; }
                .info-section { margin: 20px 0; }
                .info-row { display: table; width: 100%; margin-bottom: 20px; }
                .info-col { display: table-cell; width: 48%; vertical-align: top; padding: 0 10px; }
                .info-col:first-child { padding-left: 0; }
                .info-col:last-child { padding-right: 0; }
                .info-title { font-weight: bold; margin-bottom: 10px; font-size: " . ($forPdf ? '13px' : '14px') . "; }
                .info-item { margin-bottom: 5px; font-size: " . ($forPdf ? '11px' : '12px') . "; }
                table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                th, td { border: 1px solid #ddd; padding: " . ($forPdf ? '6px' : '8px') . "; text-align: left; }
                th { background-color: #f5f5f5; font-weight: bold; font-size: " . ($forPdf ? '11px' : '12px') . "; }
                td { font-size: " . ($forPdf ? '10px' : '11px') . "; }
                .text-center { text-align: center; }
                .text-right { text-align: right; }
                .total-row { font-weight: bold; background-color: #f9f9f9; }
                .total-row td { font-size: " . ($forPdf ? '12px' : '13px') . "; }
                .footer { margin-top: 30px; text-align: center; font-size: " . ($forPdf ? '10px' : '12px') . "; color: #666; }
                .footer p { margin: 5px 0; }
                " . (!$forPdf ? ".print-btn { background: #007bff; color: white; border: none; padding: 10px 20px; margin: 10px; border-radius: 5px; cursor: pointer; }
                @media print { .print-btn { display: none; } }" : "") . "
            </style>
        </head>
        <body>
            <div class='header'>
                <div class='company-name'>Zone Fashion</div>
                <div class='company-info'>Địa chỉ: 123 Đường ABC, Quận XYZ, TP.HCM</div>
                <div class='company-info'>Hotline: 1900 1900 | Email: info@zone-fashion.com</div>
                <div class='invoice-title'>HÓA ĐƠN BÁN HÀNG</div>
            </div>
            
            <div class='info-section'>
                <div class='info-row'>
                    <div class='info-col'>
                        <div class='info-title'>Thông tin đơn hàng:</div>
                        <div class='info-item'>Mã đơn hàng: <strong>{$orderCode}</strong></div>
                        <div class='info-item'>Ngày đặt: <strong>{$orderDate}</strong></div>
                        <div class='info-item'>Phương thức thanh toán: <strong>{$paymentMethod}</strong></div>
                    </div>
                    <div class='info-col'>
                        <div class='info-title'>Thông tin khách hàng:</div>
                        <div class='info-item'>Họ tên: <strong>{$customerName}</strong></div>
                        <div class='info-item'>Điện thoại: <strong>{$customerPhone}</strong></div>
                        <div class='info-item'>Email: <strong>{$customerEmail}</strong></div>
                        <div class='info-item'>Địa chỉ: <strong>{$shippingAddress}</strong></div>
                    </div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th class='text-center' style='width: 8%;'>STT</th>
                        <th style='width: 45%;'>Tên sản phẩm</th>
                        <th class='text-center' style='width: 10%;'>SL</th>
                        <th class='text-right' style='width: 17%;'>Đơn giá</th>
                        <th class='text-right' style='width: 20%;'>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    {$itemsHtml}
                    <tr class='total-row'>
                        <td colspan='4' class='text-right'><strong>Tổng cộng:</strong></td>
                        <td class='text-right'><strong>{$totalAmount}đ</strong></td>
                    </tr>
                </tbody>
            </table>

            <div class='footer'>
                <p><strong>Cảm ơn quý khách đã mua sắm tại Zone Fashion!</strong></p>
                <p>Đây là hóa đơn điện tử, vui lòng lưu giữ để tra cứu và bảo hành sản phẩm.</p>
            </div>
            " . (!$forPdf ? "
            <div style='text-align: center; margin-top: 20px;'>
                <button class='print-btn' onclick='window.print()'>In hóa đơn</button>
            </div>" : "") . "
        </body>
        </html>
        ";
    }

    /**
     * Get payment method display name
     */
    private function getPaymentMethodName($method)
    {
        switch ($method) {
            case 'cod': return 'Thanh toán khi nhận hàng';
            case 'vnpay': return 'VNPay';
            case 'momo': return 'MoMo';
            case 'bank_transfer': return 'Chuyển khoản ngân hàng';
            default: return 'Thanh toán khi nhận hàng';
        }
    }
}
?>
