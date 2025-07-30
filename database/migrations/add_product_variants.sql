-- Migration: Add Product Variants System
-- Created: 2025-07-30
-- Purpose: Add support for product variants (color, size, stock management)

-- Create attributes table (colors, sizes, materials, etc.)
CREATE TABLE IF NOT EXISTS `product_attributes` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create attribute values table (Đỏ, Xanh, M, L, XL, etc.)
CREATE TABLE IF NOT EXISTS `product_attribute_values` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create product variants table (combinations of attributes)
CREATE TABLE IF NOT EXISTS `product_variants` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create variant attributes mapping table (which attributes belong to which variant)
CREATE TABLE IF NOT EXISTS `product_variant_attributes` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add inventory tracking
CREATE TABLE IF NOT EXISTS `inventory_movements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `variant_id` int NOT NULL,
  `movement_type` enum('in','out','adjustment','reserved','released') COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `reference_type` enum('purchase','sale','adjustment','reservation') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` int DEFAULT NULL COMMENT 'ID của order, purchase order, etc.',
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `inventory_movements_variant_id_foreign` (`variant_id`),
  KEY `inventory_movements_type_index` (`movement_type`),
  KEY `inventory_movements_reference_index` (`reference_type`,`reference_id`),
  CONSTRAINT `inventory_movements_variant_id_foreign` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add basic color attributes
INSERT INTO `product_attributes` (`name`, `slug`, `type`, `description`, `sort_order`) VALUES
('Màu sắc', 'mau-sac', 'color', 'Màu sắc của sản phẩm', 1),
('Kích thước', 'kich-thuoc', 'size', 'Kích thước của sản phẩm', 2),
('Chất liệu', 'chat-lieu', 'material', 'Chất liệu sản phẩm', 3),
('Phong cách', 'phong-cach', 'style', 'Phong cách thiết kế', 4);

-- Add basic color values
INSERT INTO `product_attribute_values` (`attribute_id`, `value`, `slug`, `color_code`, `sort_order`) VALUES
(1, 'Đỏ', 'do', '#FF0000', 1),
(1, 'Xanh dương', 'xanh-duong', '#0000FF', 2),
(1, 'Xanh lá', 'xanh-la', '#00FF00', 3),
(1, 'Vàng', 'vang', '#FFFF00', 4),
(1, 'Cam', 'cam', '#FFA500', 5),
(1, 'Tím', 'tim', '#800080', 6),
(1, 'Hồng', 'hong', '#FFC0CB', 7),
(1, 'Đen', 'den', '#000000', 8),
(1, 'Trắng', 'trang', '#FFFFFF', 9),
(1, 'Xám', 'xam', '#808080', 10),
(1, 'Nâu', 'nau', '#A52A2A', 11);

-- Add basic size values
INSERT INTO `product_attribute_values` (`attribute_id`, `value`, `slug`, `sort_order`) VALUES
(2, 'XS', 'xs', 1),
(2, 'S', 's', 2),
(2, 'M', 'm', 3),
(2, 'L', 'l', 4),
(2, 'XL', 'xl', 5),
(2, 'XXL', 'xxl', 6),
(2, 'XXXL', 'xxxl', 7);

-- Add basic material values
INSERT INTO `product_attribute_values` (`attribute_id`, `value`, `slug`, `sort_order`) VALUES
(3, 'Cotton', 'cotton', 1),
(3, 'Polyester', 'polyester', 2),
(3, 'Len', 'len', 3),
(3, 'Da', 'da', 4),
(3, 'Lụa', 'lua', 5),
(3, 'Denim', 'denim', 6),
(3, 'Kaki', 'kaki', 7);

-- Update existing products table to support variants (optional fields)
-- Add columns for variant management
ALTER TABLE `products`
ADD COLUMN `has_variants` tinyint(1) DEFAULT '0' COMMENT 'Sản phẩm có variants hay không',
ADD COLUMN `manage_stock` tinyint(1) DEFAULT '1' COMMENT 'Quản lý tồn kho hay không',
ADD COLUMN `stock_quantity` int DEFAULT '0' COMMENT 'Tồn kho tổng (cho sản phẩm không có variants)',
ADD COLUMN `low_stock_threshold` int DEFAULT '5' COMMENT 'Ngưỡng cảnh báo hết hàng';

-- Update cart items table to support variants
ALTER TABLE `cart_items`
ADD COLUMN `variant_id` int DEFAULT NULL COMMENT 'ID của variant được chọn',
ADD KEY `cart_items_variant_id_foreign` (`variant_id`);

-- Update order items table to support variants
ALTER TABLE `order_items`
ADD COLUMN `variant_id` int DEFAULT NULL COMMENT 'ID của variant được đặt hàng',
ADD COLUMN `variant_sku` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'SKU variant tại thời điểm đặt hàng',
ADD COLUMN `variant_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tên variant tại thời điểm đặt hàng',
ADD KEY `order_items_variant_id_foreign` (`variant_id`);

-- Update wishlist table to support variants
ALTER TABLE `wishlist`
ADD COLUMN `variant_id` int DEFAULT NULL COMMENT 'ID của variant yêu thích',
ADD KEY `wishlist_variant_id_foreign` (`variant_id`);
