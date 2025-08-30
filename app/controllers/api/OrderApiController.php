<?php

require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../../core/ApiController.php';
require_once __DIR__ . '/../../core/ApiResponse.php';

class OrderApiController extends ApiController
{
    private $pdo;

    public function __construct()
    {
        parent::__construct();
        $this->pdo = Database::getInstance()->getConnection();
    }

    /**
     * Get all orders with filtering and pagination
     */
    public function index()
    {
        try {
            // Build query with filters
            $whereConditions = [];
            $params = [];

            // Status filter
            if (isset($_GET['status']) && !empty($_GET['status'])) {
                $whereConditions[] = "o.status = :status";
                $params[':status'] = $_GET['status'];
            }

            // Date range filter
            if (isset($_GET['date_from']) && !empty($_GET['date_from'])) {
                $whereConditions[] = "DATE(o.created_at) >= :date_from";
                $params[':date_from'] = $_GET['date_from'];
            }

            if (isset($_GET['date_to']) && !empty($_GET['date_to'])) {
                $whereConditions[] = "DATE(o.created_at) <= :date_to";
                $params[':date_to'] = $_GET['date_to'];
            }

            // Customer search
            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $whereConditions[] = "(o.customer_name LIKE :search OR o.customer_email LIKE :search OR o.order_code LIKE :search)";
                $params[':search'] = '%' . $_GET['search'] . '%';
            }

            $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

            // Get total count for pagination
            $countQuery = "
                SELECT COUNT(*) as total
                FROM orders o
                $whereClause
            ";

            $countStmt = $this->pdo->prepare($countQuery);
            $countStmt->execute($params);
            $totalCount = $countStmt->fetch()['total'];

            // Pagination
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
            $offset = ($page - 1) * $limit;

            // Main query with order items count
            $query = "
                SELECT
                    o.*,
                    COUNT(oi.id) as items_count,
                    GROUP_CONCAT(
                        CONCAT(oi.product_name, ' (x', oi.quantity, ')')
                        SEPARATOR ', '
                    ) as items_summary
                FROM orders o
                LEFT JOIN order_items oi ON o.id = oi.order_id
                $whereClause
                GROUP BY o.id
                ORDER BY o.created_at DESC
                LIMIT :limit OFFSET :offset
            ";

            $stmt = $this->pdo->prepare($query);

            // Bind pagination parameters
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

            // Bind filter parameters
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->execute();
            $orders = $stmt->fetchAll();

            // Format orders
            $formattedOrders = array_map([$this, 'formatOrder'], $orders);

            // Calculate total pages
            $totalPages = ceil($totalCount / $limit);

            ApiResponse::paginated($formattedOrders, $page, $totalPages, $totalCount, $limit);

        } catch (Exception $e) {
            ApiResponse::error('Failed to fetch orders: ' . $e->getMessage());
        }
    }

    /**
     * Get single order with details
     */
    public function show($id)
    {
        try {
            // Get order details
            $orderQuery = "
                SELECT o.*, u.name as user_name, u.email as user_email
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                WHERE o.id = :id
            ";

            $stmt = $this->pdo->prepare($orderQuery);
            $stmt->execute([':id' => $id]);
            $order = $stmt->fetch();

            if (!$order) {
                ApiResponse::error('Order not found', 404);
                return;
            }

            // Get order items
            $itemsQuery = "
                SELECT
                    oi.*,
                    p.name as current_product_name,
                    p.price as current_price,
                    p.stock_quantity
                FROM order_items oi
                LEFT JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = :order_id
                ORDER BY oi.id
            ";

            $stmt = $this->pdo->prepare($itemsQuery);
            $stmt->execute([':order_id' => $id]);
            $items = $stmt->fetchAll();

            // Get order status history
            $historyQuery = "
                SELECT
                    ol.*,
                    u.name as changed_by_name
                FROM order_logs ol
                LEFT JOIN users u ON ol.created_by = u.id
                WHERE ol.order_id = :order_id
                ORDER BY ol.created_at DESC
            ";

            $stmt = $this->pdo->prepare($historyQuery);
            $stmt->execute([':order_id' => $id]);
            $history = $stmt->fetchAll();

            // Format response
            $formattedOrder = $this->formatOrder($order);
            $formattedOrder['items'] = $items;
            $formattedOrder['status_history'] = $history;

            ApiResponse::success($formattedOrder);

        } catch (Exception $e) {
            ApiResponse::error('Failed to fetch order details: ' . $e->getMessage());
        }
    }

    /**
     * Create order from cart
     */
    public function store()
    {
        try {
            $input = $this->parseRequestBody();

            // Validate required fields
            $required = ['customer_info', 'shipping_address', 'payment_method'];
            foreach ($required as $field) {
                if (!isset($input[$field]) || empty($input[$field])) {
                    ApiResponse::error("Field '$field' is required");
                    return;
                }
            }

            // Validate customer info
            $customerInfo = $input['customer_info'];
            if (empty($customerInfo['name']) || empty($customerInfo['email'])) {
                ApiResponse::error('Customer name and email are required');
                return;
            }

            // Validate shipping address
            $shippingAddress = $input['shipping_address'];
            if (empty($shippingAddress['address']) || empty($shippingAddress['city'])) {
                ApiResponse::error('Shipping address and city are required');
                return;
            }

            // Get cart items
            session_start();
            if (empty($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
                ApiResponse::error('Cart is empty');
                return;
            }

            $cartItems = $_SESSION['cart'];

            // Validate stock and calculate totals
            $orderItems = [];
            $subtotal = 0;

            foreach ($cartItems as $item) {
                // Get current product info
                $productQuery = "SELECT * FROM products WHERE id = :id AND status = 'active'";
                $stmt = $this->pdo->prepare($productQuery);
                $stmt->execute([':id' => $item['product_id']]);
                $product = $stmt->fetch();

                if (!$product) {
                    ApiResponse::error("Product ID {$item['product_id']} not found or inactive");
                    return;
                }

                // Check stock
                if ($product['stock_quantity'] < $item['quantity']) {
                    ApiResponse::error("Insufficient stock for product '{$product['name']}'. Available: {$product['stock_quantity']}, Requested: {$item['quantity']}");
                    return;
                }

                // Calculate item total
                $itemTotal = $product['price'] * $item['quantity'];
                $subtotal += $itemTotal;

                $orderItems[] = [
                    'product_id' => $product['id'],
                    'product_name' => $product['name'],
                    'product_sku' => $product['sku'],
                    'quantity' => $item['quantity'],
                    'price' => $product['price'],
                    'total' => $itemTotal
                ];
            }

            // Calculate tax and shipping
            $taxRate = 0.1; // 10% VAT
            $taxAmount = $subtotal * $taxRate;
            $shippingAmount = $subtotal >= 500000 ? 0 : 30000; // Free shipping over 500k VND
            $totalAmount = $subtotal + $taxAmount + $shippingAmount;

            // Generate order code
            $orderCode = 'ORD-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

            // Begin transaction
            $this->pdo->beginTransaction();

            try {
                // Create order
                $orderQuery = "
                    INSERT INTO orders (
                        order_code, user_id, status, subtotal, tax_amount, shipping_amount, total_amount,
                        customer_name, customer_email, customer_phone,
                        shipping_address, payment_method, notes, created_at
                    ) VALUES (
                        :order_code, :user_id, 'pending', :subtotal, :tax_amount, :shipping_amount, :total_amount,
                        :customer_name, :customer_email, :customer_phone,
                        :shipping_address, :payment_method, :notes, NOW()
                    )
                ";

                $stmt = $this->pdo->prepare($orderQuery);
                $stmt->execute([
                    ':order_code' => $orderCode,
                    ':user_id' => $_SESSION['user_id'] ?? null,
                    ':subtotal' => $subtotal,
                    ':tax_amount' => $taxAmount,
                    ':shipping_amount' => $shippingAmount,
                    ':total_amount' => $totalAmount,
                    ':customer_name' => $customerInfo['name'],
                    ':customer_email' => $customerInfo['email'],
                    ':customer_phone' => $customerInfo['phone'] ?? null,
                    ':shipping_address' => json_encode($shippingAddress),
                    ':payment_method' => $input['payment_method'],
                    ':notes' => $input['notes'] ?? null
                ]);

                $orderId = $this->pdo->lastInsertId();

                // Create order items and update stock
                foreach ($orderItems as $item) {
                    // Insert order item
                    $itemQuery = "
                        INSERT INTO order_items (
                            order_id, product_id, product_name, product_sku,
                            quantity, price, total, created_at
                        ) VALUES (
                            :order_id, :product_id, :product_name, :product_sku,
                            :quantity, :price, :total, NOW()
                        )
                    ";

                    $stmt = $this->pdo->prepare($itemQuery);
                    $stmt->execute([
                        ':order_id' => $orderId,
                        ':product_id' => $item['product_id'],
                        ':product_name' => $item['product_name'],
                        ':product_sku' => $item['product_sku'],
                        ':quantity' => $item['quantity'],
                        ':price' => $item['price'],
                        ':total' => $item['total']
                    ]);

                    // Update product stock
                    $updateStockQuery = "
                        UPDATE products
                        SET stock_quantity = stock_quantity - :quantity,
                            updated_at = NOW()
                        WHERE id = :product_id
                    ";

                    $stmt = $this->pdo->prepare($updateStockQuery);
                    $stmt->execute([
                        ':quantity' => $item['quantity'],
                        ':product_id' => $item['product_id']
                    ]);
                }

                // Log order creation
                $logQuery = "
                    INSERT INTO order_logs (
                        order_id, status_from, status_to, notes, created_by, created_at
                    ) VALUES (
                        :order_id, NULL, 'pending', 'Order created from cart', :created_by, NOW()
                    )
                ";

                $stmt = $this->pdo->prepare($logQuery);
                $stmt->execute([
                    ':order_id' => $orderId,
                    ':created_by' => $_SESSION['user_id'] ?? null
                ]);

                // Clear the cart
                $_SESSION['cart'] = [];

                // Commit transaction
                $this->pdo->commit();

                // Get the created order
                $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE id = :id");
                $stmt->execute([':id' => $orderId]);
                $order = $stmt->fetch();

                ApiResponse::success([
                    'message' => 'Order created successfully',
                    'order' => $this->formatOrder($order),
                    'order_items' => $orderItems
                ]);

            } catch (Exception $e) {
                $this->pdo->rollBack();
                throw $e;
            }

        } catch (Exception $e) {
            ApiResponse::error('Failed to create order: ' . $e->getMessage());
        }
    }

    /**
     * Update order status
     */
    public function update($id)
    {
        try {
            $input = $this->parseRequestBody();

            if (!isset($input['status']) || empty($input['status'])) {
                ApiResponse::error('Status is required');
                return;
            }

            $newStatus = $input['status'];
            $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'];

            if (!in_array($newStatus, $validStatuses)) {
                ApiResponse::error('Invalid status. Valid statuses: ' . implode(', ', $validStatuses));
                return;
            }

            // Get current order
            $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $order = $stmt->fetch();

            if (!$order) {
                ApiResponse::error('Order not found', 404);
                return;
            }

            // Ensure session started for ownership check
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }

            // If a regular authenticated user is calling this API, ensure they own the order
            $currentUserId = $_SESSION['user_id'] ?? null;
            if ($currentUserId !== null && $order['user_id'] && $order['user_id'] != $currentUserId) {
                ApiResponse::error('Not authorized to update this order', 403);
                return;
            }

            $oldStatus = $order['status'];

            // Validate status transition
            if ($oldStatus === $newStatus) {
                ApiResponse::error('Order is already in this status');
                return;
            }

            // Begin transaction
            $this->pdo->beginTransaction();

            try {
                // Update order status
                $updateQuery = "
                    UPDATE orders
                    SET status = :status, updated_at = NOW()
                    WHERE id = :id
                ";

                $stmt = $this->pdo->prepare($updateQuery);
                $stmt->execute([
                    ':status' => $newStatus,
                    ':id' => $id
                ]);

                // Update shipped/delivered timestamps
                if ($newStatus === 'shipped') {
                    $stmt = $this->pdo->prepare("UPDATE orders SET shipped_at = NOW() WHERE id = :id");
                    $stmt->execute([':id' => $id]);
                } elseif ($newStatus === 'delivered') {
                    $stmt = $this->pdo->prepare("UPDATE orders SET delivered_at = NOW() WHERE id = :id");
                    $stmt->execute([':id' => $id]);
                }

                // Log status change
                $logQuery = "
                    INSERT INTO order_logs (
                        order_id, status_from, status_to, notes, created_by, created_at
                    ) VALUES (
                        :order_id, :status_from, :status_to, :notes, :created_by, NOW()
                    )
                ";

                $stmt = $this->pdo->prepare($logQuery);
                $stmt->execute([
                    ':order_id' => $id,
                    ':status_from' => $oldStatus,
                    ':status_to' => $newStatus,
                    ':notes' => $input['notes'] ?? "Status changed from $oldStatus to $newStatus",
                    ':created_by' => $_SESSION['user_id'] ?? null
                ]);

                $this->pdo->commit();

                // Get updated order
                $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE id = :id");
                $stmt->execute([':id' => $id]);
                $updatedOrder = $stmt->fetch();

                ApiResponse::success([
                    'message' => 'Order status updated successfully',
                    'order' => $this->formatOrder($updatedOrder)
                ]);

            } catch (Exception $e) {
                $this->pdo->rollBack();
                throw $e;
            }

        } catch (Exception $e) {
            ApiResponse::error('Failed to update order: ' . $e->getMessage());
        }
    }

    /**
     * Cancel order (soft delete with stock restoration)
     */
    public function destroy($id)
    {
        try {
            // Get order
            $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $order = $stmt->fetch();

            if (!$order) {
                ApiResponse::error('Order not found', 404);
                return;
            }

            if ($order['status'] === 'cancelled') {
                ApiResponse::error('Order is already cancelled');
                return;
            }

            if (in_array($order['status'], ['shipped', 'delivered'])) {
                ApiResponse::error('Cannot cancel shipped or delivered orders');
                return;
            }

            // Begin transaction
            $this->pdo->beginTransaction();

            try {
                // Get order items to restore stock
                $itemsQuery = "SELECT * FROM order_items WHERE order_id = :order_id";
                $stmt = $this->pdo->prepare($itemsQuery);
                $stmt->execute([':order_id' => $id]);
                $items = $stmt->fetchAll();

                // Restore stock for each item
                foreach ($items as $item) {
                    $updateStockQuery = "
                        UPDATE products
                        SET stock_quantity = stock_quantity + :quantity,
                            updated_at = NOW()
                        WHERE id = :product_id
                    ";

                    $stmt = $this->pdo->prepare($updateStockQuery);
                    $stmt->execute([
                        ':quantity' => $item['quantity'],
                        ':product_id' => $item['product_id']
                    ]);
                }

                // Update order status to cancelled
                $updateQuery = "
                    UPDATE orders
                    SET status = 'cancelled', updated_at = NOW()
                    WHERE id = :id
                ";

                $stmt = $this->pdo->prepare($updateQuery);
                $stmt->execute([':id' => $id]);

                // Log cancellation
                $logQuery = "
                    INSERT INTO order_logs (
                        order_id, status_from, status_to, notes, created_by, created_at
                    ) VALUES (
                        :order_id, :status_from, 'cancelled', 'Order cancelled and stock restored', :created_by, NOW()
                    )
                ";

                $stmt = $this->pdo->prepare($logQuery);
                $stmt->execute([
                    ':order_id' => $id,
                    ':status_from' => $order['status'],
                    ':created_by' => $_SESSION['user_id'] ?? null
                ]);

                $this->pdo->commit();

                ApiResponse::success([
                    'message' => 'Order cancelled successfully and stock restored'
                ]);

            } catch (Exception $e) {
                $this->pdo->rollBack();
                throw $e;
            }

        } catch (Exception $e) {
            ApiResponse::error('Failed to cancel order: ' . $e->getMessage());
        }
    }

    /**
     * Parse request body based on content type
     */
    private function parseRequestBody()
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (strpos($contentType, 'application/json') !== false) {
            $json = file_get_contents('php://input');
            return json_decode($json, true) ?? [];
        }

        return $_POST;
    }

    /**
     * Format order data for response
     */
    private function formatOrder($order)
    {
        return [
            'id' => (int)$order['id'],
            'order_code' => $order['order_code'],
            'user_id' => $order['user_id'] ? (int)$order['user_id'] : null,
            'status' => $order['status'],
            'payment_status' => $order['payment_status'],
            'totals' => [
                'subtotal' => (float)$order['subtotal'],
                'tax_amount' => (float)$order['tax_amount'],
                'shipping_amount' => (float)$order['shipping_amount'],
                'discount_amount' => (float)$order['discount_amount'],
                'total_amount' => (float)$order['total_amount']
            ],
            'customer' => [
                'name' => $order['customer_name'],
                'email' => $order['customer_email'],
                'phone' => $order['customer_phone']
            ],
            'shipping_address' => $order['shipping_address'] ? json_decode($order['shipping_address'], true) : null,
            'billing_address' => $order['billing_address'] ? json_decode($order['billing_address'], true) : null,
            'payment_method' => $order['payment_method'],
            'notes' => $order['notes'],
            'admin_notes' => $order['admin_notes'],
            'shipped_at' => $order['shipped_at'],
            'delivered_at' => $order['delivered_at'],
            'created_at' => $order['created_at'],
            'updated_at' => $order['updated_at'],
            'items_count' => isset($order['items_count']) ? (int)$order['items_count'] : 0,
            'items_summary' => $order['items_summary'] ?? null
        ];
    }
}

