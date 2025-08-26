<?php
/**
 * User Model
 * 5S Fashion E-commerce Platform
 */

class User extends BaseModel
{
    protected $table = 'users';
    protected $primaryKey = 'id';
protected $fillable = [
    'username', 'email', 'password_hash', 'full_name', 'phone',
    'avatar', 'role', 'status', 'email_verified_at', 'birthday', 'address', 'email_verify_token',
    'google_id', 'last_login_at'
];
    protected $hidden = ['password_hash', 'remember_token', 'reset_token'];

    /**
     * Get user by email
     */
    public function findByEmail($email)
    {
        return $this->findBy('email', $email);
    }

    /**
     * Get user by username
     */
    public function findByUsername($username)
    {
        return $this->findBy('username', $username);
    }

    /**
     * Get all admin users
     */
    public function getAdmins()
    {
        return $this->where(['role' => 'admin', 'status' => 'active']);
    }

    /**
     * Get all customers
     */
    public function getCustomers()
    {
        return $this->where(['role' => 'customer']);
    }

    /**
     * Create new user
     */
    public function createUser($data)
    {
        // Hash password nếu có
        if (isset($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            unset($data['password']);
        }
        // Tạo username nếu chưa có
        if (!isset($data['username']) && isset($data['email'])) {
            $data['username'] = $this->generateUsername($data['email']);
        }
        return $this->create($data);
    }

    /**
     * Update user password (with hashing)
     */
    public function updatePassword($userId, $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->update($userId, ['password_hash' => $hashedPassword]);
    }

    /**
     * Update user password (already hashed)
     */
    public function updateHashedPassword($userId, $hashedPassword)
    {
        $sql = "UPDATE {$this->table} SET password_hash = :password WHERE id = :id";
        return $this->db->execute($sql, [
            'id' => $userId,
            'password' => $hashedPassword
        ]);
    }

    /**
     * Verify user password
     */
    public function verifyPassword($userId, $password)
    {
        $user = $this->find($userId);
        if (!$user) {
            return false;
        }

        return password_verify($password, $user['password_hash']);
    }

    /**
     * Get user's orders count
     */
    public function getOrdersCount($userId)
    {
        $sql = "SELECT COUNT(*) as count FROM orders WHERE user_id = :user_id";
        $result = $this->db->fetchOne($sql, ['user_id' => $userId]);
        return $result ? (int)$result['count'] : 0;
    }

    /**
     * Get user's total spent
     */
    public function getTotalSpent($userId)
    {
        $sql = "SELECT SUM(total_amount) as total FROM orders WHERE user_id = :user_id AND status != 'cancelled'";
        $result = $this->db->fetchOne($sql, ['user_id' => $userId]);
        return $result ? (float)$result['total'] : 0;
    }

    /**
     * Generate unique username from email
     */
    protected function generateUsername($email)
    {
        $baseUsername = explode('@', $email)[0];
        $baseUsername = preg_replace('/[^a-zA-Z0-9]/', '', $baseUsername);

        $username = $baseUsername;
        $counter = 1;

        while ($this->findByUsername($username)) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        return $username;
    }

    /**
     * Validate user data
     */
    protected function validate($data)
    {
        $errors = [];

        // Email validation
        if (isset($data['email'])) {
            if (empty($data['email'])) {
                $errors['email'] = 'Email là trường bắt buộc.';
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Email không hợp lệ.';
            } elseif ($this->isEmailExists($data['email'], $data['id'] ?? null)) {
                $errors['email'] = 'Email đã tồn tại.';
            }
        }

        // Username validation
        if (isset($data['username'])) {
            if (empty($data['username'])) {
                $errors['username'] = 'Tên đăng nhập là trường bắt buộc.';
            } elseif (strlen($data['username']) < 3) {
                $errors['username'] = 'Tên đăng nhập phải có ít nhất 3 ký tự.';
            } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $data['username'])) {
                $errors['username'] = 'Tên đăng nhập chỉ được chứa chữ cái, số và dấu gạch dưới.';
            } elseif ($this->isUsernameExists($data['username'], $data['id'] ?? null)) {
                $errors['username'] = 'Tên đăng nhập đã tồn tại.';
            }
        }

        // Password validation
        if (isset($data['password']) && !empty($data['password'])) {
            if (strlen($data['password']) < 8) {
                $errors['password'] = 'Mật khẩu phải có ít nhất 8 ký tự.';
            }
        }

        // Full name validation
        if (isset($data['full_name']) && empty($data['full_name'])) {
            $errors['full_name'] = 'Họ tên là trường bắt buộc.';
        }

        // Phone validation
        if (isset($data['phone']) && !empty($data['phone'])) {
            if (!preg_match('/^[0-9]{10,11}$/', $data['phone'])) {
                $errors['phone'] = 'Số điện thoại không hợp lệ.';
            }
        }

