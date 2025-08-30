<?php

require_once dirname(__DIR__) . '/BaseController.php';
require_once __DIR__ . '/../../models/Customer.php';
require_once __DIR__ . '/../../models/Order.php';

class CustomersController extends BaseController
{
    private $customerModel;
    private $orderModel;

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

        $this->customerModel = new Customer();
        $this->orderModel = new Order();
    }

    public function index()
    {
        try {
            // Get search and filter parameters
            $search = $_GET['search'] ?? '';
            $filters = [
                'status' => $_GET['status'] ?? '',
                'tier' => $_GET['tier'] ?? '',
                'date_from' => $_GET['date_from'] ?? '',
                'date_to' => $_GET['date_to'] ?? '',
                'sort' => $_GET['sort'] ?? 'created_at',
                'order' => $_GET['order'] ?? 'DESC',
                'limit' => $_GET['limit'] ?? 20
            ];

            // Get customers with search and filters
            $customers = $this->customerModel->searchCustomers($search, $filters);

            // Get customer statistics
            $stats = $this->customerModel->getCustomerStatistics();

            // Get birthday customers this month
            $birthdayCustomers = $this->customerModel->getBirthdayCustomers();

            $data = [
                'title' => 'Quản lý khách hàng - zone Fashion Admin',
                'customers' => $customers,
                'stats' => $stats,
                'birthdayCustomers' => $birthdayCustomers,
                'search' => $search,
                'filters' => $filters,
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'url' => '/zone-fashion/admin'],
                    ['title' => 'Khách hàng']
                ]
            ];

            $this->render('admin/customers/index', $data, 'admin/layouts/main-inline');

        } catch (Exception $e) {
            $data = [
                'title' => 'Quản lý khách hàng - zone Fashion Admin',
                'error' => 'Lỗi khi tải danh sách khách hàng: ' . $e->getMessage(),
                'customers' => [],
                'stats' => [],
                'birthdayCustomers' => [],
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'url' => '/zone-fashion/admin'],
                    ['title' => 'Khách hàng']
                ]
            ];

            $this->render('admin/customers/index', $data, 'admin/layouts/main-inline');
        }
    }

    public function view($view, $data = [], $layout = 'client/layouts/app')
    {
        try {
            // If $view is numeric, treat as customer ID for backward compatibility
            if (is_numeric($view)) {
                $id = $view;
                // Get customer details with statistics
                $customer = $this->customerModel->getCustomerWithStats($id);

                if (!$customer) {
                    throw new Exception('Không tìm thấy khách hàng');
                }

                // Get customer addresses
                $addresses = $this->customerModel->getCustomerAddresses($id);

                // Get customer orders
                $orders = $this->customerModel->getCustomerOrders($id, 10);

                // Get customer tier
                $tier = $this->customerModel->getCustomerTier($customer['total_spent']);

                // Get loyalty points if implemented
                $loyaltyPoints = $this->customerModel->getCustomerPoints($id);

                $data = [
                    'title' => 'Chi tiết khách hàng: ' . $customer['full_name'] . ' - zone Fashion Admin',
                    'customer' => $customer,
                    'addresses' => $addresses,
                    'orders' => $orders,
                    'tier' => $tier,
                    'loyaltyPoints' => $loyaltyPoints,
                    'breadcrumbs' => [
                        ['title' => 'Dashboard', 'url' => '/zone-fashion/admin'],
                        ['title' => 'Khách hàng', 'url' => '/zone-fashion/admin/customers'],
                        ['title' => $customer['full_name']]
                    ]
                ];

                $this->render('admin/customers/view', $data, 'admin/layouts/main-inline');
            } else {
                // Default to parent behavior if not numeric
                parent::view($view, $data, $layout);
            }
        } catch (Exception $e) {
            header('Location: /zone-fashion/admin/customers?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function create()
    {
        try {
            $data = [
                'title' => 'Thêm khách hàng mới - zone Fashion Admin',
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'url' => '/zone-fashion/admin'],
                    ['title' => 'Khách hàng', 'url' => '/zone-fashion/admin/customers'],
                    ['title' => 'Thêm mới']
                ]
            ];

            $this->render('admin/customers/create', $data, 'admin/layouts/main-inline');

        } catch (Exception $e) {
            header('Location: /zone-fashion/admin/customers?error=' . urlencode('Lỗi khi tải form tạo khách hàng'));
            exit;
        }
    }

    public function store()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: /zone-fashion/admin/customers');
                exit;
            }

            // Validate required fields
            $requiredFields = ['full_name', 'email', 'password'];
            foreach ($requiredFields as $field) {
                if (empty($_POST[$field])) {
                    throw new Exception("Trường {$field} là bắt buộc");
                }
            }

            // Check if email already exists
            $existingUser = $this->customerModel->findBy('email', $_POST['email']);
            if ($existingUser) {
                throw new Exception('Email đã được sử dụng');
            }

            // Prepare customer data
            $customerData = [
                'full_name' => trim($_POST['full_name']),
                'email' => trim($_POST['email']),
                'phone' => $_POST['phone'] ?? null,
                'password' => $_POST['password'],
                'date_of_birth' => $_POST['date_of_birth'] ?? null,
                'gender' => $_POST['gender'] ?? null,
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
                'marketing_consent' => isset($_POST['marketing_consent']) ? 1 : 0,
                'preferred_language' => $_POST['preferred_language'] ?? 'vi'
            ];

            // Handle avatar upload
            if (!empty($_FILES['avatar']['name'])) {
                $uploadResult = $this->uploadAvatar($_FILES['avatar']);
                if ($uploadResult['success']) {
                    $customerData['avatar'] = $uploadResult['path'];
                } else {
                    throw new Exception($uploadResult['error']);
                }
            }

            // Create customer
            $customerId = $this->customerModel->createCustomer($customerData);

            if (!$customerId) {
                throw new Exception('Không thể tạo khách hàng');
            }

            // Send welcome email if needed
            if (isset($_POST['send_welcome_email'])) {
                $this->sendWelcomeEmail($customerData);
            }

            header('Location: /zone-fashion/admin/customers?success=' . urlencode('Tạo khách hàng thành công'));
            exit;

        } catch (Exception $e) {
            header('Location: /zone-fashion/admin/customers/create?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function show($id)
    {
        try {
            // Get customer details
            $customer = $this->customerModel->getCustomerWithStats($id);
            if (!$customer) {
                header('Location: /zone-fashion/admin/customers?error=' . urlencode('Không tìm thấy khách hàng'));
                exit;
            }

            // Get customer orders
            $db = Database::getInstance();
            $orders = $db->fetchAll("
                SELECT id, total_amount, status, payment_status, created_at
                FROM orders
                WHERE user_id = ?
                ORDER BY created_at DESC
                LIMIT 10
            ", [$id]);

            // Get customer statistics
            $stats = $db->fetchOne("
                SELECT
                    COUNT(*) as total_orders,
                    COALESCE(SUM(total_amount), 0) as total_spent,
                    COALESCE(AVG(total_amount), 0) as avg_order_value,
                    MAX(created_at) as last_order_date
                FROM orders
                WHERE user_id = ?
            ", [$id]);

            $this->render('admin/customers/show', [
                'customer' => $customer,
                'orders' => $orders,
                'stats' => $stats
            ]);
        } catch (Exception $e) {
            error_log('Error in CustomersController::show: ' . $e->getMessage());
            header('Location: /zone-fashion/admin/customers?error=' . urlencode('Có lỗi xảy ra khi tải chi tiết khách hàng'));
            exit;
        }
    }

    public function edit($id)
    {
        try {
            // Get customer details
            $customer = $this->customerModel->getCustomerWithStats($id);

            if (!$customer) {
                throw new Exception('Không tìm thấy khách hàng');
            }

            $data = [
                'title' => 'Chỉnh sửa khách hàng: ' . $customer['full_name'] . ' - zone Fashion Admin',
                'customer' => $customer,
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'url' => '/zone-fashion/admin'],
                    ['title' => 'Khách hàng', 'url' => '/zone-fashion/admin/customers'],
                    ['title' => 'Chỉnh sửa']
                ]
            ];

            $this->render('admin/customers/edit', $data, 'admin/layouts/main-inline');

        } catch (Exception $e) {
            header('Location: /zone-fashion/admin/customers?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function update($id)
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: /zone-fashion/admin/customers');
                exit;
            }

            // Check if customer exists
            $existingCustomer = $this->customerModel->getCustomerWithStats($id);
            if (!$existingCustomer) {
                throw new Exception('Không tìm thấy khách hàng');
            }

            // Check email uniqueness if changed
            if ($_POST['email'] !== $existingCustomer['email']) {
                $emailExists = $this->customerModel->findBy('email', $_POST['email']);
                if ($emailExists) {
                    throw new Exception('Email đã được sử dụng');
                }
            }

            // Prepare update data
            $updateData = [
                'full_name' => trim($_POST['full_name']),
                'email' => trim($_POST['email']),
                'phone' => $_POST['phone'] ?? null,
                'date_of_birth' => $_POST['date_of_birth'] ?? null,
                'gender' => $_POST['gender'] ?? null,
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
                'marketing_consent' => isset($_POST['marketing_consent']) ? 1 : 0,
                'preferred_language' => $_POST['preferred_language'] ?? 'vi'
            ];

            // Handle new avatar upload
            if (!empty($_FILES['avatar']['name'])) {
                $uploadResult = $this->uploadAvatar($_FILES['avatar']);
                if ($uploadResult['success']) {
                    // Delete old avatar
                    if ($existingCustomer['avatar']) {
                        $this->deleteAvatar($existingCustomer['avatar']);
                    }
                    $updateData['avatar'] = $uploadResult['path'];
                } else {
                    throw new Exception($uploadResult['error']);
                }
            }

            // Update customer
            $result = $this->customerModel->updateCustomer($id, $updateData);

            if (!$result) {
                throw new Exception('Không thể cập nhật khách hàng');
            }

            header('Location: /zone-fashion/admin/customers?success=' . urlencode('Cập nhật khách hàng thành công'));
            exit;

        } catch (Exception $e) {
            header('Location: /zone-fashion/admin/customers/edit/' . $id . '?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function setStatus()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: /zone-fashion/admin/customers');
                exit;
            }

            $customerId = $_POST['customer_id'] ?? null;
            $isActive = isset($_POST['is_active']) ? 1 : 0;

            if (!$customerId) {
                throw new Exception('Thiếu ID khách hàng');
            }

            // Update customer status
            $result = $this->customerModel->setCustomerStatus($customerId, $isActive);

            if (!$result) {
                throw new Exception('Không thể cập nhật trạng thái khách hàng');
            }

            if (isset($_POST['ajax']) && $_POST['ajax']) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Cập nhật trạng thái khách hàng thành công'
                ]);
            } else {
                header('Location: /zone-fashion/admin/customers?success=' . urlencode('Cập nhật trạng thái khách hàng thành công'));
            }

        } catch (Exception $e) {
            if (isset($_POST['ajax']) && $_POST['ajax']) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'error' => $e->getMessage()
                ]);
            } else {
                header('Location: /zone-fashion/admin/customers?error=' . urlencode($e->getMessage()));
            }
        }
    }

    public function verifyEmail()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: /zone-fashion/admin/customers');
                exit;
            }

            $customerId = $_POST['customer_id'] ?? null;

            if (!$customerId) {
                throw new Exception('Thiếu ID khách hàng');
            }

            // Verify email
            $result = $this->customerModel->verifyEmail($customerId);

            if (!$result) {
                throw new Exception('Không thể xác thực email');
            }

            header('Location: /zone-fashion/admin/customers?success=' . urlencode('Xác thực email thành công'));
            exit;

        } catch (Exception $e) {
            header('Location: /zone-fashion/admin/customers?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function delete($id)
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: /zone-fashion/admin/customers');
                exit;
            }

            // Check if customer exists
            $customer = $this->customerModel->getCustomerWithStats($id);
            if (!$customer) {
                throw new Exception('Không tìm thấy khách hàng');
            }

            // Soft delete customer (anonymize data)
            $result = $this->customerModel->deleteCustomer($id);

            if (!$result) {
                throw new Exception('Không thể xóa khách hàng');
            }

            header('Location: /zone-fashion/admin/customers?success=' . urlencode('Xóa khách hàng thành công'));
            exit;

        } catch (Exception $e) {
            header('Location: /zone-fashion/admin/customers?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    /**
     * Export customers
     */
    public function export()
    {
        try {
            $format = $_GET['format'] ?? 'csv';
            $search = $_GET['search'] ?? '';
            $filters = [
                'status' => $_GET['status'] ?? '',
                'tier' => $_GET['tier'] ?? '',
                'date_from' => $_GET['date_from'] ?? '',
                'date_to' => $_GET['date_to'] ?? ''
            ];

            $exportData = $this->customerModel->exportCustomers($filters);

            if ($format === 'csv') {
                $this->exportToCsv($exportData, 'customers_' . date('Y-m-d_H-i-s') . '.csv');
            } else {
                $this->exportToExcel($exportData, 'customers_' . date('Y-m-d_H-i-s') . '.xlsx');
            }

        } catch (Exception $e) {
            header('Location: /zone-fashion/admin/customers?error=' . urlencode('Lỗi khi xuất dữ liệu: ' . $e->getMessage()));
            exit;
        }
    }

    /**
     * Award loyalty points
     */
    public function awardPoints()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: /zone-fashion/admin/customers');
                exit;
            }

            $customerId = $_POST['customer_id'] ?? null;
            $points = (int)($_POST['points'] ?? 0);
            $description = $_POST['description'] ?? '';

            if (!$customerId || $points <= 0) {
                throw new Exception('Thông tin không hợp lệ');
            }

            // Award points
            $result = $this->customerModel->awardPoints($customerId, $points, $description);

            if (!$result) {
                throw new Exception('Không thể trao điểm thưởng');
            }

            header('Location: /zone-fashion/admin/customers/view/' . $customerId . '?success=' . urlencode('Trao điểm thưởng thành công'));
            exit;

        } catch (Exception $e) {
            $customerId = $_POST['customer_id'] ?? '';
            header('Location: /zone-fashion/admin/customers/view/' . $customerId . '?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    /**
     * Send marketing email to customers
     */
    public function sendMarketingEmail()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: /zone-fashion/admin/customers');
                exit;
            }

            $subject = $_POST['subject'] ?? '';
            $message = $_POST['message'] ?? '';
            $customerIds = $_POST['customer_ids'] ?? [];

            if (empty($subject) || empty($message) || empty($customerIds)) {
                throw new Exception('Vui lòng điền đầy đủ thông tin');
            }

            $count = 0;
            foreach ($customerIds as $customerId) {
                $customer = $this->customerModel->getCustomerWithStats($customerId);
                if ($customer && $customer['marketing_consent'] && $customer['email_verified_at']) {
                    // Send email (implement actual email sending)
                    // mail($customer['email'], $subject, $message);
                    $count++;
                }
            }

            header('Location: /zone-fashion/admin/customers?success=' . urlencode("Đã gửi email cho {$count} khách hàng"));
            exit;

        } catch (Exception $e) {
            header('Location: /zone-fashion/admin/customers?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    /**
     * Upload customer avatar
     */
    private function uploadAvatar($file)
    {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'error' => 'Chỉ chấp nhận file ảnh (JPEG, PNG, GIF)'];
        }

        if ($file['size'] > $maxSize) {
            return ['success' => false, 'error' => 'File ảnh không được vượt quá 2MB'];
        }

        $uploadDir = __DIR__ . '/../../../public/uploads/avatars/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = uniqid() . '_' . time() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            return ['success' => true, 'path' => '/uploads/avatars/' . $fileName];
        } else {
            return ['success' => false, 'error' => 'Không thể upload file'];
        }
    }

    /**
     * Delete customer avatar
     */
    private function deleteAvatar($imagePath)
    {
        $fullPath = __DIR__ . '/../../../public' . $imagePath;
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    /**
     * Send welcome email
     */
    private function sendWelcomeEmail($customerData)
    {
        $subject = "Chào mừng bạn đến với zone Fashion!";
        $message = "Xin chào {$customerData['full_name']}, cảm ơn bạn đã đăng ký tài khoản tại zone Fashion.";

        // TODO: Implement actual email sending
        // mail($customerData['email'], $subject, $message);
    }

    /**
     * Export to CSV
     */
    private function exportToCsv($data, $filename)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');

        // UTF-8 BOM for proper Excel display
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        if (!empty($data)) {
            // Write headers
            fputcsv($output, array_keys($data[0]));

            // Write data
            foreach ($data as $row) {
                fputcsv($output, $row);
            }
        }

        fclose($output);
        exit;
    }

    /**
     * API endpoint to get customer data for AJAX requests
     */
    public function api($id)
    {
        header('Content-Type: application/json');

        try {
            $customer = $this->customerModel->getCustomerWithStats($id);

            if (!$customer) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Không tìm thấy khách hàng'
                ]);
                return;
            }

            echo json_encode([
                'success' => true,
                'data' => $customer
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi khi tải dữ liệu khách hàng: ' . $e->getMessage()
            ]);
        }
    }
}