?>
{
    private $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get all orders with filtering and pagination
     */
    public function index()
    {
        try {
            // Build query with filters
            $whereConditions = [];
            $params = [];

            // Status filter
            if (isset($_GET['status']) && !empty($_GET['status'])) {
                $whereConditions[] = "o.status = :status";
                $params[':status'] = $_GET['status'];
            }

            // Date range filter
            if (isset($_GET['date_from']) && !empty($_GET['date_from'])) {
                $whereConditions[] = "DATE(o.created_at) >= :date_from";
                $params[':date_from'] = $_GET['date_from'];
            }

            if (isset($_GET['date_to']) && !empty($_GET['date_to'])) {
                $whereConditions[] = "DATE(o.created_at) <= :date_to";
                $params[':date_to'] = $_GET['date_to'];
            }

            // Customer search
            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $whereConditions[] = "(o.customer_name LIKE :search OR o.customer_email LIKE :search OR o.order_code LIKE :search)";
                $params[':search'] = '%' . $_GET['search'] . '%';
            }

            $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

            // Get total count for pagination
            $countQuery = "
                SELECT COUNT(*) as total
                FROM orders o
                $whereClause
            ";

            $countStmt = $this->db->prepare($countQuery);
            $countStmt->execute($params);
            $totalOrders = $countStmt->fetch()['total'];

            // Pagination
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
            $offset = ($page - 1) * $limit;

            // Main query with order items count
            $query = "
                SELECT
                    o.*,
                    COUNT(oi.id) as items_count,
                    GROUP_CONCAT(
                        CONCAT(oi.product_name, ' (x', oi.quantity, ')')
                        SEPARATOR ', '
                    ) as items_summary
                FROM orders o
                LEFT JOIN order_items oi ON o.id = oi.order_id
                $whereClause
                GROUP BY o.id
                ORDER BY o.created_at DESC
                LIMIT :limit OFFSET :offset
            ";

            $stmt = $this->db->prepare($query);

            // Bind pagination parameters
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

            // Bind filter parameters
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->execute();
            $orders = $stmt->fetchAll();

            // Format orders
            $formattedOrders = array_map([$this, 'formatOrder'], $orders);

            return $this->paginated($formattedOrders, $totalOrders, $page, $limit);

        } catch (Exception $e) {
            return $this->error('Failed to fetch orders: ' . $e->getMessage());
        }
    }

    /**
     * Get single order with details
     */
    public function show($id)
    {
        try {
            // Get order details
            $orderQuery = "
                SELECT o.*, u.name as user_name, u.email as user_email
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                WHERE o.id = :id
            ";

            $stmt = $this->db->prepare($orderQuery);
            $stmt->execute([':id' => $id]);
            $order = $stmt->fetch();

            if (!$order) {
                return $this->error('Order not found', 404);
            }

            // Get order items
            $itemsQuery = "
                SELECT
                    oi.*,
                    p.name as current_product_name,
                    p.price as current_price,
                    p.stock_quantity
                FROM order_items oi
                LEFT JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = :order_id
                ORDER BY oi.id
            ";

            $stmt = $this->db->prepare($itemsQuery);
            $stmt->execute([':order_id' => $id]);
            $items = $stmt->fetchAll();

            // Get order status history
            $historyQuery = "
                SELECT
                    ol.*,
                    u.name as changed_by_name
                FROM order_logs ol
                LEFT JOIN users u ON ol.created_by = u.id
                WHERE ol.order_id = :order_id
                ORDER BY ol.created_at DESC
            ";

            $stmt = $this->db->prepare($historyQuery);
            $stmt->execute([':order_id' => $id]);
            $history = $stmt->fetchAll();

            // Format response
            $formattedOrder = $this->formatOrder($order);
            $formattedOrder['items'] = $items;
            $formattedOrder['status_history'] = $history;

            return $this->success($formattedOrder);

        } catch (Exception $e) {
            return $this->error('Failed to fetch order details: ' . $e->getMessage());
        }
    }

    /**
     * Create order from cart
     */
    public function store()
    {
        try {
            $input = $this->getJsonInput();

            // Validate required fields
            $required = ['customer_info', 'shipping_address', 'payment_method'];
            foreach ($required as $field) {
                if (!isset($input[$field]) || empty($input[$field])) {
                    return $this->error("Field '$field' is required");
                }
            }

            // Validate customer info
            $customerInfo = $input['customer_info'];
            if (empty($customerInfo['name']) || empty($customerInfo['email'])) {
                return $this->error('Customer name and email are required');
            }

            // Validate shipping address
            $shippingAddress = $input['shipping_address'];
            if (empty($shippingAddress['address']) || empty($shippingAddress['city'])) {
                return $this->error('Shipping address and city are required');
            }

            // Get cart items
            session_start();
            if (empty($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
                return $this->error('Cart is empty');
            }

            $cartItems = $_SESSION['cart'];

            // Validate stock and calculate totals
            $orderItems = [];
            $subtotal = 0;

            foreach ($cartItems as $item) {
                // Get current product info
                $productQuery = "SELECT * FROM products WHERE id = :id AND status = 'active'";
                $stmt = $this->db->prepare($productQuery);
                $stmt->execute([':id' => $item['product_id']]);
                $product = $stmt->fetch();

                if (!$product) {
                    return $this->error("Product ID {$item['product_id']} not found or inactive");
                }

                // Check stock
                if ($product['stock_quantity'] < $item['quantity']) {
                    return $this->error("Insufficient stock for product '{$product['name']}'. Available: {$product['stock_quantity']}, Requested: {$item['quantity']}");
                }

                // Calculate item total
                $itemTotal = $product['price'] * $item['quantity'];
                $subtotal += $itemTotal;

                $orderItems[] = [
                    'product_id' => $product['id'],
                    'product_name' => $product['name'],
                    'product_sku' => $product['sku'],
                    'quantity' => $item['quantity'],
                    'price' => $product['price'],
                    'total' => $itemTotal
                ];
            }

            // Calculate tax and shipping
            $taxRate = 0.1; // 10% VAT
            $taxAmount = $subtotal * $taxRate;
            $shippingAmount = $subtotal >= 500000 ? 0 : 30000; // Free shipping over 500k VND
            $totalAmount = $subtotal + $taxAmount + $shippingAmount;

            // Generate order code
            $orderCode = 'ORD-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

            // Begin transaction
            $this->db->beginTransaction();

            try {
                // Create order
                $orderQuery = "
                    INSERT INTO orders (
                        order_code, user_id, status, subtotal, tax_amount, shipping_amount, total_amount,
                        customer_name, customer_email, customer_phone,
                        shipping_address, payment_method, notes, created_at
                    ) VALUES (
                        :order_code, :user_id, 'pending', :subtotal, :tax_amount, :shipping_amount, :total_amount,
                        :customer_name, :customer_email, :customer_phone,
                        :shipping_address, :payment_method, :notes, NOW()
                    )
                ";

                $stmt = $this->db->prepare($orderQuery);
                $stmt->execute([
                    ':order_code' => $orderCode,
                    ':user_id' => $_SESSION['user_id'] ?? null,
                    ':subtotal' => $subtotal,
                    ':tax_amount' => $taxAmount,
                    ':shipping_amount' => $shippingAmount,
                    ':total_amount' => $totalAmount,
                    ':customer_name' => $customerInfo['name'],
                    ':customer_email' => $customerInfo['email'],
                    ':customer_phone' => $customerInfo['phone'] ?? null,
                    ':shipping_address' => json_encode($shippingAddress),
                    ':payment_method' => $input['payment_method'],
                    ':notes' => $input['notes'] ?? null
                ]);

                $orderId = $this->db->lastInsertId();

                // Create order items and update stock
                foreach ($orderItems as $item) {
                    // Insert order item
                    $itemQuery = "
                        INSERT INTO order_items (
                            order_id, product_id, product_name, product_sku,
                            quantity, price, total, created_at
                        ) VALUES (
                            :order_id, :product_id, :product_name, :product_sku,
                            :quantity, :price, :total, NOW()
                        )
                    ";

                    $stmt = $this->db->prepare($itemQuery);
                    $stmt->execute([
                        ':order_id' => $orderId,
                        ':product_id' => $item['product_id'],
                        ':product_name' => $item['product_name'],
                        ':product_sku' => $item['product_sku'],
                        ':quantity' => $item['quantity'],
                        ':price' => $item['price'],
                        ':total' => $item['total']
                    ]);

                    // Update product stock
                    $updateStockQuery = "
                        UPDATE products
                        SET stock_quantity = stock_quantity - :quantity,
                            updated_at = NOW()
                        WHERE id = :product_id
                    ";

                    $stmt = $this->db->prepare($updateStockQuery);
                    $stmt->execute([
                        ':quantity' => $item['quantity'],
                        ':product_id' => $item['product_id']
                    ]);
                }

                // Log order creation
                $logQuery = "
                    INSERT INTO order_logs (
                        order_id, status_from, status_to, notes, created_by, created_at
                    ) VALUES (
                        :order_id, NULL, 'pending', 'Order created from cart', :created_by, NOW()
                    )
                ";

                $stmt = $this->db->prepare($logQuery);
                $stmt->execute([
                    ':order_id' => $orderId,
                    ':created_by' => $_SESSION['user_id'] ?? null
                ]);

                // Clear the cart
                $_SESSION['cart'] = [];

                // Commit transaction
                $this->db->commit();

                // Get the created order
                $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = :id");
                $stmt->execute([':id' => $orderId]);
                $order = $stmt->fetch();

                return $this->success([
                    'message' => 'Order created successfully',
                    'order' => $this->formatOrder($order),
                    'order_items' => $orderItems
                ]);

            } catch (Exception $e) {
                $this->db->rollBack();
                throw $e;
            }

        } catch (Exception $e) {
            return $this->error('Failed to create order: ' . $e->getMessage());
        }
    }

    /**
     * Update order status
     */
    public function update($id)
    {
        try {
            $input = $this->getJsonInput();

            if (!isset($input['status']) || empty($input['status'])) {
                return $this->error('Status is required');
            }

            $newStatus = $input['status'];
            $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'];

            if (!in_array($newStatus, $validStatuses)) {
                return $this->error('Invalid status. Valid statuses: ' . implode(', ', $validStatuses));
            }

            // Get current order
            $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $order = $stmt->fetch();

            if (!$order) {
                return $this->error('Order not found', 404);
            }

            $oldStatus = $order['status'];

            // Validate status transition
            if ($oldStatus === $newStatus) {
                return $this->error('Order is already in this status');
            }

            // Begin transaction
            $this->db->beginTransaction();

            try {
                // Update order status
                $updateQuery = "
                    UPDATE orders
                    SET status = :status, updated_at = NOW()
                    WHERE id = :id
                ";

                $stmt = $this->db->prepare($updateQuery);
                $stmt->execute([
                    ':status' => $newStatus,
                    ':id' => $id
                ]);

                // Update shipped/delivered timestamps
                if ($newStatus === 'shipped') {
                    $stmt = $this->db->prepare("UPDATE orders SET shipped_at = NOW() WHERE id = :id");
                    $stmt->execute([':id' => $id]);
                } elseif ($newStatus === 'delivered') {
                    $stmt = $this->db->prepare("UPDATE orders SET delivered_at = NOW() WHERE id = :id");
                    $stmt->execute([':id' => $id]);
                }

                // Log status change
                $logQuery = "
                    INSERT INTO order_logs (
                        order_id, status_from, status_to, notes, created_by, created_at
                    ) VALUES (
                        :order_id, :status_from, :status_to, :notes, :created_by, NOW()
                    )
                ";

                $stmt = $this->db->prepare($logQuery);
                $stmt->execute([
                    ':order_id' => $id,
                    ':status_from' => $oldStatus,
                    ':status_to' => $newStatus,
                    ':notes' => $input['notes'] ?? "Status changed from $oldStatus to $newStatus",
                    ':created_by' => $_SESSION['user_id'] ?? null
                ]);

                $this->db->commit();

                // Get updated order
                $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = :id");
                $stmt->execute([':id' => $id]);
                $updatedOrder = $stmt->fetch();

                return $this->success([
                    'message' => 'Order status updated successfully',
                    'order' => $this->formatOrder($updatedOrder)
                ]);

            } catch (Exception $e) {
                $this->db->rollBack();
                throw $e;
            }

        } catch (Exception $e) {
            return $this->error('Failed to update order: ' . $e->getMessage());
        }
    }

    /**
     * Cancel order (soft delete with stock restoration)
     */
    public function destroy($id)
    {
        try {
            // Get order
            $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $order = $stmt->fetch();

            if (!$order) {
                return $this->error('Order not found', 404);
            }

            if ($order['status'] === 'cancelled') {
                return $this->error('Order is already cancelled');
            }

            if (in_array($order['status'], ['shipped', 'delivered'])) {
                return $this->error('Cannot cancel shipped or delivered orders');
            }

            // Begin transaction
            $this->db->beginTransaction();

            try {
                // Get order items to restore stock
                $itemsQuery = "SELECT * FROM order_items WHERE order_id = :order_id";
                $stmt = $this->db->prepare($itemsQuery);
                $stmt->execute([':order_id' => $id]);
                $items = $stmt->fetchAll();

                // Restore stock for each item
                foreach ($items as $item) {
                    $updateStockQuery = "
                        UPDATE products
                        SET stock_quantity = stock_quantity + :quantity,
                            updated_at = NOW()
                        WHERE id = :product_id
                    ";

                    $stmt = $this->db->prepare($updateStockQuery);
                    $stmt->execute([
                        ':quantity' => $item['quantity'],
                        ':product_id' => $item['product_id']
                    ]);
                }

                // Update order status to cancelled
                $updateQuery = "
                    UPDATE orders
                    SET status = 'cancelled', updated_at = NOW()
                    WHERE id = :id
                ";

                $stmt = $this->db->prepare($updateQuery);
                $stmt->execute([':id' => $id]);

                // Log cancellation
                $logQuery = "
                    INSERT INTO order_logs (
                        order_id, status_from, status_to, notes, created_by, created_at
                    ) VALUES (
                        :order_id, :status_from, 'cancelled', 'Order cancelled and stock restored', :created_by, NOW()
                    )
                ";

                $stmt = $this->db->prepare($logQuery);
                $stmt->execute([
                    ':order_id' => $id,
                    ':status_from' => $order['status'],
                    ':created_by' => $_SESSION['user_id'] ?? null
                ]);

                $this->db->commit();

                return $this->success([
                    'message' => 'Order cancelled successfully and stock restored'
                ]);

            } catch (Exception $e) {
                $this->db->rollBack();
                throw $e;
            }

        } catch (Exception $e) {
            return $this->error('Failed to cancel order: ' . $e->getMessage());
        }
    }

    /**
     * Format order data for response
     */
    private function formatOrder($order)
    {
        return [
            'id' => (int)$order['id'],
            'order_code' => $order['order_code'],
            'user_id' => $order['user_id'] ? (int)$order['user_id'] : null,
            'status' => $order['status'],
            'payment_status' => $order['payment_status'],
            'totals' => [
                'subtotal' => (float)$order['subtotal'],
                'tax_amount' => (float)$order['tax_amount'],
                'shipping_amount' => (float)$order['shipping_amount'],
                'discount_amount' => (float)$order['discount_amount'],
                'total_amount' => (float)$order['total_amount']
            ],
            'customer' => [
                'name' => $order['customer_name'],
                'email' => $order['customer_email'],
                'phone' => $order['customer_phone']
            ],
            'shipping_address' => $order['shipping_address'] ? json_decode($order['shipping_address'], true) : null,
            'billing_address' => $order['billing_address'] ? json_decode($order['billing_address'], true) : null,
            'payment_method' => $order['payment_method'],
            'notes' => $order['notes'],
            'admin_notes' => $order['admin_notes'],
            'shipped_at' => $order['shipped_at'],
            'delivered_at' => $order['delivered_at'],
            'created_at' => $order['created_at'],
            'updated_at' => $order['updated_at'],
            'items_count' => isset($order['items_count']) ? (int)$order['items_count'] : 0,
            'items_summary' => $order['items_summary'] ?? null
        ];
    }
}

?>
