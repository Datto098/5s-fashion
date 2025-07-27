<?php
// Simple file server for uploads
$file = $_GET['file'] ?? '';

if (empty($file)) {
    http_response_code(404);
    exit('File not specified');
}

// Clean the file path
$file = ltrim($file, '/');
$filePath = __DIR__ . '/public/uploads/' . $file;

// Check if file exists and is within uploads directory
if (!file_exists($filePath) || strpos(realpath($filePath), realpath(__DIR__ . '/public/uploads/')) !== 0) {
    http_response_code(404);
    exit('File not found');
}

// Get file info
$fileInfo = pathinfo($filePath);
$extension = strtolower($fileInfo['extension']);

// Set appropriate content type
$contentTypes = [
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif',
    'webp' => 'image/webp'
];

$contentType = $contentTypes[$extension] ?? 'application/octet-stream';

// Output file
header('Content-Type: ' . $contentType);
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: public, max-age=31536000'); // Cache for 1 year

readfile($filePath);
?>
