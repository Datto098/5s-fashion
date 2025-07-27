<?php
/**
 * Account Controller (Client)
 * 5S Fashion E-commerce Platform
 */

class AccountController extends Controller
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

        $this->view('client/account/index', $data);
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

        $this->view('client/account/profile', $data);
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
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $birthday = $_POST['birthday'] ?? null;
        $gender = $_POST['gender'] ?? null;

        // Validation
        if (empty($name) || empty($email)) {
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
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'birthday' => $birthday,
            'gender' => $gender,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $success = $this->userModel->update($user['id'], $updateData);

        if ($success) {
            // Update session with new data
            $updatedUser = $this->userModel->find($user['id']);
            $_SESSION['user'] = $updatedUser;

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

        $this->view('client/account/password', $data);
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

        // Get user orders (if Order model exists)
        $orders = [];
        // $orders = $this->model('Order')->getUserOrders($user['id']);

        $data = [
            'title' => 'Đơn Hàng - 5S Fashion',
            'orders' => $orders
        ];

        $this->view('client/account/orders', $data);
    }

    /**
     * Show order detail
     */
    public function orderDetail($id)
    {
        $user = getUser();

        // Get order detail (if Order model exists)
        $order = null;
        // $order = $this->model('Order')->getUserOrder($user['id'], $id);

        if (!$order) {
            setFlash('error', 'Đơn hàng không tồn tại');
            redirect('orders');
        }

        $data = [
            'title' => 'Chi Tiết Đơn Hàng #' . $id . ' - 5S Fashion',
            'order' => $order
        ];

        $this->view('client/account/order-detail', $data);
    }

    /**
     * Show user addresses
     */
    public function addresses()
    {
        $user = getUser();

        // Get user addresses (if Address model exists)
        $addresses = [];
        // $addresses = $this->model('Address')->getUserAddresses($user['id']);

        $data = [
            'title' => 'Địa Chỉ - 5S Fashion',
            'addresses' => $addresses
        ];

        $this->view('client/account/addresses', $data);
    }

    /**
     * Add new address
     */
    public function addAddress()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('addresses');
        }

        $user = getUser();

        // Add address logic here when Address model is ready
        setFlash('info', 'Chức năng thêm địa chỉ đang được phát triển');
        redirect('addresses');
    }

    /**
     * Update address
     */
    public function updateAddress($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            redirect('addresses');
        }

        $user = getUser();

        // Update address logic here when Address model is ready
        setFlash('info', 'Chức năng cập nhật địa chỉ đang được phát triển');
        redirect('addresses');
    }

    /**
     * Delete address
     */
    public function deleteAddress($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            redirect('addresses');
        }

        $user = getUser();

        // Delete address logic here when Address model is ready
        setFlash('info', 'Chức năng xóa địa chỉ đang được phát triển');
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

        $this->view('client/account/wishlist', $data);
    }
}
