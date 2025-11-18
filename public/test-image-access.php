<?php
// Test ảnh endpoint
header('Content-Type: application/json');

$testImages = [
    'webp' => '689710bd6913c_1754730685.webp',
    'jpg' => '68a0901e8f2a6_1755353118.jpg'
];

$results = [];

foreach ($testImages as $type => $filename) {
    $filepath = __DIR__ . '/uploads/products/' . $filename;
    $url = '/zone-fashion/uploads/products/' . $filename;
    
    $results[$type] = [
        'filename' => $filename,
        'file_exists' => file_exists($filepath),
        'file_size' => file_exists($filepath) ? filesize($filepath) : 0,
        'url' => $url,
        'full_path' => $filepath
    ];
}

echo json_encode([
    'results' => $results,
    'current_dir' => __DIR__,
    'uploads_dir' => __DIR__ . '/uploads/products/'
], JSON_PRETTY_PRINT);
?>