<?php

require_once dirname(__DIR__) . '/BaseController.php';
require_once __DIR__ . '/../../models/User.php';

class UsersController extends BaseController
{
    private $userModel;

    public function __construct()
    {
        // Start session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check admin authentication
        if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
            header('Location: /zone-fashion/admin/login');
            exit;
        }

        // Ensure admin_id is set for compatibility with views
        if (!isset($_SESSION['admin_id']) && isset($_SESSION['admin_user']['id'])) {
            $_SESSION['admin_id'] = $_SESSION['admin_user']['id'];
        }

        $this->userModel = new User();
    }

    public function index()
    {
        try {
            // Get search and filter parameters
            $search = $_GET['search'] ?? '';
            $filters = [
                'role' => $_GET['role'] ?? 'admin', // Default to admin users only
                'status' => $_GET['status'] ?? '',
                'date_from' => $_GET['date_from'] ?? '',
                'date_to' => $_GET['date_to'] ?? '',
                'sort' => $_GET['sort'] ?? 'created_at',
                'order' => $_GET['order'] ?? 'DESC',
                'limit' => $_GET['limit'] ?? 50
            ];

            // Get admin users with search and filters
            $users = $this->userModel->searchAdmins($search, $filters);

            // Get user statistics
            $stats = $this->userModel->getAdminStatistics();

            $data = [
                'title' => 'Quản lý Admin - zone Fashion Admin',
                'users' => $users,
                'stats' => $stats,
                'search' => $search,
                'filters' => $filters,
                'breadcrumbs' => [
                    ['name' => 'Dashboard', 'url' => '/zone-fashion/admin'],
                    ['name' => 'Quản lý admin', 'url' => '']
                ]
            ];

            $this->render('admin/users/index', $data, 'admin/layouts/main-inline');
        } catch (Exception $e) {
            error_log('Error in UsersController::index: ' . $e->getMessage());
            $this->render('admin/users/index', [
                'title' => 'Quản lý Admin - zone Fashion Admin',
                'users' => [],
                'stats' => [],
                'error' => 'Có lỗi xảy ra khi tải dữ liệu'
            ], 'admin/layouts/main-inline');
        }
    }

    public function create()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Validate required fields
                $requiredFields = ['username', 'email', 'password', 'full_name'];
                foreach ($requiredFields as $field) {
                    if (empty($_POST[$field])) {
                        throw new Exception('Vui lòng điền đầy đủ thông tin');
                    }
                }

                // Check if username or email already exists
                $existingUser = $this->userModel->findBy('username', $_POST['username']);
                if ($existingUser) {
                    throw new Exception('Tên đăng nhập đã được sử dụng');
                }

                $existingEmail = $this->userModel->findBy('email', $_POST['email']);
                if ($existingEmail) {
                    throw new Exception('Email đã được sử dụng');
                }

                // Handle avatar upload
                $avatarPath = null;
                if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
                    $avatarPath = $this->handleAvatarUpload($_FILES['avatar']);
                }

                // Create new admin user
                $userData = [
                    'username' => $_POST['username'],
                    'email' => $_POST['email'],
                    'password_hash' => password_hash($_POST['password'], PASSWORD_DEFAULT),
                    'full_name' => $_POST['full_name'],
                    'phone' => $_POST['phone'] ?? null,
                    'avatar' => $avatarPath,
                    'role' => 'admin',
                    'status' => $_POST['status'] ?? 'active'
                ];

                $result = $this->userModel->create($userData);

                if ($result) {
                    header('Location: /zone-fashion/admin/users?success=' . urlencode('Tạo tài khoản admin thành công'));
                } else {
                    throw new Exception('Không thể tạo tài khoản admin');
                }
                exit;
            }

            // Show create form
            $data = [
                'title' => 'Tạo tài khoản Admin - zone Fashion Admin',
                'breadcrumbs' => [
                    ['name' => 'Dashboard', 'url' => '/zone-fashion/admin'],
                    ['name' => 'Quản lý admin', 'url' => '/zone-fashion/admin/users'],
                    ['name' => 'Tạo mới', 'url' => '']
                ]
            ];

            $this->render('admin/users/create', $data, 'admin/layouts/main-inline');

        } catch (Exception $e) {
            error_log('Error in UsersController::create: ' . $e->getMessage());
            header('Location: /zone-fashion/admin/users?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function show($id)
    {
        try {
            $user = $this->userModel->getAdminWithStats($id);
            if (!$user || $user['role'] !== 'admin') {
                header('Location: /zone-fashion/admin/users?error=' . urlencode('Không tìm thấy tài khoản admin'));
                exit;
            }

            $data = [
                'title' => 'Chi tiết Admin: ' . $user['full_name'] . ' - zone Fashion Admin',
                'user' => $user,
                'breadcrumbs' => [
                    ['name' => 'Dashboard', 'url' => '/zone-fashion/admin'],
                    ['name' => 'Quản lý admin', 'url' => '/zone-fashion/admin/users'],
                    ['name' => 'Chi tiết', 'url' => '']
                ]
            ];

            $this->render('admin/users/show', $data, 'admin/layouts/main-inline');
        } catch (Exception $e) {
            error_log('Error in UsersController::show: ' . $e->getMessage());
            header('Location: /zone-fashion/admin/users?error=' . urlencode('Có lỗi xảy ra'));
            exit;
        }
    }

    public function edit($id)
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Check if this is an AJAX request
                $isAjax = !empty($_SERVER['HTTP_CONTENT_TYPE']) && $_SERVER['HTTP_CONTENT_TYPE'] === 'application/json'
                    || !empty($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] === 'application/json';

                if ($isAjax) {
                    // Handle AJAX request
                    $input = json_decode(file_get_contents('php://input'), true);

                    // Get existing user
                    $existingUser = $this->userModel->getAdminWithStats($id);
                    if (!$existingUser || $existingUser['role'] !== 'admin') {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'message' => 'Không tìm thấy tài khoản admin']);
                        exit;
                    }

                    // Validate required fields
                    $requiredFields = ['username', 'email', 'full_name'];
                    foreach ($requiredFields as $field) {
                        if (empty($input[$field])) {
                            header('Content-Type: application/json');
                            echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin']);
                            exit;
                        }
                    }

                    // Check username uniqueness if changed
                    if ($input['username'] !== $existingUser['username']) {
                        $existingUsername = $this->userModel->findBy('username', $input['username']);
                        if ($existingUsername) {
                            header('Content-Type: application/json');
                            echo json_encode(['success' => false, 'message' => 'Tên đăng nhập đã được sử dụng']);
                            exit;
                        }
                    }

                    // Check email uniqueness if changed
                    if ($input['email'] !== $existingUser['email']) {
                        $existingEmail = $this->userModel->findBy('email', $input['email']);
                        if ($existingEmail) {
                            header('Content-Type: application/json');
                            echo json_encode(['success' => false, 'message' => 'Email đã được sử dụng']);
                            exit;
                        }
                    }

                    // Prepare update data
                    $updateData = [
                        'username' => $input['username'],
                        'email' => $input['email'],
                        'full_name' => $input['full_name'],
                        'phone' => $input['phone'] ?? null,
                        'status' => $input['status'] ?? 'active'
                    ];

                    // Update password if provided
                    if (!empty($input['password'])) {
                        $updateData['password_hash'] = password_hash($input['password'], PASSWORD_DEFAULT);
                    }

                    $result = $this->userModel->update($id, $updateData);

                    header('Content-Type: application/json');
                    if ($result) {
                        echo json_encode(['success' => true, 'message' => 'Cập nhật tài khoản admin thành công']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Không thể cập nhật tài khoản admin']);
                    }
                    exit;
                }

                // Handle regular form submission
                // Get existing user
                $existingUser = $this->userModel->getAdminWithStats($id);
                if (!$existingUser || $existingUser['role'] !== 'admin') {
                    throw new Exception('Không tìm thấy tài khoản admin');
                }

                // Validate required fields
                $requiredFields = ['username', 'email', 'full_name'];
                foreach ($requiredFields as $field) {
                    if (empty($_POST[$field])) {
                        throw new Exception('Vui lòng điền đầy đủ thông tin');
                    }
                }

                // Check username uniqueness if changed
                if ($_POST['username'] !== $existingUser['username']) {
                    $existingUsername = $this->userModel->findBy('username', $_POST['username']);
                    if ($existingUsername) {
                        throw new Exception('Tên đăng nhập đã được sử dụng');
                    }
                }

                // Check email uniqueness if changed
                if ($_POST['email'] !== $existingUser['email']) {
                    $existingEmail = $this->userModel->findBy('email', $_POST['email']);
                    if ($existingEmail) {
                        throw new Exception('Email đã được sử dụng');
                    }
                }

                // Handle avatar upload
                $avatarPath = $existingUser['avatar'];
                if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
                    $avatarPath = $this->handleAvatarUpload($_FILES['avatar']);
                    // Delete old avatar if exists
                    if ($existingUser['avatar'] && file_exists($existingUser['avatar'])) {
                        unlink($existingUser['avatar']);
                    }
                }

                // Prepare update data
                $updateData = [
                    'username' => $_POST['username'],
                    'email' => $_POST['email'],
                    'full_name' => $_POST['full_name'],
                    'phone' => $_POST['phone'] ?? null,
                    'avatar' => $avatarPath,
                    'status' => $_POST['status'] ?? 'active'
                ];

                // Update password if provided
                if (!empty($_POST['password'])) {
                    $updateData['password_hash'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
                }

                $result = $this->userModel->update($id, $updateData);

                if ($result) {
                    header('Location: /zone-fashion/admin/users?success=' . urlencode('Cập nhật tài khoản admin thành công'));
                } else {
                    throw new Exception('Không thể cập nhật tài khoản admin');
                }
                exit;
            }

            // Show edit form
            $user = $this->userModel->getAdminWithStats($id);
            if (!$user || $user['role'] !== 'admin') {
                header('Location: /zone-fashion/admin/users?error=' . urlencode('Không tìm thấy tài khoản admin'));
                exit;
            }

            $data = [
                'title' => 'Chỉnh sửa Admin: ' . $user['full_name'] . ' - zone Fashion Admin',
                'user' => $user,
                'breadcrumbs' => [
                    ['name' => 'Dashboard', 'url' => '/zone-fashion/admin'],
                    ['name' => 'Quản lý admin', 'url' => '/zone-fashion/admin/users'],
                    ['name' => 'Chỉnh sửa', 'url' => '']
                ]
            ];

            $this->render('admin/users/edit', $data, 'admin/layouts/main-inline');

        } catch (Exception $e) {
            error_log('Error in UsersController::edit: ' . $e->getMessage());
            header('Location: /zone-fashion/admin/users?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function delete()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $userId = $input['user_id'] ?? null;

            if (!$userId) {
                throw new Exception('Thiếu ID tài khoản');
            }

            // Prevent self-deletion
            if ($userId == $_SESSION['admin_id']) {
                throw new Exception('Không thể xóa tài khoản của chính mình');
            }

            // Check if user exists and is admin
            $user = $this->userModel->getAdminWithStats($userId);
            if (!$user || $user['role'] !== 'admin') {
                throw new Exception('Không tìm thấy tài khoản admin');
            }

            $result = $this->userModel->delete($userId);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Xóa tài khoản admin thành công']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể xóa tài khoản admin']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function updateStatus()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $userId = $input['user_id'] ?? null;
            $status = $input['status'] ?? null;

            if (!$userId || !$status) {
                throw new Exception('Thiếu thông tin cần thiết');
            }

            // Prevent self-deactivation
            if ($userId == $_SESSION['admin_id'] && $status !== 'active') {
                throw new Exception('Không thể vô hiệu hóa tài khoản của chính mình');
            }

            $validStatuses = ['active', 'inactive', 'banned'];
            if (!in_array($status, $validStatuses)) {
                throw new Exception('Trạng thái không hợp lệ');
            }

            $result = $this->userModel->updateStatus($userId, $status);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Cập nhật trạng thái thành công']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể cập nhật trạng thái']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    private function handleAvatarUpload($file)
    {
        $uploadDir = 'uploads/avatars/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception('Chỉ chấp nhận file ảnh (JPG, PNG, GIF)');
        }

        if ($file['size'] > 2 * 1024 * 1024) { // 2MB
            throw new Exception('File ảnh quá lớn (tối đa 2MB)');
        }

        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = 'avatar_' . time() . '_' . rand(1000, 9999) . '.' . $fileExtension;
        $uploadPath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return $uploadPath;
        }

        throw new Exception('Không thể tải lên file ảnh');
    }
}
