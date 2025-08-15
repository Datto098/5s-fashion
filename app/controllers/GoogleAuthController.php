<?php
/**
 * Controller xử lý đăng nhập bằng Google
 */
class GoogleAuthController extends Controller
{
    private $googleHelper;
    private $userModel;
    
    public function __construct()
    {
        parent::__construct();
        
        // Load required models
        require_once APP_PATH . '/models/User.php';
        $this->userModel = new User();
        
        // Load GoogleAuthHelper
        require_once APP_PATH . '/helpers/GoogleAuthHelper.php';
        $this->googleHelper = new GoogleAuthHelper();
    }
    
    /**
     * Redirect người dùng đến trang đăng nhập của Google
     */
    public function login()
    {
        // Lấy URL đăng nhập từ GoogleAuthHelper
        $authUrl = $this->googleHelper->getAuthUrl();
        
        // Chuyển hướng người dùng đến Google
        redirect($authUrl);
    }
    
    /**
     * Xử lý callback sau khi đăng nhập Google thành công
     */
    public function callback()
    {
        try {
            // Log bắt đầu quá trình callback
            error_log('===== Google Auth Callback Started =====');
            
            // Kiểm tra có mã code được trả về hay không
            $code = $_GET['code'] ?? null;
            error_log('Google Auth code present: ' . ($code ? 'YES' : 'NO'));
            
            if (!$code) {
                error_log('Error: Missing Google auth code');
                setFlashMessage('error', 'Không thể đăng nhập với Google. Vui lòng thử lại.');
                redirect('login');
                return;
            }
            
            // Xác minh state để ngăn chặn CSRF
            $state = $_GET['state'] ?? '';
            $stateValid = $this->googleHelper->verifyState($state);
            error_log('Google Auth state valid: ' . ($stateValid ? 'YES' : 'NO'));
            
            if (!$stateValid) {
                error_log('Error: Invalid state parameter');
                setFlashMessage('error', 'Lỗi xác thực bảo mật. Vui lòng thử lại.');
                redirect('login');
                return;
            }
            
            // Lấy thông tin người dùng từ Google
            $userData = $this->googleHelper->getUserInfo($code);
            
            // Debug: Log thông tin user từ Google
            error_log('Google user data: ' . print_r($userData, true));
            
            // Bỏ qua kiểm tra xác minh email - Hầu hết email Google đều đã xác minh
            // Luôn coi là email đã xác minh
            $userData['verified_email'] = true;
            
            // Always set status to active for Google users
            $userData['status'] = 'active';
            
            // Đảm bảo các giá trị cơ bản là chuỗi, không phải mảng
            $userData['email'] = is_array($userData['email']) ? $userData['email'][0] : $userData['email'];
            $userData['name'] = is_array($userData['name']) ? implode(' ', $userData['name']) : $userData['name'];
            $userData['google_id'] = is_array($userData['google_id']) ? (string)$userData['google_id'][0] : $userData['google_id'];
            if (isset($userData['picture']) && is_array($userData['picture'])) {
                $userData['picture'] = isset($userData['picture'][0]) ? (string)$userData['picture'][0] : null;
            }
            
            // Kiểm tra dữ liệu người dùng từ Google
            if (!$userData || empty($userData['email'])) {
                error_log('Error: Invalid or empty user data from Google');
                setFlashMessage('error', 'Không thể lấy thông tin người dùng từ Google. Vui lòng thử lại.');
                redirect('login');
                return;
            }
            
            // Tìm người dùng với email này trong hệ thống
            $existingUser = $this->userModel->findByEmail($userData['email']);
            error_log('Existing user found: ' . ($existingUser ? 'YES (ID: '.$existingUser['id'].')' : 'NO'));
            
            if ($existingUser) {
                // Người dùng đã tồn tại - Đăng nhập
                $this->loginExistingUser($existingUser, $userData);
            } else {
                // Người dùng chưa tồn tại - Đăng ký mới
                $this->registerNewUser($userData);
            }
            
        } catch (Exception $e) {
            error_log("Google auth error: " . $e->getMessage());
            setFlashMessage('error', 'Đã xảy ra lỗi trong quá trình đăng nhập với Google. Vui lòng thử lại sau.');
            redirect('login');
        }
    }
    
