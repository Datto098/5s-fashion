<?php
header('Content-Type: application/json; charset=utf-8');

// Get raw input
$rawInput = file_get_contents('php://input');
echo json_encode([
    'raw_input' => $rawInput,
    'decoded' => json_decode($rawInput, true),
    'post' => $_POST,
    'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'not set'
], JSON_UNESCAPED_UNICODE);
?>
