<?php
require_once 'app/core/Database.php';

try {
    $db = Database::getInstance();

    // Test getFeaturedProducts query
    $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.status = 'published'
            AND p.featured = 1
            ORDER BY p.created_at DESC
            LIMIT 3";

    $products = $db->fetchAll($sql, []);

    echo "Featured products with category_slug:\n";
    foreach ($products as $product) {
        echo "ID: " . $product['id'] . "\n";
        echo "Name: " . $product['name'] . "\n";
        echo "Category: " . ($product['category_name'] ?? 'No category') . "\n";
        echo "Category Slug: " . ($product['category_slug'] ?? 'No slug') . "\n";
        echo "---\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