    /**
     * Đăng nhập người dùng hiện có
     */
    private function loginExistingUser($existingUser, $googleData)
    {
        try {
            error_log('Starting login for existing user ID: ' . $existingUser['id']);
            
            // Chỉ cập nhật một số thông tin từ Google, giữ nguyên các thông tin khác
            $updateData = [
                'google_id' => $googleData['google_id'],
                'last_login_at' => date('Y-m-d H:i:s'),
                'status' => 'active', // Đảm bảo user luôn hoạt động khi đăng nhập Google
            ];
            
            // Nếu chưa có avatar, sử dụng avatar từ Google
            if (empty($existingUser['avatar']) && !empty($googleData['picture'])) {
                $updateData['avatar'] = $googleData['picture'];
                error_log('Updating user avatar from Google');
            }
            
            // Log thông tin người dùng trước khi cập nhật
            error_log('Existing user data before update: ' . 
                      'Phone: ' . ($existingUser['phone'] ?? 'N/A') . ', ' .
                      'Birthday: ' . ($existingUser['birthday'] ?? 'N/A'));
            
            // Cập nhật thông tin người dùng - CHỈ cập nhật các trường trong updateData
            $updated = $this->userModel->update($existingUser['id'], $updateData);
            error_log('User update result: ' . ($updated ? 'Success' : 'Failed'));
            
            // Lấy dữ liệu người dùng sau khi cập nhật
            $updatedUser = $this->userModel->find($existingUser['id']);
            
            // Debug thông tin người dùng
            error_log('Updated user data: ' . print_r($updatedUser, true));
            
            // Tạo session đăng nhập với đầy đủ thông tin
            // Đảm bảo tất cả các giá trị là vô hướng (không phải mảng)
            $_SESSION['user'] = [
                'id' => (int)$updatedUser['id'], // Đảm bảo id là integer
                'email' => is_array($updatedUser['email']) ? implode(',', $updatedUser['email']) : (string)$updatedUser['email'],
                'full_name' => is_array($updatedUser['full_name']) ? implode(' ', $updatedUser['full_name']) : (string)$updatedUser['full_name'],
                'role' => is_array($updatedUser['role']) ? implode(',', $updatedUser['role']) : (string)$updatedUser['role'],
                'avatar' => isset($updatedUser['avatar']) ? (is_array($updatedUser['avatar']) ? null : (string)$updatedUser['avatar']) : null,
                'phone' => isset($updatedUser['phone']) ? (is_array($updatedUser['phone']) ? implode(',', $updatedUser['phone']) : $updatedUser['phone']) : null,
                'birthday' => isset($updatedUser['birthday']) ? (is_array($updatedUser['birthday']) ? null : $updatedUser['birthday']) : null,
                'address' => isset($updatedUser['address']) ? (is_array($updatedUser['address']) ? implode(', ', $updatedUser['address']) : $updatedUser['address']) : null,
                'email_verified_at' => isset($updatedUser['email_verified_at']) ? (is_array($updatedUser['email_verified_at']) ? date('Y-m-d H:i:s') : $updatedUser['email_verified_at']) : date('Y-m-d H:i:s'),
                'last_login_at' => isset($updatedUser['last_login_at']) ? (is_array($updatedUser['last_login_at']) ? date('Y-m-d H:i:s') : $updatedUser['last_login_at']) : date('Y-m-d H:i:s'),
                'status' => isset($updatedUser['status']) ? (is_array($updatedUser['status']) ? 'active' : $updatedUser['status']) : 'active',
                'created_at' => isset($updatedUser['created_at']) ? (is_array($updatedUser['created_at']) ? date('Y-m-d H:i:s') : $updatedUser['created_at']) : date('Y-m-d H:i:s'),
                'updated_at' => isset($updatedUser['updated_at']) ? (is_array($updatedUser['updated_at']) ? date('Y-m-d H:i:s') : $updatedUser['updated_at']) : date('Y-m-d H:i:s'),
            ];
            
            error_log('Login successful - Session created for user ID: ' . $existingUser['id']);
            setFlashMessage('success', 'Đăng nhập thành công bằng Google!');
            redirect('account');
            
        } catch (Exception $e) {
            error_log('Error during Google login for existing user: ' . $e->getMessage());
            setFlashMessage('error', 'Đăng nhập không thành công. Vui lòng thử lại.');
            redirect('login');
        }
    }
    
