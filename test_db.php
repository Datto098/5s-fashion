<?php
try {
    $conn = new PDO('mysql:host=localhost', 'root', '');

    // Check if 5s_fashion database exists
    $result = $conn->query("SHOW DATABASES LIKE '5s_fashion'");
    if ($result->rowCount() > 0) {
        echo "Database 5s_fashion exists\n";

        // Use the database
        $conn->exec('USE 5s_fashion');

        // Show tables
        $result = $conn->query('SHOW TABLES');
        echo "Tables in 5s_fashion:\n";
        while($row = $result->fetch(PDO::FETCH_NUM)) {
            echo "- " . $row[0] . "\n";
        }
    } else {
        echo "Database 5s_fashion does not exist\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
