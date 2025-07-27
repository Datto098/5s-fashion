<?php
/**
 * Admin Authentication Controller
 * 5S Fashion E-commerce Platform
 * Clean MVC structure - all HTML in views
 */

class AuthController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Show login form
     */
    public function login()
    {
        // Simple authentication check
        session_start();
        if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']) {
            header('Location: /5s-fashion/admin');
            exit;
        }

        $data = [
            'pageTitle' => 'Đăng nhập Admin',
            'errors' => [],
            'email' => ''
        ];

        $this->render('admin/auth/login', $data);
    }

    /**
     * Process login
     */
    public function processLogin()
    {
        session_start();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /5s-fashion/admin/login');
            exit;
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        // Simple validation
        $errors = [];
        if (empty($email)) {
            $errors['email'] = 'Email không được để trống';
        }
        if (empty($password)) {
            $errors['password'] = 'Mật khẩu không được để trống';
        }

        if (!empty($errors)) {
            $data = [
                'pageTitle' => 'Đăng nhập Admin',
                'errors' => $errors,
                'email' => $email
            ];
            $this->render('admin/auth/login', $data);
            return;
        }

        // Simple authentication (in real app, check against database)
        if ($email === 'admin@5sfashion.com' && $password === 'admin123') {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user'] = [
                'id' => 1,
                'email' => $email,
                'name' => 'Admin User',
                'role' => 'admin'
            ];

            // Also set user session for client area compatibility
            $_SESSION['user_id'] = 1;
            $_SESSION['user_role'] = 'admin';
            $_SESSION['user'] = [
                'id' => 1,
                'email' => $email,
                'full_name' => 'Admin User',
                'role' => 'admin',
                'status' => 'active'
            ];

            header('Location: /5s-fashion/admin');
            exit;
        } else {
            $data = [
                'pageTitle' => 'Đăng nhập Admin',
                'errors' => ['login' => 'Email hoặc mật khẩu không chính xác'],
                'email' => $email
            ];
            $this->render('admin/auth/login', $data);
        }
    }

    /**
     * Logout
     */
    public function logout()
    {
        session_start();
        session_destroy();
        header('Location: /5s-fashion/admin/login');
        exit;
    }
}
