<?php

require_once dirname(__DIR__) . '/BaseController.php';
require_once __DIR__ . '/../../models/Setting.php';

class SettingsController extends BaseController
{
    private $settingModel;

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

        $this->settingModel = new Setting();
    }

    public function index()
    {
        try {
            // Get all settings grouped by category
            $settings = $this->settingModel->getAllGrouped();

            $data = [
                'title' => 'Cài đặt hệ thống - zone Fashion Admin',
                'settings' => $settings,
                'breadcrumbs' => [
                    ['name' => 'Dashboard', 'url' => '/zone-fashion/admin'],
                    ['name' => 'Cài đặt', 'url' => '']
                ]
            ];

            $this->render('admin/settings/index', $data, 'admin/layouts/main-inline');
        } catch (Exception $e) {
            error_log('Error in SettingsController::index: ' . $e->getMessage());
            $this->render('admin/settings/index', [
                'title' => 'Cài đặt hệ thống - zone Fashion Admin',
                'settings' => [],
                'error' => 'Có lỗi xảy ra khi tải dữ liệu'
            ], 'admin/layouts/main-inline');
        }
    }

    public function update()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $group = $_POST['group'] ?? null;
            if (!$group) {
                throw new Exception('Thiếu thông tin nhóm');
            }

            $updated = 0;
            foreach ($_POST as $key => $value) {
                if ($key !== 'group' && strpos($key, 'setting_') === 0) {
                    $settingKey = str_replace('setting_', '', $key);

                    // Handle file uploads
                    if (isset($_FILES[$key]) && $_FILES[$key]['error'] === 0) {
                        $value = $this->handleFileUpload($_FILES[$key], $settingKey);
                    }

                    if ($this->settingModel->updateValue($settingKey, $value)) {
                        $updated++;
                    }
                }
            }

            if ($updated > 0) {
                header('Location: /zone-fashion/admin/settings?success=' . urlencode("Đã cập nhật $updated cài đặt"));
            } else {
                header('Location: /zone-fashion/admin/settings?error=' . urlencode('Không có thay đổi nào được lưu'));
            }
            exit;

        } catch (Exception $e) {
            error_log('Error in SettingsController::update: ' . $e->getMessage());
            header('Location: /zone-fashion/admin/settings?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function reset()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $group = $_POST['group'] ?? null;
            if (!$group) {
                throw new Exception('Thiếu thông tin nhóm');
            }

            $result = $this->settingModel->resetCategory($group);

            if ($result) {
                header('Location: /zone-fashion/admin/settings?success=' . urlencode('Đã khôi phục cài đặt mặc định'));
            } else {
                header('Location: /zone-fashion/admin/settings?error=' . urlencode('Không thể khôi phục cài đặt'));
            }
            exit;

        } catch (Exception $e) {
            error_log('Error in SettingsController::reset: ' . $e->getMessage());
            header('Location: /zone-fashion/admin/settings?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    private function handleFileUpload($file, $settingKey)
    {
        $uploadDir = 'uploads/settings/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = $settingKey . '_' . time() . '.' . $fileExtension;
        $uploadPath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return $uploadPath;
        }

        throw new Exception('Không thể tải lên file');
    }
}
