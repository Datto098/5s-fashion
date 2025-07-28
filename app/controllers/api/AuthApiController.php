<?php

require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../../core/ApiController.php';
require_once __DIR__ . '/../../core/ApiResponse.php';
require_once __DIR__ . '/../../helpers/JWT.php';

class AuthApiController extends ApiController
{
    private $pdo;

    public function __construct()
    {
        parent::__construct();
        $this->pdo = Database::getInstance()->getConnection();
    }

    /**
     * User registration
     */
    public function register()
    {
        try {
            $input = $this->parseRequestBody();

            // Validate required fields
            $required = ['name', 'email', 'password'];
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

            // Validate email format
            if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
                ApiResponse::error('Invalid email format', 422);
                return;
            }

            // Validate password strength
            if (strlen($input['password']) < 6) {
                ApiResponse::error('Password must be at least 6 characters', 422);
                return;
            }

            // Check if email already exists
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->execute([':email' => $input['email']]);

            if ($stmt->fetch()) {
                ApiResponse::error('Email already exists', 422);
                return;
            }

            // Hash password
            $passwordHash = password_hash($input['password'], PASSWORD_DEFAULT);

            // Create user
            $userQuery = "
                INSERT INTO users (
                    name, email, password, phone, role, status, created_at
                ) VALUES (
                    :name, :email, :password, :phone, 'customer', 'active', NOW()
                )
            ";

            $stmt = $this->pdo->prepare($userQuery);
            $stmt->execute([
                ':name' => trim($input['name']),
                ':email' => trim($input['email']),
                ':password' => $passwordHash,
                ':phone' => $input['phone'] ?? null
            ]);

            $userId = $this->pdo->lastInsertId();

            // Get created user
            $stmt = $this->pdo->prepare("SELECT id, name, email, phone, role, status, created_at FROM users WHERE id = :id");
            $stmt->execute([':id' => $userId]);
            $user = $stmt->fetch();

            // Generate JWT token
            $tokenPayload = [
                'sub' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role']
            ];

            $token = JWT::encode($tokenPayload, 86400 * 7); // 7 days

            ApiResponse::success([
                'message' => 'User registered successfully',
                'user' => $this->formatUser($user),
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => 86400 * 7
            ]);

        } catch (Exception $e) {
            ApiResponse::error('Registration failed: ' . $e->getMessage());
        }
    }

    /**
     * User login
     */
    public function login()
    {
        try {
            $input = $this->parseRequestBody();

            // Validate required fields
            if (empty($input['email']) || empty($input['password'])) {
                ApiResponse::error('Email and password are required', 422);
                return;
            }

            // Find user by email
            $stmt = $this->pdo->prepare("
                SELECT id, name, email, password, phone, role, status, created_at
                FROM users
                WHERE email = :email
            ");
            $stmt->execute([':email' => $input['email']]);
            $user = $stmt->fetch();

            if (!$user) {
                ApiResponse::error('Invalid credentials', 401);
                return;
            }

            // Check user status
            if ($user['status'] !== 'active') {
                ApiResponse::error('Account is inactive', 403);
                return;
            }

            // Verify password
            if (!password_verify($input['password'], $user['password'])) {
                ApiResponse::error('Invalid credentials', 401);
                return;
            }

            // Update last login
            $stmt = $this->pdo->prepare("UPDATE users SET updated_at = NOW() WHERE id = :id");
            $stmt->execute([':id' => $user['id']]);

            // Generate JWT token
            $tokenPayload = [
                'sub' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role']
            ];

            $token = JWT::encode($tokenPayload, 86400 * 7); // 7 days

            // Remove password from response
            unset($user['password']);

            ApiResponse::success([
                'message' => 'Login successful',
                'user' => $this->formatUser($user),
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => 86400 * 7
            ]);

        } catch (Exception $e) {
            ApiResponse::error('Login failed: ' . $e->getMessage());
        }
    }

    /**
     * Get current user profile
     */
    public function profile()
    {
        try {
            $currentUser = JWT::getCurrentUser();

            if (!$currentUser) {
                ApiResponse::error('Unauthorized', 401);
                return;
            }

            // Get user details
            $stmt = $this->pdo->prepare("
                SELECT id, name, email, phone, role, status, created_at, updated_at
                FROM users
                WHERE id = :id AND status = 'active'
            ");
            $stmt->execute([':id' => $currentUser['sub']]);
            $user = $stmt->fetch();

            if (!$user) {
                ApiResponse::error('User not found', 404);
                return;
            }

            ApiResponse::success([
                'user' => $this->formatUser($user)
            ]);

        } catch (Exception $e) {
            ApiResponse::error('Failed to get profile: ' . $e->getMessage());
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile()
    {
        try {
            $currentUser = JWT::getCurrentUser();

            if (!$currentUser) {
                ApiResponse::error('Unauthorized', 401);
                return;
            }

            $input = $this->parseRequestBody();

            // Validate allowed fields
            $allowedFields = ['name', 'phone'];
            $updateData = [];
            $params = [':id' => $currentUser['sub']];

            foreach ($allowedFields as $field) {
                if (isset($input[$field]) && !empty(trim($input[$field]))) {
                    $updateData[] = "{$field} = :{$field}";
                    $params[":{$field}"] = trim($input[$field]);
                }
            }

            if (empty($updateData)) {
                ApiResponse::error('No valid fields to update', 422);
                return;
            }

            // Update user
            $updateQuery = "
                UPDATE users
                SET " . implode(', ', $updateData) . ", updated_at = NOW()
                WHERE id = :id AND status = 'active'
            ";

            $stmt = $this->pdo->prepare($updateQuery);
            $stmt->execute($params);

            if ($stmt->rowCount() === 0) {
                ApiResponse::error('User not found or no changes made', 404);
                return;
            }

            // Get updated user
            $stmt = $this->pdo->prepare("
                SELECT id, name, email, phone, role, status, created_at, updated_at
                FROM users
                WHERE id = :id
            ");
            $stmt->execute([':id' => $currentUser['sub']]);
            $user = $stmt->fetch();

            ApiResponse::success([
                'message' => 'Profile updated successfully',
                'user' => $this->formatUser($user)
            ]);

        } catch (Exception $e) {
            ApiResponse::error('Failed to update profile: ' . $e->getMessage());
        }
    }

    /**
     * Change password
     */
    public function changePassword()
    {
        try {
            $currentUser = JWT::getCurrentUser();

            if (!$currentUser) {
                ApiResponse::error('Unauthorized', 401);
                return;
            }

            $input = $this->parseRequestBody();

            // Validate required fields
            if (empty($input['current_password']) || empty($input['new_password'])) {
                ApiResponse::error('Current password and new password are required', 422);
                return;
            }

            // Validate new password strength
            if (strlen($input['new_password']) < 6) {
                ApiResponse::error('New password must be at least 6 characters', 422);
                return;
            }

            // Get current user with password
            $stmt = $this->pdo->prepare("SELECT password FROM users WHERE id = :id");
            $stmt->execute([':id' => $currentUser['sub']]);
            $user = $stmt->fetch();

            if (!$user) {
                ApiResponse::error('User not found', 404);
                return;
            }

            // Verify current password
            if (!password_verify($input['current_password'], $user['password'])) {
                ApiResponse::error('Current password is incorrect', 422);
                return;
            }

            // Hash new password
            $newPasswordHash = password_hash($input['new_password'], PASSWORD_DEFAULT);

            // Update password
            $stmt = $this->pdo->prepare("
                UPDATE users
                SET password = :password, updated_at = NOW()
                WHERE id = :id
            ");
            $stmt->execute([
                ':password' => $newPasswordHash,
                ':id' => $currentUser['sub']
            ]);

            ApiResponse::success([
                'message' => 'Password changed successfully'
            ]);

        } catch (Exception $e) {
            ApiResponse::error('Failed to change password: ' . $e->getMessage());
        }
    }

    /**
     * Logout (token invalidation would be handled client-side)
     */
    public function logout()
    {
        ApiResponse::success([
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Refresh token
     */
    public function refresh()
    {
        try {
            $currentUser = JWT::getCurrentUser();

            if (!$currentUser) {
                ApiResponse::error('Unauthorized', 401);
                return;
            }

            // Generate new token
            $tokenPayload = [
                'sub' => $currentUser['sub'],
                'name' => $currentUser['name'],
                'email' => $currentUser['email'],
                'role' => $currentUser['role']
            ];

            $token = JWT::encode($tokenPayload, 86400 * 7); // 7 days

            ApiResponse::success([
                'message' => 'Token refreshed successfully',
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => 86400 * 7
            ]);

        } catch (Exception $e) {
            ApiResponse::error('Failed to refresh token: ' . $e->getMessage());
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
     * Check authentication status
     */
    public function check()
    {
        try {
            if (isLoggedIn()) {
                ApiResponse::success([
                    'authenticated' => true,
                    'user' => $this->formatUser($_SESSION['user'])
                ], 'User is authenticated');
            } else {
                ApiResponse::success([
                    'authenticated' => false
                ], 'User is not authenticated');
            }
        } catch (Exception $e) {
            ApiResponse::error('Failed to check authentication: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Format user data for response
     */
    private function formatUser($user)
    {
        return [
            'id' => (int)$user['id'],
            'name' => $user['full_name'] ?? $user['name'] ?? null,
            'email' => $user['email'] ?? null,
            'phone' => $user['phone'] ?? null,
            'role' => $user['role'] ?? null,
            'status' => $user['status'] ?? null,
            'created_at' => $user['created_at'] ?? null,
            'updated_at' => $user['updated_at'] ?? null
        ];
    }
}

?>
