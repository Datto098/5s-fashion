<?php
/**
 * Order Model
 * zone Fashion E-commerce Platform
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
     * Check if a user has purchased and received a product
     * 
     * @param int $userId User ID
     * @param int $productId Product ID
     * @param array $statuses Array of valid statuses to consider (e.g. 'delivered', 'completed')
     * @return bool True if user has purchased and received product
     */
    public function hasUserPurchasedProduct($userId, $productId, $statuses = ['delivered', 'completed'])
    {
        $placeholders = implode(',', array_fill(0, count($statuses), '?'));
        
        $sql = "SELECT COUNT(*) as count 
                FROM {$this->table} o
                JOIN order_items oi ON o.id = oi.order_id
                WHERE o.user_id = ? 
                AND oi.product_id = ?
                AND o.status IN ($placeholders)";
                
        $params = array_merge([$userId, $productId], $statuses);
        $result = $this->db->fetchOne($sql, $params);
        
        return $result['count'] > 0;
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
            $orderData['status'] = 'pending';

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
            // Determine if payment method should immediately decrement stock (prepaid)
            $paymentMethod = $orderData['payment_method'] ?? 'cod';
            $prepaidMethods = ['vnpay', 'momo', 'card']; // immediate decrement for these

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

                // Stock handling: use row locks (SELECT ... FOR UPDATE) inside the same transaction
                try {
                    if (defined('STOCK_MODE') && STOCK_MODE === 'product') {
                        // Product-level stock handling: decrement immediately (products table has no reserved)
                        $sql = "SELECT stock_quantity FROM products WHERE id = ? FOR UPDATE";
                        $stmt = $this->db->getConnection()->prepare($sql);
                        $stmt->execute([$item['product_id']]);
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        $stock = isset($row['stock_quantity']) ? (int)$row['stock_quantity'] : 0;

                        if ($stock >= $item['quantity']) {
                            $upd = "UPDATE products SET stock_quantity = GREATEST(0, stock_quantity - ?), updated_at = NOW() WHERE id = ?";
                            $this->db->execute($upd, [$item['quantity'], $item['product_id']]);
                        } else {
                            throw new Exception('Số lượng trong kho không đủ cho sản phẩm ID ' . $item['product_id']);
                        }

                    } else {
                        // Variant-level handling
                        if (!empty($item['variant_id'])) {
                            $pdo = $this->db->getConnection();
                            $selectSql = "SELECT stock_quantity, reserved_quantity FROM product_variants WHERE id = ? FOR UPDATE";
                            $stmt = $pdo->prepare($selectSql);
                            $stmt->execute([$item['variant_id']]);
                            $variantRow = $stmt->fetch(PDO::FETCH_ASSOC);

                            $stockQty = isset($variantRow['stock_quantity']) ? (int)$variantRow['stock_quantity'] : 0;
                            $reservedQty = isset($variantRow['reserved_quantity']) ? (int)$variantRow['reserved_quantity'] : 0;

                            if (in_array(strtolower($paymentMethod), $prepaidMethods, true)) {
                                // Prepaid: decrement stock immediately
                                if ($stockQty >= $item['quantity']) {
                                    $upd = "UPDATE product_variants SET stock_quantity = GREATEST(0, stock_quantity - ?), updated_at = NOW() WHERE id = ?";
                                    $this->db->execute($upd, [$item['quantity'], $item['variant_id']]);
                                } else {
                                    throw new Exception('Số lượng trong kho không đủ cho variant ID ' . $item['variant_id']);
                                }
                            } else {
                                // Unpaid (COD, bank_transfer etc.): reserve only
                                $available = $stockQty - $reservedQty;
                                if ($available >= $item['quantity']) {
                                    $upd = "UPDATE product_variants SET reserved_quantity = reserved_quantity + ?, updated_at = NOW() WHERE id = ?";
                                    $this->db->execute($upd, [$item['quantity'], $item['variant_id']]);
                                } else {
                                    throw new Exception('Không đủ số lượng khả dụng để đặt trước cho variant ID ' . $item['variant_id']);
                                }
                            }
                        } else {
                            // No variant_id: product-level fallback (decrement immediately)
                            $sql = "SELECT stock_quantity FROM products WHERE id = ? FOR UPDATE";
                            $stmt = $this->db->getConnection()->prepare($sql);
                            $stmt->execute([$item['product_id']]);
                            $row = $stmt->fetch(PDO::FETCH_ASSOC);
                            $stock = isset($row['stock_quantity']) ? (int)$row['stock_quantity'] : 0;

                            if ($stock >= $item['quantity']) {
                                $upd = "UPDATE products SET stock_quantity = GREATEST(0, stock_quantity - ?), updated_at = NOW() WHERE id = ?";
                                $this->db->execute($upd, [$item['quantity'], $item['product_id']]);
                            } else {
                                throw new Exception('Số lượng trong kho không đủ cho sản phẩm ID ' . $item['product_id']);
                            }
                        }
                    }
                } catch (Exception $e) {
                    error_log('[ORDER CREATE] Stock reservation/adjust failed: ' . $e->getMessage());
                    // Rethrow to rollback whole transaction and return error to caller
                    throw $e;
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
     * Finalize stock for an order (decrease actual stock and reduce reserved)
     */
    public function finalizeOrderStock($orderId)
    {
        $items = $this->getOrderItems($orderId);

        try {
            // Only begin a transaction if one isn't already active. This allows callers
            // (e.g. controllers) to wrap multiple operations in a single transaction
            // and avoids nested transaction errors on the same PDO connection.
            $transactionStarted = false;
            if (!$this->db->getConnection()->inTransaction()) {
                $this->db->beginTransaction();
                $transactionStarted = true;
            }

            $pdo = $this->db->getConnection();

            foreach ($items as $item) {
                if (!empty($item['variant_id'])) {
                    // Lock the variant row
                    $select = $pdo->prepare("SELECT stock_quantity, reserved_quantity FROM product_variants WHERE id = ? FOR UPDATE");
                    $select->execute([$item['variant_id']]);
                    $row = $select->fetch(PDO::FETCH_ASSOC);

                    $stockQty = isset($row['stock_quantity']) ? (int)$row['stock_quantity'] : 0;
                    $reservedQty = isset($row['reserved_quantity']) ? (int)$row['reserved_quantity'] : 0;

                    // Only adjust reserved->sold when there is a reserved amount
                    if ($reservedQty >= $item['quantity']) {
                        $sql = "UPDATE product_variants SET stock_quantity = GREATEST(0, stock_quantity - ?), reserved_quantity = GREATEST(0, reserved_quantity - ?) WHERE id = ? AND product_id = ?";
                        $this->db->execute($sql, [$item['quantity'], $item['quantity'], $item['variant_id'], $item['product_id']]);
                    } else {
                        // If there was no reservation (prepaid path), ensure we don't double-decrement.
                        // If stock still has enough quantity, decrement stock and leave reserved at 0.
                        if ($stockQty >= $item['quantity']) {
                            $sql = "UPDATE product_variants SET stock_quantity = GREATEST(0, stock_quantity - ?), reserved_quantity = GREATEST(0, reserved_quantity - ?) WHERE id = ? AND product_id = ?";
                            $this->db->execute($sql, [$item['quantity'], min($reservedQty, $item['quantity']), $item['variant_id'], $item['product_id']]);
                        } else {
                            // If not enough stock, throw to let caller decide (could re-open reservation or notify)
                            throw new Exception('Insufficient stock to finalize order item for variant ' . $item['variant_id']);
                        }
                    }

                } else {
                    // Product-level: lock and decrement
                    $select = $pdo->prepare("SELECT stock_quantity FROM products WHERE id = ? FOR UPDATE");
                    $select->execute([$item['product_id']]);
                    $row = $select->fetch(PDO::FETCH_ASSOC);
                    $stockQty = isset($row['stock_quantity']) ? (int)$row['stock_quantity'] : 0;

                    if ($stockQty >= $item['quantity']) {
                        $sql = "UPDATE products SET stock_quantity = GREATEST(0, stock_quantity - ?), updated_at = NOW() WHERE id = ?";
                        $this->db->execute($sql, [$item['quantity'], $item['product_id']]);
                    } else {
                        throw new Exception('Insufficient stock to finalize order item for product ' . $item['product_id']);
                    }
                }
            }

            // Only commit if we started the transaction here
            if (!empty($transactionStarted)) {
                $this->db->commit();
            }
        } catch (Exception $e) {
            // Only rollback if we started the transaction here and it's still active
            if (!empty($transactionStarted) && $this->db->getConnection()->inTransaction()) {
                $this->db->rollback();
            }
            throw $e;
        }
    }

    /**
     * Release reserved stock for an order (used on cancel/failed payment)
     */
    public function releaseReservedForOrder($orderId)
    {
        $items = $this->getOrderItems($orderId);

        foreach ($items as $item) {
            if (!empty($item['variant_id'])) {
                $sql = "UPDATE product_variants SET reserved_quantity = GREATEST(0, reserved_quantity - ?) WHERE id = ? AND product_id = ?";
                $this->db->execute($sql, [$item['quantity'], $item['variant_id'], $item['product_id']]);
            } else {
                // No variant-level reservation exists on products table; nothing to release for product-level items
                // (product-level stock was already decremented at order creation in product mode)
            }
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
            $itemData['total'],

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
            
            // If changing from cancelled to an active status, handle inventory reinstatement
            if ($currentStatus === 'cancelled' && $status !== 'cancelled') {
                error_log("[ORDER STATUS] Transitioning from cancelled to {$status} for order {$id}");
                try {
                    // This call will decrement stock or reserve quantities as needed
                    $this->reinstateOrder($id);
                } catch (Exception $e) {
                    // Nếu không đủ tồn kho, vẫn cho phép chuyển trạng thái nhưng ghi log lỗi
                    error_log("[ORDER STATUS WARNING] Could not reinstate inventory for order {$id}: " . $e->getMessage());
                    // Add a note to the order about the inventory issue
                    $adminNotes = ($adminNotes ? $adminNotes . "\n" : '') . 
                        "CHÚ Ý: Đơn hàng được khôi phục nhưng không thể trừ tồn kho do số lượng không đủ. Vui lòng kiểm tra tồn kho sản phẩm.";
                }
            }

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

            // Load order to inspect payment method/status
            $order = $this->find($id);
            if (!$order) {
                throw new Exception('Order not found');
            }

            $paymentMethod = strtolower($order['payment_method'] ?? '');
            $paymentStatus = strtolower($order['payment_status'] ?? '');

            // Prepaid methods where stock was decremented at creation
            $prepaidMethods = ['vnpay', 'momo', 'card'];
            $isPrepaid = in_array($paymentMethod, $prepaidMethods, true) || $paymentStatus === 'paid';

            // Debug log: trace cancel call context
            error_log(sprintf("[ORDER CANCEL] Order ID=%s, payment_method=%s, payment_status=%s, isPrepaid=%s", $id, $paymentMethod, $paymentStatus, $isPrepaid ? '1' : '0'));

            // Restore stock / release reserved for each item appropriately
            $items = $this->getOrderItems($id);
            $pdo = $this->db->getConnection();

            foreach ($items as $item) {
                // Variant-level handling
                if (!empty($item['variant_id'])) {
                    // Lock variant row
                    $select = $pdo->prepare("SELECT stock_quantity, reserved_quantity FROM product_variants WHERE id = ? FOR UPDATE");
                    $select->execute([$item['variant_id']]);
                    $row = $select->fetch(PDO::FETCH_ASSOC);

                    // Debug log current variant state
                    error_log(sprintf("[ORDER CANCEL] Item order_id=%s, variant_id=%s, product_id=%s, qty=%s, current_stock=%s, current_reserved=%s", $id, $item['variant_id'], $item['product_id'], $item['quantity'], $row['stock_quantity'] ?? 'NULL', $row['reserved_quantity'] ?? 'NULL'));

                    // Always restore the stock quantity regardless of payment method
                    // This ensures products return to inventory when an admin cancels an order
                    $sql = "UPDATE product_variants SET stock_quantity = stock_quantity + ?, updated_at = NOW() WHERE id = ? AND product_id = ?";
                    $this->db->execute($sql, [$item['quantity'], $item['variant_id'], $item['product_id']]);
                    
                    error_log(sprintf("[ORDER CANCEL] Restored stock for variant_id=%s by %s", $item['variant_id'], $item['quantity']));
                    
                    // If order was COD/unpaid, also release any reserved quantity
                    if (!$isPrepaid) {
                        $sql = "UPDATE product_variants SET reserved_quantity = GREATEST(0, reserved_quantity - ?), updated_at = NOW() WHERE id = ? AND product_id = ?";
                        $this->db->execute($sql, [$item['quantity'], $item['variant_id'], $item['product_id']]);
                        
                        error_log(sprintf("[ORDER CANCEL] Released reserved for variant_id=%s by %s", $item['variant_id'], $item['quantity']));
                    }
                } else {
                    // Product-level handling
                    // Always restore stock for products when canceling orders
                    // Lock product row
                    $select = $pdo->prepare("SELECT stock_quantity FROM products WHERE id = ? FOR UPDATE");
                    $select->execute([$item['product_id']]);
                    $row = $select->fetch(PDO::FETCH_ASSOC);

                    error_log(sprintf("[ORDER CANCEL] Item order_id=%s, product_id=%s, qty=%s, current_stock=%s", $id, $item['product_id'], $item['quantity'], $row['stock_quantity'] ?? 'NULL'));

                    $sql = "UPDATE products SET stock_quantity = stock_quantity + ?, updated_at = NOW() WHERE id = ?";
                    $this->db->execute($sql, [$item['quantity'], $item['product_id']]);

                    error_log(sprintf("[ORDER CANCEL] Restored stock for product_id=%s by %s", $item['product_id'], $item['quantity']));
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

            // Log status change from previous to cancelled
            try {
                $currentStatus = $order['status'] ?? null;
                if ($currentStatus && $currentStatus !== 'cancelled') {
                    $this->addOrderLog($id, $currentStatus, 'cancelled', $reason);
                }
            } catch (Exception $e) {
                // Non-fatal: don't break cancellation if logging fails
                error_log('Order cancel: failed to add log - ' . $e->getMessage());
            }

            $this->db->commit();
            return $result;

        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    /**
     * Reinstate an order previously cancelled: attempt to re-apply the original stock/reservation changes.
     * Returns true on success, throws Exception on failure.
     */
    public function reinstateOrder($id)
    {
        try {
            $this->db->beginTransaction();

            $order = $this->find($id);
            if (!$order) {
                throw new Exception('Order not found');
            }

            // Determine if original path decremented stock (prepaid) or reserved (unpaid)
            $paymentMethod = strtolower($order['payment_method'] ?? '');
            $paymentStatus = strtolower($order['payment_status'] ?? '');
            $prepaidMethods = ['vnpay', 'momo', 'card'];
            $isPrepaid = in_array($paymentMethod, $prepaidMethods, true) || $paymentStatus === 'paid';

            $items = $this->getOrderItems($id);
            $pdo = $this->db->getConnection();

            foreach ($items as $item) {
                if (!empty($item['variant_id'])) {
                    // Lock variant row
                    $select = $pdo->prepare("SELECT stock_quantity, reserved_quantity FROM product_variants WHERE id = ? FOR UPDATE");
                    $select->execute([$item['variant_id']]);
                    $row = $select->fetch(PDO::FETCH_ASSOC);

                    $stockQty = isset($row['stock_quantity']) ? (int)$row['stock_quantity'] : 0;
                    $reservedQty = isset($row['reserved_quantity']) ? (int)$row['reserved_quantity'] : 0;

                    // Always decrement stock quantity when reinstating (opposite of cancelOrder)
                    if ($stockQty >= $item['quantity']) {
                        $sql = "UPDATE product_variants SET stock_quantity = GREATEST(0, stock_quantity - ?), updated_at = NOW() WHERE id = ? AND product_id = ?";
                        $this->db->execute($sql, [$item['quantity'], $item['variant_id'], $item['product_id']]);
                        error_log(sprintf("[ORDER REINSTATE] Deducted stock for variant_id=%s by %s", $item['variant_id'], $item['quantity']));
                    } else {
                        // Trừ số lượng có sẵn trong kho (nếu có) và ghi log
                        if ($stockQty > 0) {
                            $sql = "UPDATE product_variants SET stock_quantity = 0, updated_at = NOW() WHERE id = ? AND product_id = ?";
                            $this->db->execute($sql, [$item['variant_id'], $item['product_id']]);
                            error_log(sprintf("[ORDER REINSTATE WARNING] Partially deducted stock for variant_id=%s by %s (available: %s)", $item['variant_id'], $item['quantity'], $stockQty));
                        }
                        throw new Exception('Insufficient stock to reinstate item for variant ' . $item['variant_id']);
                    }
                    
                    // Additionally, for unpaid orders (COD), re-reserve the quantity
                    if (!$isPrepaid) {
                        $available = $stockQty - $reservedQty;
                        if ($available >= $item['quantity']) {
                            $sql = "UPDATE product_variants SET reserved_quantity = reserved_quantity + ?, updated_at = NOW() WHERE id = ? AND product_id = ?";
                            $this->db->execute($sql, [$item['quantity'], $item['variant_id'], $item['product_id']]);
                            error_log(sprintf("[ORDER REINSTATE] Reserved quantity for variant_id=%s by %s", $item['variant_id'], $item['quantity']));
                        } else {
                            throw new Exception('Insufficient available stock to re-reserve variant ' . $item['variant_id']);
                        }
                    }

                } else {
                    // Product-level handling - always decrement stock regardless of payment method
                    // Decrement product stock (ensure enough exists)
                    $select = $pdo->prepare("SELECT stock_quantity FROM products WHERE id = ? FOR UPDATE");
                    $select->execute([$item['product_id']]);
                    $row = $select->fetch(PDO::FETCH_ASSOC);
                    $stockQty = isset($row['stock_quantity']) ? (int)$row['stock_quantity'] : 0;

                    if ($stockQty >= $item['quantity']) {
                        $sql = "UPDATE products SET stock_quantity = GREATEST(0, stock_quantity - ?), updated_at = NOW() WHERE id = ?";
                        $this->db->execute($sql, [$item['quantity'], $item['product_id']]);
                        error_log(sprintf("[ORDER REINSTATE] Deducted stock for product_id=%s by %s", $item['product_id'], $item['quantity']));
                    } else {
                        // Trừ số lượng có sẵn trong kho (nếu có) và ghi log
                        if ($stockQty > 0) {
                            $sql = "UPDATE products SET stock_quantity = 0, updated_at = NOW() WHERE id = ?";
                            $this->db->execute($sql, [$item['product_id']]);
                            error_log(sprintf("[ORDER REINSTATE WARNING] Partially deducted stock for product_id=%s by %s (available: %s)", $item['product_id'], $item['quantity'], $stockQty));
                        }
                        throw new Exception('Insufficient product stock to reinstate item for product ' . $item['product_id']);
                    }
                }
            }

            $this->db->commit();
            return true;

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
