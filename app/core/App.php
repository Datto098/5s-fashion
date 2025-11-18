<?php
/**
 * Application Core Class
 * zone Fashion E-commerce Platform
 */

class App
{
    protected $controller = 'HomeController';
    protected $method = 'index';
    protected $params = [];

    public function __construct()
    {
        // Load helper functions
        require_once APP_PATH . '/helpers/functions.php';

        $url = $this->parseUrl();

        // Check if this is an API request - don't route through this system
        if (isset($url[0]) && $url[0] === 'api') {
            // Let api.php handle this
            return;
        }

        // Check if this is an admin route
        if (isset($url[0]) && $url[0] === 'admin') {
            array_shift($url); // Remove 'admin' from URL
            $this->routeAdmin($url);
        } else {
            // This is a client route
            $this->routeClient($url);
        }
    }    private function routeAdmin($url)
    {
        $this->controller = 'DashboardController'; // Default admin controller

        // Special handling for nested routes like /admin/products/{id}/variants/{action}
        if (isset($url[0], $url[1], $url[2], $url[3])) {
            // Check for pattern: controller/id/subcontroller/action
            if ($url[0] === 'products' && $url[2] === 'variants') {
                $this->controller = 'ProductVariantsController';
                $productId = $url[1];
                $action = $url[3];

                // Special case for /admin/products/{productId}/variants/fix-duplicates
                if ($action === 'fix-duplicates') {
                    $controllerFile = APP_PATH . '/controllers/admin/' . $this->controller . '.php';
                    if (file_exists($controllerFile)) {
                        require_once $controllerFile;
                        $this->controller = new $this->controller;
                        $this->method = 'fixDuplicateAttributes';
                        $this->params = [$productId];
                        call_user_func_array([$this->controller, $this->method], $this->params);
                        return;
                    }
                }

                // Check for 5-parameter route: /admin/products/{productId}/variants/{variantId}/{action}
                if (isset($url[4])) {
                    $variantId = $url[3];
                    $action = $url[4];

                    if ($action === 'data') {
                        $controllerFile = APP_PATH . '/controllers/admin/' . $this->controller . '.php';
                        if (file_exists($controllerFile)) {
                            require_once $controllerFile;
                            $this->controller = new $this->controller;
                            $this->method = 'getVariantData';
                            $this->params = [$variantId];
                            call_user_func_array([$this->controller, $this->method], $this->params);
                            return;
                        }
                    } elseif ($action === 'update') {
                        $controllerFile = APP_PATH . '/controllers/admin/' . $this->controller . '.php';
                        if (file_exists($controllerFile)) {
                            require_once $controllerFile;
                            $this->controller = new $this->controller;
                            $this->method = 'update';
                            $this->params = [$productId, $variantId];
                            call_user_func_array([$this->controller, $this->method], $this->params);
                            return;
                        }
                    } elseif ($action === 'delete') {
                        $controllerFile = APP_PATH . '/controllers/admin/' . $this->controller . '.php';
                        if (file_exists($controllerFile)) {
                            require_once $controllerFile;
                            $this->controller = new $this->controller;
                            $this->method = 'delete';
                            $this->params = [$productId, $variantId];
                            call_user_func_array([$this->controller, $this->method], $this->params);
                            return;
                        }
                    }
                }                $controllerFile = APP_PATH . '/controllers/admin/' . $this->controller . '.php';
                if (file_exists($controllerFile)) {
                    require_once $controllerFile;
                    $this->controller = new $this->controller;

                    // Map actions
                    switch ($action) {
                        case 'generate':
                            $this->method = 'generateVariants';
                            $this->params = [$productId];
                            break;
                        case 'create':
                            $this->method = 'create';
                            $this->params = [$productId];
                            break;
                        default:
                            $this->method = 'index';
                            $this->params = [$productId];
                            break;
                    }

                    // Call the controller method with parameters
                    call_user_func_array([$this->controller, $this->method], $this->params);
                    return;
                }
            }
        }

        // Standard admin routing
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
            // Fallback to default admin controller
            require_once APP_PATH . '/controllers/admin/DashboardController.php';
            $this->controller = new DashboardController();
        }

