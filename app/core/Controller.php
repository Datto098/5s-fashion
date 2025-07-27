<?php
/**
 * Core Controller Class
 * 5S Fashion E-commerce Platform
 */

abstract class Controller
{
    protected $data = [];

    public function __construct()
    {
        // Initialize session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Load model
     */
    protected function model($model)
    {
        require_once MODEL_PATH . '/' . $model . '.php';
        return new $model();
    }

    /**
     * Load and render view
     */
    protected function view($viewPath, $data = [])
    {
        // Merge controller data with passed data
        $data = array_merge($this->data, $data);

        // Extract data to variables
        extract($data);

        // Build view file path
        $viewFile = VIEW_PATH . '/' . str_replace('.', '/', $viewPath) . '.php';

        if (!file_exists($viewFile)) {
            die("View file not found: {$viewFile}");
        }

        require_once $viewFile;
    }

    /**
     * Redirect to URL
     */
    protected function redirect($url)
    {
        // If URL doesn't start with http, make it relative to base URL
        if (!preg_match('/^https?:\/\//', $url)) {
            $url = BASE_URL . '/' . ltrim($url, '/');
        }

        header("Location: {$url}");
        exit;
    }

    /**
     * Return JSON response
     */
    protected function json($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Get request input
     */
    protected function input($key = null, $default = null)
    {
        if ($key === null) {
            return $_REQUEST;
        }

        return $_REQUEST[$key] ?? $default;
    }

    /**
     * Get POST input
     */
    protected function post($key = null, $default = null)
    {
        if ($key === null) {
            return $_POST;
        }

        return $_POST[$key] ?? $default;
    }

    /**
     * Get GET input
     */
    protected function get($key = null, $default = null)
    {
        if ($key === null) {
            return $_GET;
        }

        return $_GET[$key] ?? $default;
    }

    /**
     * Validate request data
     */
    protected function validateRequest($rules, $data = null)
    {
        $data = $data ?: $_POST;
        $errors = [];

        foreach ($rules as $field => $ruleString) {
            $fieldRules = explode('|', $ruleString);
            $value = $data[$field] ?? null;

            foreach ($fieldRules as $rule) {
                $ruleParts = explode(':', $rule);
                $ruleName = $ruleParts[0];
                $ruleValue = $ruleParts[1] ?? null;

                switch ($ruleName) {
                    case 'required':
                        if (empty($value)) {
                            $errors[$field][] = ucfirst($field) . ' là trường bắt buộc.';
                        }
                        break;

                    case 'email':
                        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field][] = ucfirst($field) . ' phải là email hợp lệ.';
                        }
                        break;

                    case 'min':
                        if (!empty($value) && strlen($value) < $ruleValue) {
                            $errors[$field][] = ucfirst($field) . " phải có ít nhất {$ruleValue} ký tự.";
                        }
                        break;

                    case 'max':
                        if (!empty($value) && strlen($value) > $ruleValue) {
                            $errors[$field][] = ucfirst($field) . " không được vượt quá {$ruleValue} ký tự.";
                        }
                        break;

                    case 'numeric':
                        if (!empty($value) && !is_numeric($value)) {
                            $errors[$field][] = ucfirst($field) . ' phải là số.';
                        }
                        break;

                    case 'unique':
                        if (!empty($value)) {
                            $tableParts = explode(',', $ruleValue);
                            $table = $tableParts[0];
                            $column = $tableParts[1] ?? $field;

                            $db = Database::getInstance();
                            $result = $db->fetchOne("SELECT COUNT(*) as count FROM {$table} WHERE {$column} = :value", ['value' => $value]);

                            if ($result && $result['count'] > 0) {
                                $errors[$field][] = ucfirst($field) . ' đã tồn tại.';
                            }
                        }
                        break;
                }
            }
        }

        return $errors;
    }

    /**
     * Set flash message
     */
    protected function flashMessage($type, $message)
    {
        $_SESSION['flash'][$type] = $message;
    }

    /**
     * Get flash messages
     */
    protected function getFlashMessages()
    {
        $messages = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $messages;
    }

    /**
     * Check if user is authenticated
     */
    protected function isAuthenticated()
    {
        return isset($_SESSION['auth_user']);
    }

    /**
     * Get authenticated user
     */
    protected function user()
    {
        return $_SESSION['auth_user'] ?? null;
    }

    /**
     * Require authentication
     */
    protected function requireAuth()
    {
        if (!$this->isAuthenticated()) {
            $this->flashMessage('error', 'Vui lòng đăng nhập để tiếp tục.');
            $this->redirect('/admin/login');
        }
    }

    /**
     * Require admin role
     */
    protected function requireAdmin()
    {
        $this->requireAuth();

        $user = $this->user();
        if (!$user || $user['role'] !== 'admin') {
            $this->flashMessage('error', 'Bạn không có quyền truy cập.');
            $this->redirect('/admin/login');
        }
    }

    /**
     * Upload file
     */
    protected function uploadFile($file, $path, $allowedTypes = null)
    {
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            return false;
        }

        $allowedTypes = $allowedTypes ?: ALLOWED_IMAGE_TYPES;
        $uploadPath = PUBLIC_PATH . $path;

        // Create directory if not exists
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Validate file
        $fileInfo = pathinfo($file['name']);
        $extension = strtolower($fileInfo['extension']);

        if (!in_array($extension, $allowedTypes)) {
            return false;
        }

        if ($file['size'] > MAX_UPLOAD_SIZE) {
            return false;
        }

        // Generate unique filename
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $destination = $uploadPath . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $path . '/' . $filename;
        }

        return false;
    }

    /**
     * Delete file
     */
    protected function deleteFile($filePath)
    {
        $fullPath = PUBLIC_PATH . $filePath;
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }

    /**
     * Generate CSRF token
     */
    protected function generateCsrfToken()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verify CSRF token
     */
    protected function verifyCsrfToken($token)
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Sanitize input
     */
    protected function sanitize($input)
    {
        if (is_array($input)) {
            return array_map([$this, 'sanitize'], $input);
        }

        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Format price for display
     */
    protected function formatPrice($price)
    {
        return number_format($price, 0, ',', '.') . ' ₫';
    }

    /**
     * Format date for display
     */
    protected function formatDate($date, $format = 'd/m/Y H:i')
    {
        return date($format, strtotime($date));
    }
}
