<?php
$conn = new PDO('mysql:host=localhost;dbname=5s_fashion', 'root', '');

// Check categories table structure
$result = $conn->query("DESCRIBE categories");
echo "Categories table structure:\n";
while($row = $result->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}

// Check sample category data
echo "\nSample categories:\n";
$result = $conn->query("SELECT * FROM categories LIMIT 3");
while($row = $result->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: " . $row['id'] . " - Name: " . $row['name'];
    if (isset($row['slug'])) {
        echo " - Slug: " . $row['slug'];
    }
    echo "\n";
}
?>