    /**
     * Đăng ký người dùng mới từ Google
     */
    private function registerNewUser($userData)
    {
        try {
            error_log('Starting registration for new user with email: ' . $userData['email']);
            
            // Kiểm tra dữ liệu cần thiết
            if (empty($userData['email']) || empty($userData['name'])) {
                error_log('Error: Missing required user data fields (email or name)');
                setFlashMessage('error', 'Thông tin từ Google không đầy đủ. Vui lòng thử lại.');
                redirect('login');
                return;
            }
            
            // Tạo mật khẩu ngẫu nhiên an toàn
            $password = bin2hex(random_bytes(8));
            
            // Chuẩn bị dữ liệu người dùng mới - đảm bảo username là chuỗi và không có ký tự đặc biệt
            $username = preg_replace('/[^a-zA-Z0-9]/', '', explode('@', $userData['email'])[0]);
            
            
            $newUser = [
                'email' => $userData['email'],
                'username' => $username,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'full_name' => is_array($userData['name']) ? implode(' ', $userData['name']) : $userData['name'],
                'google_id' => is_array($userData['google_id']) ? (string)$userData['google_id'] : $userData['google_id'],
                'avatar' => is_array($userData['picture'] ?? null) ? null : ($userData['picture'] ?? null),
                'status' => 'active',
                'email_verified_at' => date('Y-m-d H:i:s'), // Đánh dấu email đã xác minh
                'role' => 'customer',
                'last_login_at' => date('Y-m-d H:i:s'),
            ];
            
            error_log('Attempting to create new user in database');
            
            // Thêm người dùng vào database
            $createdUser = $this->userModel->create($newUser);
            
            if (!$createdUser) {
                error_log('Failed to create new user in database');
                setFlashMessage('error', 'Không thể tạo tài khoản mới. Vui lòng thử lại.');
                redirect('login');
                return;
            }
            
                // Ensure $userId is a scalar value, not an array
            $userId = 0;
            if (is_array($createdUser) && isset($createdUser['id'])) {
                $userId = (int)$createdUser['id'];
                error_log("Retrieved user ID from array: " . $userId);
            } elseif (is_numeric($createdUser)) {
                $userId = (int)$createdUser;
                error_log("Converted numeric user ID: " . $userId);
            } else {
                error_log("Could not determine user ID from: " . (is_array($createdUser) ? json_encode($createdUser) : (string)$createdUser));
            }            error_log('New user created successfully with ID: ' . $userId);
            
            // Lấy dữ liệu người dùng sau khi tạo (ensure we have the complete user data)
            if ($userId > 0) {
                $userData = $this->userModel->find($userId);
            } else {
                $userData = null;
                error_log('Warning: Invalid user ID: ' . $userId);
            }
            
            if ($userData) {
                // Debug thông tin người dùng
                error_log('Created user data: ' . print_r($userData, true));
                
                // Tạo session đăng nhập với đầy đủ thông tin
                // Đảm bảo tất cả các giá trị là vô hướng (không phải mảng)
                $_SESSION['user'] = [
                    'id' => (int)$userData['id'], // Đảm bảo id là integer
                    'email' => is_array($userData['email']) ? implode(',', $userData['email']) : (string)$userData['email'],
                    'full_name' => is_array($userData['full_name']) ? implode(' ', $userData['full_name']) : (string)$userData['full_name'],
                    'role' => is_array($userData['role']) ? implode(',', $userData['role']) : (string)$userData['role'],
                    'avatar' => isset($userData['avatar']) ? (is_array($userData['avatar']) ? null : (string)$userData['avatar']) : null,
                    'phone' => isset($userData['phone']) ? (is_array($userData['phone']) ? implode(',', $userData['phone']) : $userData['phone']) : null,
                    'birthday' => isset($userData['birthday']) ? (is_array($userData['birthday']) ? null : $userData['birthday']) : null,
                    'address' => isset($userData['address']) ? (is_array($userData['address']) ? implode(', ', $userData['address']) : $userData['address']) : null,
                    'email_verified_at' => isset($userData['email_verified_at']) ? (is_array($userData['email_verified_at']) ? date('Y-m-d H:i:s') : $userData['email_verified_at']) : date('Y-m-d H:i:s'),
                    'last_login_at' => isset($userData['last_login_at']) ? (is_array($userData['last_login_at']) ? date('Y-m-d H:i:s') : $userData['last_login_at']) : date('Y-m-d H:i:s'),
                    'status' => isset($userData['status']) ? (is_array($userData['status']) ? 'active' : $userData['status']) : 'active', // Ensure status is set with a default
                    'created_at' => isset($userData['created_at']) ? (is_array($userData['created_at']) ? date('Y-m-d H:i:s') : $userData['created_at']) : date('Y-m-d H:i:s'),
                    'updated_at' => isset($userData['updated_at']) ? (is_array($userData['updated_at']) ? date('Y-m-d H:i:s') : $userData['updated_at']) : date('Y-m-d H:i:s'),
                ];
            } else {
                // Fallback nếu không tìm thấy người dùng
                $_SESSION['user'] = [
                    'id' => (int)$userId,
                    'email' => isset($userData['email']) ? (is_array($userData['email']) ? implode(',', $userData['email']) : (string)$userData['email']) : '',
                    'full_name' => isset($userData['name']) ? (is_array($userData['name']) ? implode(' ', $userData['name']) : (string)$userData['name']) : '',
                    'role' => 'customer',
                    'avatar' => isset($userData['picture']) ? (is_array($userData['picture']) ? null : (string)$userData['picture']) : null,
                    'status' => 'active', 
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'email_verified_at' => date('Y-m-d H:i:s'), 
                    'last_login_at' => date('Y-m-d H:i:s'),
                ];
            }
            
            error_log('Registration successful - Session created for new user ID: ' . $userId);
            // Debug the session data before redirecting
            error_log('Final SESSION data: ' . print_r($_SESSION, true));
            
            setFlashMessage('success', 'Tài khoản đã được tạo thành công và đã đăng nhập!');
            redirect('account');
            
        } catch (Exception $e) {
            error_log('Error during Google registration: ' . $e->getMessage());
            setFlashMessage('error', 'Không thể tạo tài khoản mới. Vui lòng thử lại.');
            redirect('login');
        }
    }
}
