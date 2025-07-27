<?php
/**
 * Application Core Class
 * 5S Fashion E-commerce Platform
 */

class App
{
    protected $controller = 'DashboardController';
    protected $method = 'index';
    protected $params = [];

    public function __construct()
    {
        $url = $this->parseUrl();

        // Skip the 'admin' part from URL for admin routing
        if (isset($url[0]) && $url[0] === 'admin') {
            array_shift($url);
        }

        // Set controller
        if (isset($url[0])) {
            $controllerName = ucfirst($url[0]) . 'Controller';
            $controllerFile = APP_PATH . '/controllers/admin/' . $controllerName . '.php';

            if (file_exists($controllerFile)) {
                $this->controller = $controllerName;
                unset($url[0]);
            }
        }

        // Include the controller file
        $controllerFile = APP_PATH . '/controllers/admin/' . $this->controller . '.php';
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            $this->controller = new $this->controller;
        } else {
            // Fallback to default controller
            require_once APP_PATH . '/controllers/admin/DashboardController.php';
            $this->controller = new DashboardController();
        }

        // Set method
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        // Set parameters
        $this->params = $url ? array_values($url) : [];

        // Call the controller method with parameters
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function parseUrl()
    {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }
}
