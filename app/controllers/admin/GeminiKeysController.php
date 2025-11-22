<?php
/**
 * Gemini API Keys Controller
 * zone Fashion E-commerce Platform
 * 
 * Admin panel controller for managing Gemini AI API keys
 * Features: CRUD operations, key testing, usage monitoring, auto health check
 */

require_once dirname(__DIR__) . '/BaseController.php';
require_once __DIR__ . '/../../models/GeminiApiKey.php';

class GeminiKeysController extends BaseController
{
    private $geminiKeyModel;

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

        $this->geminiKeyModel = new GeminiApiKey();
    }

    /**
     * Display list of API keys with search and filters
     */
    public function index()
    {
        try {
            // Get search and filter parameters
            $search = $_GET['search'] ?? '';
            $filters = [
                'status' => $_GET['status'] ?? '',
                'test_status' => $_GET['test_status'] ?? ''
            ];
            
            $page = (int)($_GET['page'] ?? 1);
            $limit = (int)($_GET['limit'] ?? 20);

            // Get API keys with pagination
            $apiKeys = $this->geminiKeyModel->getAll($search, $filters, $page, $limit);
            $totalKeys = $this->geminiKeyModel->getCount($search, $filters);
            $totalPages = ceil($totalKeys / $limit);

            // Get statistics
            $stats = $this->geminiKeyModel->getStatistics();

            $data = [
                'title' => 'Quản lý Gemini API Keys - zone Fashion Admin',
                'api_keys' => $apiKeys,
                'stats' => $stats,
                'search' => $search,
                'filters' => $filters,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $totalPages,
                    'total_items' => $totalKeys,
                    'limit' => $limit
                ],
                'breadcrumbs' => [
                    ['name' => 'Dashboard', 'url' => '/zone-fashion/admin'],
                    ['name' => 'Quản lý Gemini Keys', 'url' => '']
                ]
            ];

            $this->render('admin/gemini-keys/index', $data, 'admin/layouts/main-inline');

        } catch (Exception $e) {
            error_log('Error in GeminiKeysController::index: ' . $e->getMessage());
            
            // Fallback data in case of error
            $data = [
                'title' => 'Quản lý Gemini API Keys - zone Fashion Admin',
                'api_keys' => [],
                'stats' => [
                    'total_keys' => 0,
                    'active_keys' => 0,
                    'error_keys' => 0,
                    'total_usage_today' => 0,
                    'total_usage_month' => 0
                ],
                'search' => '',
                'filters' => [],
                'pagination' => [
                    'current_page' => 1,
                    'total_pages' => 0,
                    'total_items' => 0,
                    'limit' => 20
                ],
                'error' => 'Có lỗi xảy ra khi tải danh sách API keys',
                'breadcrumbs' => [
                    ['name' => 'Dashboard', 'url' => '/zone-fashion/admin'],
                    ['name' => 'Quản lý Gemini Keys', 'url' => '']
                ]
            ];

            $this->render('admin/gemini-keys/index', $data, 'admin/layouts/main-inline');
        }
    }

    /**
     * Show create form
     */
    public function create()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Handle form submission
                $data = [
                    'name' => trim($_POST['name'] ?? ''),
                    'api_key' => trim($_POST['api_key'] ?? ''),
                    'status' => $_POST['status'] ?? 'active',
                    'daily_limit' => (int)($_POST['daily_limit'] ?? 1000),
                    'monthly_limit' => (int)($_POST['monthly_limit'] ?? 30000),
                    'notes' => trim($_POST['notes'] ?? ''),
                    'created_by' => $_SESSION['admin_user']['id'] ?? $_SESSION['user_id'] ?? 1
                ];

                // Validation
                if (empty($data['name'])) {
                    throw new Exception('Tên API key không được để trống');
                }

                if (empty($data['api_key'])) {
                    throw new Exception('API key không được để trống');
                }

                // Test API key before saving
                $testResult = $this->geminiKeyModel->testApiKey($data['api_key']);
                if (!$testResult['success']) {
                    throw new Exception('API key không hợp lệ: ' . $testResult['message']);
                }

                // Create the API key
                try {
                    $result = $this->geminiKeyModel->create($data);
                    if ($result) {
                        header('Location: /zone-fashion/admin/gemini-keys?success=' . urlencode('Tạo API key thành công'));
                        exit;
                    } else {
                        throw new Exception('Không thể tạo API key');
                    }
                } catch (Exception $dbError) {
                    $error = $dbError->getMessage();
                    
                    // Check for duplicate key error
                    if (strpos($error, 'Duplicate entry') !== false && strpos($error, 'unique_api_key') !== false) {
                        $error = 'API key này đã tồn tại trong hệ thống';
                    }
                    
                    throw new Exception($error);
                }
            }

            // Show create form
            $data = [
                'title' => 'Tạo Gemini API Key - zone Fashion Admin',
                'breadcrumbs' => [
                    ['name' => 'Dashboard', 'url' => '/zone-fashion/admin'],
                    ['name' => 'Quản lý Gemini Keys', 'url' => '/zone-fashion/admin/gemini-keys'],
                    ['name' => 'Tạo mới', 'url' => '']
                ]
            ];

            $this->render('admin/gemini-keys/create', $data, 'admin/layouts/main-inline');

        } catch (Exception $e) {
            error_log('Error in GeminiKeysController::create: ' . $e->getMessage());
            
            // Show create form with error
            $data = [
                'title' => 'Tạo Gemini API Key - zone Fashion Admin',
                'error' => $e->getMessage(),
                'old_data' => $_POST ?? [],
                'breadcrumbs' => [
                    ['name' => 'Dashboard', 'url' => '/zone-fashion/admin'],
                    ['name' => 'Quản lý Gemini Keys', 'url' => '/zone-fashion/admin/gemini-keys'],
                    ['name' => 'Tạo mới', 'url' => '']
                ]
            ];

            $this->render('admin/gemini-keys/create', $data, 'admin/layouts/main-inline');
        }
    }

    /**
     * Show API key details
     */
    public function show($id)
    {
        try {
            $apiKey = $this->geminiKeyModel->getById($id);
            if (!$apiKey) {
                header('Location: /zone-fashion/admin/gemini-keys?error=' . urlencode('Không tìm thấy API key'));
                exit;
            }

            $data = [
                'title' => 'Chi tiết API Key: ' . $apiKey['name'] . ' - zone Fashion Admin',
                'api_key' => $apiKey,
                'breadcrumbs' => [
                    ['name' => 'Dashboard', 'url' => '/zone-fashion/admin'],
                    ['name' => 'Quản lý Gemini Keys', 'url' => '/zone-fashion/admin/gemini-keys'],
                    ['name' => 'Chi tiết', 'url' => '']
                ]
            ];

            $this->render('admin/gemini-keys/show', $data, 'admin/layouts/main-inline');
        } catch (Exception $e) {
            error_log('Error in GeminiKeysController::show: ' . $e->getMessage());
            header('Location: /zone-fashion/admin/gemini-keys?error=' . urlencode('Có lỗi xảy ra khi tải chi tiết API key'));
            exit;
        }
    }

    /**
     * Show edit form and handle updates
     */
    public function edit($id)
    {
        try {
            $apiKey = $this->geminiKeyModel->getById($id);
            if (!$apiKey) {
                header('Location: /zone-fashion/admin/gemini-keys?error=' . urlencode('Không tìm thấy API key'));
                exit;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Handle form submission
                $data = [
                    'name' => trim($_POST['name'] ?? ''),
                    'api_key' => trim($_POST['api_key'] ?? ''),
                    'status' => $_POST['status'] ?? 'active',
                    'daily_limit' => (int)($_POST['daily_limit'] ?? 1000),
                    'monthly_limit' => (int)($_POST['monthly_limit'] ?? 30000),
                    'notes' => trim($_POST['notes'] ?? '')
                ];

                // Validation
                if (empty($data['name'])) {
                    throw new Exception('Tên API key không được để trống');
                }

                if (empty($data['api_key'])) {
                    throw new Exception('API key không được để trống');
                }

                // Test API key if changed
                if ($data['api_key'] !== $apiKey['api_key']) {
                    $testResult = $this->geminiKeyModel->testApiKey($data['api_key']);
                    if (!$testResult['success']) {
                        throw new Exception('API key không hợp lệ: ' . $testResult['message']);
                    }
                }

                // Update the API key
                $result = $this->geminiKeyModel->update($id, $data);

                if ($result) {
                    header('Location: /zone-fashion/admin/gemini-keys?success=' . urlencode('Cập nhật API key thành công'));
                } else {
                    throw new Exception('Không thể cập nhật API key');
                }
                exit;
            }

            $data = [
                'title' => 'Chỉnh sửa API Key: ' . $apiKey['name'] . ' - zone Fashion Admin',
                'api_key' => $apiKey,
                'breadcrumbs' => [
                    ['name' => 'Dashboard', 'url' => '/zone-fashion/admin'],
                    ['name' => 'Quản lý Gemini Keys', 'url' => '/zone-fashion/admin/gemini-keys'],
                    ['name' => 'Chỉnh sửa', 'url' => '']
                ]
            ];

            $this->render('admin/gemini-keys/edit', $data, 'admin/layouts/main-inline');

        } catch (Exception $e) {
            error_log('Error in GeminiKeysController::edit: ' . $e->getMessage());
            header('Location: /zone-fashion/admin/gemini-keys?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    /**
     * Delete API key
     */
    public function delete()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $keyId = $input['key_id'] ?? null;

            if (!$keyId) {
                throw new Exception('Thiếu ID API key');
            }

            // Check if this is the last active key
            $stats = $this->geminiKeyModel->getStatistics();
            $apiKey = $this->geminiKeyModel->getById($keyId);
            
            if ($stats['active_keys'] <= 1 && $apiKey['status'] === 'active') {
                throw new Exception('Không thể xóa key cuối cùng đang hoạt động');
            }

            $result = $this->geminiKeyModel->delete($keyId);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Xóa API key thành công']);
            } else {
                throw new Exception('Không thể xóa API key');
            }

        } catch (Exception $e) {
            error_log('Error in GeminiKeysController::delete: ' . $e->getMessage());
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Test API key functionality
     */
    public function test()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $keyId = $input['key_id'] ?? null;
            $apiKeyValue = $input['api_key'] ?? null;

            // Case 1: Test existing API key from database
            if ($keyId) {
                $apiKey = $this->geminiKeyModel->getById($keyId);
                if (!$apiKey) {
                    throw new Exception('Không tìm thấy API key');
                }
                $apiKeyValue = $apiKey['api_key'];
            }

            // Case 2: Test new API key from form input
            if (!$apiKeyValue) {
                throw new Exception('Thiếu API key để test');
            }

            // Test the API key
            $testResult = $this->geminiKeyModel->testApiKey($apiKeyValue);

            // Update test status in database if testing existing key
            if ($keyId) {
                $this->updateTestStatus($keyId, $testResult);
            }

            echo json_encode([
                'success' => $testResult['success'],
                'message' => $testResult['message'],
                'tested_at' => date('Y-m-d H:i:s')
            ]);

        } catch (Exception $e) {
            error_log('Error in GeminiKeysController::test: ' . $e->getMessage());
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Test API key from input (for create form)
     * Handles URL: /admin/gemini-keys/test-input
     */
    public function testInput()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $apiKey = $input['api_key'] ?? null;

            if (!$apiKey) {
                throw new Exception('Thiếu API key');
            }

            // Test the API key directly
            $testResult = $this->geminiKeyModel->testApiKey($apiKey);

            echo json_encode([
                'success' => $testResult['success'],
                'message' => $testResult['message'],
                'tested_at' => date('Y-m-d H:i:s')
            ]);

        } catch (Exception $e) {
            error_log('Error in GeminiKeysController::testInput: ' . $e->getMessage());
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Test all active API keys (health check)
     */
    public function testAll()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $apiKeys = $this->geminiKeyModel->getAll('', ['status' => 'active'], 1, 100);
            $results = [];

            foreach ($apiKeys as $apiKey) {
                $testResult = $this->geminiKeyModel->testApiKey($apiKey['api_key']);
                $this->updateTestStatus($apiKey['id'], $testResult);
                
                $results[] = [
                    'id' => $apiKey['id'],
                    'name' => $apiKey['name'],
                    'success' => $testResult['success'],
                    'message' => $testResult['message']
                ];
            }

            echo json_encode([
                'success' => true,
                'message' => 'Đã test tất cả API keys',
                'results' => $results,
                'tested_at' => date('Y-m-d H:i:s')
            ]);

        } catch (Exception $e) {
            error_log('Error in GeminiKeysController::testAll: ' . $e->getMessage());
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Reset usage counters
     */
    public function resetUsage()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $type = $input['type'] ?? 'daily'; // daily or monthly

            if ($type === 'daily') {
                $result = $this->geminiKeyModel->resetDailyUsage();
                $message = 'Reset daily usage thành công';
            } else if ($type === 'monthly') {
                $result = $this->geminiKeyModel->resetMonthlyUsage();
                $message = 'Reset monthly usage thành công';
            } else {
                throw new Exception('Loại reset không hợp lệ');
            }

            if ($result) {
                echo json_encode(['success' => true, 'message' => $message]);
            } else {
                throw new Exception('Không thể reset usage counters');
            }

        } catch (Exception $e) {
            error_log('Error in GeminiKeysController::resetUsage: ' . $e->getMessage());
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Update test status in database
     */
    private function updateTestStatus($keyId, $testResult)
    {
        try {
            $status = $testResult['success'] ? 'success' : 'failed';
            $errorMessage = $testResult['success'] ? null : $testResult['message'];

            $sql = "UPDATE gemini_api_keys SET 
                        last_test_at = NOW(),
                        last_test_status = ?,
                        last_error_message = ?,
                        updated_at = NOW()
                    WHERE id = ?";

            $this->geminiKeyModel->executeQuery($sql, [$status, $errorMessage, $keyId]);

            // Auto-disable key if test failed multiple times
            if (!$testResult['success']) {
                $this->checkAndDisableFailedKey($keyId);
            }

        } catch (Exception $e) {
            error_log('Error updating test status: ' . $e->getMessage());
        }
    }

    /**
     * Check if key should be auto-disabled after multiple failures
     */
    private function checkAndDisableFailedKey($keyId)
    {
        try {
            // Get recent test history (last 5 tests)
            $sql = "SELECT last_test_status FROM gemini_api_keys WHERE id = ?";
            $apiKey = $this->geminiKeyModel->fetchOne($sql, [$keyId]);

            // If last test failed, disable the key to prevent further errors
            if ($apiKey && $apiKey['last_test_status'] === 'failed') {
                $sql = "UPDATE gemini_api_keys SET 
                            status = 'error',
                            updated_at = NOW()
                        WHERE id = ?";
                        
                $this->geminiKeyModel->executeQuery($sql, [$keyId]);
                
                error_log("Auto-disabled API key ID: $keyId due to failed test");
            }

        } catch (Exception $e) {
            error_log('Error checking failed key: ' . $e->getMessage());
        }
    }
}