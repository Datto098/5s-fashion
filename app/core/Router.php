<?php
/**
 * Web Router Class
 * 5S Fashion E-commerce Platform
 */

class Router
{
    private $routes = [];
    private $groups = [];
    private $notFoundHandler;

    public function __construct()
    {
        // Initialize
    }

    /**
     * Add GET route
     */
    public function get($path, $handler)
    {
        $this->addRoute('GET', $path, $handler);
    }

    /**
     * Add POST route
     */
    public function post($path, $handler)
    {
        $this->addRoute('POST', $path, $handler);
    }

    /**
     * Add PUT route
     */
    public function put($path, $handler)
    {
        $this->addRoute('PUT', $path, $handler);
    }

    /**
     * Add DELETE route
     */
    public function delete($path, $handler)
    {
        $this->addRoute('DELETE', $path, $handler);
    }

    /**
     * Add route with method
     */
    private function addRoute($method, $path, $handler)
    {
        // Add group prefix if in group
        if (!empty($this->groups)) {
            $groupPrefix = implode('', $this->groups);
            $path = $groupPrefix . $path;
        }

        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }

    /**
     * Create route group
     */
    public function group($prefix, $callback)
    {
        $this->groups[] = $prefix;
        $callback();
        array_pop($this->groups);
    }

    /**
     * Set 404 handler
     */
    public function set404($handler)
    {
        $this->notFoundHandler = $handler;
    }

    /**
     * Run the router
     */
    public function run()
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Remove the /5s-fashion prefix if it exists
        $requestUri = preg_replace('/^\/5s-fashion/', '', $requestUri);

        // Remove leading slash and normalize
        $requestUri = rtrim($requestUri, '/');
        if (empty($requestUri)) {
            $requestUri = '/';
        }

        // Find matching route
        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            $pattern = $this->convertToRegex($route['path']);

            if (preg_match($pattern, $requestUri, $matches)) {
                // Debug: Log successful route match
                error_log("Router: Matched route - Method: {$requestMethod}, URI: {$requestUri}, Handler: {$route['handler']}");

                // Remove full match
                array_shift($matches);

                // Call handler
                $this->callHandler($route['handler'], $matches);
                return;
            }
        }

        // Debug: Log no route found
        error_log("Router: No route found - Method: {$requestMethod}, URI: {$requestUri}");

        // No route found, call 404 handler
        if ($this->notFoundHandler) {
            call_user_func($this->notFoundHandler);
        } else {
            http_response_code(404);
            echo "404 Not Found";
        }
    }

    /**
     * Convert route path to regex pattern
     */
    private function convertToRegex($path)
    {
        // Handle root path specially
        if ($path === '/') {
            return '/^\/$/';
        }

        // Escape special regex characters
        $pattern = preg_quote($path, '/');

        // Replace escaped parameter placeholders with regex groups
        $pattern = preg_replace('/\\\{([^}]+)\\\}/', '([^\/]+)', $pattern);

        return '/^' . $pattern . '$/';
    }

    /**
     * Call route handler
     */
    private function callHandler($handler, $params = [])
    {
        error_log("Router: Calling handler - {$handler} with params: " . json_encode($params));

        if (is_callable($handler)) {
            // Closure or function
            call_user_func_array($handler, $params);
        } elseif (is_string($handler)) {
            // Controller@method format
            if (strpos($handler, '@') !== false) {
                list($controller, $method) = explode('@', $handler);

                // Include controller file
                $controllerFile = APP_PATH . '/controllers/' . $controller . '.php';
                if (!file_exists($controllerFile)) {
                    error_log("Router ERROR: Controller file not found: {$controllerFile}");
                    throw new Exception("Controller file not found: {$controllerFile}");
                }

                require_once $controllerFile;

                if (!class_exists($controller)) {
                    error_log("Router ERROR: Controller class not found: {$controller}");
                    throw new Exception("Controller class not found: {$controller}");
                }

                $controllerInstance = new $controller();

                if (!method_exists($controllerInstance, $method)) {
                    error_log("Router ERROR: Method {$method} not found in controller {$controller}");
                    throw new Exception("Method {$method} not found in controller {$controller}");
                }

                error_log("Router: About to call {$controller}->{$method}");
                call_user_func_array([$controllerInstance, $method], $params);
                error_log("Router: Finished calling {$controller}->{$method}");
            } else {
                // Simple function name
                if (function_exists($handler)) {
                    call_user_func_array($handler, $params);
                } else {
                    throw new Exception("Function not found: {$handler}");
                }
            }
        } else {
            throw new Exception("Invalid route handler");
        }
    }
}
?>
