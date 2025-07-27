<?php
/**
 * Authentication Middleware
 * 5S Fashion E-commerce Platform
 */

class AuthMiddleware
{
    /**
     * Handle authentication check
     */
    public function handle()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!$this->check()) {
            $this->redirectToLogin();
        }
    }

    /**
     * Handle admin authentication
     */
    public function handleAdmin()
    {
        $this->handle();

        $user = $this->user();
        if (!$user || $user['role'] !== 'admin') {
            $this->redirectToLogin('Bạn không có quyền truy cập.');
        }
    }

    /**
     * Login user
     */
    public function login($email, $password, $remember = false)
    {
        $db = Database::getInstance();

        // Find user by email
        $user = $db->fetchOne(
            "SELECT * FROM users WHERE email = :email AND status = 'active'",
            ['email' => $email]
        );

        if ($user && $this->verifyPassword($password, $user['password_hash'])) {
            // Update last login
            $db->execute(
                "UPDATE users SET last_login = NOW() WHERE id = :id",
                ['id' => $user['id']]
            );

            // Set session
            $_SESSION['auth_user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'full_name' => $user['full_name'],
                'role' => $user['role'],
                'avatar' => $user['avatar']
            ];

            // Handle remember me
            if ($remember) {
                $this->setRememberToken($user['id']);
            }

            return true;
        }

        return false;
    }

    /**
     * Logout user
     */
    public function logout()
    {
        // Clear remember token if exists
        if (isset($_COOKIE['remember_token'])) {
            $this->clearRememberToken();
        }

        // Clear session
        unset($_SESSION['auth_user']);

        // Destroy session if empty
        if (empty($_SESSION)) {
            session_destroy();
        }
    }

    /**
     * Check if user is authenticated
     */
    public function check()
    {
        // Check session first
        if (isset($_SESSION['auth_user'])) {
            return true;
        }

        // Check remember token
        if (isset($_COOKIE['remember_token'])) {
            return $this->checkRememberToken($_COOKIE['remember_token']);
        }

        return false;
    }

    /**
     * Get authenticated user
     */
    public function user()
    {
        return $_SESSION['auth_user'] ?? null;
    }

    /**
     * Hash password
     */
    public function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Verify password
     */
    protected function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * Set remember token
     */
    protected function setRememberToken($userId)
    {
        $token = bin2hex(random_bytes(32));
        $hashedToken = hash('sha256', $token);

        $db = Database::getInstance();
        $db->execute(
            "UPDATE users SET remember_token = :token WHERE id = :id",
            ['token' => $hashedToken, 'id' => $userId]
        );

        // Set cookie for 1 year
        setcookie('remember_token', $token, time() + (365 * 24 * 60 * 60), '/', '', false, true);
    }

    /**
     * Check remember token
     */
    protected function checkRememberToken($token)
    {
        $hashedToken = hash('sha256', $token);

        $db = Database::getInstance();
        $user = $db->fetchOne(
            "SELECT * FROM users WHERE remember_token = :token AND status = 'active'",
            ['token' => $hashedToken]
        );

        if ($user) {
            // Set session
            $_SESSION['auth_user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'full_name' => $user['full_name'],
                'role' => $user['role'],
                'avatar' => $user['avatar']
            ];

            return true;
        }

        // Invalid token, clear cookie
        $this->clearRememberToken();
        return false;
    }

    /**
     * Clear remember token
     */
    protected function clearRememberToken()
    {
        if (isset($_SESSION['auth_user'])) {
            $db = Database::getInstance();
            $db->execute(
                "UPDATE users SET remember_token = NULL WHERE id = :id",
                ['id' => $_SESSION['auth_user']['id']]
            );
        }

        setcookie('remember_token', '', time() - 3600, '/', '', false, true);
    }

    /**
     * Redirect to login
     */
    protected function redirectToLogin($message = 'Vui lòng đăng nhập để tiếp tục.')
    {
        $_SESSION['flash']['error'] = $message;
        header('Location: ' . BASE_URL . '/admin/login');
        exit;
    }

    /**
     * Generate password reset token
     */
    public function generatePasswordResetToken($email)
    {
        $db = Database::getInstance();

        // Check if user exists
        $user = $db->fetchOne(
            "SELECT id FROM users WHERE email = :email AND status = 'active'",
            ['email' => $email]
        );

        if (!$user) {
            return false;
        }

        // Generate token
        $token = bin2hex(random_bytes(32));
        $hashedToken = hash('sha256', $token);
        $expiry = date('Y-m-d H:i:s', time() + 3600); // 1 hour

        // Save token
        $db->execute(
            "UPDATE users SET reset_token = :token, reset_token_expiry = :expiry WHERE id = :id",
            [
                'token' => $hashedToken,
                'expiry' => $expiry,
                'id' => $user['id']
            ]
        );

        return $token;
    }

    /**
     * Reset password with token
     */
    public function resetPassword($token, $newPassword)
    {
        $hashedToken = hash('sha256', $token);

        $db = Database::getInstance();
        $user = $db->fetchOne(
            "SELECT id FROM users WHERE reset_token = :token AND reset_token_expiry > NOW() AND status = 'active'",
            ['token' => $hashedToken]
        );

        if (!$user) {
            return false;
        }

        // Update password and clear reset token
        $hashedPassword = $this->hashPassword($newPassword);
        $db->execute(
            "UPDATE users SET password_hash = :password, reset_token = NULL, reset_token_expiry = NULL WHERE id = :id",
            [
                'password' => $hashedPassword,
                'id' => $user['id']
            ]
        );

        return true;
    }
}
