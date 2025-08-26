<?php

/**
 * Account Controller (Client)
 * 5S Fashion E-commerce Platform
 */

class AccountController extends BaseController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = $this->model('User');

        // Check if user is logged in
        if (!isLoggedIn()) {
            redirect('login');
        }
    }

    /**
     * Account dashboard
     */
    public function index()
    {
        $user = getUser();

        // Get user's recent orders (if Order model exists)
        $recentOrders = [];
        // $recentOrders = $this->model('Order')->getUserOrders($user['id'], 5);

        $data = [
            'title' => 'Tài Khoản - 5S Fashion',
            'user' => $user,
            'recent_orders' => $recentOrders
        ];

        $this->render('client/account/index', $data,'client/layouts/app');
    }

    /**
     * Show profile form
     */
    public function profile()
    {
        $user = getUser();

        $data = [
            'title' => 'Thông Tin Cá Nhân - 5S Fashion',
            'user' => $user
        ];

        $this->render('client/account/profile', $data,'client/layouts/app');
    }

    /**
     * Update profile
     */
    public function updateProfile()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('account/profile');
        }

        $user = getUser();
        $full_name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $birthday = $_POST['birthday'] ?? null;

        // Validation
        if (empty($full_name) || empty($email)) {
            setFlash('error', 'Vui lòng nhập đầy đủ họ tên và email');
            redirect('account/profile');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            setFlash('error', 'Email không hợp lệ');
            redirect('account/profile');
        }

        // Check if email exists for other users
        $existingUser = $this->userModel->findByEmail($email);
        if ($existingUser && $existingUser['id'] != $user['id']) {
            setFlash('error', 'Email này đã được sử dụng bởi tài khoản khác');
            redirect('account/profile');
        }

        // Update user data
        $updateData = [
            'full_name' => $full_name,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'birthday' => $birthday,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $success = $this->userModel->update($user['id'], $updateData);

        if ($success) {
            // Update session with new data
            $updatedUser = $this->userModel->find($user['id']);
            $_SESSION['user'] = $updatedUser;
            print_r($updatedUser); // Debugging line to check updated user data
            error_log("DEBUG AccountController - Updated User: " . json_encode($updatedUser));


            setFlash('success', 'Cập nhật thông tin thành công!');
        } else {
            setFlash('error', 'Có lỗi xảy ra khi cập nhật thông tin');
        }

        redirect('account/profile');
    }

    /**
     * Show password change form
     */
    public function passwordForm()
    {
        $data = [
            'title' => 'Đổi Mật Khẩu - 5S Fashion'
        ];

        $this->render('client/account/password', $data,'client/layouts/app');
    }

    /**
     * Update password
     */
    public function updatePassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('account/password');
        }

        $user = getUser();
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validation
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            setFlash('error', 'Vui lòng nhập đầy đủ thông tin');
            redirect('account/password');
        }

        // Verify current password
        if (!password_verify($currentPassword, $user['password_hash'])) {
            setFlash('error', 'Mật khẩu hiện tại không chính xác');
            redirect('account/password');
        }

        if (strlen($newPassword) < 6) {
            setFlash('error', 'Mật khẩu mới phải có ít nhất 6 ký tự');
            redirect('account/password');
        }

        if ($newPassword !== $confirmPassword) {
            setFlash('error', 'Mật khẩu xác nhận không khớp');
            redirect('account/password');
        }

        // Update password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $success = $this->userModel->update($user['id'], [
            'password_hash' => $hashedPassword,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        if ($success) {
            setFlash('success', 'Đổi mật khẩu thành công!');
        } else {
            setFlash('error', 'Có lỗi xảy ra khi đổi mật khẩu');
        }

        redirect('account/password');
    }

    /**
     * Show user orders
     */
    public function orders()
    {
        $user = getUser();

        // Get user orders with items
        require_once dirname(__DIR__) . '/models/Order.php';
        $orderModel = new Order();
        $orders = $orderModel->getByUserWithItems($user['id']);

        $data = [
            'title' => 'Đơn Hàng - 5S Fashion',
            'orders' => $orders
        ];

        $this->render('client/account/orders', $data,'client/layouts/app');
    }

    /**
     * Show order detail
     */
    public function orderDetail($id)
    {
        $user = getUser();

        // Get order detail
        require_once dirname(__DIR__) . '/models/Order.php';
        $orderModel = new Order();
        $order = $orderModel->getFullDetails($id);

        // Verify order belongs to current user
        if (!$order || $order['user_id'] != $user['id']) {
            setFlash('error', 'Đơn hàng không tồn tại');
            redirect('orders');
        }

        $data = [
            'title' => 'Chi Tiết Đơn Hàng #' . $order['order_code'] . ' - 5S Fashion',
            'order' => $order
        ];

        $this->render('client/account/order-detail', $data, 'client/layouts/app');
    }

    /**
     * Show user addresses
     */
    public function addresses()
    {
        $user = getUser();

        // Get user addresses (Customer model)
        $addresses = $this->model('Customer')->getCustomerAddresses($user['id']);

        $data = [
            'title' => 'Địa Chỉ - 5S Fashion',
            'addresses' => $addresses
        ];

        $this->render('client/account/addresses', $data, 'client/layouts/app');
    }

    /**
     * Add new address
     */
    public function addAddress()
    {
        // error_log('addAddress() called - Method: ' . $_SERVER['REQUEST_METHOD']);
        // error_log('POST data: ' . print_r($_POST, true));
        // error_log('AJAX check: ' . (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) ? $_SERVER['HTTP_X_REQUESTED_WITH'] : 'NO'));

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Sai phương thức']);
                exit;
            }
            redirect('addresses');
        }

        $user = getUser();
        $customerModel = $this->model('Customer');
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $note = trim($_POST['note'] ?? '');
        $is_default = isset($_POST['is_default']) ? 1 : 0;

        // Validate required fields
        if (empty($name) || empty($address) || empty($phone)) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Vui lòng nhập đầy đủ họ tên, số điện thoại và địa chỉ']);
                exit;
            }
            setFlash('error', 'Vui lòng nhập đầy đủ họ tên, số điện thoại và địa chỉ');
            redirect('addresses');
        }

        $addressData = [
            'user_id' => $user['id'],
            'name' => $name,
            'phone' => $phone,
            'address' => $address,
            'note' => $note,
            'is_default' => $is_default
        ];

        // Debug để xem dữ liệu truyền vào
        error_log('AddAddress Data: ' . print_r($addressData, true));

        $result = $customerModel->addCustomerAddress($addressData);

        // Debug để xem kết quả
        error_log('AddAddress Result: ' . ($result ? 'TRUE' : 'FALSE'));

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Thêm địa chỉ thành công!']);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi thêm địa chỉ',
                    'debug' => [
                        'addressData' => $addressData,
                        'result' => $result
                    ]
                ]);
            }
            exit;
        }

        if ($result) {
            setFlash('success', 'Thêm địa chỉ thành công!');
        } else {
            setFlash('error', 'Có lỗi xảy ra khi thêm địa chỉ');
        }
        redirect('addresses');
    }

    /**
     * Update address
     */
    public function updateAddress($id)
    {
        $method = $_SERVER['REQUEST_METHOD'];
        // Chấp nhận PUT hoặc POST (không cần _method)
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        if ($method !== 'POST') {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Sai phương thức']);
                exit;
            }
            redirect('addresses');
        }

        $user = getUser();
        $customerModel = $this->model('Customer');
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $note = trim($_POST['note'] ?? '');
        $is_default = isset($_POST['is_default']) ? 1 : 0;
        // Validate required fields
        if (empty($name) || empty($address) || empty($phone)) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Vui lòng nhập đầy đủ họ tên, số điện thoại và địa chỉ']);
                exit;
            }
            setFlash('error', 'Vui lòng nhập đầy đủ họ tên, số điện thoại và địa chỉ');
            redirect('addresses');
        }

        $addressData = [
            'user_id' => $user['id'],
            'name' => $name,
            'phone' => $phone,
            'address' => $address,
            'note' => $note,
            'is_default' => $is_default
        ];

        $result = $customerModel->updateCustomerAddress($id, $user['id'], $addressData);

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Cập nhật địa chỉ thành công!']);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi cập nhật địa chỉ',
                    'debug' => [
                        'addressData' => $addressData,
                        'result' => $result
                    ]
                ]);
            }
            exit;
        }

        if ($result) {
            setFlash('success', 'Cập nhật địa chỉ thành công!');
        } else {
            setFlash('error', 'Có lỗi xảy ra khi cập nhật địa chỉ');
        }
        redirect('addresses');
    }

    /**
     * Delete address
     */
    public function deleteAddress($id)
    {
        // Chấp nhận cả DELETE và POST (nếu gọi bằng AJAX POST)
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method !== 'DELETE' && $method !== 'POST') {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Sai phương thức']);
                exit;
            }
            redirect('addresses');
        }

        $user = getUser();
        $customerModel = $this->model('Customer');
        $result = $customerModel->deleteCustomerAddress($id, $user['id']);

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Đã xóa địa chỉ thành công!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể xóa địa chỉ!']);
            }
            exit;
        }

        if ($result) {
            setFlash('success', 'Đã xóa địa chỉ thành công!');
        } else {
            setFlash('error', 'Không thể xóa địa chỉ!');
        }
        redirect('addresses');
    }
    // Change Default Address
    public function setDefaultAddress($id)
    {
        $user = getUser();
        $customerModel = $this->model('Customer');
        $result = $customerModel->setDefaultAddress($id, $user['id']);

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Đã đặt địa chỉ mặc định!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể đặt địa chỉ mặc định!']);
            }
            exit;
        }

        if ($result) {
            setFlash('success', 'Đã đặt địa chỉ mặc định!');
        } else {
            setFlash('error', 'Không thể đặt địa chỉ mặc định!');
        }
        redirect('addresses');
    }

    /**
     * Show wishlist
     */
    public function wishlist()
    {
        $user = getUser();

        // Get real wishlist data from database
        try {
            $wishlistModel = $this->model('Wishlist');
            $wishlist = $wishlistModel->getUserWishlist($user['id']);
        } catch (Exception $e) {
            // If there's an error with the model, set empty array
            $wishlist = [];
            error_log('Wishlist error in AccountController: ' . $e->getMessage());
        }

        // If there's an error with the model, set empty array
        if ($wishlist === false || $wishlist === null) {
            $wishlist = [];
        }

        $data = [
            'title' => 'Sản Phẩm Yêu Thích - 5S Fashion',
            'user' => $user,
            'wishlist' => $wishlist
        ];

        $this->render('client/account/wishlist', $data, 'client/layouts/app');
    }
}
