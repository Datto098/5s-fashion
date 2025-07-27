<?php
/**
 * Base Controller Class
 * 5S Fashion E-commerce Platform
 */

class BaseController
{
    protected function render($view, $data = [], $layout = 'admin/layouts/app')
    {
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
}
?>
