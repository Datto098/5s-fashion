<?php
require_once 'app/core/Database.php';
require_once 'app/core/Model.php';
require_once 'app/models/Product.php';

try {
    // Test connecting to 5s_fashion database
    $conn = new PDO('mysql:host=localhost;dbname=5s_fashion', 'root', '');

    // Test simple query
    $result = $conn->query("SELECT id, name, status, featured FROM products LIMIT 3");
    echo "Sample products from 5s_fashion database:\n";
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: " . $row['id'] . " - Name: " . $row['name'] . " - Status: " . $row['status'] . " - Featured: " . $row['featured'] . "\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