        return $errors;
    }

    /**
     * Check if email exists
     */
    private function isEmailExists($email, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE email = :email";
        $params = ['email' => $email];

        if ($excludeId) {
            $sql .= " AND id != :id";
            $params['id'] = $excludeId;
        }

        $result = $this->db->fetchOne($sql, $params);
        return $result && $result['count'] > 0;
    }

    /**
     * Check if username exists
     */
    private function isUsernameExists($username, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE username = :username";
        $params = ['username' => $username];

        if ($excludeId) {
            $sql .= " AND id != :id";
            $params['id'] = $excludeId;
        }

        $result = $this->db->fetchOne($sql, $params);
        return $result && $result['count'] > 0;
    }

    /**
     * Get user statistics
     */
    public function getStatistics()
    {
        return [
            'total_users' => $this->count(),
            'total_admins' => $this->count(['role' => 'admin']),
            'total_customers' => $this->count(['role' => 'customer']),
            'active_users' => $this->count(['status' => 'active']),
            'inactive_users' => $this->count(['status' => 'inactive']),
            'banned_users' => $this->count(['status' => 'banned'])
        ];
    }

    /**
     * Search users
     */
    public function search($query, $filters = [], $page = 1, $limit = 25)
    {
        $whereConditions = [];
        $params = [];

        // Search in name, email, username
        if (!empty($query)) {
            $whereConditions[] = "(full_name LIKE :query OR email LIKE :query OR username LIKE :query)";
            $params['query'] = "%{$query}%";
        }

        // Role filter
        if (!empty($filters['role'])) {
            $whereConditions[] = "role = :role";
            $params['role'] = $filters['role'];
        }

        // Status filter
        if (!empty($filters['status'])) {
            $whereConditions[] = "status = :status";
            $params['status'] = $filters['status'];
        }

        // Date range filter
        if (!empty($filters['date_from'])) {
            $whereConditions[] = "created_at >= :date_from";
            $params['date_from'] = $filters['date_from'] . ' 00:00:00';
        }

        if (!empty($filters['date_to'])) {
            $whereConditions[] = "created_at <= :date_to";
            $params['date_to'] = $filters['date_to'] . ' 23:59:59';
        }

        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM {$this->table} {$whereClause}";
        $totalResult = $this->db->fetchOne($countSql, $params);
        $total = $totalResult ? (int)$totalResult['total'] : 0;

        // Get paginated data
        $offset = ($page - 1) * $limit;
        $params['limit'] = $limit;
        $params['offset'] = $offset;

        $sql = "SELECT * FROM {$this->table} {$whereClause} ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $data = $this->db->fetchAll($sql, $params);

        // Remove sensitive data
        $data = array_map([$this, 'hideFields'], $data);

        return [
            'data' => $data,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $total,
                'last_page' => ceil($total / $limit),
                'from' => $offset + 1,
                'to' => min($offset + $limit, $total)
            ]
        ];
    }

    /**
     * Get recent customers
     */
    public function getRecentCustomers($limit = 10)
    {
        $sql = "SELECT * FROM {$this->table} WHERE role = 'customer' ORDER BY created_at DESC LIMIT :limit";
        $data = $this->db->fetchAll($sql, ['limit' => $limit]);
        return array_map([$this, 'hideFields'], $data);
    }

    /**
     * Get admin statistics
     */
    public function getAdminStatistics()
    {
        $stats = [
            'total_admins' => 0,
            'active_admins' => 0,
            'inactive_admins' => 0,
            'banned_admins' => 0,
            'recent_logins' => 0
        ];

        // Get total admin counts by status
        $sql = "SELECT status, COUNT(*) as count FROM {$this->table} WHERE role = 'admin' GROUP BY status";
        $results = $this->db->fetchAll($sql);

        foreach ($results as $row) {
            switch ($row['status']) {
                case 'active':
                    $stats['active_admins'] = (int)$row['count'];
                    break;
                case 'inactive':
                    $stats['inactive_admins'] = (int)$row['count'];
                    break;
                case 'banned':
                    $stats['banned_admins'] = (int)$row['count'];
                    break;
            }
            $stats['total_admins'] += (int)$row['count'];
        }

        // Get recent logins (last 30 days) - disabled until last_login column is added
        // For now, return 0 as we don't have login tracking
        $stats['recent_logins'] = 0;        return $stats;
    }

    /**
     * Get admin user with additional statistics
     */
    public function getAdminWithStats($id)
    {
        $user = $this->find($id);
        if (!$user || $user['role'] !== 'admin') {
            return null;
        }

        // Remove sensitive data
        $user = $this->hideFields($user);

        // Add activity statistics
        $user['login_count'] = $this->getLoginCount($id);
        $user['days_since_last_login'] = $this->getDaysSinceLastLogin($id);
        $user['created_orders'] = $this->getCreatedOrdersCount($id);

        return $user;
    }

    /**
     * Update user status
     */
    public function updateStatus($userId, $status)
    {
        $validStatuses = ['active', 'inactive', 'banned'];
        if (!in_array($status, $validStatuses)) {
            return false;
        }

        return $this->update($userId, ['status' => $status]);
    }

    /**
     * Get login count for user (if tracking is implemented)
     */
    private function getLoginCount($userId)
    {
        // This would require a login_logs table to track login history
        // For now, return a placeholder
        return 0;
    }

    /**
     * Get days since last login
     */
    private function getDaysSinceLastLogin($userId)
    {
        // last_login column doesn't exist yet, return null for now
        return null;

        // TODO: Uncomment when last_login column is added to database
        /*
        $user = $this->find($userId);
        if (!$user || !isset($user['last_login']) || !$user['last_login']) {
            return null;
        }

        $lastLogin = new DateTime($user['last_login']);
        $now = new DateTime();
        $diff = $now->diff($lastLogin);

        return $diff->days;
        */
    }

    /**
     * Get count of orders created by admin (if admin creates orders)
     */
    private function getCreatedOrdersCount($adminId)
    {
        // This would be used if admins can create orders on behalf of customers
        // For now, return 0
        return 0;
    }

    /**
     * Enhanced search method for admin users only
     */
    public function searchAdmins($query, $filters = [])
    {
        $whereConditions = ["role = 'admin'"];
        $params = [];

        // Search in name, email, username
        if (!empty($query)) {
            $whereConditions[] = "(full_name LIKE :query OR email LIKE :query OR username LIKE :query)";
            $params['query'] = "%{$query}%";
        }

        // Status filter
        if (!empty($filters['status'])) {
            $whereConditions[] = "status = :status";
            $params['status'] = $filters['status'];
        }

        // Date range filter
        if (!empty($filters['date_from'])) {
            $whereConditions[] = "created_at >= :date_from";
            $params['date_from'] = $filters['date_from'] . ' 00:00:00';
        }

        if (!empty($filters['date_to'])) {
            $whereConditions[] = "created_at <= :date_to";
            $params['date_to'] = $filters['date_to'] . ' 23:59:59';
        }

        $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);

        // Build ORDER BY clause
        $sort = $filters['sort'] ?? 'created_at';
        $order = $filters['order'] ?? 'DESC';
        $limit = $filters['limit'] ?? 50;

        // Validate sort field
        $allowedSorts = ['created_at', 'full_name', 'username', 'email', 'status'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'created_at';
        }

        // Validate order direction
        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

        $sql = "SELECT * FROM {$this->table} {$whereClause} ORDER BY {$sort} {$order} LIMIT :limit";
        $params['limit'] = $limit;

        $data = $this->db->fetchAll($sql, $params);

        // Remove sensitive data
        return array_map([$this, 'hideFields'], $data);
    }

    /**
     * Update user's last login time
     */
    public function updateLastLogin($userId)
    {
        // Temporarily disabled until last_login_at column is added to database
        // TODO: Run migration to add last_login_at column
        $sql = "UPDATE {$this->table} SET last_login_at = NOW() WHERE id = :id";
        return $this->db->execute($sql, ['id' => $userId]);
    }

    /**
     * Save password reset token
     */
    public function saveResetToken($userId, $token, $expiry)
    {
        // Temporarily disabled until reset_token columns are added to database
        // TODO: Run migration to add reset_token and reset_token_expires_at columns
        $sql = "UPDATE {$this->table} SET reset_token = :token, reset_token_expires_at = :expiry WHERE id = :id";
        return $this->db->execute($sql, [
            'id' => $userId,
            'token' => $token,
            'expiry' => $expiry
        ]);
    }

    /**
     * Update user password (for reset password)
     */
    public function resetUserPassword($userId, $hashedPassword)
    {
        $sql = "UPDATE {$this->table} SET password_hash = :password WHERE id = :id";
        return $this->db->execute($sql, [
            'id' => $userId,
            'password' => $hashedPassword
        ]);
    }

    /**
     * Clear reset token
     */
    public function clearResetToken($userId)
    {
        // Temporarily disabled until reset_token columns are added to database
        // TODO: Run migration to add reset_token and reset_token_expires_at columns
        // $sql = "UPDATE {$this->table} SET reset_token = NULL, reset_token_expires_at = NULL WHERE id = :id";
        // return $this->db->execute($sql, ['id' => $userId]);
        return true;
    }
}
