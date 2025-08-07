<?php
/**
 * Auth Controller (Client)
 * 5S Fashion E-commerce Platform
 */

class AuthController extends Controller
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = $this->model('User');
    }

    /**
     * Show login form
     */
    public function loginForm()
    {
        // If this is a POST request, handle login instead
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->login();
        }

        // If already logged in, redirect
        if (isLoggedIn()) {
            $user = getUser();
            if ($user['role'] === 'admin') {
                redirect('admin/dashboard');
            } else {
                redirect('/');
            }
        }

        $data = [
            'title' => 'Đăng Nhập - 5S Fashion'
        ];

        $this->view('client/auth/login', $data);
    }

    /**
     * Process login
     */
    public function login()
    {
        error_log("AuthController: login() method called with REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);

        // Ensure this is called only for POST requests (via loginForm method)
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log("AuthController: Not POST request, redirecting to login form");
            redirect('login');
            return;
        }

        error_log("AuthController: Processing POST login request");

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        error_log("AuthController: Login attempt for email: " . $email);

        // Validation
        if (empty($email) || empty($password)) {
            setFlash('error', 'Vui lòng nhập đầy đủ thông tin');
            redirect('login');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            setFlash('error', 'Email không hợp lệ');
            redirect('login');
            return;
        }

        try {
            // Check user credentials
            $user = $this->userModel->findByEmail($email);
            error_log("AuthController: User found: " . ($user ? "Yes" : "No"));

            if (!$user) {
                setFlash('error', 'Email hoặc mật khẩu không chính xác');
                redirect('login');
                return;
            }

            // Debug password verification
            error_log("AuthController: Verifying password for user: " . $user['email']);
            error_log("AuthController: Password hash exists: " . (isset($user['password_hash']) ? "Yes" : "No"));

            if (!password_verify($password, $user['password_hash'])) {
                error_log("AuthController: Password verification failed");
                setFlash('error', 'Email hoặc mật khẩu không chính xác');
                redirect('login');
                return;
            }


            // Chặn đăng nhập nếu chưa xác thực email 
            if ($user['role'] === 'customer' && empty($user['email_verified_at'])) {
                setFlash('error', 'Bạn cần xác thực email trước khi đăng nhập. Vui lòng kiểm tra email.');
                redirect('login');
                return;
            }

            if ($user['status'] !== 'active') {
                setFlash('error', 'Tài khoản của bạn đã bị khóa');
                redirect('login');
                return;
            }

            // Login successful
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user'] = $user;

            // Debug: Log successful login
            error_log("Login successful for user: " . $user['email'] . " (Role: " . $user['role'] . ")");

            // If admin user, also set admin session keys
            if ($user['role'] === 'admin') {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_user'] = [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'name' => $user['full_name'],
                    'role' => $user['role']
                ];
            }

            // Update last login
            $this->userModel->updateLastLogin($user['id']);
            $user['last_login_at'] = date('Y-m-d H:i:s'); 
            $_SESSION['user'] = $user;

            // Force save session
            session_write_close();
            session_start(); // Restart to ensure session is saved

            setFlash('success', 'Đăng nhập thành công!');

            // Debug: Check session after setting
            error_log("Session after login - admin_logged_in: " . (isset($_SESSION['admin_logged_in']) ? 'true' : 'false'));
            error_log("Session after login - user_role: " . ($_SESSION['user_role'] ?? 'not set'));

            // Debug: Check what URL we're redirecting to
            $redirectUrl = ($user['role'] === 'admin') ? 'admin' : '/';
            error_log("Redirecting to: " . $redirectUrl);
            error_log("BASE_URL: " . BASE_URL);

            // Redirect based on role with proper URL construction
            if ($user['role'] === 'admin') {
                header('Location: ' . BASE_URL . '/admin');
                exit();
            } else {
                header('Location: ' . BASE_URL . '/');
                exit();
            }

        } catch (Exception $e) {
            error_log("AuthController: Login error: " . $e->getMessage());
            setFlash('error', 'Có lỗi xảy ra trong quá trình đăng nhập');
            redirect('login');
        }
    }

    /**
     * Show register form
     */
    public function registerForm()
    {
        // If already logged in, redirect
        if (isLoggedIn()) {
            redirect('/');
        }

        $data = [
            'title' => 'Đăng Ký - 5S Fashion'
        ];

        $this->view('client/auth/register', $data);
    }

    /**
     * Process registration
     */
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('register');
        }

        // Handle both 'name' and 'first_name'/'last_name' formats
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $name = trim($firstName . ' ' . $lastName);
        $username = $lastName; // Lấy tên đăng nhập từ trường "Tên"

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? $_POST['password_confirmation'] ?? '';
        $phone = trim($_POST['phone'] ?? '');

        // Validation
        if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
            setFlash('error', 'Vui lòng nhập đầy đủ thông tin');
            redirect('register');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            setFlash('error', 'Email không hợp lệ');
            redirect('register');
        }

        if (strlen($password) < 6) {
            setFlash('error', 'Mật khẩu phải có ít nhất 6 ký tự');
            redirect('register');
        }

        if ($password !== $confirmPassword) {
            setFlash('error', 'Mật khẩu xác nhận không khớp');
            redirect('register');
        }

        // Check terms agreement
        if (!isset($_POST['terms']) || $_POST['terms'] !== 'on') {
            setFlash('error', 'Vui lòng đồng ý với điều khoản sử dụng');
            redirect('register');
        }

        // Check if email exists
        if ($this->userModel->findByEmail($email)) {
            setFlash('error', 'Email này đã được sử dụng');
            redirect('register');
        }

        // Check if username exists
        if ($this->userModel->findByUsername($username)) {
            setFlash('error', 'Tên đăng nhập đã tồn tại, vui lòng chọn tên khác');
            redirect('register');
        }

        // Tạo token xác thực email
        $verifyToken = bin2hex(random_bytes(32));
        $userData = [
            'username' => $username,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'full_name' => $name,
            'phone' => $phone,
            'role' => 'customer',
            'status' => 'active',
            'email_verify_token' => $verifyToken
        ];
        $userId = $this->userModel->createUser($userData);

        if ($userId) {
            // Gửi email xác thực
            require_once APP_PATH . '/helpers/PHPMailerHelper.php';
            require_once APP_PATH . '/helpers/functions.php';
            $verifyUrl = url('public/verify-email/' . $verifyToken);
            $sendResult = PHPMailerHelper::sendVerificationEmail($email, $name, $verifyUrl);
            if ($sendResult) {
                setFlash('success', 'Đăng ký thành công! Vui lòng kiểm tra email để xác thực tài khoản.');
            } else {
                setFlash('error', 'Đăng ký thành công nhưng gửi email xác thực thất bại.');
            }
            redirect('login');
        } else {
            setFlash('error', 'Có lỗi xảy ra khi đăng ký');
            redirect('register');
        }
    }

    /**
     * Logout user
     */
    public function logout()
    {
        // Destroy session
        session_destroy();

        // Clear session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        // Start new session for flash message
        session_start();
        setFlash('success', 'Đã đăng xuất thành công');

        redirect('/');
    }

    /**
     * Show forgot password form
     */
    public function forgotPasswordForm()
    {
        $data = [
            'title' => 'Quên Mật Khẩu - 5S Fashion'
        ];

        $this->view('client/auth/forgot-password', $data);
    }

    /**
     * Alias for forgot password form (URL compatibility)
     */
    public function forgot_password()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return $this->forgotPasswordForm();
        } else {
            return $this->forgotPassword();
        }
    }

    /**
     * Process forgot password
     */
    public function forgotPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('forgot-password');
        }

        $email = trim($_POST['email'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            setFlash('error', 'Vui lòng nhập email hợp lệ');
            redirect('forgot-password');
        }

        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            setFlash('error', 'Email không tồn tại trong hệ thống');
            redirect('forgot-password');
        }

        // Generate reset token
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Save reset token
        $this->userModel->saveResetToken($user['id'], $token, $expiry);

        // In a real application, send email here
        // For demo, just show success message
        setFlash('success', 'Link đặt lại mật khẩu đã được gửi đến email của bạn');
        redirect('login');
    }

    /**
     * Show reset password form
     */
    public function resetPasswordForm($token)
    {
        // Verify token
        $user = $this->userModel->findByResetToken($token);
        if (!$user) {
            setFlash('error', 'Link đặt lại mật khẩu không hợp lệ hoặc đã hết hạn');
            redirect('login');
        }

        $data = [
            'title' => 'Đặt Lại Mật Khẩu - 5S Fashion',
            'token' => $token
        ];

        $this->view('client/auth/reset-password', $data);
    }

    /**
     * Alias for reset password (URL compatibility)
     */
    public function reset_password()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $token = $_GET['token'] ?? '';
            return $this->resetPasswordForm($token);
        } else {
            return $this->resetPassword();
        }
    }

    /**
     * Process reset password
     */
    public function resetPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('login');
        }

        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($password) || empty($confirmPassword)) {
            setFlash('error', 'Vui lòng nhập đầy đủ thông tin');
            redirect("reset-password/{$token}");
        }

        if (strlen($password) < 6) {
            setFlash('error', 'Mật khẩu phải có ít nhất 6 ký tự');
            redirect("reset-password/{$token}");
        }

        if ($password !== $confirmPassword) {
            setFlash('error', 'Mật khẩu xác nhận không khớp');
            redirect("reset-password/{$token}");
        }

        // Verify token and update password
        $user = $this->userModel->findByResetToken($token);
        if (!$user) {
            setFlash('error', 'Link đặt lại mật khẩu không hợp lệ hoặc đã hết hạn');
            redirect('login');
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $this->userModel->resetUserPassword($user['id'], $hashedPassword);
        $this->userModel->clearResetToken($user['id']);

        setFlash('success', 'Mật khẩu đã được đặt lại thành công');
        redirect('login');
    }
}
