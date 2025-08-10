<?php
/**
 * Order Model
 * 5S Fashion E-commerce Platform
 */

class Order extends BaseModel
{
    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id', 'order_code', 'customer_name', 'customer_email', 'customer_phone',
        'subtotal', 'tax_amount', 'shipping_amount', 'discount_amount', 'total_amount',
        // 'status', // Removed from fillable - let database default handle it
        'payment_method', 'payment_status', 'shipping_address', 'billing_address',
        'notes', 'admin_notes', 'shipped_at', 'delivered_at'
    ];

    /**
     * Get order by order code
     */
    public function findByOrderCode($orderCode)
    {
        return $this->findBy('order_code', $orderCode);
    }

    /**
     * Get orders by user
     */
    public function getByUser($userId, $limit = null)
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE user_id = ?
                ORDER BY created_at DESC";

        if ($limit) {
            $sql .= " LIMIT ?";
            return $this->db->fetchAll($sql, [$userId, $limit]);
        }

        return $this->db->fetchAll($sql, [$userId]);
    }

    /**
     * Get orders by user with items
     */
    public function getByUserWithItems($userId, $limit = null)
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE user_id = ?
                ORDER BY created_at DESC";

        if ($limit) {
            $sql .= " LIMIT ?";
            $orders = $this->db->fetchAll($sql, [$userId, $limit]);
        } else {
            $orders = $this->db->fetchAll($sql, [$userId]);
        }

        // Get items for each order
        foreach ($orders as &$order) {
            $order['items'] = $this->getOrderItems($order['id']);
        }

        return $orders;
    }

    /**
     * Get orders by status
     */
    public function getByStatus($status, $limit = null)
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE status = ?
                ORDER BY created_at DESC";

        if ($limit) {
            $sql .= " LIMIT ?";
            return $this->db->fetchAll($sql, [$status, $limit]);
        }

        return $this->db->fetchAll($sql, [$status]);
    }

    /**
     * Get recent orders for admin
     */
    public function getRecent($limit = 10)
    {
        $sql = "SELECT o.*, u.full_name as customer_full_name, u.email as customer_email_verified
                FROM {$this->table} o
                LEFT JOIN users u ON o.user_id = u.id
                ORDER BY o.created_at DESC
                LIMIT ?";

        return $this->db->fetchAll($sql, [$limit]);
    }

    /**
     * Get order with full details
     */
    public function getFullDetails($id)
    {
        $sql = "SELECT o.*,
                       u.full_name as customer_full_name, u.email as customer_email_verified,
                       u.phone as customer_phone_verified, u.avatar as customer_avatar
                FROM {$this->table} o
                LEFT JOIN users u ON o.user_id = u.id
                WHERE o.id = ?";

        $order = $this->db->fetchOne($sql, [$id]);

        if ($order) {
            // Get order items
            $order['items'] = $this->getOrderItems($id);

            // Parse JSON fields
            if ($order['shipping_address']) {
                $order['shipping_address'] = json_decode($order['shipping_address'], true);
            }

            if ($order['billing_address']) {
                $order['billing_address'] = json_decode($order['billing_address'], true);
            }
        }

        return $order;
    }

    /**
     * Get order items
     */
    public function getOrderItems($orderId)
    {
        $sql = "SELECT oi.*, p.name as product_name, p.featured_image, p.slug as product_slug
                FROM order_items oi
                LEFT JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?
                ORDER BY oi.id ASC";

        $items = $this->db->fetchAll($sql, [$orderId]);

        // Parse variant info JSON
        foreach ($items as &$item) {
            if ($item['variant_info']) {
                $item['variant_info'] = json_decode($item['variant_info'], true);
            }
        }

        return $items;
    }

    /**
     * Create new order
     */
    public function createOrder($orderData, $items)
    {
        try {
            $this->db->beginTransaction();

            // Generate order code
            $orderData['order_code'] = $this->generateOrderCode();

            // Calculate totals
            $subtotal = 0;
            foreach ($items as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }

            $orderData['subtotal'] = $subtotal;
            $orderData['total_amount'] = $subtotal + ($orderData['tax_amount'] ?? 0) + ($orderData['shipping_amount'] ?? 0) - ($orderData['discount_amount'] ?? 0);

            // Convert addresses to JSON
            if (isset($orderData['shipping_address']) && is_array($orderData['shipping_address'])) {
                $orderData['shipping_address'] = json_encode($orderData['shipping_address']);
            }

            if (isset($orderData['billing_address']) && is_array($orderData['billing_address'])) {
                $orderData['billing_address'] = json_encode($orderData['billing_address']);
            }

            // Insert order
            error_log('[ORDER CREATE] Order data before create: ' . print_r($orderData, true));
            $orderResult = $this->create($orderData);

            if ($orderResult) {
                error_log('[ORDER CREATE] Order created successfully with ID: ' . $orderResult['id']);
                error_log('[ORDER CREATE] Created order status: ' . ($orderResult['status'] ?? 'NULL'));
            } else {
                error_log('[ORDER CREATE] Failed to create order');
            }

            if (!$orderResult) {
                throw new Exception('Failed to create order');
            }

            $orderId = $orderResult['id'];

            // Insert order items
            foreach ($items as $item) {
                $item['order_id'] = $orderId;
                $item['total'] = $item['price'] * $item['quantity'];

                if (isset($item['variant_info'])) {
                    if (is_array($item['variant_info'])) {
                        $item['variant_info'] = json_encode($item['variant_info']);
                    } elseif (is_string($item['variant_info']) && !empty($item['variant_info'])) {
                        // If it's a plain string, wrap it in a simple structure
                        $item['variant_info'] = json_encode(['info' => $item['variant_info']]);
                    } else {
                        $item['variant_info'] = null;
                    }
                }

                $this->createOrderItem($item);

                // Update product stock if variant_id is provided
                if (!empty($item['variant_id'])) {
                    $this->updateProductStock($item['product_id'], $item['variant_id'], $item['quantity']);
                }
            }

            $this->db->commit();
            return $orderId;

        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    /**
     * Create order item
     */
    private function createOrderItem($itemData)
    {
        $sql = "INSERT INTO order_items (order_id, product_id, variant_id, product_name, product_sku, variant_info, quantity, price, total, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

        return $this->db->execute($sql, [
            $itemData['order_id'],
            $itemData['product_id'],
            $itemData['variant_id'] ?? null,
            $itemData['product_name'],
            $itemData['product_sku'] ?? null,
            $itemData['variant_info'] ?? null,
            $itemData['quantity'],
            $itemData['price'],
            $itemData['total']
        ]);
    }

    /**
     * Update product stock
     */
    private function updateProductStock($productId, $variantId, $quantity)
    {
        $sql = "UPDATE product_variants
                SET stock_quantity = stock_quantity - ?
                WHERE id = ? AND product_id = ? AND stock_quantity >= ?";

        return $this->db->execute($sql, [$quantity, $variantId, $productId, $quantity]);
    }

    /**
     * Update order status
     */
    public function updateStatus($id, $status, $adminNotes = null)
    {
        try {
            $this->db->beginTransaction();

            // Get current order status
            $currentOrder = $this->find($id);
            if (!$currentOrder) {
                throw new Exception('Order not found');
            }

            $currentStatus = $currentOrder['status'];

            // Update order status
            $sql = "UPDATE {$this->table} SET status = ?, updated_at = NOW()";
            $params = [$status];

            if ($adminNotes) {
                $sql .= ", admin_notes = ?";
                $params[] = $adminNotes;
            }

            // Set shipped_at or delivered_at timestamps
            if ($status === 'shipped') {
                $sql .= ", shipped_at = NOW()";
            } elseif ($status === 'delivered') {
                $sql .= ", delivered_at = NOW()";
            }

            $sql .= " WHERE id = ?";
            $params[] = $id;

            $result = $this->db->execute($sql, $params);

            // Log status change
            if ($result && $currentStatus !== $status) {
                $this->addOrderLog($id, $currentStatus, $status, $adminNotes);
            }

            $this->db->commit();
            return $result;

        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    /**
     * Add order log
     */
    private function addOrderLog($orderId, $statusFrom, $statusTo, $notes = null)
    {
        // Get current admin user ID (assuming it's stored in session)
        $createdBy = $_SESSION['admin_user']['id'] ?? 1; // Default to admin ID 1 if not set

        $sql = "INSERT INTO order_logs (order_id, status_from, status_to, notes, created_by, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())";

        return $this->db->execute($sql, [$orderId, $statusFrom, $statusTo, $notes, $createdBy]);
    }

    /**
     * Update payment status with extended parameters
     */
    public function updatePaymentStatus($id, $paymentStatus, $transactionId = null, $paymentNote = null)
    {
        $sql = "UPDATE {$this->table}
                SET payment_status = ?, updated_at = NOW()";
        $params = [$paymentStatus];

        // Skip transaction_id as column doesn't exist
        // if ($transactionId) {
        //     $sql .= ", transaction_id = ?";
        //     $params[] = $transactionId;
        // }

        if ($paymentNote) {
            $sql .= ", admin_notes = ?";
            $params[] = $paymentNote;
        }

        $sql .= " WHERE id = ?";
        $params[] = $id;

        return $this->db->execute($sql, $params);
    }

    /**
     * Cancel order
     */
    public function cancelOrder($id, $reason = null)
    {
        try {
            $this->db->beginTransaction();

            // Get order items to restore stock
            $items = $this->getOrderItems($id);

            // Restore stock for each item
            foreach ($items as $item) {
                if ($item['variant_id']) {
                    $sql = "UPDATE product_variants
                            SET stock_quantity = stock_quantity + ?
                            WHERE id = ? AND product_id = ?";
                    $this->db->execute($sql, [$item['quantity'], $item['variant_id'], $item['product_id']]);
                }
            }

            // Update order status
            $sql = "UPDATE {$this->table} SET status = 'cancelled', updated_at = NOW()";
            $params = [];

            if ($reason) {
                $sql .= ", admin_notes = ?";
                $params[] = $reason;
            }

            $sql .= " WHERE id = ?";
            $params[] = $id;

            $result = $this->db->execute($sql, $params);

            $this->db->commit();
            return $result;

        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    /**
     * Generate unique order code
     */
    public function generateOrderCode()
    {
        do {
            $code = 'ORD' . date('ymd') . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $exists = $this->findByOrderCode($code);
        } while ($exists);

        return $code;
    }

    /**
     * Get order statistics
     */
    public function getStatistics($dateFrom = null, $dateTo = null)
    {
        $sql = "SELECT
                    COUNT(*) as total_orders,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
                    SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing_orders,
                    SUM(CASE WHEN status = 'shipped' THEN 1 ELSE 0 END) as shipped_orders,
                    SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered_orders,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders,
                    SUM(CASE WHEN status IN ('processing', 'shipped', 'delivered') THEN total_amount ELSE 0 END) as total_revenue,
                    AVG(CASE WHEN status IN ('processing', 'shipped', 'delivered') THEN total_amount ELSE NULL END) as average_order_value
                FROM {$this->table}";

        $params = [];

        if ($dateFrom || $dateTo) {
            $sql .= " WHERE ";
            $conditions = [];

            if ($dateFrom) {
                $conditions[] = "created_at >= ?";
                $params[] = $dateFrom;
            }

            if ($dateTo) {
                $conditions[] = "created_at <= ?";
                $params[] = $dateTo . ' 23:59:59';
            }

            $sql .= implode(' AND ', $conditions);
        }

        return $this->db->fetchOne($sql, $params);
    }

    /**
     * Get pending orders statistics
     */
    public function getPendingStatistics()
    {
        $sql = "SELECT
                    COUNT(*) as total_pending,
                    SUM(total_amount) as total_value,
                    COUNT(CASE WHEN TIMESTAMPDIFF(HOUR, created_at, NOW()) > 24 THEN 1 END) as urgent_orders,
                    COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as today_orders,
                    AVG(total_amount) as average_value,
                    MIN(created_at) as oldest_order,
                    MAX(created_at) as newest_order
                FROM {$this->table}
                WHERE status = 'pending'";

        return $this->db->fetchOne($sql);
    }

    /**
     * Get revenue by date range
     */
    public function getRevenueByDateRange($dateFrom, $dateTo, $groupBy = 'day')
    {
        $dateFormat = $groupBy === 'month' ? '%Y-%m' : '%Y-%m-%d';

        $sql = "SELECT
                    DATE_FORMAT(created_at, ?) as period,
                    COUNT(*) as orders,
                    SUM(total_amount) as revenue
                FROM {$this->table}
                WHERE created_at >= ? AND created_at <= ?
                AND status IN ('processing', 'shipped', 'delivered')
                GROUP BY period
                ORDER BY period ASC";

        return $this->db->fetchAll($sql, [$dateFormat, $dateFrom, $dateTo . ' 23:59:59']);
    }

    /**
     * Get top customers by order value
     */
    public function getTopCustomers($limit = 10)
    {
        $sql = "SELECT
                    customer_name,
                    customer_email,
                    COUNT(*) as total_orders,
                    SUM(total_amount) as total_spent,
                    AVG(total_amount) as average_order_value
                FROM {$this->table}
                WHERE status IN ('processing', 'shipped', 'delivered')
                GROUP BY customer_email
                ORDER BY total_spent DESC
                LIMIT ?";

        return $this->db->fetchAll($sql, [$limit]);
    }

    /**
     * Get pending orders count
     */
    public function getPendingCount()
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE status = 'pending'";
        $result = $this->db->fetchOne($sql);
        return $result ? (int)$result['count'] : 0;
    }

    /**
     * Get orders that need attention (pending > 24h, processing > 72h)
     */
    public function getOrdersNeedingAttention()
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE (status = 'pending' AND created_at < DATE_SUB(NOW(), INTERVAL 24 HOUR))
                   OR (status = 'processing' AND created_at < DATE_SUB(NOW(), INTERVAL 72 HOUR))
                ORDER BY created_at ASC";

        return $this->db->fetchAll($sql);
    }

    /**
     * Search orders
     */
    public function search($query, $filters = [])
    {
        $sql = "SELECT o.*, u.full_name as customer_full_name
                FROM {$this->table} o
                LEFT JOIN users u ON o.user_id = u.id
                WHERE 1=1";

        $params = [];

        // Text search
        if (!empty($query)) {
            $sql .= " AND (o.order_code LIKE ? OR o.customer_name LIKE ? OR o.customer_email LIKE ?)";
            $searchTerm = "%{$query}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        // Status filter
        if (!empty($filters['status'])) {
            $sql .= " AND o.status = ?";
            $params[] = $filters['status'];
        }

        // Payment status filter
        if (!empty($filters['payment_status'])) {
            $sql .= " AND o.payment_status = ?";
            $params[] = $filters['payment_status'];
        }

        // Payment method filter
        if (!empty($filters['payment_method'])) {
            $sql .= " AND o.payment_method = ?";
            $params[] = $filters['payment_method'];
        }

        // Date range
        if (!empty($filters['date_from'])) {
            $sql .= " AND o.created_at >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $sql .= " AND o.created_at <= ?";
            $params[] = $filters['date_to'] . ' 23:59:59';
        }

        // Amount range
        if (!empty($filters['amount_min'])) {
            $sql .= " AND o.total_amount >= ?";
            $params[] = $filters['amount_min'];
        }

        if (!empty($filters['amount_max'])) {
            $sql .= " AND o.total_amount <= ?";
            $params[] = $filters['amount_max'];
        }

        // Sort
        $orderBy = $filters['sort'] ?? 'created_at';
        $orderDir = $filters['order'] ?? 'DESC';

        $allowedSorts = ['created_at', 'total_amount', 'customer_name', 'status'];
        if (in_array($orderBy, $allowedSorts)) {
            $sql .= " ORDER BY o.{$orderBy} {$orderDir}";
        } else {
            $sql .= " ORDER BY o.created_at DESC";
        }

        // Limit
        if (!empty($filters['limit'])) {
            $sql .= " LIMIT ?";
            $params[] = $filters['limit'];
        }

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Update payment method
     */
    public function updatePaymentMethod($orderId, $paymentMethod, $paymentStatus = 'pending')
    {
        $sql = "UPDATE {$this->table}
                SET payment_method = ?, payment_status = ?, updated_at = NOW()
                WHERE id = ?";

        return $this->db->query($sql, [$paymentMethod, $paymentStatus, $orderId]);
    }

    /**
     * Update order status
     */
    public function updateOrderStatus($orderId, $status, $note = null)
    {
        $sql = "UPDATE {$this->table}
                SET status = ?, updated_at = NOW()";
        $params = [$status];

        if ($note) {
            $sql .= ", admin_notes = ?";
            $params[] = $note;
        }

        $sql .= " WHERE id = ?";
        $params[] = $orderId;

        return $this->db->query($sql, $params);
    }

    /**
     * Get order with items by order code
     */
    public function getOrderWithItems($orderCode)
    {
        // Get order
        $order = $this->findByOrderCode($orderCode);
        if (!$order) {
            return null;
        }

        // Get order items
        $sql = "SELECT oi.*, p.name as product_name, p.featured_image as image, p.slug as product_slug
                FROM order_items oi
                LEFT JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?";

        $items = $this->db->fetchAll($sql, [$order['id']]);

        $order['items'] = $items;
        return $order;
    }
}
