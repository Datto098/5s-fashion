<?php
/**
 * Simple Migration Script for Product Variants
 */

require_once __DIR__ . '/../app/core/Database.php';

try {
    $db = Database::getInstance()->getConnection();

    echo "Creating Product Variants Tables...\n";

    // Create product_attributes table
    $sql = "CREATE TABLE IF NOT EXISTS `product_attributes` (
        `id` int NOT NULL AUTO_INCREMENT,
        `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tên thuộc tính (Màu sắc, Kích thước, etc.)',
        `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
        `type` enum('color','size','material','style') COLLATE utf8mb4_unicode_ci NOT NULL,
        `description` text COLLATE utf8mb4_unicode_ci,
        `sort_order` int DEFAULT '0',
        `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `product_attributes_slug_unique` (`slug`),
        KEY `product_attributes_type_index` (`type`),
        KEY `product_attributes_status_index` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $db->exec($sql);
    echo "✓ Created product_attributes table\n";

    // Create product_attribute_values table
    $sql = "CREATE TABLE IF NOT EXISTS `product_attribute_values` (
        `id` int NOT NULL AUTO_INCREMENT,
        `attribute_id` int NOT NULL,
        `value` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Giá trị (Đỏ, XL, Cotton, etc.)',
        `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
        `color_code` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã màu hex cho thuộc tính màu sắc',
        `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Hình ảnh cho giá trị thuộc tính',
        `sort_order` int DEFAULT '0',
        `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `product_attribute_values_slug_unique` (`slug`),
        KEY `product_attribute_values_attribute_id_foreign` (`attribute_id`),
        KEY `product_attribute_values_status_index` (`status`),
        CONSTRAINT `product_attribute_values_attribute_id_foreign` FOREIGN KEY (`attribute_id`) REFERENCES `product_attributes` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $db->exec($sql);
    echo "✓ Created product_attribute_values table\n";

    // Create product_variants table
    $sql = "CREATE TABLE IF NOT EXISTS `product_variants` (
        `id` int NOT NULL AUTO_INCREMENT,
        `product_id` int NOT NULL,
        `sku` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'SKU riêng cho variant',
        `variant_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tên variant (Áo thun - Đỏ - XL)',
        `price` decimal(10,2) DEFAULT NULL COMMENT 'Giá riêng cho variant (null = dùng giá gốc)',
        `sale_price` decimal(10,2) DEFAULT NULL COMMENT 'Giá sale riêng cho variant',
        `cost_price` decimal(10,2) DEFAULT NULL COMMENT 'Giá vốn riêng cho variant',
        `stock_quantity` int NOT NULL DEFAULT '0' COMMENT 'Số lượng tồn kho',
        `reserved_quantity` int NOT NULL DEFAULT '0' COMMENT 'Số lượng đã đặt hàng nhưng chưa thanh toán',
        `weight` decimal(8,2) DEFAULT NULL,
        `dimensions` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Hình ảnh đại diện cho variant',
        `gallery` json DEFAULT NULL COMMENT 'Thư viện ảnh cho variant',
        `status` enum('active','inactive','out_of_stock') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
        `sort_order` int DEFAULT '0',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `product_variants_sku_unique` (`sku`),
        KEY `product_variants_product_id_foreign` (`product_id`),
        KEY `product_variants_status_index` (`status`),
        KEY `product_variants_stock_index` (`stock_quantity`),
        CONSTRAINT `product_variants_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $db->exec($sql);
    echo "✓ Created product_variants table\n";

    // Create product_variant_attributes table
    $sql = "CREATE TABLE IF NOT EXISTS `product_variant_attributes` (
        `id` int NOT NULL AUTO_INCREMENT,
        `variant_id` int NOT NULL,
        `attribute_value_id` int NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `product_variant_attributes_unique` (`variant_id`,`attribute_value_id`),
        KEY `product_variant_attributes_variant_id_foreign` (`variant_id`),
        KEY `product_variant_attributes_attribute_value_id_foreign` (`attribute_value_id`),
        CONSTRAINT `product_variant_attributes_variant_id_foreign` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE,
        CONSTRAINT `product_variant_attributes_attribute_value_id_foreign` FOREIGN KEY (`attribute_value_id`) REFERENCES `product_attribute_values` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $db->exec($sql);
    echo "✓ Created product_variant_attributes table\n";

    // Add columns to products table
    try {
        $sql = "ALTER TABLE `products`
                ADD COLUMN `has_variants` tinyint(1) DEFAULT '0' COMMENT 'Sản phẩm có variants hay không',
                ADD COLUMN `manage_stock` tinyint(1) DEFAULT '1' COMMENT 'Quản lý tồn kho hay không',
                ADD COLUMN `stock_quantity` int DEFAULT '0' COMMENT 'Tồn kho tổng (cho sản phẩm không có variants)',
                ADD COLUMN `low_stock_threshold` int DEFAULT '5' COMMENT 'Ngưỡng cảnh báo hết hàng'";
        $db->exec($sql);
        echo "✓ Added columns to products table\n";
    } catch (Exception $e) {
        echo "⚠️  Products table columns may already exist: " . $e->getMessage() . "\n";
    }

    echo "\n✅ All tables created successfully!\n";
    echo "\nInserting sample data...\n";

    // Insert basic attributes
    $attributes = [
        ['name' => 'Màu sắc', 'slug' => 'mau-sac', 'type' => 'color', 'sort_order' => 1],
        ['name' => 'Kích thước', 'slug' => 'kich-thuoc', 'type' => 'size', 'sort_order' => 2],
        ['name' => 'Chất liệu', 'slug' => 'chat-lieu', 'type' => 'material', 'sort_order' => 3],
    ];

    foreach ($attributes as $attr) {
        $sql = "INSERT IGNORE INTO product_attributes (`name`, slug, `type`, sort_order) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$attr['name'], $attr['slug'], $attr['type'], $attr['sort_order']]);
    }
    echo "✓ Inserted basic attributes\n";

    // Insert color values
    $colors = [
        ['attribute_id' => 1, 'value' => 'Đỏ', 'slug' => 'do', 'color_code' => '#FF0000', 'sort_order' => 1],
        ['attribute_id' => 1, 'value' => 'Xanh dương', 'slug' => 'xanh-duong', 'color_code' => '#0000FF', 'sort_order' => 2],
        ['attribute_id' => 1, 'value' => 'Xanh lá', 'slug' => 'xanh-la', 'color_code' => '#00FF00', 'sort_order' => 3],
        ['attribute_id' => 1, 'value' => 'Vàng', 'slug' => 'vang', 'color_code' => '#FFFF00', 'sort_order' => 4],
        ['attribute_id' => 1, 'value' => 'Đen', 'slug' => 'den', 'color_code' => '#000000', 'sort_order' => 5],
        ['attribute_id' => 1, 'value' => 'Trắng', 'slug' => 'trang', 'color_code' => '#FFFFFF', 'sort_order' => 6],
    ];

    foreach ($colors as $color) {
        $sql = "INSERT IGNORE INTO product_attribute_values (attribute_id, `value`, slug, color_code, sort_order) VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$color['attribute_id'], $color['value'], $color['slug'], $color['color_code'], $color['sort_order']]);
    }
    echo "✓ Inserted color values\n";

    // Insert size values
    $sizes = [
        ['attribute_id' => 2, 'value' => 'XS', 'slug' => 'xs', 'sort_order' => 1],
        ['attribute_id' => 2, 'value' => 'S', 'slug' => 's', 'sort_order' => 2],
        ['attribute_id' => 2, 'value' => 'M', 'slug' => 'm', 'sort_order' => 3],
        ['attribute_id' => 2, 'value' => 'L', 'slug' => 'l', 'sort_order' => 4],
        ['attribute_id' => 2, 'value' => 'XL', 'slug' => 'xl', 'sort_order' => 5],
        ['attribute_id' => 2, 'value' => 'XXL', 'slug' => 'xxl', 'sort_order' => 6],
    ];

    foreach ($sizes as $size) {
        $sql = "INSERT IGNORE INTO product_attribute_values (attribute_id, `value`, slug, sort_order) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$size['attribute_id'], $size['value'], $size['slug'], $size['sort_order']]);
    }
    echo "✓ Inserted size values\n";

    echo "\n🎉 Migration completed successfully!\n";
    echo "You can now create product variants in the admin panel.\n";

} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
