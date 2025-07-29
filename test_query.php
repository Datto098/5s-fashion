<?php
$conn = new PDO('mysql:host=localhost;dbname=5s_fashion', 'root', '');

// Test the exact query that was failing
$sql = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([1]); // Test with product ID 1
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
    echo "Query successful!\n";
    echo "Product: " . $result['name'] . "\n";
    echo "Category: " . ($result['category_name'] ?? 'No category') . "\n";
} else {
    echo "No product found with ID 1\n";
}
?>
