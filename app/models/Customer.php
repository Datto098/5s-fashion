  
<?php
/**
 * Customer Model (extends User)
 * 5S Fashion E-commerce Platform
 */

class Customer extends BaseModel
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable = [
        'full_name', 'email', 'phone', 'avatar',
        'status', 'email_verified_at'
    ];

    /**
     * Get customers only (exclude admin users)
     */
    public function getCustomers($limit = null)
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE role = 'customer'
                ORDER BY created_at DESC";

        if ($limit) {
            $sql .= " LIMIT ?";
            return $this->db->fetchAll($sql, [$limit]);
        }

        return $this->db->fetchAll($sql);
    }

    /**
     * Get customer with statistics
     */
    public function getCustomerWithStats($id)
    {
        $sql = "SELECT u.*,
                       (SELECT COUNT(*) FROM orders WHERE user_id = u.id) as total_orders,
                       (SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE user_id = u.id AND status IN ('processing', 'shipped', 'delivered')) as total_spent,
                       (SELECT COUNT(*) FROM orders WHERE user_id = u.id AND status = 'pending') as pending_orders,
                       (SELECT created_at FROM orders WHERE user_id = u.id ORDER BY created_at DESC LIMIT 1) as last_order_date
                FROM {$this->table} u
                WHERE u.id = ? AND u.role = 'customer'";

        return $this->db->fetchOne($sql, [$id]);
    }

    /**
     * Get customer addresses
     */
    public function getCustomerAddresses($customerId)
    {
        $sql = "SELECT * FROM customer_addresses
                WHERE user_id = ?
                ORDER BY is_default DESC, created_at ASC";

        return $this->db->fetchAll($sql, [$customerId]);
    }
    
    /**
     * Alias for getCustomerAddresses - Get addresses by user ID
     */
    public function getAddressesByUserId($userId)
    {
        return $this->getCustomerAddresses($userId);
    }

    /**
     * Get customer orders
     */
    public function getCustomerOrders($customerId, $limit = 10)
    {
        $sql = "SELECT * FROM orders
                WHERE user_id = ?
                ORDER BY created_at DESC
                LIMIT ?";

        return $this->db->fetchAll($sql, [$customerId, $limit]);
    }

    /**
     * Get active customers (updated within last 30 days as activity indicator)
     */
    public function getActiveCustomers($days = 30)
    {
        $sql = "SELECT u.*,
                       (SELECT COUNT(*) FROM orders WHERE user_id = u.id) as total_orders,
                       (SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE user_id = u.id AND status IN ('processing', 'shipped', 'delivered')) as total_spent
                FROM {$this->table} u
                WHERE u.role = 'customer'
                AND u.status = 'active'
                AND u.updated_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                ORDER BY u.updated_at DESC";

        return $this->db->fetchAll($sql, [$days]);
    }

    /**
     * Get customers by spending tier
     */
    public function getCustomersByTier($tier = 'all')
    {
        $sql = "SELECT u.*,
                       COALESCE(SUM(o.total_amount), 0) as total_spent,
                       COUNT(o.id) as total_orders
                FROM {$this->table} u
                LEFT JOIN orders o ON u.id = o.user_id AND o.status IN ('processing', 'shipped', 'delivered')
                WHERE u.role = 'customer'
                GROUP BY u.id";

        if ($tier !== 'all') {
            switch ($tier) {
                case 'vip':
                    $sql .= " HAVING total_spent >= 5000000"; // 5M VND
                    break;
                case 'gold':
                    $sql .= " HAVING total_spent >= 2000000 AND total_spent < 5000000"; // 2-5M VND
                    break;
                case 'silver':
                    $sql .= " HAVING total_spent >= 500000 AND total_spent < 2000000"; // 500K-2M VND
                    break;
                case 'bronze':
                    $sql .= " HAVING total_spent < 500000"; // < 500K VND
                    break;
            }
        }

        $sql .= " ORDER BY total_spent DESC";

        return $this->db->fetchAll($sql);
    }

    /**
     * Get customer statistics
     */
    public function getCustomerStatistics()
    {
        $sql = "SELECT
                    COUNT(*) as total_customers,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_customers,
                    SUM(CASE WHEN email_verified_at IS NOT NULL THEN 1 ELSE 0 END) as verified_customers,
                    SUM(CASE WHEN updated_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as recent_active,
                    SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as new_this_month
                FROM {$this->table}
                WHERE role = 'customer'";

        $stats = $this->db->fetchOne($sql);

        // Get customer spending tiers
        $tierSql = "SELECT
                        SUM(CASE WHEN total_spent >= 5000000 THEN 1 ELSE 0 END) as vip_customers,
                        SUM(CASE WHEN total_spent >= 2000000 AND total_spent < 5000000 THEN 1 ELSE 0 END) as gold_customers,
                        SUM(CASE WHEN total_spent >= 500000 AND total_spent < 2000000 THEN 1 ELSE 0 END) as silver_customers,
                        SUM(CASE WHEN total_spent < 500000 THEN 1 ELSE 0 END) as bronze_customers
                    FROM (
                        SELECT u.id, COALESCE(SUM(o.total_amount), 0) as total_spent
                        FROM {$this->table} u
                        LEFT JOIN orders o ON u.id = o.user_id AND o.status IN ('processing', 'shipped', 'delivered')
                        WHERE u.role = 'customer'
                        GROUP BY u.id
                    ) customer_spending";

        $tierStats = $this->db->fetchOne($tierSql);

        return array_merge($stats, $tierStats);
    }

    /**
     * Search customers
     */
    public function searchCustomers($query, $filters = [])
    {
        $sql = "SELECT u.*,
                       COUNT(o.id) as total_orders,
                       COALESCE(SUM(CASE WHEN o.status IN ('processing', 'shipped', 'delivered') THEN o.total_amount ELSE 0 END), 0) as total_spent,
                       MAX(o.created_at) as last_order_date
                FROM {$this->table} u
                LEFT JOIN orders o ON u.id = o.user_id
                WHERE u.role = 'customer'";

        $params = [];

        // Text search
        if (!empty($query)) {
            $sql .= " AND (u.full_name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)";
            $searchTerm = "%{$query}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        // Status filter
        if (!empty($filters['status'])) {
            if ($filters['status'] === 'active') {
                $sql .= " AND u.status = 'active'";
            } elseif ($filters['status'] === 'inactive') {
                $sql .= " AND u.status = 'inactive'";
            } elseif ($filters['status'] === 'banned') {
                $sql .= " AND u.status = 'banned'";
            } elseif ($filters['status'] === 'verified') {
                $sql .= " AND u.email_verified_at IS NOT NULL";
            } elseif ($filters['status'] === 'unverified') {
                $sql .= " AND u.email_verified_at IS NULL";
            }
        }

        // Registration date range
        if (!empty($filters['date_from'])) {
            $sql .= " AND u.created_at >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $sql .= " AND u.created_at <= ?";
            $params[] = $filters['date_to'] . ' 23:59:59';
        }

        $sql .= " GROUP BY u.id";

        // Spending tier filter
        if (!empty($filters['tier'])) {
            switch ($filters['tier']) {
                case 'vip':
                    $sql .= " HAVING total_spent >= 5000000";
                    break;
                case 'gold':
                    $sql .= " HAVING total_spent >= 2000000 AND total_spent < 5000000";
                    break;
                case 'silver':
                    $sql .= " HAVING total_spent >= 500000 AND total_spent < 2000000";
                    break;
                case 'bronze':
                    $sql .= " HAVING total_spent < 500000";
                    break;
            }
        }

        // Sort
        $orderBy = $filters['sort'] ?? 'created_at';
        $orderDir = $filters['order'] ?? 'DESC';

        $allowedSorts = ['created_at', 'full_name', 'email', 'total_spent', 'total_orders', 'last_order_date'];
        if (in_array($orderBy, $allowedSorts)) {
            $sql .= " ORDER BY {$orderBy} {$orderDir}";
        } else {
            $sql .= " ORDER BY u.created_at DESC";
        }

        // Limit
        if (!empty($filters['limit'])) {
            $sql .= " LIMIT ?";
            $params[] = $filters['limit'];
        }

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Create customer
     */
    public function createCustomer($data)
    {
        $data['role'] = 'customer';
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        return $this->create($data);
    }

      /**
     * Set default address for customer
     */
    public function setDefaultAddress($addressId, $customerId)
    {
        // Unset all current default addresses
        $this->db->execute("UPDATE customer_addresses SET is_default = 0 WHERE user_id = ?", [$customerId]);
        // Set the selected address as default
        $sql = "UPDATE customer_addresses SET is_default = 1 WHERE id = ? AND user_id = ?";
        return $this->db->execute($sql, [$addressId, $customerId]);
    }

    /**
     * Update customer
     */
    public function updateCustomer($id, $data)
    {
        // Remove sensitive fields that shouldn't be updated directly
        unset($data['password'], $data['role'], $data['email_verified_at']);

        return $this->update($id, $data);
    }

    /**
     * Activate/Deactivate customer
     */
    public function setCustomerStatus($id, $status)
    {
        // Validate status
        $validStatuses = ['active', 'inactive', 'banned'];
        if (!in_array($status, $validStatuses)) {
            $status = 'inactive';
        }

        return $this->update($id, ['status' => $status]);
    }

    /**
     * Verify customer email
     */
    public function verifyEmail($id)
    {
        return $this->update($id, ['email_verified_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Add customer address
     */
    public function addCustomerAddress($addressData)
    {
        // Validate required fields
        if (empty($addressData['name']) || empty($addressData['address']) || empty($addressData['phone'])) {
            return false;
        }

        // Get customer ID from address data
        $customerId = $addressData['user_id'] ?? 0;

        // Check if user has any address
        $count = $this->db->fetchOne("SELECT COUNT(*) as cnt FROM customer_addresses WHERE user_id = ?", [$customerId]);
        $isFirst = empty($count) || $count['cnt'] == 0;

        // Nếu là địa chỉ đầu tiên thì luôn set mặc định
        if ($isFirst) {
            $addressData['is_default'] = 1;
        } elseif (!empty($addressData['is_default'])) {
            // Nếu chọn mặc định thì unset các địa chỉ cũ
            $this->db->execute("UPDATE customer_addresses SET is_default = 0 WHERE user_id = ?", [$customerId]);
        }

        // Đảm bảo is_default luôn là 1 hoặc 0 (không phải 'on')
        if (isset($addressData['is_default'])) {
            $addressData['is_default'] = ($addressData['is_default'] === 'on' || $addressData['is_default'] == 1) ? 1 : 0;
        } else {
            $addressData['is_default'] = 0;
        }

        $addressData['user_id'] = $customerId;
        $addressData['created_at'] = date('Y-m-d H:i:s');
        $addressData['updated_at'] = date('Y-m-d H:i:s');

        // Chỉ lấy các trường có trong bảng
        $fields = ['user_id', 'name', 'phone', 'address', 'is_default', 'note', 'created_at', 'updated_at'];
        $insertFields = [];
        $placeholders = [];
        $values = [];
        foreach ($fields as $field) {
            if (array_key_exists($field, $addressData)) {
                $insertFields[] = $field;
                $placeholders[] = '?';
                $values[] = $addressData[$field];
            }
        }

        $sql = "INSERT INTO customer_addresses (" . implode(',', $insertFields) . ") VALUES (" . implode(',', $placeholders) . ")";
        return $this->db->execute($sql, $values);
    }

    /**
     * Update customer address
     */
    public function updateCustomerAddress($addressId, $customerId, $addressData)
    {
        // If this is set as default, unset other defaults first
        if (!empty($addressData['is_default'])) {
            $sql = "UPDATE customer_addresses SET is_default = 0 WHERE user_id = ? AND id != ?";
            $this->db->execute($sql, [$customerId, $addressId]);
        }

        $addressData['updated_at'] = date('Y-m-d H:i:s');

        $sql = "UPDATE customer_addresses SET ";
        $setParts = [];
        $params = [];

        foreach ($addressData as $key => $value) {
            $setParts[] = "{$key} = ?";
            $params[] = $value;
        }

        $sql .= implode(', ', $setParts);
        $sql .= " WHERE id = ? AND user_id = ?";
        $params[] = $addressId;
        $params[] = $customerId;

        return $this->db->execute($sql, $params);
    }

    /**
     * Delete customer address
     */
    public function deleteCustomerAddress($addressId, $customerId)
    {
        $sql = "DELETE FROM customer_addresses WHERE id = ? AND user_id = ?";
        return $this->db->execute($sql, [$addressId, $customerId]);
    }

    /**
     * Get customer tier based on spending
     */
    public function getCustomerTier($totalSpent)
    {
        if ($totalSpent >= 5000000) {
            return ['tier' => 'vip', 'name' => 'VIP', 'color' => 'purple'];
        } elseif ($totalSpent >= 2000000) {
            return ['tier' => 'gold', 'name' => 'Gold', 'color' => 'yellow'];
        } elseif ($totalSpent >= 500000) {
            return ['tier' => 'silver', 'name' => 'Silver', 'color' => 'gray'];
        } else {
            return ['tier' => 'bronze', 'name' => 'Bronze', 'color' => 'orange'];
        }
    }

    /**
     * Get customer loyalty points (if implementing)
     */
    public function getCustomerPoints($customerId)
    {
        $sql = "SELECT COALESCE(SUM(points), 0) as total_points
                FROM customer_loyalty_points
                WHERE user_id = ? AND (expires_at IS NULL OR expires_at > NOW())";

        $result = $this->db->fetchOne($sql, [$customerId]);
        return $result ? (int)$result['total_points'] : 0;
    }

    /**
     * Award loyalty points
     */
    public function awardPoints($customerId, $points, $description, $expiresAt = null)
    {
        $sql = "INSERT INTO customer_loyalty_points (user_id, points, description, expires_at, created_at)
                VALUES (?, ?, ?, ?, NOW())";

        return $this->db->execute($sql, [$customerId, $points, $description, $expiresAt]);
    }

    /**
     * Get customer birthday this month
     * Note: date_of_birth field doesn't exist in current schema, returning empty array
     */
    public function getBirthdayCustomers($month = null)
    {
        // Since date_of_birth field doesn't exist in users table schema,
        // return empty array to prevent SQL errors
        return [];

        /* Original code when date_of_birth field exists:
        $month = $month ?? date('m');

        $sql = "SELECT * FROM {$this->table}
                WHERE role = 'customer'
                AND MONTH(date_of_birth) = ?
                AND date_of_birth IS NOT NULL
                ORDER BY DAY(date_of_birth) ASC";

        return $this->db->fetchAll($sql, [$month]);
        */
    }

    /**
     * Get customers for email marketing (active customers with email)
     */
    public function getMarketingSubscribers()
    {
        $sql = "SELECT id, full_name, email
                FROM {$this->table}
                WHERE role = 'customer'
                AND status = 'active'
                AND email IS NOT NULL
                AND email_verified_at IS NOT NULL
                ORDER BY created_at DESC";

        return $this->db->fetchAll($sql);
    }

    /**
     * Update last active timestamp (using updated_at as activity indicator)
     */
    public function updateLastActive($customerId)
    {
        $sql = "UPDATE {$this->table} SET updated_at = NOW() WHERE id = ?";
        return $this->db->execute($sql, [$customerId]);
    }

    /**
     * Delete customer (soft delete by deactivating)
     */
    public function deleteCustomer($id)
    {
        // Instead of hard delete, deactivate and anonymize
        $anonymizedEmail = 'deleted_' . $id . '@deleted.local';
        $anonymizedPhone = 'DELETED';

        $sql = "UPDATE {$this->table}
                SET status = 'inactive',
                    email = ?,
                    phone = ?,
                    full_name = 'Deleted User',
                    avatar = NULL,
                    updated_at = NOW()
                WHERE id = ? AND role = 'customer'";

        return $this->db->execute($sql, [$anonymizedEmail, $anonymizedPhone, $id]);
    }

    /**
     * Export customers data
     */
    public function exportCustomers($filters = [])
    {
        $customers = $this->searchCustomers('', $filters);

        $export = [];
        foreach ($customers as $customer) {
            $tier = $this->getCustomerTier($customer['total_spent']);

            // Map status to Vietnamese
            $statusMap = [
                'active' => 'Hoạt động',
                'inactive' => 'Không hoạt động',
                'banned' => 'Bị khóa'
            ];

            $export[] = [
                'ID' => $customer['id'],
                'Họ tên' => $customer['full_name'],
                'Email' => $customer['email'],
                'Điện thoại' => $customer['phone'],
                'Trạng thái' => $statusMap[$customer['status']] ?? 'Không xác định',
                'Email xác thực' => $customer['email_verified_at'] ? 'Đã xác thực' : 'Chưa xác thực',
                'Hạng thành viên' => $tier['name'],
                'Tổng đơn hàng' => $customer['total_orders'],
                'Tổng chi tiêu' => number_format($customer['total_spent']),
                'Đơn hàng cuối' => $customer['last_order_date'],
                'Ngày đăng ký' => $customer['created_at'],
                'Cập nhật cuối' => $customer['updated_at']
            ];
        }

        return $export;
    }
}
