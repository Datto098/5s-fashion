<?php
/**
 * Migration Script for Product Variants
 */

require_once __DIR__ . '/../app/core/Database.php';

try {
    $db = Database::getInstance();

    echo "Running Product Variants Migration...\n";

    // Read migration file
    $migrationFile = __DIR__ . '/migrations/add_product_variants.sql';
    if (!file_exists($migrationFile)) {
        throw new Exception("Migration file not found: $migrationFile");
    }

    $sql = file_get_contents($migrationFile);
    $statements = explode(';', $sql);

    $executed = 0;
    $errors = 0;

    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (empty($statement) || strpos($statement, '--') === 0) {
            continue;
        }

        try {
            $db->getConnection()->exec($statement);
            $executed++;
            echo "✓ Executed: " . substr($statement, 0, 50) . "...\n";
        } catch (Exception $e) {
            $errors++;
            echo "✗ Error: " . $e->getMessage() . "\n";
            echo "  Statement: " . substr($statement, 0, 100) . "...\n";
        }
    }

    echo "\nMigration Summary:\n";
    echo "- Executed: $executed statements\n";
    echo "- Errors: $errors statements\n";

    if ($errors === 0) {
        echo "\n✅ Migration completed successfully!\n";
    } else {
        echo "\n⚠️  Migration completed with errors. Please check the output above.\n";
    }

} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
