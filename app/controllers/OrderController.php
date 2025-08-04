<?php

/**
 * Order Controller
 * Handle order and address management for checkout
 * 5S Fashion E-commerce Platform
 */

class OrderController extends Controller
{
    private $userModel;
    private $customerModel;

    public function __construct()
    {
        $this->userModel = $this->model('User');
        $this->customerModel = $this->model('Customer');

        // Check if user is logged in for address operations
        if (!isLoggedIn()) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
                exit;
            }
            redirect('login');
        }
    }

    /**
     * Get user addresses for AJAX
     */
    public function getAddresses()
    {
        header('Content-Type: application/json');
        
        try {
            $user = getUser();
            $addresses = $this->customerModel->getCustomerAddresses($user['id']);
            
            echo json_encode([
                'success' => true,
                'addresses' => $addresses ?: []
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Không thể tải địa chỉ: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Add new address
     */
    public function addAddress()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Sai phương thức']);
            exit;
        }

        try {
            // Get JSON input
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                // Fallback to POST data
                $input = $_POST;
            }

            $user = getUser();
            
            $name = trim($input['name'] ?? $input['customerName'] ?? '');
            $phone = trim($input['phone'] ?? $input['customerPhone'] ?? '');
            $address = trim($input['address'] ?? '');
            $note = trim($input['note'] ?? $input['notes'] ?? '');
            $is_default = isset($input['is_default']) || isset($input['setDefault']) ? 1 : 0;

            // Validate required fields
            if (empty($name) || empty($address) || empty($phone)) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng nhập đầy đủ họ tên, số điện thoại và địa chỉ']);
                exit;
            }

            $addressData = [
                'user_id' => $user['id'], 
                'name' => $name,
                'phone' => $phone,
                'address' => $address,
                'note' => $note,
                'is_default' => $is_default
            ];

            $result = $this->customerModel->addCustomerAddress($addressData);

            if ($result) {
                // Get updated addresses list
                $addresses = $this->customerModel->getCustomerAddresses($user['id']);
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Thêm địa chỉ thành công!',
                    'addresses' => $addresses ?: []
                ]);
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Có lỗi xảy ra khi thêm địa chỉ'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update address
     */
    public function updateAddress($id)
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Sai phương thức']);
            exit;
        }

        try {
            // Get JSON input for PUT requests
            if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
                $input = json_decode(file_get_contents('php://input'), true);
            } else {
                $input = $_POST;
            }
            
            if (!$input) {
                echo json_encode(['success' => false, 'message' => 'Không có dữ liệu']);
                exit;
            }

            $user = getUser();
            
            $name = trim($input['name'] ?? '');
            $phone = trim($input['phone'] ?? '');
            $address = trim($input['address'] ?? '');
            $note = trim($input['note'] ?? '');
            $is_default = isset($input['is_default']) ? 1 : 0;

            // Validate required fields
            if (empty($name) || empty($address) || empty($phone)) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng nhập đầy đủ họ tên, số điện thoại và địa chỉ']);
                exit;
            }

            $addressData = [
                'user_id' => $user['id'],
                'name' => $name,
                'phone' => $phone,
                'address' => $address,
                'note' => $note,
                'is_default' => $is_default
            ];

            $result = $this->customerModel->updateCustomerAddress($id, $user['id'], $addressData);

            if ($result) {
                // Get updated addresses list
                $addresses = $this->customerModel->getCustomerAddresses($user['id']);
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Cập nhật địa chỉ thành công!',
                    'addresses' => $addresses ?: []
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi cập nhật địa chỉ'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete address
     */
    public function deleteAddress($id)
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Sai phương thức']);
            exit;
        }

        try {
            $user = getUser();
            $result = $this->customerModel->deleteCustomerAddress($id, $user['id']);

            if ($result) {
                // Get updated addresses list
                $addresses = $this->customerModel->getCustomerAddresses($user['id']);
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Đã xóa địa chỉ thành công!',
                    'addresses' => $addresses ?: []
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể xóa địa chỉ!']);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Set default address
     */
    public function setDefaultAddress($id)
    {
        header('Content-Type: application/json');
        
        try {
            $user = getUser();
            $result = $this->customerModel->setDefaultAddress($id, $user['id']);

            if ($result) {
                // Get updated addresses list
                $addresses = $this->customerModel->getCustomerAddresses($user['id']);
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Đã đặt địa chỉ mặc định!',
                    'addresses' => $addresses ?: []
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể đặt địa chỉ mặc định!']);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Place order
     */
    public function placeOrder()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Sai phương thức']);
            exit;
        }

        try {
            // Get JSON input
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                echo json_encode(['success' => false, 'message' => 'Không có dữ liệu đơn hàng']);
                exit;
            }

            $user = getUser();
            
            // Validate order data
            if (empty($input['address_id']) && empty($input['guest_address'])) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng chọn địa chỉ giao hàng']);
                exit;
            }

            if (empty($input['payment_method'])) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng chọn phương thức thanh toán']);
                exit;
            }

            if (empty($input['items']) || !is_array($input['items'])) {
                echo json_encode(['success' => false, 'message' => 'Giỏ hàng trống']);
                exit;
            }

            // Here you would implement order creation logic
            // For now, just return success
            echo json_encode([
                'success' => true,
                'message' => 'Đặt hàng thành công!',
                'order_id' => rand(10000, 99999) // Temporary order ID
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi đặt hàng: ' . $e->getMessage()
            ]);
        }
    }
}
?>
