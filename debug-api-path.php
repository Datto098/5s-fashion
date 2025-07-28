<?php
header('Content-Type: application/json; charset=utf-8');

echo json_encode([
    'get_url' => $_GET['url'] ?? 'not set',
    'request_uri' => $_SERVER['REQUEST_URI'] ?? 'not set',
    'script_name' => $_SERVER['SCRIPT_NAME'] ?? 'not set',
    'all_get' => $_GET,
    'server_vars' => [
        'HTTP_HOST' => $_SERVER['HTTP_HOST'] ?? 'not set',
        'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? 'not set'
    ]
], JSON_UNESCAPED_UNICODE);
?>
