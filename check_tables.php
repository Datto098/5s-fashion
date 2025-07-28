<?php
$conn = new PDO('mysql:host=localhost;dbname=5s_fashion', 'root', '');
$result = $conn->query('SHOW TABLES');
echo "Tables in 5s_fashion:\n";
while($row = $result->fetch(PDO::FETCH_NUM)) {
    echo "- " . $row[0] . "\n";
}
?>
