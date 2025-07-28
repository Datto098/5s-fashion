<?php
try {
    $conn = new PDO('mysql:host=localhost', 'root', '');
    $conn->exec('USE 5s_fashion');

    // Describe products table
    $result = $conn->query('DESCRIBE products');
    echo "Structure of products table:\n";
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . " - " . $row['Type'] . " - " . $row['Null'] . " - " . $row['Default'] . "\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
