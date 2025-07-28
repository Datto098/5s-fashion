<?php

/**
 * API Response Helper Class
 * Standardized JSON responses for API endpoints
 */
class ApiResponse
{
    /**
     * Success response
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     * @return void
     */
    public static function success($data = null, $message = 'Success', $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');

        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('c'),
            'status_code' => $statusCode
        ];

        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Error response
     * @param string $message
     * @param int $statusCode
     * @param mixed $errors
     * @return void
     */
    public static function error($message = 'Error occurred', $statusCode = 400, $errors = null)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');

        $response = [
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'timestamp' => date('c'),
            'status_code' => $statusCode
        ];

        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Validation error response
     * @param array $validationErrors
     * @return void
     */
    public static function validationError($validationErrors)
    {
        self::error('Validation failed', 422, $validationErrors);
    }

    /**
     * Not found response
     * @param string $message
     * @return void
     */
    public static function notFound($message = 'Resource not found')
    {
        self::error($message, 404);
    }

    /**
     * Unauthorized response
     * @param string $message
     * @return void
     */
    public static function unauthorized($message = 'Unauthorized access')
    {
        self::error($message, 401);
    }

    /**
     * Forbidden response
     * @param string $message
     * @return void
     */
    public static function forbidden($message = 'Access forbidden')
    {
        self::error($message, 403);
    }

    /**
     * Server error response
     * @param string $message
     * @return void
     */
    public static function serverError($message = 'Internal server error')
    {
        self::error($message, 500);
    }

    /**
     * Paginated response
     * @param array $data
     * @param int $currentPage
     * @param int $totalPages
     * @param int $totalItems
     * @param int $perPage
     * @return void
     */
    public static function paginated($data, $currentPage, $totalPages, $totalItems, $perPage)
    {
        $response = [
            'success' => true,
            'message' => 'Data retrieved successfully',
            'data' => $data,
            'pagination' => [
                'current_page' => (int)$currentPage,
                'total_pages' => (int)$totalPages,
                'total_items' => (int)$totalItems,
                'per_page' => (int)$perPage,
                'has_next' => $currentPage < $totalPages,
                'has_prev' => $currentPage > 1
            ],
            'timestamp' => date('c'),
            'status_code' => 200
        ];

        header('Content-Type: application/json');
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}
