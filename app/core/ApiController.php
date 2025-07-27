<?php

require_once __DIR__ . '/ApiResponse.php';

/**
 * Base API Controller
 * Foundation for all API controllers
 */
abstract class ApiController
{
    protected $db;
    protected $requestMethod;
    protected $requestData;
    protected $headers;

    public function __construct()
    {
        // Enable CORS
        $this->enableCors();

        // Get request method and data
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        $this->headers = getallheaders();

        // Get request data based on method
        $this->getRequestData();

        // Initialize database
        $this->initDatabase();

        // Handle preflight requests
        if ($this->requestMethod === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }

    /**
     * Enable CORS headers
     */
    private function enableCors()
    {
        // Allow requests from any origin (adjust for production)
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        header('Access-Control-Max-Age: 86400'); // Cache preflight for 24 hours
    }

    /**
     * Get request data based on HTTP method
     */
    private function getRequestData()
    {
        switch ($this->requestMethod) {
            case 'GET':
                $this->requestData = $_GET;
                break;
            case 'POST':
                $this->requestData = $this->parseRequestBody();
                break;
            case 'PUT':
            case 'PATCH':
                $this->requestData = $this->parseRequestBody();
                break;
            case 'DELETE':
                $this->requestData = $this->parseRequestBody();
                break;
            default:
                $this->requestData = [];
        }
    }

    /**
     * Parse request body (JSON or form data)
     */
    private function parseRequestBody()
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (strpos($contentType, 'application/json') !== false) {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            return $data ?? [];
        } else {
            return $_POST;
        }
    }

    /**
     * Initialize database connection
     */
    private function initDatabase()
    {
        try {
            $config = require __DIR__ . '/../config/database.php';
            $db = $config['connections']['mysql'];

            $this->db = new PDO(
                "mysql:host={$db['host']};dbname={$db['database']};charset=utf8mb4",
                $db['username'],
                $db['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (Exception $e) {
            ApiResponse::serverError('Database connection failed');
        }
    }

    /**
     * Validate required fields
     * @param array $required
     * @param array $data
     * @return array|null
     */
    protected function validateRequired($required, $data = null)
    {
        $data = $data ?? $this->requestData;
        $errors = [];

        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $errors[$field] = "The {$field} field is required";
            }
        }

        return empty($errors) ? null : $errors;
    }

    /**
     * Sanitize input data
     * @param mixed $data
     * @return mixed
     */
    protected function sanitize($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }

        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Get pagination parameters
     * @return array
     */
    protected function getPaginationParams()
    {
        $page = max(1, (int)($this->requestData['page'] ?? 1));
        $limit = min(100, max(1, (int)($this->requestData['limit'] ?? 15)));
        $offset = ($page - 1) * $limit;

        return [
            'page' => $page,
            'limit' => $limit,
            'offset' => $offset
        ];
    }

    /**
     * Get filter parameters
     * @param array $allowedFilters
     * @return array
     */
    protected function getFilterParams($allowedFilters = [])
    {
        $filters = [];

        foreach ($allowedFilters as $filter) {
            if (isset($this->requestData[$filter]) && !empty($this->requestData[$filter])) {
                $filters[$filter] = $this->sanitize($this->requestData[$filter]);
            }
        }

        return $filters;
    }

    /**
     * Get sort parameters
     * @param array $allowedSorts
     * @param string $defaultSort
     * @return array
     */
    protected function getSortParams($allowedSorts = [], $defaultSort = 'id')
    {
        $sortBy = $this->requestData['sort_by'] ?? $defaultSort;
        $sortOrder = strtoupper($this->requestData['sort_order'] ?? 'ASC');

        // Validate sort field
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = $defaultSort;
        }

        // Validate sort order
        if (!in_array($sortOrder, ['ASC', 'DESC'])) {
            $sortOrder = 'ASC';
        }

        return [
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder
        ];
    }

    /**
     * Check if request method is allowed
     * @param array $allowedMethods
     */
    protected function checkMethod($allowedMethods)
    {
        if (!in_array($this->requestMethod, $allowedMethods)) {
            ApiResponse::error('Method not allowed', 405);
        }
    }

    /**
     * Log API request (for debugging)
     */
    protected function logRequest()
    {
        error_log(sprintf(
            'API Request: %s %s - Data: %s',
            $this->requestMethod,
            $_SERVER['REQUEST_URI'],
            json_encode($this->requestData)
        ));
    }
}
