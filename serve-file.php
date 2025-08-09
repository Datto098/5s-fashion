<?php
// Simple file server for uploads - Debug version
$file = $_GET['file'] ?? '';

if (empty($file)) {
    http_response_code(404);
    exit('File not specified');
}

// Clean the file path
$file = ltrim($file, '/');

// Try multiple possible paths
$possiblePaths = [
    __DIR__ . '/public/uploads/' . $file,
    __DIR__ . '/uploads/' . $file,
    __DIR__ . '/public/' . $file
];

$filePath = null;
foreach ($possiblePaths as $path) {
    if (file_exists($path)) {
        $filePath = $path;
        break;
    }
}

// Debug information
error_log("serve-file.php Debug:");
error_log("Requested file: " . $file);
error_log("Trying paths:");
foreach ($possiblePaths as $i => $path) {
    error_log("  Path {$i}: " . $path . " - " . (file_exists($path) ? 'EXISTS' : 'NOT FOUND'));
}
error_log("Selected path: " . ($filePath ?? 'NONE'));
error_log("__DIR__: " . __DIR__);

// Check if file exists
if (!$filePath) {
    http_response_code(404);
    exit('File not found. Tried paths: ' . implode(', ', $possiblePaths));
}

// Security check - ensure file is within uploads directory
$allowedPaths = [
    realpath(__DIR__ . '/public/uploads/'),
    realpath(__DIR__ . '/uploads/'),
    realpath(__DIR__ . '/public/')
];

$fileRealPath = realpath($filePath);
$isAllowed = false;

foreach ($allowedPaths as $allowedPath) {
    if ($allowedPath && strpos($fileRealPath, $allowedPath) === 0) {
        $isAllowed = true;
        break;
    }
}

if (!$isAllowed) {
    http_response_code(403);
    exit('Access denied');
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
