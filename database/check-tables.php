<?php
require_once __DIR__ . '/../app/core/Database.php';

try {
    $db = Database::getInstance()->getConnection();

    echo "Checking table structures...\n\n";

    // Check product_attributes
    echo "=== product_attributes ===\n";
    $result = $db->query('DESCRIBE product_attributes');
    foreach ($result as $row) {
        echo $row['Field'] . ' - ' . $row['Type'] . "\n";
    }

    echo "\n=== product_attribute_values ===\n";
    $result = $db->query('DESCRIBE product_attribute_values');
    foreach ($result as $row) {
        echo $row['Field'] . ' - ' . $row['Type'] . "\n";
    }

    echo "\nTesting insert...\n";
    $sql = "INSERT IGNORE INTO product_attributes (`name`, slug, `type`, sort_order) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($sql);
    $result = $stmt->execute(['Test', 'test', 'color', 1]);
    echo "Insert result: " . ($result ? 'Success' : 'Failed') . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
