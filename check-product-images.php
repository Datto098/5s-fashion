<?php
require_once 'app/helpers/functions.php';
require_once 'app/config/database.php'; 
require_once 'app/core/Database.php';

try {
    $db = Database::getInstance();
    $products = $db->fetchAll('SELECT id, name, featured_image FROM products WHERE status = "published" AND featured_image IS NOT NULL LIMIT 3');
    
    echo "Products with images:\n";
    foreach ($products as $p) {
        echo "ID: {$p['id']}, Name: {$p['name']}, Image: {$p['featured_image']}\n";
        
        // Check if file exists
        $imagePath = "public/uploads/products/{$p['featured_image']}";
        if (file_exists($imagePath)) {
            echo "  ✅ File exists\n";
        } else {
            echo "  ❌ File missing\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>