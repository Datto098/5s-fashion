<?php
$conn = new PDO('mysql:host=localhost;dbname=5s_fashion', 'root', '');
$result = $conn->query('SELECT id, name, featured FROM products WHERE featured = 1 LIMIT 3');
echo "Featured products:\n";
while($row = $result->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: " . $row['id'] . " - Name: " . $row['name'] . " - Featured: " . $row['featured'] . "\n";
}
?>
