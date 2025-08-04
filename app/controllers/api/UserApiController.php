<?php

require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../../core/ApiController.php';
require_once __DIR__ . '/../../core/ApiResponse.php';
require_once __DIR__ . '/../../helpers/JWT.php';

class UserApiController extends ApiController
{
    private $pdo;

    public function __construct()
    {
        parent::__construct();
        $this->pdo = Database::getInstance()->getConnection();
    }

    /**
     * Get user orders
     */
    public function orders()
    {
        try {
            $currentUser = JWT::getCurrentUser();

            if (!$currentUser) {
                ApiResponse::error('Unauthorized', 401);
                return;
            }

            // Pagination
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $offset = ($page - 1) * $limit;

            // Status filter
            $statusFilter = '';
            $params = [':user_id' => $currentUser['sub']];

            if (isset($_GET['status']) && !empty($_GET['status'])) {
                $statusFilter = " AND o.status = :status";
                $params[':status'] = $_GET['status'];
            }

            // Get total count
            $countQuery = "
                SELECT COUNT(*) as total
                FROM orders o
                WHERE o.user_id = :user_id $statusFilter
            ";

            $countStmt = $this->pdo->prepare($countQuery);
            $countStmt->execute($params);
            $totalCount = $countStmt->fetch()['total'];

            // Get orders
            $ordersQuery = "
                SELECT
                    o.*,
                    COUNT(oi.id) as items_count,
                    GROUP_CONCAT(
                        CONCAT(oi.product_name, ' (x', oi.quantity, ')')
                        SEPARATOR ', '
                    ) as items_summary
                FROM orders o
                LEFT JOIN order_items oi ON o.id = oi.order_id
                WHERE o.user_id = :user_id $statusFilter
                GROUP BY o.id
                ORDER BY o.created_at DESC
                LIMIT :limit OFFSET :offset
            ";

            $stmt = $this->pdo->prepare($ordersQuery);
            $stmt->bindValue(':user_id', $currentUser['sub'], PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

            if (isset($params[':status'])) {
                $stmt->bindValue(':status', $params[':status']);
            }

            $stmt->execute();
            $orders = $stmt->fetchAll();

            // Format orders
            $formattedOrders = array_map([$this, 'formatOrder'], $orders);

            // Calculate pagination
            $totalPages = ceil($totalCount / $limit);

            ApiResponse::paginated($formattedOrders, $page, $totalPages, $totalCount, $limit);

        } catch (Exception $e) {
            ApiResponse::error('Failed to fetch orders: ' . $e->getMessage());
        }
    }

    /**
     * Get single order details
     */
    public function orderDetails($orderId)
    {
        try {
            $currentUser = JWT::getCurrentUser();

            if (!$currentUser) {
                ApiResponse::error('Unauthorized', 401);
                return;
            }

            // Get order
            $orderQuery = "
                SELECT * FROM orders
                WHERE id = :id AND user_id = :user_id
            ";

            $stmt = $this->pdo->prepare($orderQuery);
            $stmt->execute([
                ':id' => $orderId,
                ':user_id' => $currentUser['sub']
            ]);
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
                    p.image
                FROM order_items oi
                LEFT JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = :order_id
                ORDER BY oi.id
            ";

            $stmt = $this->pdo->prepare($itemsQuery);
            $stmt->execute([':order_id' => $orderId]);
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
            $stmt->execute([':order_id' => $orderId]);
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
     * Get user wishlist
     */
    public function wishlist()
    {
        try {
            $currentUser = JWT::getCurrentUser();

            if (!$currentUser) {
                ApiResponse::error('Unauthorized', 401);
                return;
            }

            // Get wishlist items
            session_start();
            $wishlistKey = 'wishlist_user_' . $currentUser['sub'];
            $wishlist = $_SESSION[$wishlistKey] ?? [];

            if (empty($wishlist)) {
                ApiResponse::success([
                    'items' => [],
                    'total_items' => 0,
                    'message' => 'Wishlist is empty'
                ]);
                return;
            }

            // Get product details for wishlist items
            $productIds = array_keys($wishlist);
            $placeholders = str_repeat('?,', count($productIds) - 1) . '?';

            $query = "
                SELECT
                    p.*,
                    c.name as category_name,
                    b.name as brand_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE p.id IN ($placeholders) AND p.status = 'active'
                ORDER BY p.name
            ";

            $stmt = $this->pdo->prepare($query);
            $stmt->execute($productIds);
            $products = $stmt->fetchAll();

            // Format wishlist items
            $wishlistItems = [];
            foreach ($products as $product) {
                $wishlistItems[] = [
                    'product_id' => (int)$product['id'],
                    'name' => $product['name'],
                    'slug' => $product['slug'],
                    'price' => (float)$product['price'],
                    'sale_price' => $product['sale_price'] ? (float)$product['sale_price'] : null,
                    'image' => $product['image'],
                    'category' => $product['category_name'],
                    'brand' => $product['brand_name'],
                    'stock_quantity' => (int)$product['stock_quantity'],
                    'in_stock' => $product['stock_quantity'] > 0,
                    'added_at' => $wishlist[$product['id']]['added_at'] ?? date('Y-m-d H:i:s')
                ];
            }

            ApiResponse::success([
                'items' => $wishlistItems,
                'total_items' => count($wishlistItems)
            ]);

        } catch (Exception $e) {
            ApiResponse::error('Failed to fetch wishlist: ' . $e->getMessage());
        }
    }

    /**
     * Get user addresses
     */
    public function addresses()
    {
        try {
            $currentUser = JWT::getCurrentUser();

            if (!$currentUser) {
                error_log("No current user found");
                ApiResponse::error('Unauthorized', 401);
                return;
            }

            error_log("Current user ID: " . $currentUser['sub']);

            // Use Customer model like AccountController does
            require_once __DIR__ . '/../../models/Customer.php';
            $customerModel = new Customer();
            $addresses = $customerModel->getCustomerAddresses($currentUser['sub']);

            error_log("Found " . count($addresses) . " addresses for user " . $currentUser['sub']);

            ApiResponse::success($addresses);

        } catch (Exception $e) {
            error_log("Error in addresses(): " . $e->getMessage());
            ApiResponse::error('Failed to fetch addresses: ' . $e->getMessage());
        }
    }

    /**
     * Add new address
     */
    public function addAddress()
    {
        try {
            $currentUser = JWT::getCurrentUser();

            if (!$currentUser) {
                ApiResponse::error('Unauthorized', 401);
                return;
            }

            $input = $this->parseRequestBody();

            // Validate required fields
            $required = ['name', 'phone', 'address'];
            $errors = [];

            foreach ($required as $field) {
                if (!isset($input[$field]) || empty(trim($input[$field]))) {
                    $errors[$field] = "The {$field} field is required";
                }
            }

            if (!empty($errors)) {
                ApiResponse::error('Validation failed', 422, $errors);
                return;
            }

            // Use Customer model like AccountController does
            require_once __DIR__ . '/../../models/Customer.php';
            $customerModel = new Customer();

            $addressData = [
                'user_id' => $currentUser['sub'],
                'name' => trim($input['name']),
                'phone' => trim($input['phone']),
                'province' => trim($input['province'] ?? 'ho-chi-minh'),
                'district' => trim($input['district'] ?? 'quan-1'),
                'ward' => trim($input['ward'] ?? 'phuong-ben-nghe'),
                'address' => trim($input['address']),
                'note' => trim($input['note'] ?? ''),
                'lat' => trim($input['lat'] ?? ''),
                'lng' => trim($input['lng'] ?? ''),
                'is_default' => isset($input['is_default']) && $input['is_default'] ? 1 : 0
            ];

            $result = $customerModel->addCustomerAddress($addressData);

            if ($result) {
                // Get updated addresses list
                $addresses = $customerModel->getCustomerAddresses($currentUser['sub']);
                
                ApiResponse::success([
                    'message' => 'Address added successfully',
                    'data' => $addresses
                ]);
            } else {
                ApiResponse::error('Failed to add address');
            }

        } catch (Exception $e) {
            ApiResponse::error('Failed to add address: ' . $e->getMessage());
        }
    }

    /**
     * Update address
     */
    /**
     * Update address
     */
    public function updateAddress($addressId)
    {
        try {
            $currentUser = JWT::getCurrentUser();

            if (!$currentUser) {
                ApiResponse::error('Unauthorized', 401);
                return;
            }

            $input = $this->parseRequestBody();

            // Use Customer model like AccountController does
            require_once __DIR__ . '/../../models/Customer.php';
            $customerModel = new Customer();

            // Prepare address data for update
            $addressData = [];
            $allowedFields = ['name', 'phone', 'province', 'district', 'ward', 'address', 'note', 'lat', 'lng', 'is_default'];
            
            foreach ($allowedFields as $field) {
                if (isset($input[$field])) {
                    if ($field === 'is_default') {
                        $addressData[$field] = $input[$field] ? 1 : 0;
                    } else {
                        $addressData[$field] = trim($input[$field]);
                    }
                }
            }

            if (empty($addressData)) {
                ApiResponse::error('No fields to update', 400);
                return;
            }

            $addressData['user_id'] = $currentUser['sub'];

            $result = $customerModel->updateCustomerAddress($addressId, $currentUser['sub'], $addressData);

            if ($result) {
                // Get updated addresses list
                $addresses = $customerModel->getCustomerAddresses($currentUser['sub']);
                
                ApiResponse::success([
                    'message' => 'Address updated successfully',
                    'data' => $addresses
                ]);
            } else {
                ApiResponse::error('Failed to update address or address not found', 404);
            }

        } catch (Exception $e) {
            ApiResponse::error('Failed to update address: ' . $e->getMessage());
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
            'payment_method' => $order['payment_method'],
            'notes' => $order['notes'],
            'shipped_at' => $order['shipped_at'],
            'delivered_at' => $order['delivered_at'],
            'created_at' => $order['created_at'],
            'updated_at' => $order['updated_at'],
            'items_count' => isset($order['items_count']) ? (int)$order['items_count'] : 0,
            'items_summary' => $order['items_summary'] ?? null
        ];
    }

    /**
     * Update address
     */
    /**
     * Delete address
     */
    public function deleteAddress($id)
    {
        try {
            $currentUser = JWT::getCurrentUser();

            if (!$currentUser) {
                ApiResponse::error('Unauthorized', 401);
                return;
            }

            // Use Customer model like AccountController does
            require_once __DIR__ . '/../../models/Customer.php';
            $customerModel = new Customer();

            $result = $customerModel->deleteCustomerAddress($id, $currentUser['sub']);

            if ($result) {
                // Get updated addresses list
                $addresses = $customerModel->getCustomerAddresses($currentUser['sub']);
                
                ApiResponse::success([
                    'message' => 'Address deleted successfully',
                    'data' => $addresses
                ]);
            } else {
                ApiResponse::error('Failed to delete address or address not found', 404);
            }

        } catch (Exception $e) {
            ApiResponse::error('Failed to delete address: ' . $e->getMessage());
        }
    }
}

?>
