<?php
/**
 * Base Controller Class
 * Zone Fashion E-commerce Platform
 */

class BaseController
{
    protected function loadCommonData()
    {
        // Load data needed for all client pages, such as categories for navigation
        $data = [];

        // Only load navigation categories for client pages (not admin)
        if (strpos($_SERVER['REQUEST_URI'], '/admin/') === false) {
            // Make sure Category model is loaded
            if (!class_exists('Category')) {
                require_once APP_PATH . '/models/Category.php';
            }

            $categoryModel = new Category();
            $data['navCategories'] = $categoryModel->getNavigationCategories();
        }

        return $data;
    }

    protected function render($view, $data = [], $layout = 'admin/layouts/app')
    {
        // Load common data for all views
        $commonData = $this->loadCommonData();
        $data = array_merge($commonData, $data);

        // Start output buffering
        ob_start();

        // Extract variables for the view
        extract($data);

        // Include the view file
        $viewFile = APP_PATH . '/views/' . $view . '.php';
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            throw new Exception("View file not found: " . $viewFile);
        }

        // Get the content
        $content = ob_get_clean();

        // If layout is specified, render within layout
        if ($layout) {
            $layoutFile = APP_PATH . '/views/' . $layout . '.php';
            if (file_exists($layoutFile)) {
                // Extract data again for layout access
                extract($data);
                include $layoutFile;
            } else {
                // If no layout file, just output content
                echo $content;
            }
        } else {
            // No layout, just output content
            echo $content;
        }
    }

    protected function renderJSON($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect($url, $message = null, $type = 'success')
    {
        if ($message) {
            $_SESSION[$type . '_message'] = $message;
        }
        header('Location: ' . $url);
        exit;
    }

    protected function ensureSessionStarted()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Check if user is authenticated
     * @return bool
     */
    protected function isUserAuthenticated()
    {
        $this->ensureSessionStarted();
        return isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
    }

    /**
     * Check if admin is authenticated
     * @return bool
     */
    protected function isAdminAuthenticated()
    {
        $this->ensureSessionStarted();
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }

    /**
     * Get current user ID
     * @return int|null
     */
    protected function getCurrentUserId()
    {
        $this->ensureSessionStarted();
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Get current user data
     * @return array|null
     */
    protected function getCurrentUser()
    {
        $this->ensureSessionStarted();
        return $_SESSION['user'] ?? null;
    }

    /**
     * Convenience loader for models.
     * Usage: $this->model('User') returns an instance of User.
     * It will attempt APP_PATH-based include first, then relative fallbacks.
     * Instances are cached per-request.
     *
     * @param string $modelName
     * @return object
     * @throws Exception if model class cannot be loaded
     */
    protected function model($modelName)
    {
        static $models = [];

        if (isset($models[$modelName])) {
            return $models[$modelName];
        }

        $className = $modelName;

        if (!class_exists($className)) {
            // Try APP_PATH first, then relative fallbacks
            if (defined('APP_PATH') && file_exists(APP_PATH . '/models/' . $modelName . '.php')) {
                require_once APP_PATH . '/models/' . $modelName . '.php';
            } elseif (file_exists(__DIR__ . '/../models/' . $modelName . '.php')) {
                require_once __DIR__ . '/../models/' . $modelName . '.php';
            } elseif (file_exists(__DIR__ . '/../../models/' . $modelName . '.php')) {
                require_once __DIR__ . '/../../models/' . $modelName . '.php';
            }
        }

        if (class_exists($className)) {
            $models[$modelName] = new $className();
            return $models[$modelName];
        }

        throw new Exception("Model class not found: {$modelName}");
    }

    /**
     * Shortcut for rendering a client view. Keeps compatibility with existing
     * controller code calling $this->view(...).
     *
     * @param string $view
     * @param array $data
     * @param string|null $layout
     * @return void
     */
    protected function view($view, $data = [], $layout = 'client/layouts/app')
    {
        $this->render($view, $data, $layout);
    }
}
?>
