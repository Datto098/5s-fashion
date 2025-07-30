<?php
require_once __DIR__ . '/../app/core/Database.php';

try {
    $db = Database::getInstance()->getConnection();

    echo "Dropping old variant tables...\n";

    $db->exec('SET FOREIGN_KEY_CHECKS = 0');
    $db->exec('DROP TABLE IF EXISTS product_variant_attributes');
    $db->exec('DROP TABLE IF EXISTS product_variants');
    $db->exec('DROP TABLE IF EXISTS product_attribute_values');
    $db->exec('DROP TABLE IF EXISTS product_attributes');
    $db->exec('SET FOREIGN_KEY_CHECKS = 1');

    echo "✓ Dropped old tables\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
