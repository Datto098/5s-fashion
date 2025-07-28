<?php

require_once __DIR__ . '/../core/ApiController.php';

/**
 * API Router
 * Routes API requests to appropriate controllers
 */
class ApiRouter
{
    private $routes = [];
    private $middlewares = [];

    /**
     * Add GET route
     */
    public function get($path, $handler, $middlewares = [])
    {
        $this->addRoute('GET', $path, $handler, $middlewares);
    }

    /**
     * Add POST route
     */
    public function post($path, $handler, $middlewares = [])
    {
        $this->addRoute('POST', $path, $handler, $middlewares);
    }

    /**
     * Add PUT route
     */
    public function put($path, $handler, $middlewares = [])
    {
        $this->addRoute('PUT', $path, $handler, $middlewares);
    }

    /**
     * Add DELETE route
     */
    public function delete($path, $handler, $middlewares = [])
    {
        $this->addRoute('DELETE', $path, $handler, $middlewares);
    }

    /**
     * Add route for any method
     */
    public function any($path, $handler, $middlewares = [])
    {
        $methods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'];
        foreach ($methods as $method) {
            $this->addRoute($method, $path, $handler, $middlewares);
        }
    }

    /**
     * Add route
     */
    private function addRoute($method, $path, $handler, $middlewares = [])
    {
        $this->routes[$method][$path] = [
            'handler' => $handler,
            'middlewares' => $middlewares
        ];
    }

    /**
     * Add global middleware
     */
    public function middleware($middleware)
    {
        $this->middlewares[] = $middleware;
    }

    /**
     * Dispatch request
     */
    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = $this->getCurrentPath();

        // Handle preflight requests
        if ($method === 'OPTIONS') {
            http_response_code(200);
            exit;
        }

        // Find matching route
        $route = $this->findRoute($method, $path);

        if (!$route) {
            ApiResponse::notFound('API endpoint not found');
        }

        try {
            // Run global middlewares
            $this->runMiddlewares($this->middlewares);

            // Run route-specific middlewares
            $this->runMiddlewares($route['middlewares']);

            // Execute handler
            $this->executeHandler($route['handler'], $path);

        } catch (Exception $e) {
            error_log('API Error: ' . $e->getMessage());
            ApiResponse::serverError('An error occurred while processing your request');
        }
    }

    /**
     * Get current request path
     */
    private function getCurrentPath()
    {
        $requestUri = $_SERVER['REQUEST_URI'];
        $path = parse_url($requestUri, PHP_URL_PATH);

        // Remove project folder from path first (for WAMP: /5s-fashion)
        $projectFolder = '/5s-fashion';
        if (strpos($path, $projectFolder) === 0) {
            $path = substr($path, strlen($projectFolder));
        }

        // Remove /api prefix if present
        if (strpos($path, '/api') === 0) {
            $path = substr($path, 4);
        }

        return $path ?: '/';
    }

    /**
     * Find matching route
     */
    private function findRoute($method, $path)
    {
        // Exact match first
        if (isset($this->routes[$method][$path])) {
            return $this->routes[$method][$path];
        }

        // Pattern matching for routes with parameters
        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $routePath => $route) {
                if ($this->matchRoute($routePath, $path)) {
                    return $route;
                }
            }
        }

        return null;
    }

    /**
     * Match route pattern with current path
     */
    private function matchRoute($routePath, $currentPath)
    {
        // Convert route pattern to regex
        $pattern = preg_replace('/\{(\w+)\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';

        return preg_match($pattern, $currentPath);
    }

    /**
     * Extract parameters from path
     */
    private function extractParams($routePath, $currentPath)
    {
        $params = [];

        // Get parameter names from route
        preg_match_all('/\{(\w+)\}/', $routePath, $paramNames);

        // Get parameter values from current path
        $pattern = preg_replace('/\{(\w+)\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $currentPath, $matches)) {
            array_shift($matches); // Remove full match

            foreach ($paramNames[1] as $index => $name) {
                if (isset($matches[$index])) {
                    $params[$name] = $matches[$index];
                }
            }
        }

        return $params;
    }

    /**
     * Run middlewares
     */
    private function runMiddlewares($middlewares)
    {
        foreach ($middlewares as $middleware) {
            if (is_callable($middleware)) {
                $middleware();
            } elseif (is_string($middleware) && class_exists($middleware)) {
                $middlewareInstance = new $middleware();
                if (method_exists($middlewareInstance, 'handle')) {
                    $middlewareInstance->handle();
                }
            }
        }
    }

    /**
     * Execute route handler
     */
    private function executeHandler($handler, $path)
    {
        if (is_callable($handler)) {
            $handler();
        } elseif (is_string($handler)) {
            // Parse controller@method format
            if (strpos($handler, '@') !== false) {
                list($controllerClass, $method) = explode('@', $handler);

                if (class_exists($controllerClass)) {
                    $controller = new $controllerClass();

                    if (method_exists($controller, $method)) {
                        // Extract route parameters
                        $params = $this->extractRouteParameters($path);
                        $controller->$method($params);
                    } else {
                        ApiResponse::serverError("Method {$method} not found in {$controllerClass}");
                    }
                } else {
                    ApiResponse::serverError("Controller {$controllerClass} not found");
                }
            }
        }
    }

    /**
     * Extract parameters from route path
     */
    private function extractRouteParameters($path)
    {
        $params = [];
        $pathParts = explode('/', trim($path, '/'));

        // Simple parameter extraction - can be enhanced
        if (count($pathParts) >= 3) {
            $params['id'] = end($pathParts);
        }

        return $params;
    }
}
