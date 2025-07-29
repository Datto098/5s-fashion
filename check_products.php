<?php
$conn = new PDO('mysql:host=localhost;dbname=5s_fashion', 'root', '');

// Check if there are any products
$result = $conn->query("SELECT COUNT(*) as count FROM products");
$count = $result->fetch(PDO::FETCH_ASSOC);
echo "Total products: " . $count['count'] . "\n";

// Get first few products
$result = $conn->query("SELECT id, name, status FROM products LIMIT 3");
echo "Sample products:\n";
while($row = $result->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: " . $row['id'] . " - Name: " . $row['name'] . " - Status: " . $row['status'] . "\n";
}
?>