        // Special pattern: /admin/{controller}/{id}/{action}
        // After removing controller key, $url[1] may be the id and $url[2] the action.
        if (isset($url[1]) && is_numeric($url[1]) && isset($url[2]) && method_exists($this->controller, $url[2])) {
            $this->method = $url[2];
            // params should be only the id
            $this->params = [$url[1]];
            // Call and return early
            call_user_func_array([$this->controller, $this->method], $this->params);
            return;
        }

        // Set method (standard behavior)
        if (isset($url[1])) {
            // Convert kebab-case to camelCase for method names
            $methodName = str_replace('-', '', lcfirst(str_replace('-', ' ', $url[1])));
            $methodName = str_replace(' ', '', ucwords($methodName));
            
            if (method_exists($this->controller, $methodName)) {
                $this->method = $methodName;
                unset($url[1]);
            } elseif (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        // Set parameters
        $this->params = $url ? array_values($url) : [];

        // Call the controller method with parameters
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    private function routeClient($url)
    {
        // Load client routes
        $routes = include APP_PATH . '/routes/client_routes.php';

        // Get the full URL path
        $fullPath = implode('/', $url);
        $method = $_SERVER['REQUEST_METHOD'];

        // Check for exact route matches first (handles both GET and POST)
        foreach ($routes as $route => $handler) {
            if ($route === $fullPath || ($route === '' && empty($fullPath))) {
                $this->handleRoute($handler);
                return;
            }
        }

        // Check for pattern matches (like product/{slug})
        foreach ($routes as $route => $handler) {
            if (strpos($route, '{') !== false) {
                $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $route);
                if (preg_match('#^' . $pattern . '$#', $fullPath, $matches)) {
                    array_shift($matches); // Remove full match
                    $this->params = $matches;
                    $this->handleRoute($handler);
                    return;
                }
            }
        }

        // Check if it's a valid category or brand URL pattern
        if (!empty($fullPath)) {
            $segments = explode('/', $fullPath);
            $firstSegment = $segments[0];

            // Skip special URLs that shouldn't be treated as categories
            $skipPatterns = ['ajax', 'api', 'admin', 'uploads', 'assets', 'public', 'order', 'payment', 'cart', 'wishlist', 'account', 'auth', 'login', 'register', 'vouchers', 'contact', 'about', 'blog', 'search'];

            // Only handle category-like URLs if they're not in skip patterns AND have valid format
            if (!in_array($firstSegment, $skipPatterns) && preg_match('/^[a-z0-9-]+$/', $firstSegment)) {
                // Check if it's a valid category slug in database
                try {
                    require_once APP_PATH . '/models/Category.php';
                    $categoryModel = new Category();
                    
                    $category = $categoryModel->findBySlug($firstSegment);
                    if ($category && $category['status'] === 'active') {
                        $_GET['category'] = $firstSegment;
                        $this->handleRoute('HomeController@shop');
                        return;
                    }
                } catch (Exception $e) {
                    // If there's an error checking category, continue to 404
                    error_log("Error checking category: " . $e->getMessage());
                }
            }
        }

        // No route found, show 404
        $this->show404();
    }

    private function handleRoute($handler)
    {
        if (is_string($handler)) {
            list($controllerName, $method) = explode('@', $handler);
            $controllerFile = APP_PATH . '/controllers/' . $controllerName . '.php';

            if (file_exists($controllerFile)) {
                // Load BaseController first if it exists
                $baseControllerFile = APP_PATH . '/controllers/BaseController.php';
                if (file_exists($baseControllerFile)) {
                    require_once $baseControllerFile;
                }

                require_once $controllerFile;
                $this->controller = new $controllerName;
                $this->method = $method;

                // Call the controller method with parameters
                call_user_func_array([$this->controller, $this->method], $this->params);
            } else {
                $this->show404();
            }
        }
    }

    private function show404()
    {
        http_response_code(404);
        
        // Check if 404.php file exists
        $errorFile = VIEW_PATH . '/errors/404.php';
        if (file_exists($errorFile)) {
            require_once $errorFile;
        } else {
            // Fallback if 404.php doesn't exist
            echo "<h1>404 - Page Not Found</h1>";
            echo "<p>The page you are looking for could not be found.</p>";
            echo '<a href="' . BASE_URL . '">Go to Homepage</a>';
        }
        exit;
    }

    public function parseUrl()
    {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }
}
