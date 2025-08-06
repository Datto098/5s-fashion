<?php

require_once __DIR__ . '/../app/config/constants.php';
require_once __DIR__ . '/../app/helpers/JWT.php';

header('Content-Type: application/json; charset=utf-8');

// Get current user from JWT
$currentUser = JWT::getCurrentUser();

echo json_encode([
    'current_user' => $currentUser,
    'headers' => getallheaders(),
    'token_from_header' => $_SERVER['HTTP_AUTHORIZATION'] ?? null,
    'session' => $_SESSION ?? []
], JSON_PRETTY_PRINT);
