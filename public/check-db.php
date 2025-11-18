<?php
// Quick database check
header('Content-Type: application/json');
define('BASE_PATH', dirname(dirname(__FILE__)));
define('APP_PATH', BASE_PATH . '/app');

try {
    // Database connection
    $servername = "localhost";
    $username = "root"; 
    $password = "";
    $dbname = "zone_fashion";

    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Count products
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM products");
    $stmt->execute();
    $totalProducts = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Get sample products
    $stmt = $pdo->prepare("SELECT id, name, status FROM products LIMIT 5");
    $stmt->execute();
    $sampleProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'total_products' => $totalProducts,
        'sample_products' => $sampleProducts
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>