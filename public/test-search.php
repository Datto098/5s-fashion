<?php
// Simple test endpoint for search suggestions
header('Content-Type: application/json');

// Define constants
define('BASE_PATH', dirname(dirname(__FILE__)));
define('APP_PATH', BASE_PATH . '/app');

try {
    // Basic database connection test
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "zone_fashion";

    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = $_GET['q'] ?? '';
    
    if (empty(trim($query))) {
        echo json_encode(['suggestions' => []]);
        exit;
    }

    // Simple search query
    $sql = "SELECT id, name, slug, price, sale_price, featured_image 
            FROM products 
            WHERE status = 'published' 
            AND (name LIKE ? OR description LIKE ?) 
            LIMIT 6";
    
    $searchTerm = '%' . $query . '%';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$searchTerm, $searchTerm]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $suggestions = [];
    foreach ($products as $product) {
        $effectivePrice = ($product['sale_price'] > 0) ? $product['sale_price'] : $product['price'];
        $hasDiscount = $product['sale_price'] > 0 && $product['sale_price'] < $product['price'];
        $discountPercent = $hasDiscount ? round((($product['price'] - $effectivePrice) / $product['price']) * 100) : 0;

        $suggestions[] = [
            'id' => (int)$product['id'],
            'name' => $product['name'],
            'slug' => $product['slug'],
            'image' => $product['featured_image'],
            'price' => (int)$effectivePrice,
            'original_price' => (int)$product['price'],
            'has_discount' => $hasDiscount,
            'discount_percent' => $discountPercent,
            'category' => '',
            'url' => '/zone-fashion/product/' . $product['slug']
        ];
    }

    echo json_encode([
        'suggestions' => $suggestions,
        'total' => count($products),
        'query' => $query
    ]);

} catch (Exception $e) {
    echo json_encode([
        'error' => 'Đã xảy ra lỗi: ' . $e->getMessage(),
        'suggestions' => []
    ]);
}
?>