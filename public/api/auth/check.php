<?php
// Direct API endpoint for auth check
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Start session
session_start();

// Include helper functions
require_once __DIR__ . '/../app/helpers/functions.php';

try {
    if (isLoggedIn()) {
        $response = [
            'success' => true,
            'data' => [
                'authenticated' => true,
                'user' => [
                    'id' => $_SESSION['user']['id'],
                    'name' => $_SESSION['user']['name'],
                    'email' => $_SESSION['user']['email']
                ]
            ],
            'message' => 'User is authenticated',
            'timestamp' => date('c'),
            'status_code' => 200
        ];
    } else {
        $response = [
            'success' => true,
            'data' => [
                'authenticated' => false
            ],
            'message' => 'User is not authenticated',
            'timestamp' => date('c'),
            'status_code' => 200
        ];
    }

    echo json_encode($response);

} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => 'Failed to check authentication: ' . $e->getMessage(),
        'errors' => null,
        'timestamp' => date('c'),
        'status_code' => 500
    ];

    http_response_code(500);
    echo json_encode($response);
}
?>
