<?php
require_once __DIR__ . '/../app/core/Database.php';

try {
    $pdo = Database::getInstance()->getConnection();

    $sql = "CREATE TABLE IF NOT EXISTS user_coupons (
        id int AUTO_INCREMENT PRIMARY KEY,
        user_id int NOT NULL,
        coupon_id int NOT NULL,
        saved_at timestamp DEFAULT CURRENT_TIMESTAMP,
        used_at timestamp NULL DEFAULT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE CASCADE,
        UNIQUE KEY unique_user_coupon (user_id, coupon_id)
    )";

    $pdo->exec($sql);
    echo "✅ Table user_coupons created successfully!";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
