-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1:3306
-- Thời gian đã tạo: Th8 13, 2025 lúc 10:08 PM
-- Phiên bản máy phục vụ: 8.2.0
-- Phiên bản PHP: 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `5s_fashion`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `banners`
--

DROP TABLE IF EXISTS `banners`;
CREATE TABLE IF NOT EXISTS `banners` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtitle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_text` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` enum('hero','sidebar','footer','popup') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'hero',
  `sort_order` int DEFAULT '0',
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `start_date` timestamp NULL DEFAULT NULL,
  `end_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `banners_position_index` (`position`),
  KEY `banners_status_index` (`status`),
  KEY `banners_sort_order_index` (`sort_order`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `banners`
--

INSERT INTO `banners` (`id`, `title`, `subtitle`, `image`, `mobile_image`, `link_url`, `link_text`, `position`, `sort_order`, `status`, `start_date`, `end_date`, `created_at`, `updated_at`) VALUES
(1, 'Bộ Sưu Tập Mùa Hè 2025', 'Khám phá xu hướng thời trang mới nhất', 'banner-summer-2025.jpg', NULL, '/collections/summer-2025', 'Khám Phá Ngay', 'hero', 1, 'active', '2025-07-25 13:10:32', '2025-09-23 13:10:32', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(2, 'Sale Up To 50%', 'Giảm giá sốc cuối tháng', 'banner-sale-50.jpg', NULL, '/sale', 'Mua Ngay', 'hero', 2, 'active', '2025-07-25 13:10:32', '2025-08-09 13:10:32', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(3, 'Thời Trang Công Sở', 'Lịch lãm và chuyên nghiệp', 'banner-office-wear.jpg', NULL, '/categories/cong-so', 'Xem Thêm', 'hero', 3, 'active', '2025-07-25 13:10:32', '2025-08-24 13:10:32', '2025-07-25 13:10:32', '2025-07-25 13:10:32');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `brands`
--

DROP TABLE IF EXISTS `brands`;
CREATE TABLE IF NOT EXISTS `brands` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `brands_slug_unique` (`slug`),
  KEY `brands_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `brands`
--

INSERT INTO `brands` (`id`, `name`, `slug`, `description`, `logo`, `website`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Nike', 'nike', 'Thương hiệu thể thao hàng đầu thế giới', NULL, 'https://nike.com', 'active', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(2, 'Adidas', 'adidas', 'Thương hiệu thể thao nổi tiếng', NULL, 'https://adidas.com', 'active', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(3, 'Zara', 'zara', 'Thương hiệu thời trang nhanh từ Tây Ban Nha', NULL, 'https://zara.com', 'active', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(4, 'H&M', 'hm', 'Thương hiệu thời trang bình dân từ Thụy Điển', NULL, 'https://hm.com', 'active', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(5, 'Uniqlo', 'uniqlo', 'Thương hiệu thời trang Nhật Bản', NULL, 'https://uniqlo.com', 'active', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(6, 'Louis Vuitton', 'louis-vuitton', 'Thương hiệu luxury hàng đầu', NULL, 'https://louisvuitton.com', 'active', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(7, 'Gucci', 'gucci', 'Thương hiệu thời trang cao cấp từ Ý', NULL, 'https://gucci.com', 'active', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(8, 'Canifa', 'canifa', 'Thương hiệu thời trang Việt Nam', NULL, 'https://canifa.com', 'active', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(9, 'IVY Moda', 'ivy-moda', 'Thương hiệu thời trang công sở Việt Nam', NULL, 'https://ivymoda.com', 'active', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(10, 'Routine', 'routine', 'Thương hiệu thời trang trẻ Việt Nam', NULL, 'https://routine.vn', 'active', '2025-07-25 13:10:32', '2025-07-25 13:10:32');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `carts`
--

DROP TABLE IF EXISTS `carts`;
CREATE TABLE IF NOT EXISTS `carts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL COMMENT 'ID user (null nếu guest)',
  `session_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Session ID cho guest user',
  `product_id` int NOT NULL COMMENT 'ID sản phẩm',
  `variant_id` int DEFAULT NULL COMMENT 'ID variant (null nếu không có variant)',
  `quantity` int NOT NULL DEFAULT '1' COMMENT 'Số lượng',
  `price` decimal(10,2) NOT NULL COMMENT 'Giá tại thời điểm thêm vào cart',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_session_id` (`session_id`),
  KEY `idx_product_id` (`product_id`),
  KEY `idx_variant_id` (`variant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `carts`
--

INSERT INTO `carts` (`id`, `user_id`, `session_id`, `product_id`, `variant_id`, `quantity`, `price`, `created_at`, `updated_at`) VALUES
(67, 1018, 'cart_689c6be18c0a9_1755081697', 4, 21, 3, 599000.00, '2025-08-13 14:02:15', '2025-08-13 14:02:15'),
(68, 1018, 'cart_689c6be18c0a9_1755081697', 4, NULL, 1, 599000.00, '2025-08-13 16:04:00', '2025-08-13 16:04:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` int DEFAULT NULL,
  `sort_order` int DEFAULT '0',
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `meta_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`),
  KEY `categories_parent_id_foreign` (`parent_id`),
  KEY `categories_status_index` (`status`),
  KEY `categories_sort_order_index` (`sort_order`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `image`, `parent_id`, `sort_order`, `status`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(1, 'Thời Trang Nam', 'thoi-trang-nam', 'Tất cả sản phẩm thời trang dành cho nam giới', NULL, NULL, 1, 'active', 'Thời Trang Nam - 5S Fashion', 'Khám phá bộ sưu tập thời trang nam hiện đại và phong cách tại 5S Fashion', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(2, 'Thời Trang Nữ', 'thoi-trang-nu', 'Tất cả sản phẩm thời trang dành cho nữ giới', NULL, NULL, 2, 'active', 'Thời Trang Nữ - 5S Fashion', 'Bộ sưu tập thời trang nữ đa dạng và trendy tại 5S Fashion', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(3, 'Phụ Kiện', 'phu-kien', 'Các loại phụ kiện thời trang', 'uploads/categories/categories_6886486ae3810.webp', NULL, 3, 'active', 'Phụ Kiện Thời Trang - 5S Fashion', 'Phụ kiện thời trang chất lượng cao, hoàn thiện phong cách của bạn', '2025-07-25 13:10:32', '2025-07-27 15:40:26'),
(4, 'Giày Dép', 'giay-dep', 'Các loại giày dép thời trang', 'uploads/categories/categories_6886487a50f3e.webp', NULL, 4, 'active', 'Giày Dép Thời Trang - 5S Fashion', 'Bộ sưu tập giày dép đa dạng cho mọi phong cách', '2025-07-25 13:10:32', '2025-07-27 15:40:42'),
(5, 'Áo Thun Nam', 'ao-thun-nam', 'Áo thun nam các loại', 'uploads/categories/categories_688646e366d79.webp', 1, 1, 'active', 'Áo Thun Nam', 'Áo thun nam chất lượng, thiết kế đa dạng', '2025-07-25 13:10:32', '2025-07-27 15:33:55'),
(6, 'Áo Sơ Mi Nam', 'ao-so-mi-nam', 'Áo sơ mi nam công sở và casual', NULL, 1, 2, 'active', 'Áo Sơ Mi Nam', 'Áo sơ mi nam lịch lãm, phù hợp mọi dịp', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(7, 'Quần Jean Nam', 'quan-jean-nam', 'Quần jean nam nhiều kiểu dáng', NULL, 1, 3, 'active', 'Quần Jean Nam', 'Quần jean nam chất lượng, phong cách hiện đại', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(8, 'Quần Kaki Nam', 'quan-kaki-nam', 'Quần kaki nam lịch sự', NULL, 1, 4, 'active', 'Quần Kaki Nam', 'Quần kaki nam thanh lịch cho công sở', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(9, 'Áo Khoác Nam', 'ao-khoac-nam', 'Áo khoác nam đa dạng', NULL, 1, 5, 'active', 'Áo Khoác Nam', 'Áo khoác nam thời trang, giữ ấm hiệu quả', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(10, 'Áo Thun Nữ', 'ao-thun-nu', 'Áo thun nữ thời trang', 'uploads/categories/categories_6886485ee9aee.webp', 2, 1, 'active', 'Áo Thun Nữ', 'Áo thun nữ đa dạng mẫu mã, phong cách trẻ trung', '2025-07-25 13:10:32', '2025-07-27 15:40:14'),
(11, 'Áo Sơ Mi Nữ', 'ao-so-mi-nu', 'Áo sơ mi nữ công sở và casual', NULL, 2, 2, 'active', 'Áo Sơ Mi Nữ', 'Áo sơ mi nữ thanh lịch, phù hợp công sở', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(12, 'Chân Váy', 'chan-vay', 'Chân váy nhiều kiểu dáng', NULL, 2, 3, 'active', 'Chân Váy', 'Chân váy thời trang, làm nổi bật vẻ đẹp phụ nữ', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(13, 'Quần Jean Nữ', 'quan-jean-nu', 'Quần jean nữ trendy', NULL, 2, 4, 'active', 'Quần Jean Nữ', 'Quần jean nữ phong cách, tôn dáng', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(14, 'Đầm Nữ', 'dam-nu', 'Đầm nữ dự tiệc và casual', NULL, 2, 5, 'active', 'Đầm Nữ', 'Đầm nữ sang trọng cho mọi dịp đặc biệt', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(15, 'Túi Xách', 'tui-xach', 'Túi xách thời trang', NULL, 3, 1, 'active', 'Túi Xách', 'Túi xách chất lượng, thiết kế thời trang', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(16, 'Dây Nịt', 'day-nit', 'Dây nịt nam nữ', NULL, 3, 2, 'active', 'Dây Nịt', 'Dây nịt da thật, hoàn thiện phong cách', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(17, 'Mũ Nón', 'mu-non', 'Mũ nón thời trang', NULL, 3, 3, 'active', 'Mũ Nón', 'Mũ nón thời trang bảo vệ và tôn lên phong cách', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(18, 'Kính Mát', 'kinh-mat', 'Kính mát thời trang', NULL, 3, 4, 'active', 'Kính Mát', 'Kính mát thời trang, bảo vệ mắt hiệu quả', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(100, 'SEAL MẠNH', 'seal-manh', 'SEAL MẠNH', 'uploads/categories/categories_688648ef5bf16.webp', NULL, 0, 'active', 'SEAL MẠNH', 'SEAL MẠNH', '2025-07-27 15:42:39', '2025-07-27 15:42:39');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `coupons`
--

DROP TABLE IF EXISTS `coupons`;
CREATE TABLE IF NOT EXISTS `coupons` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `type` enum('percentage','fixed_amount') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `minimum_amount` decimal(10,2) DEFAULT NULL,
  `maximum_discount` decimal(10,2) DEFAULT NULL,
  `usage_limit` int DEFAULT NULL,
  `used_count` int DEFAULT '0',
  `user_limit` int DEFAULT NULL,
  `valid_from` timestamp NULL DEFAULT NULL,
  `valid_until` timestamp NULL DEFAULT NULL,
  `status` enum('active','inactive','expired') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `is_featured` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `coupons_code_unique` (`code`),
  KEY `coupons_status_index` (`status`),
  KEY `coupons_valid_from_index` (`valid_from`),
  KEY `coupons_valid_until_index` (`valid_until`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `name`, `description`, `type`, `value`, `minimum_amount`, `maximum_discount`, `usage_limit`, `used_count`, `user_limit`, `valid_from`, `valid_until`, `status`, `is_featured`, `created_at`, `updated_at`) VALUES
(1, 'WELCOME10', 'Chào mừng khách hàng mới', 'Giảm 10% cho đơn hàng đầu tiên', 'percentage', 10.00, 200000.00, NULL, 100, 0, NULL, '2025-07-25 13:10:32', '2025-08-24 13:10:32', 'active', 0, '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(2, 'FREESHIP', 'Miễn phí vận chuyển', 'Miễn phí ship cho đơn từ 500k', 'fixed_amount', 30000.00, 500000.00, NULL, 200, 0, NULL, '2025-07-25 13:10:32', '2025-07-08 13:10:32', 'active', 0, '2025-07-25 13:10:32', '2025-08-13 14:08:26'),
(3, 'SALE20', 'Giảm giá 20%', 'Giảm 20% cho đơn hàng trên 1 triệu', 'percentage', 20.00, 1000000.00, NULL, 50, 0, NULL, '2025-07-25 13:10:32', '2025-08-09 13:10:32', 'active', 0, '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(4, 'SUMMER50', 'Ưu đãi mùa hè', 'Giảm 50k cho đơn hàng mùa hè', 'fixed_amount', 50000.00, 300000.00, NULL, 150, 0, NULL, '2025-07-25 13:10:32', '2025-09-08 13:10:32', 'active', 0, '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(5, 'NEW11', 'NEW11', 'NEW11', 'percentage', 10.00, 10000.00, 20000.00, 10, 0, 1, '2025-07-28 06:13:00', '2025-07-30 06:13:00', 'active', 0, '2025-07-28 06:13:48', '2025-07-28 06:13:48'),
(6, 'WELCOME20', 'Giảm 20% cho khách mới', 'Chào m?ng b?n ??n v?i 5S Fashion! Gi?m 20% cho ??n hàng ??u tiên.', 'percentage', 20.00, 200000.00, 80000.00, 100, 0, NULL, '2025-07-28 06:29:57', '2025-08-27 06:29:57', 'active', 1, '2025-07-28 06:29:57', '2025-07-28 07:59:33'),
(7, 'SAVE50K', 'Gi?m 50.000?', 'Gi?m ngay 50.000? cho ??n hàng t? 400.000?', 'fixed_amount', 50000.00, 400000.00, NULL, 200, 0, NULL, '2025-07-28 06:30:13', '2025-08-27 06:30:13', 'active', 1, '2025-07-28 06:30:13', '2025-07-28 06:30:13');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `coupon_usage`
--

DROP TABLE IF EXISTS `coupon_usage`;
CREATE TABLE IF NOT EXISTS `coupon_usage` (
  `id` int NOT NULL AUTO_INCREMENT,
  `coupon_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `order_id` int NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `coupon_usage_coupon_id_foreign` (`coupon_id`),
  KEY `coupon_usage_user_id_foreign` (`user_id`),
  KEY `coupon_usage_order_id_foreign` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `customer_addresses`
--

DROP TABLE IF EXISTS `customer_addresses`;
CREATE TABLE IF NOT EXISTS `customer_addresses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_default` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `customer_addresses_user_id_foreign` (`user_id`),
  KEY `customer_addresses_is_default_index` (`is_default`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `customer_addresses`
--

INSERT INTO `customer_addresses` (`id`, `user_id`, `name`, `phone`, `address`, `is_default`, `created_at`, `updated_at`, `note`) VALUES
(2, 1002, 'Nguyễn Văn A', '0901234567', 'North Nugent Avenue, Johnson City, Blanco County, Texas, 78636, Hoa Kỳ', 0, '2025-07-25 13:10:32', '2025-08-03 07:54:50', ''),
(3, 1003, 'Trần Thị B', '0912345678', '789 Cách Mạng Tháng 8, Quận 10', 1, '2025-07-25 13:10:32', '2025-07-25 13:10:32', ''),
(4, 1004, 'Lê Văn C', '0923456789', '321 Nguyễn Trãi, Quận 5', 1, '2025-07-25 13:10:32', '2025-07-25 13:10:32', ''),
(9, 1002, 'Do Ngoc Hieu', '00000000003', '80, Phố Trần Phú, Điện Biên, Phường Ba Đình, Hà Nội, 10160, Việt Nam', 1, '2025-08-03 07:40:47', '2025-08-03 21:28:06', 'ljasdfb'),
(11, 1018, 'Do Ngoc', '03748646524', 'Bệnh viện Nhi Trung ương, 18, Ngõ 879 La Thành, Láng Thượng, Phường Láng, Hà Nội, 10080, Việt Nam', 0, '2025-08-04 19:03:00', '2025-08-11 06:50:42', 'd'),
(18, 1, 'Nguyễn Tiến Đạt', '0375099213', 'Địa chỉ cụ thể 1, Phường 2, Quận Hoàn Kiếm, Thành phố Hà Nội', 1, '2025-08-09 13:40:15', '2025-08-09 13:40:15', ''),
(19, 1019, 'Nguyen Tien Dat', '0375099213', 'Discovery Central, 67, Tran Phu Street, Điện Biên, Ba Đình District, Hà Nội, 10160, Vietnam', 1, '2025-08-10 07:57:55', '2025-08-10 07:57:55', ''),
(20, 1018, 'Do Ngoc Hieu', '0384946973', '131, Đường La Thành, Ô Chợ Dừa, Quận Đống Đa, Hà Nội, 10178, Việt Nam', 1, '2025-08-12 18:33:11', '2025-08-12 18:33:11', '');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `newsletter_subscriptions`
--

DROP TABLE IF EXISTS `newsletter_subscriptions`;
CREATE TABLE IF NOT EXISTS `newsletter_subscriptions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive','unsubscribed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `subscribed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `unsubscribed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `newsletter_subscriptions_email_unique` (`email`),
  KEY `newsletter_subscriptions_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `newsletter_subscriptions`
--

INSERT INTO `newsletter_subscriptions` (`id`, `email`, `name`, `status`, `subscribed_at`, `unsubscribed_at`) VALUES
(1, 'john.doe@email.com', 'John Doe', 'active', '2025-07-25 13:10:32', NULL),
(2, 'jane.smith@email.com', 'Jane Smith', 'active', '2025-07-25 13:10:32', NULL),
(3, 'peter.parker@email.com', 'Peter Parker', 'active', '2025-07-25 13:10:32', NULL),
(4, 'mary.jane@email.com', 'Mary Jane', 'active', '2025-07-25 13:10:32', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `order_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `shipping_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled','refunded') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_method` enum('cod','bank_transfer','vnpay','momo','zalopay','credit_card') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'cod',
  `payment_status` enum('pending','paid','failed','refunded') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `shipping_address` json NOT NULL,
  `billing_address` json DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `admin_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `shipped_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_code_unique` (`order_code`),
  KEY `orders_user_id_foreign` (`user_id`),
  KEY `orders_status_index` (`status`),
  KEY `orders_payment_status_index` (`payment_status`),
  KEY `orders_created_at_index` (`created_at`),
  KEY `orders_user_status_date` (`user_id`,`status`,`created_at`),
  KEY `orders_status_payment_date` (`status`,`payment_status`,`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=100107 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_code`, `customer_name`, `customer_email`, `customer_phone`, `subtotal`, `tax_amount`, `shipping_amount`, `discount_amount`, `total_amount`, `status`, `payment_method`, `payment_status`, `shipping_address`, `billing_address`, `notes`, `admin_notes`, `shipped_at`, `delivered_at`, `created_at`, `updated_at`) VALUES
(100095, 1019, 'ORD2508100697', 'Nguyen Tien Dat', 'dat@gmail.com', '0375099213', 599000.00, 0.00, 30000.00, 0.00, 629000.00, 'cancelled', 'cod', 'pending', '{\"name\": \"Nguyen Tien Dat\", \"phone\": \"0375099213\", \"address\": \"Discovery Central, 67, Tran Phu Street, Điện Biên, Ba Đình District, Hà Nội, 10160, Vietnam\"}', NULL, '', 'Khách hàng yêu cầu hủy', NULL, NULL, '2025-08-10 11:55:36', '2025-08-10 13:28:12'),
(100096, 1019, 'ORD2508102294', 'Nguyen Tien Dat', 'dat@gmail.com', '0375099213', 599000.00, 0.00, 30000.00, 0.00, 629000.00, 'processing', 'vnpay', 'paid', '{\"name\": \"Nguyen Tien Dat\", \"phone\": \"0375099213\", \"address\": \"Discovery Central, 67, Tran Phu Street, Điện Biên, Ba Đình District, Hà Nội, 10160, Vietnam\"}', NULL, '', NULL, NULL, NULL, '2025-08-10 11:56:14', '2025-08-10 11:56:48'),
(100097, 1019, 'ORD2508103133', 'Nguyen Tien Dat', 'dat@gmail.com', '0375099213', 729000.00, 0.00, 30000.00, 0.00, 759000.00, 'cancelled', 'cod', 'pending', '{\"name\": \"Nguyen Tien Dat\", \"phone\": \"0375099213\", \"address\": \"Discovery Central, 67, Tran Phu Street, Điện Biên, Ba Đình District, Hà Nội, 10160, Vietnam\"}', NULL, '', 'Khách hàng yêu cầu hủy', NULL, NULL, '2025-08-10 13:29:22', '2025-08-10 13:29:37'),
(100098, 1019, 'ORD2508102787', 'Nguyen Tien Dat', 'dat@gmail.com', '0375099213', 699000.00, 0.00, 30000.00, 0.00, 729000.00, 'cancelled', 'cod', 'pending', '{\"name\": \"Nguyen Tien Dat\", \"phone\": \"0375099213\", \"address\": \"Discovery Central, 67, Tran Phu Street, Điện Biên, Ba Đình District, Hà Nội, 10160, Vietnam\"}', NULL, '', 'Khách hàng yêu cầu hủy', NULL, NULL, '2025-08-10 13:30:26', '2025-08-10 13:33:00'),
(100099, 1019, 'ORD2508104506', 'Nguyen Tien Dat', 'dat@gmail.com', '0375099213', 559000.00, 0.00, 30000.00, 0.00, 589000.00, 'processing', 'cod', 'pending', '{\"name\": \"Nguyen Tien Dat\", \"phone\": \"0375099213\", \"address\": \"Discovery Central, 67, Tran Phu Street, Điện Biên, Ba Đình District, Hà Nội, 10160, Vietnam\"}', NULL, '', NULL, NULL, NULL, '2025-08-10 13:40:28', '2025-08-10 13:40:28'),
(100100, 1019, 'ORD2508104453', 'Nguyen Tien Dat', 'dat@gmail.com', '0375099213', 559000.00, 0.00, 30000.00, 0.00, 589000.00, 'delivered', 'cod', 'pending', '{\"name\": \"Nguyen Tien Dat\", \"phone\": \"0375099213\", \"address\": \"Discovery Central, 67, Tran Phu Street, Điện Biên, Ba Đình District, Hà Nội, 10160, Vietnam\"}', NULL, '', 'Cập nhật từ admin interface', '2025-08-10 13:59:54', '2025-08-10 14:01:14', '2025-08-10 13:42:14', '2025-08-10 14:01:14'),
(100101, 1018, 'ORD2508116038', 'Do Ngoc', 'dongochieu333@gmail.com', '03748646524', 2995000.00, 0.00, 30000.00, 0.00, 3025000.00, 'pending', 'vnpay', 'pending', '{\"name\": \"Do Ngoc\", \"phone\": \"03748646524\", \"address\": \"Bệnh viện Nhi Trung ương, 18, Ngõ 879 La Thành, Láng Thượng, Phường Láng, Hà Nội, 10080, Việt Nam\"}', NULL, '', NULL, NULL, NULL, '2025-08-11 06:46:25', '2025-08-11 06:46:25'),
(100102, 1018, 'ORD2508112403', 'Do Ngoc', 'dongochieu333@gmail.com', '03748646524', 2995000.00, 0.00, 30000.00, 0.00, 3025000.00, 'processing', 'cod', 'pending', '{\"name\": \"Do Ngoc\", \"phone\": \"03748646524\", \"address\": \"Bệnh viện Nhi Trung ương, 18, Ngõ 879 La Thành, Láng Thượng, Phường Láng, Hà Nội, 10080, Việt Nam\"}', NULL, '', NULL, NULL, NULL, '2025-08-11 06:46:33', '2025-08-11 06:46:33'),
(100103, 1018, 'ORD2508138169', 'Do Ngoc', 'dongochieu333@gmail.com', '03748646524', 1797000.00, 0.00, 30000.00, 0.00, 1827000.00, 'cancelled', 'cod', 'pending', '{\"name\": \"Do Ngoc\", \"phone\": \"03748646524\", \"address\": \"Bệnh viện Nhi Trung ương, 18, Ngõ 879 La Thành, Láng Thượng, Phường Láng, Hà Nội, 10080, Việt Nam\"}', NULL, '', 'Khách hàng yêu cầu hủy', NULL, NULL, '2025-08-12 19:13:25', '2025-08-12 19:13:45'),
(100104, 1018, 'ORD2508136514', 'Do Ngoc', 'dongochieu333@gmail.com', '03748646524', 2396000.00, 0.00, 30000.00, 0.00, 2426000.00, 'processing', 'cod', 'pending', '{\"name\": \"Do Ngoc\", \"phone\": \"03748646524\", \"address\": \"Bệnh viện Nhi Trung ương, 18, Ngõ 879 La Thành, Láng Thượng, Phường Láng, Hà Nội, 10080, Việt Nam\"}', NULL, '', NULL, NULL, NULL, '2025-08-13 11:28:24', '2025-08-13 11:28:24'),
(100105, 1018, 'ORD2508138896', 'Do Ngoc Hieu', 'dongochieu333@gmail.com', '0384946973', 2396000.00, 0.00, 30000.00, 239600.00, 2186400.00, 'processing', 'cod', 'pending', '{\"name\": \"Do Ngoc Hieu\", \"phone\": \"0384946973\", \"address\": \"131, Đường La Thành, Ô Chợ Dừa, Quận Đống Đa, Hà Nội, 10178, Việt Nam\"}', NULL, '', NULL, NULL, NULL, '2025-08-13 13:02:45', '2025-08-13 13:02:45'),
(100106, 1018, 'ORD2508132840', 'Do Ngoc Hieu', 'dongochieu333@gmail.com', '0384946973', 599000.00, 0.00, 30000.00, 50000.00, 579000.00, 'delivered', 'cod', 'pending', '{\"name\": \"Do Ngoc Hieu\", \"phone\": \"0384946973\", \"address\": \"131, Đường La Thành, Ô Chợ Dừa, Quận Đống Đa, Hà Nội, 10178, Việt Nam\"}', NULL, '', 'Cập nhật từ admin interface', NULL, '2025-08-13 16:33:46', '2025-08-13 13:31:56', '2025-08-13 16:33:46');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_items`
--

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `variant_id` int DEFAULT NULL,
  `product_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_sku` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `variant_info` json DEFAULT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  KEY `order_items_product_id_foreign` (`product_id`),
  KEY `order_items_variant_id_foreign` (`variant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `variant_id`, `product_name`, `product_sku`, `variant_info`, `quantity`, `price`, `total`, `created_at`, `updated_at`) VALUES
(84, 100095, 4, NULL, 'Váy Maxi Nữ Hoa Nhí', '', NULL, 1, 599000.00, 599000.00, '2025-08-10 11:55:36', '2025-08-10 11:55:36'),
(85, 100096, 4, NULL, 'Váy Maxi Nữ Hoa Nhí', '', NULL, 1, 599000.00, 599000.00, '2025-08-10 11:56:14', '2025-08-10 11:56:14'),
(86, 100097, 5, NULL, 'Quần Âu Nữ Ống Suông', '', NULL, 1, 729000.00, 729000.00, '2025-08-10 13:29:22', '2025-08-10 13:29:22'),
(87, 100098, 6, NULL, 'Áo Khoác Nam Bomber', '', NULL, 1, 699000.00, 699000.00, '2025-08-10 13:30:26', '2025-08-10 13:30:26'),
(88, 100099, 7, NULL, 'Đầm Nữ Midi Cổ V', '', NULL, 1, 559000.00, 559000.00, '2025-08-10 13:40:28', '2025-08-10 13:40:28'),
(89, 100100, 7, NULL, 'Đầm Nữ Midi Cổ V', '', NULL, 1, 559000.00, 559000.00, '2025-08-10 13:42:14', '2025-08-10 13:42:14'),
(90, 100101, 4, NULL, 'Váy Maxi Nữ Hoa Nhí', '', NULL, 5, 599000.00, 2995000.00, '2025-08-11 06:46:25', '2025-08-11 06:46:25'),
(91, 100102, 4, NULL, 'Váy Maxi Nữ Hoa Nhí', '', NULL, 5, 599000.00, 2995000.00, '2025-08-11 06:46:33', '2025-08-11 06:46:33'),
(92, 100103, 4, NULL, 'Váy Maxi Nữ Hoa Nhí', '', NULL, 3, 599000.00, 1797000.00, '2025-08-12 19:13:25', '2025-08-12 19:13:25'),
(93, 100104, 4, NULL, 'Váy Maxi Nữ Hoa Nhí', '', NULL, 4, 599000.00, 2396000.00, '2025-08-13 11:28:24', '2025-08-13 11:28:24'),
(94, 100105, 4, NULL, 'Váy Maxi Nữ Hoa Nhí', '', NULL, 4, 599000.00, 2396000.00, '2025-08-13 13:02:45', '2025-08-13 13:02:45'),
(95, 100106, 4, NULL, 'Váy Maxi Nữ Hoa Nhí', '', NULL, 1, 599000.00, 599000.00, '2025-08-13 13:31:56', '2025-08-13 13:31:56');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_logs`
--

DROP TABLE IF EXISTS `order_logs`;
CREATE TABLE IF NOT EXISTS `order_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `status_from` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_to` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `order_logs_order_id_foreign` (`order_id`),
  KEY `order_logs_created_by_foreign` (`created_by`),
  KEY `order_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `order_logs`
--

INSERT INTO `order_logs` (`id`, `order_id`, `status_from`, `status_to`, `notes`, `created_by`, `created_at`) VALUES
(26, 100100, 'processing', 'shipped', 'Cập nhật từ admin interface', 1, '2025-08-10 13:59:54'),
(27, 100100, 'shipped', 'delivered', 'Cập nhật từ admin interface', 1, '2025-08-10 14:00:07'),
(28, 100100, 'delivered', 'cancelled', 'Cập nhật từ admin interface', 1, '2025-08-10 14:00:15'),
(29, 100100, 'cancelled', 'delivered', 'Cập nhật từ admin interface', 1, '2025-08-10 14:01:14'),
(30, 100106, 'processing', 'delivered', 'Cập nhật từ admin interface', 1, '2025-08-13 16:33:46');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` timestamp NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `password_reset_tokens_email_index` (`email`),
  KEY `password_reset_tokens_token_index` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `posts`
--

DROP TABLE IF EXISTS `posts`;
CREATE TABLE IF NOT EXISTS `posts` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `thumbnail` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `author_id` int UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `status` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `author_id` (`author_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `posts`
--

INSERT INTO `posts` (`id`, `title`, `content`, `thumbnail`, `author_id`, `created_at`, `updated_at`, `status`) VALUES
(10, 'BÁNH BÔNG CẢI XANH', '<p><strong class=\"ql-size-large\">BÁNH BÔNG CẢI XANH Demo</strong></p><p><strong>Chuẩn bị: </strong>15 phút </p><p><strong>Nấu: </strong>1 giờ 25 phút</p><p><strong>Khẩu phần: </strong>4 người</p><p><br></p><p>❗️❗️❗️<span style=\"color: rgb(230, 0, 0);\">&nbsp;THÀNH PHẦN GÂY DỊ ỨNG&nbsp;❗️❗️❗️</span></p><p><span style=\"color: rgb(230, 0, 0);\">Công thức có thể chứa men, sulfit, gluten, lúa mì và trứng.</span></p><p><br></p><p><img src=\"data:image/png;base64,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\" alt=\"Bánh bông cải xanh chay\" height=\"321\" width=\"481\"></p><p><em>Bông cải xanh cực kỳ bổ dưỡng đóng vai trò trung tâm trong những chiếc bánh rán lành mạnh này, được phục vụ kèm roti, salad xanh và sữa chua.</em></p><p><br></p><p>Nguyên liệu (17)</p><ul><li>350g bông cải cắt nhỏ</li><li>1 chén đậu tằm (broad beans) đông lạnh, rã đông và bóc vỏ</li><li>1 tép tỏi, đập dập</li><li>3 cây hành lá, cắt nhỏ</li><li>2 muỗng canh lá bạc hà tươi băm nhỏ, thêm vài nhánh để trang trí</li><li>1 chén bột đậu gà (xem ghi chú)</li><li>½ thìa cà phê bột nở</li><li>1 thìa cà phê bột thì là xay</li><li>1 thìa cà phê bột ngò xay</li><li>1 quả trứng, đánh nhẹ</li><li>¼ chén dầu thực vật</li><li>4 quả dưa leo baby, thái lát mỏng</li><li>Roti nóng, để ăn kèm</li><li>Lá xà lách, để ăn kèm</li><li><strong>Sốt sữa chua xanh:</strong></li><li>½ chén sữa chua Hy Lạp không đường</li><li>1 chén lá bạc hà tươi</li><li>1 thìa cà phê nước cốt chanh</li></ul><p>Quy trình</p><p><strong>Bước 1</strong></p><p>– Cho 350 g bông cải xanh, 150 g đậu tằm, 2 tép tỏi, ½ củ hành tây và 1 nắm húng lủi vào máy xay thực phẩm. Nhấn, nhồi cho đến khi nguyên liệu được băm nhuyễn.</p><p>– Trong một tô lớn, trộn đều 1 chén (khoảng 125 g) bột mì, 1 muỗng cà phê bột nở, 1 muỗng cà phê thì là xay và 1 muỗng cà phê hạt ngò xay. Nêm ½ muỗng cà phê muối và chút tiêu.</p><p>– Tạo một “giếng” ở giữa hỗn hợp bột, đập vào 1 quả trứng và đổ ⅔ chén (≈160 ml) nước. Dùng phới lồng đánh đến khi bột mịn, không vón cục.</p><p>– Cho hỗn hợp bông cải đã xay vào tô bột, trộn đều cho đến khi quyện hẳn.</p><p><strong>Bước 2</strong></p><p>– Đun nóng dầu ăn trong chảo chống dính ở lửa vừa.</p><p>– Múc mỗi lần ¼ chén (≈60 ml) hỗn hợp bột cho lên chảo, nhẹ nhàng nén dẹt thành miếng tròn đường kính khoảng 6 cm. Làm lần lượt 3 miếng rồi rán mỗi mặt khoảng 2 phút hoặc đến khi vàng đều và chín.</p><p>– Vớt ra giấy thấm dầu. Tiếp tục rán phần bột còn lại để được tổng cộng 12 chiếc fritters.</p><p><strong>Bước 3</strong></p><p>– Trong khi chờ, làm “sữa chua xanh”: cho 200 g sữa chua không đường, 1 nắm húng lủi và 1 muỗng canh nước cốt chanh vào máy xay nhỏ. Xay đến khi hỗn hợp mịn, có màu xanh nhạt. Nêm ¼ muỗng cà phê muối và chút tiêu cho vừa ăn.</p><p><strong>Bước 4</strong></p><p>– Bày bánh bông cải xanh ra đĩa, rưới hoặc chấm kèm sữa chua xanh. Thêm dưa leo thái lát, roti (hoặc bánh mì), và rau salad theo ý thích. Trang trí thêm lá húng tươi trước khi thưởng thức.</p><p><strong>Recipe notes</strong></p><p>Make-ahead tips:</p><p>- Để đông: bọc giấy nến và màng bọc rồi đông lạnh.</p><p>- Rã đông: để ngăn mát 24h.</p><p>- Hâm nóng: nướng lại như hướng dẫn.</p>', '/uploads/posts/1755024158_tải xuống.png', 1, '2025-07-12 23:49:08', '2025-08-13 01:42:38', 1);
INSERT INTO `posts` (`id`, `title`, `content`, `thumbnail`, `author_id`, `created_at`, `updated_at`, `status`) VALUES
(18, 'Coppy New', '<h1>					“Cũ người mới ta” - 5 thương hiệu Việt nâng tầm khái niệm quần áo cũ</h1><p>																																																																			<span style=\"color: rgb(108, 117, 125);\">Apr 14, 2025</span></p><p><br></p><p><img src=\"https://stylerepublik.vn/uploads/2025/04/Untitled-design-2-1.png\" alt=\"“Cũ người mới ta” - 5 thương hiệu Việt nâng tầm khái niệm quần áo cũ&nbsp;\" width=\"728\" style=\"display: block; margin: auto;\"></p><p><br></p><p><em style=\"background-color: transparent;\">Trong bối cảnh bão hoà của bức tranh thời trang đồ cũ, @moon_me.studio, @Ngoaos_2hnd, @rewear.renew, @2.abnormal, @clavicule.vintage đem đến làn gió mới bằng những sáng tạo - mang theo âm hưởng của dòng chảy đương đại trên những món đồ bị lãng quên.&nbsp;</em></p><p><span style=\"background-color: transparent;\">Khi ngành công nghiệp thời trang tập trung đề cao tính&nbsp;</span><a href=\"https://stylerepublik.vn/tai-sao-thoi-trang-ben-vung-con-xa-xi-voi-nguoi-viet\" target=\"_blank\" style=\"background-color: transparent; color: blue;\">bền vững</a><span style=\"background-color: transparent;\">, thị phần quần áo&nbsp;</span><a href=\"https://stylerepublik.vn/khi-thoi-trang-second-hand-tro-thanh-moi-de-doa-tu-co-hoi-den-rac-o-viet-nam\" target=\"_blank\" style=\"background-color: transparent; color: blue;\">second-hand</a><span style=\"background-color: transparent;\">&nbsp;hay thời trang vintage tiếp tục bước vào thời kỳ hoàng kim. Tuy nhiên, ở thời khắc thời trang song hành với chủ nghĩa thoát ly, tôn vinh bản sắc, đã không có ít thương hiệu kinh doanh thời trang qua sử dụng xuất hiện. Họ “hồi sinh” những món đồ bị thời gian vùi lấp, cho chúng “sống” cuộc đời cùng dáng hình mới.</span></p><blockquote>Ở Việt Nam, không khó để tìm một tiệm hay một tài khoản trên mạng xã hội bán quần áo đã qua sử dụng, nhưng đó sẽ là một thách thức đối với những thương hiệu chuyên tái chế hay tái thiết kế lại đồ cũ.&nbsp;</blockquote><blockquote><br></blockquote><p><img src=\"https://stylerepublik.vn/uploads/2025/04/Untitled-design-2-1.png\" alt=\"Hinh anh “Cũ người mới ta” - 5 thương hiệu Việt nâng tầm khái niệm quần áo cũ&nbsp; 1\" height=\"1000\" width=\"864\" style=\"display: block; margin: auto;\"></p><p><br></p><p><span style=\"background-color: transparent;\">Rework hay recycle không còn là thuật ngữ xa lạ trong quyển từ điển thời trang vintage hoặc quần áo second-hand; nhưng nó chưa phổ biến ở Việt Nam. Trong bối cảnh đang bão hoà của “sân chơi” này - khi nhiều thương hiệu bán đồ cũ “mọc lên như nấm”,&nbsp;@moon_me.studio, @Ngoaos_2hnd, @rewear.renew, @2.abnormal, và @clavicule.vintage là những cái tên nổi bật, góp thêm phần sôi động. Với tư duy thời trang bứt phá, họ biến những thiết kế đã bị lãng quên thành các sáng tạo mới, thời thượng không kém gì những kiểu dáng đang thịnh hành.&nbsp;</span></p><h2>2.abnormal</h2><p><span style=\"background-color: transparent;\">Sở hữu gần 150 nghìn người theo dõi trên Instagram,&nbsp;</span><em style=\"background-color: transparent; color: inherit;\">2.abnormal</em><span style=\"background-color: transparent;\">&nbsp;có lẽ là một “điểm đến” quá đỗi quen thuộc đối với những tín đồ Việt yêu thích thời trang vintage. Vũ trụ mang tên</span><em style=\"background-color: transparent; color: inherit;\">&nbsp;2.abnormal</em><span style=\"background-color: transparent;\">&nbsp;thu hút với nét thẩm mỹ hoài niệm, sang trọng và nữ tính; tất cả toát ra từ những chiếc corset, đầm ngủ lụa, cũng như hàng loạt món trang sức tái sinh từ nhiều thế kỷ trước. Bên cạnh những món đồ cũ được tuyển chọn cẩn thận, @2.abnormal còn nổi tiếng với những thiết kế thời trang được làm lại từ nhiều món đồ cũ: một chiếc áo sweater bằng len có thể trở một set đồ co-ord gồm áo croptop và mini skirt - vừa ấm áp cho khoảnh khắc giao mùa từ Hè sang tTu, vừa năng động trùng khớp với tinh thần của ngày Hạ. Đối với bạn, một chiếc áo sơ mi mặc một lần đã có thể bị bỏ quên trong một góc tủ; nhưng ở vũ trụ sáng tạo của&nbsp;</span><em style=\"background-color: transparent; color: inherit;\">2.abnormal</em><span style=\"background-color: transparent;\">&nbsp;nó sẽ được hóa phép thành một set đồ gồm áo yếm và váy ngắn xòe thời thượng không kém gì các thiết kế được “</span><a href=\"https://stylerepublik.vn/lam-chu-mua-he-nhu-it-girl-chinh-hieu-bang-4-cach-chinh-phuc-hot-pants\" target=\"_blank\" style=\"background-color: transparent; color: blue;\">It-Girl</a><span style=\"background-color: transparent;\">” ưa chuộng.&nbsp;</span></p><p><span style=\"background-color: transparent;\"><span class=\"ql-cursor\">﻿</span></span></p><iframe class=\"ql-video\" frameborder=\"0\" allowfullscreen=\"true\" src=\"https://www.instagram.com/p/DFzzPNLPnsC/embed/captioned/?cr=1&amp;v=14&amp;wp=675&amp;rd=https%3A%2F%2Fstylerepublik.vn&amp;rp=%2Fcu-nguoi-moi-ta-5-thuong-hieu-viet-nang-tam-khai-niem-quan-ao-cu#%7B%22ci%22%3A0%2C%22os%22%3A2138.5%2C%22ls%22%3A497.5%2C%22le%22%3A1741.800000011921%7D\" height=\"1023\"></iframe><p><img src=\"https://stylerepublik.vn/static/assets/images/common/img-fallback.png\" alt=\"Hinh anh 2.abnormal 1\" height=\"1080\" width=\"1920\"></p><p><img src=\"https://stylerepublik.vn/static/assets/images/common/img-fallback.png\" alt=\"Hinh anh 2.abnormal 2\" height=\"1080\" width=\"1920\"></p><p><img src=\"https://stylerepublik.vn/static/assets/images/common/img-fallback.png\" alt=\"Hinh anh 2.abnormal 3\" height=\"1080\" width=\"1920\"></p><h2>clavicule.vintage</h2><p><span style=\"background-color: transparent;\">Với lượt theo dõi gần 50 nghìn người trên Instagram,&nbsp;</span><em style=\"background-color: transparent; color: inherit;\">clavicule.vintage</em><span style=\"background-color: transparent;\">&nbsp;khẳng định “tiếng nói” sáng tạo mạnh mẽ của mình qua những món hàng hiệu qua sử dụng được tuyển chọn. Tuy là thương hiệu bán đồ cũ,&nbsp;</span><em style=\"background-color: transparent; color: inherit;\">clavicule.vintage</em><span style=\"background-color: transparent;\">&nbsp;gây ấn tượng bằng cách tạo độ nhận diện với&nbsp;hình ảnh chỉn chu và có màu sắc riêng.&nbsp;Bên cạnh chất lượng quần áo, điều níu chân khách hàng ở lại&nbsp;</span><em style=\"background-color: transparent; color: inherit;\">clavicule.vintage</em><span style=\"background-color: transparent;\">&nbsp;là những tác phẩm reworked hay được tái chế từ các nguyên tác có sẵn. Từ những chiếc áo sơ mi cũ thanh lịch của Ralph Lauren, Lacoste, Tommy Hilfiger,...cho đến nhiều kiểu dáng thể thao của&nbsp;</span><a href=\"https://stylerepublik.vn/nike-skechers-nin-tho-theo-doi-muc-thue-quan-my-ap-len-viet-nam\" target=\"_blank\" style=\"background-color: transparent; color: blue;\">Nike</a><span style=\"background-color: transparent;\">&nbsp;và adidas,&nbsp;</span><em style=\"background-color: transparent; color: inherit;\">clavicule.vintage</em><span style=\"background-color: transparent;\">&nbsp;biến thành những chiếc corset 1-0-2, hoặc những thiết kế áo điệu đà và \"trendy\".</span></p><iframe class=\"ql-video\" frameborder=\"0\" allowfullscreen=\"true\" src=\"https://www.instagram.com/reel/DHlXWPjPEm0/embed/captioned/?cr=1&amp;v=14&amp;wp=675&amp;rd=https%3A%2F%2Fstylerepublik.vn&amp;rp=%2Fcu-nguoi-moi-ta-5-thuong-hieu-viet-nang-tam-khai-niem-quan-ao-cu#%7B%22ci%22%3A1%2C%22os%22%3A2152.5%2C%22ls%22%3A497.5%2C%22le%22%3A1741.800000011921%7D\" height=\"981\"></iframe><p><br></p><p><br></p><p><a href=\"https://stylerepublik.vn/uploads/2025/04/11-5.png\" target=\"_blank\" style=\"color: blue;\"><img src=\"https://stylerepublik.vn/static/assets/images/common/img-fallback.png\" alt=\"11\"></a></p><p><br></p><p><br></p><p><br></p><p><br></p><p><a href=\"https://stylerepublik.vn/uploads/2025/04/12-4.png\" target=\"_blank\" style=\"color: blue;\"><img src=\"https://stylerepublik.vn/static/assets/images/common/img-fallback.png\" alt=\"12\"></a></p><p><br></p><p><br></p><p><img src=\"https://stylerepublik.vn/static/assets/images/common/img-fallback.png\" alt=\"Hinh anh clavicule.vintage 1\" height=\"1080\" width=\"1920\"></p><h2>rewear.renew</h2><p><span style=\"background-color: transparent;\">Khác biệt hoàn toàn với những tiệm bán quần áo cũ sở hữu nhiều đa dạng hạng mục,&nbsp;</span><em style=\"background-color: transparent; color: inherit;\">rewear.renew</em><span style=\"background-color: transparent;\">&nbsp;là thế giới riêng và mơ mộng của những thước vải tulle. Là một thương hiệu đồ cũ chuyên về một chất liệu cụ thể, ắt hẳn&nbsp;</span><em style=\"background-color: transparent; color: inherit;\">rewear.renew</em><span style=\"background-color: transparent;\">&nbsp;đã phải đối mặt với nhiều thách thức để tồn tại trên thị trường. Để sở hữu độ nhận diện như hiện tại,&nbsp;</span><em style=\"background-color: transparent; color: inherit;\">rewear.renew</em><span style=\"background-color: transparent;\">&nbsp;đã mang đến hàng loạt kiểu dáng sáng tạo khó đoán bằng vải tulle, thay vì chỉ “gói gọn” trong những chiếc váy xoè&nbsp;</span><a href=\"https://stylerepublik.vn/5-thuong-hieu-balletcore\" target=\"_blank\" style=\"background-color: transparent; color: blue;\">balletcore</a><span style=\"background-color: transparent;\">&nbsp;quá đỗi quen thuộc với chúng ta. Từ những mảnh vải đã cũ,&nbsp;</span><em style=\"background-color: transparent; color: inherit;\">rewear.renew</em><span style=\"background-color: transparent;\">&nbsp;“dệt” thành nhiều phom dáng trong một bộ sưu tập hoàn chỉnh gồm những chiếc đầm dạ hội may đo tỉ mỉ, đan cài với các phom dáng mang tính ứng dụng cao. Không còn được cho là sến sẩm như trước đây,&nbsp;&nbsp;</span><em style=\"background-color: transparent; color: inherit;\">rewear.renew</em><span style=\"background-color: transparent;\">&nbsp;đem vải tulle vào ngôn ngữ thời trang hiện đại cùng khả năng thích nghi với đa dạng kiểu phong cách.&nbsp;</span></p><iframe class=\"ql-video\" frameborder=\"0\" allowfullscreen=\"true\" src=\"https://www.instagram.com/reel/DD3sWnWvwu_/embed/captioned/?cr=1&amp;v=14&amp;wp=675&amp;rd=https%3A%2F%2Fstylerepublik.vn&amp;rp=%2Fcu-nguoi-moi-ta-5-thuong-hieu-viet-nang-tam-khai-niem-quan-ao-cu#%7B%22ci%22%3A2%2C%22os%22%3A2161.199999988079%2C%22ls%22%3A497.5%2C%22le%22%3A1741.800000011921%7D\" height=\"945\"></iframe><p><br></p><p><br></p><p><a href=\"https://stylerepublik.vn/uploads/2025/04/2-10.png\" target=\"_blank\" style=\"color: blue;\"><img src=\"https://stylerepublik.vn/static/assets/images/common/img-fallback.png\" alt=\"2\"></a></p><p><br></p><p><br></p><p><br></p><p><br></p><p><a href=\"https://stylerepublik.vn/uploads/2025/04/9-4.png\" target=\"_blank\" style=\"color: blue;\"><img src=\"https://stylerepublik.vn/static/assets/images/common/img-fallback.png\" alt=\"9\"></a></p><p><br></p><p><br></p><p><img src=\"https://stylerepublik.vn/static/assets/images/common/img-fallback.png\" alt=\"Hinh anh rewear.renew 1\" height=\"1080\" width=\"1920\"></p><h2>moon_me.studio</h2><p><span style=\"background-color: transparent;\">Với tinh thần hưởng ứng xu hướng nhạy bén,&nbsp;</span><em style=\"background-color: transparent; color: inherit;\">moon_me.studio</em><span style=\"background-color: transparent;\">&nbsp;nổi tiếng đem những món đồ cũ bắt kịp nhịp độ của cuộc đua thời trang đương đại. Không kém cạnh những xưởng sản xuất thời trang nhanh,&nbsp;</span><em style=\"background-color: transparent; color: inherit;\">moon_me.studio</em><span style=\"background-color: transparent;\">&nbsp;gây ấn tượng với khả năng “bắt trend” thần tốc nhưng điểm cộng ở đây là sự thân thiện trong chất liệu. Ở&nbsp;</span><em style=\"background-color: transparent; color: inherit;\">moon_me.studio</em><span style=\"background-color: transparent;\">, những chiếc áo jersey adidas, Puma hoặc Nike đã cũ hay kém thu hút sẽ có thể biến thành các chiếc corset sành điệu, đầm hoặc chân váy bí “trendy”. Vừa khuyến khích người mua hưởng ứng phong trào bảo vệ môi trường bằng việc mua quần áo cũ,&nbsp;</span><em style=\"background-color: transparent; color: inherit;\">moon_me.studio</em><span style=\"background-color: transparent;\">&nbsp;vừa tạo cơ hội cho khách hàng khám phá ra một cuộc sống hoàn toàn mới của những món đồ từng bị lãng quên. Bởi lẽ, ở đây, một chiếc áo thun jersey thể thao không chỉ dừng lại ở cách mặc truyền thống trong sách vở mà nó có thể được “sử dụng” theo một cách khác, chẳng hạn khi nó được may đo thành chiếc đầm trẻ trung hay một corset quyến rũ. Từ đó, giới hạn thời trang đã được mở rộng.&nbsp;</span></p><iframe class=\"ql-video\" frameborder=\"0\" allowfullscreen=\"true\" src=\"https://www.instagram.com/reel/DIEZoyqtzJ8/embed/captioned/?cr=1&amp;v=14&amp;wp=675&amp;rd=https%3A%2F%2Fstylerepublik.vn&amp;rp=%2Fcu-nguoi-moi-ta-5-thuong-hieu-viet-nang-tam-khai-niem-quan-ao-cu#%7B%22ci%22%3A3%2C%22os%22%3A4995.600000023842%2C%22ls%22%3A497.5%2C%22le%22%3A1741.800000011921%7D\" height=\"999\"></iframe><p><img src=\"https://stylerepublik.vn/static/assets/images/common/img-fallback.png\" alt=\"Hinh anh moon_me.studio 1\" height=\"1080\" width=\"1920\"></p><iframe class=\"ql-video\" frameborder=\"0\" allowfullscreen=\"true\" src=\"https://www.instagram.com/reel/DHqpDh9N3T5/embed/captioned/?cr=1&amp;v=14&amp;wp=675&amp;rd=https%3A%2F%2Fstylerepublik.vn&amp;rp=%2Fcu-nguoi-moi-ta-5-thuong-hieu-viet-nang-tam-khai-niem-quan-ao-cu#%7B%22ci%22%3A4%2C%22os%22%3A5013.600000023842%2C%22ls%22%3A497.5%2C%22le%22%3A1741.800000011921%7D\" height=\"981\"></iframe><p><br></p><p><br></p><p><a href=\"https://stylerepublik.vn/uploads/2025/04/Snapins.ai_485927996_18044254148594457_4554719122563677924_n_1080.jpg\" target=\"_blank\" bis_size=\"{&quot;x&quot;:325,&quot;y&quot;:-4469,&quot;w&quot;:1080,&quot;h&quot;:14,&quot;abs_x&quot;:325,&quot;abs_y&quot;:-4469}\" style=\"color: blue;\"><img src=\"https://stylerepublik.vn/uploads/2025/04/Snapins.ai_485927996_18044254148594457_4554719122563677924_n_1080.jpg\" alt=\"Snapins.ai_485927996_18044254148594457_4554719122563677924_n_1080\" bis_size=\"{&quot;x&quot;:325,&quot;y&quot;:-5000,&quot;w&quot;:1080,&quot;h&quot;:1080,&quot;abs_x&quot;:325,&quot;abs_y&quot;:-5000}\" bis_id=\"bn_87u9b43vkqy21qo7poho6m\"></a></p><p><br></p><p><br></p><p><br></p><p><br></p><p><a href=\"https://stylerepublik.vn/uploads/2025/04/Snapins.ai_485089616_18044254166594457_8599664919930782926_n_1080.jpg\" target=\"_blank\" bis_size=\"{&quot;x&quot;:325,&quot;y&quot;:-4377,&quot;w&quot;:1080,&quot;h&quot;:14,&quot;abs_x&quot;:325,&quot;abs_y&quot;:-4377}\" style=\"color: blue;\"><img src=\"https://stylerepublik.vn/uploads/2025/04/Snapins.ai_485089616_18044254166594457_8599664919930782926_n_1080.jpg\" alt=\"Snapins.ai_485089616_18044254166594457_8599664919930782926_n_1080\" bis_size=\"{&quot;x&quot;:325,&quot;y&quot;:-4908,&quot;w&quot;:1080,&quot;h&quot;:1080,&quot;abs_x&quot;:325,&quot;abs_y&quot;:-4908}\" bis_id=\"bn_c70ju0jr9i6sqm4ykn4qto\"></a></p><p><br></p><p><br></p><p><br></p><p><br></p><p><a href=\"https://stylerepublik.vn/uploads/2025/04/Snapins.ai_490412693_18046580672594457_4864660881531251613_n_1080.jpg\" target=\"_blank\" style=\"color: blue;\"><img src=\"https://stylerepublik.vn/uploads/2025/04/Snapins.ai_490412693_18046580672594457_4864660881531251613_n_1080.jpg\" alt=\"Snapins.ai_490412693_18046580672594457_4864660881531251613_n_1080\"></a></p><p><br></p><p><br></p><p><br></p><p><br></p><p><a href=\"https://stylerepublik.vn/uploads/2025/04/Snapins.ai_484918053_18043728578594457_4980099216326314940_n_1080.jpg\" target=\"_blank\" style=\"color: blue;\"><img src=\"https://stylerepublik.vn/uploads/2025/04/Snapins.ai_484918053_18043728578594457_4980099216326314940_n_1080.jpg\" alt=\"Snapins.ai_484918053_18043728578594457_4980099216326314940_n_1080\"></a></p><p><br></p><p><br></p><h2>Ngoaos_2hnd</h2><p><span style=\"background-color: transparent;\">Là một thương hiệu quần áo cũ, ở&nbsp;</span><em style=\"background-color: transparent; color: inherit;\">Ngoaos_2hnd</em><span style=\"background-color: transparent;\">&nbsp;khái niệm “đã qua sử dụng” không nằm trong từ điển sáng tạo. Bởi lẽ, sự tồn tại của những món đồ cũ đã bị thay thế hoàn toàn bởi các kiểu dáng mới - sáng tạo đến mức không thể nhận ra. Những chiếc quần jeans lỗi thời được rã cấu trúc, rồi hoá thân thành chiếc váy bí đang được giới trẻ yêu thích, hoặc một chiếc đầm cổ yếm nữ tính. Một chiếc áo sơ mi hay váy jeans quá khổ - không ai mặc vừa trong phút chốc đã trở thành một set đồ gồm áo croptop và váy bất đối xứng. Điều khiến&nbsp;</span><em style=\"background-color: transparent; color: inherit;\">Ngoaos_2hnd</em><span style=\"background-color: transparent;\">&nbsp;trở nên khác biệt so với các thương hiệu rework/ recycle tương tự chính là đường nét thiết kế phức tạp, mang tính độc bản rất cao - mà bạn không dễ kiếm trên thị trường.&nbsp;</span><em style=\"background-color: transparent; color: inherit;\">Ngoaos_2hnd</em><span style=\"background-color: transparent;\">&nbsp;cũng gây ấn tượng với khả năng thích nghi độc đáo; chẳng hạn, vào những dịp quan trọng như Tết Nguyên Đán, thương hiệu “lấy lòng” bằng những chiếc áo dài được tái kiến tạo bằng quần áo cũ.&nbsp;</span></p><iframe class=\"ql-video\" frameborder=\"0\" allowfullscreen=\"true\" src=\"https://www.instagram.com/reel/DH42N9kpFsg/embed/captioned/?cr=1&amp;v=14&amp;wp=675&amp;rd=https%3A%2F%2Fstylerepublik.vn&amp;rp=%2Fcu-nguoi-moi-ta-5-thuong-hieu-viet-nang-tam-khai-niem-quan-ao-cu#%7B%22ci%22%3A5%2C%22os%22%3A5031.100000023842%2C%22ls%22%3A497.5%2C%22le%22%3A1741.800000011921%7D\" height=\"1023\"></iframe><p><img src=\"https://stylerepublik.vn/static/assets/images/common/img-fallback.png\" alt=\"Hinh anh Ngoaos_2hnd 1\" height=\"1080\" width=\"1920\"></p><p><img src=\"https://stylerepublik.vn/static/assets/images/common/img-fallback.png\" alt=\"Hinh anh Ngoaos_2hnd 2\" height=\"1080\" width=\"1920\"></p><p><span style=\"background-color: rgb(255, 255, 255);\"><img src=\"https://stylerepublik.vn/uploads/2025/04/6-6.png\" alt=\"Hinh anh Ngoaos_2hnd 3\" height=\"1080\" width=\"1920\"></span></p><p><br></p>', '/uploads/posts/1755024501_6-6.png', 1, '2025-08-13 01:48:21', '2025-08-13 01:48:21', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) NOT NULL,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `cost_price` decimal(10,2) DEFAULT NULL,
  `category_id` int NOT NULL,
  `brand_id` int DEFAULT NULL,
  `featured_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gallery` json DEFAULT NULL,
  `status` enum('draft','published','out_of_stock') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'draft',
  `featured` tinyint(1) DEFAULT '0',
  `weight` decimal(8,2) DEFAULT NULL,
  `dimensions` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `material` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `care_instructions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `gender` enum('nam','nu','unisex') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'unisex',
  `season` enum('spring','summer','fall','winter','all_season') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'all_season',
  `style` enum('casual','formal','sport','vintage','modern') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'casual',
  `meta_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `views` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `has_variants` tinyint(1) DEFAULT '0' COMMENT 'Sản phẩm có variants hay không',
  `manage_stock` tinyint(1) DEFAULT '1' COMMENT 'Quản lý tồn kho hay không',
  `stock_quantity` int DEFAULT '0' COMMENT 'Tồn kho tổng (cho sản phẩm không có variants)',
  `low_stock_threshold` int DEFAULT '5' COMMENT 'Ngưỡng cảnh báo hết hàng',
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_slug_unique` (`slug`),
  UNIQUE KEY `products_sku_unique` (`sku`),
  KEY `products_category_id_foreign` (`category_id`),
  KEY `products_brand_id_foreign` (`brand_id`),
  KEY `products_status_index` (`status`),
  KEY `products_featured_index` (`featured`),
  KEY `products_gender_index` (`gender`),
  KEY `products_price_index` (`price`),
  KEY `products_category_status_featured` (`category_id`,`status`,`featured`),
  KEY `products_brand_status` (`brand_id`,`status`),
  KEY `products_price_range` (`price`,`sale_price`)
) ENGINE=InnoDB AUTO_INCREMENT=10023 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `name`, `slug`, `sku`, `short_description`, `description`, `price`, `sale_price`, `cost_price`, `category_id`, `brand_id`, `featured_image`, `gallery`, `status`, `featured`, `weight`, `dimensions`, `material`, `care_instructions`, `gender`, `season`, `style`, `meta_title`, `meta_description`, `views`, `created_at`, `updated_at`, `has_variants`, `manage_stock`, `stock_quantity`, `low_stock_threshold`) VALUES
(4, 'Váy Maxi Nữ Hoa Nhí', 'quan-kaki-nam-4', 'VMN001', 'Váy maxi nữ họa tiết hoa nhí dễ thương', 'Váy maxi nữ với họa tiết hoa nhí dễ thương, phù hợp cho dạo phố và du lịch. Chất liệu voan mềm mại, thoáng mát.', 599000.00, NULL, NULL, 5, 4, '/uploads/products/689710bd6913c_1754730685.webp', '[\"/uploads/products/689710bd69f23_1754730685_0.webp\", \"/uploads/products/689710bd6a269_1754730685_1.webp\"]', 'published', 1, NULL, '', NULL, NULL, 'unisex', 'all_season', 'casual', '', '', 112, '2025-07-27 04:06:19', '2025-08-09 10:11:19', 1, 0, 0, 5),
(5, 'Quần Âu Nữ Ống Suông', 'ao-hoodie-nam-5', 'QAN001', 'Quần âu nữ ống suông thanh lịch', 'Quần âu nữ ống suông với thiết kế thanh lịch, phù hợp cho công sở và những dịp trang trọng.', 729000.00, 0.00, NULL, 4, 5, '/uploads/products/6886434ae7a37_1753629514.webp', NULL, 'published', 1, NULL, '', NULL, NULL, 'unisex', 'all_season', 'casual', '', '', 67, '2025-07-27 04:06:19', '2025-07-28 06:47:10', 0, 1, 0, 5),
(6, 'Áo Khoác Nam Bomber', 'ao-thun-nu-crop-6', 'AKN001', 'Áo khoác nam style bomber thời trang', 'Áo khoác nam style bomber với thiết kế trẻ trung, năng động. Chất liệu polyester chống gió, giữ ấm tốt.', 899000.00, 699000.00, NULL, 1, 1, '/uploads/products/6886435667f65_1753629526.webp', NULL, 'published', 1, NULL, '', NULL, NULL, 'unisex', 'all_season', 'casual', '', '', 234, '2025-07-27 04:06:19', '2025-07-27 08:18:46', 0, 1, 0, 5),
(7, 'Đầm Nữ Midi Cổ V', 'quan-jean-nu-skinny-7', 'DAN001', 'Đầm nữ midi cổ V thanh lịch', 'Đầm nữ midi với thiết kế cổ V thanh lịch, phù hợp cho những buổi hẹn hò và dự tiệc.', 759000.00, 559000.00, NULL, 5, 3, '/uploads/products/6886436366223_1753629539.webp', NULL, 'published', 1, NULL, '', NULL, NULL, 'unisex', 'all_season', 'casual', '', '', 145, '2025-07-27 04:06:19', '2025-07-27 08:18:59', 0, 1, 0, 5),
(8, 'Áo Polo Nam Premium', 'ao-kieu-nu-hoa-tiet-8', 'APN001', 'Áo polo nam chất liệu premium cao cấp', 'Áo polo nam với chất liệu premium, thiết kế tinh tế. Phù hợp cho cả môi trường công sở và leisure.', 549000.00, 0.00, NULL, 1, 2, '/uploads/products/688643718e0ea_1753629553.webp', NULL, 'published', 0, NULL, '', NULL, NULL, 'unisex', 'all_season', 'casual', '', '', 98, '2025-07-27 04:06:19', '2025-07-27 08:19:13', 0, 1, 0, 5),
(9, 'Chân Váy Nữ Xòe', 'chan-vay-nu-xoe-9', 'CVN001', 'Chân váy nữ xòe dễ thương', 'Chân váy nữ xòe với thiết kế dễ thương, trẻ trung. Dễ phối đồ với nhiều loại áo khác nhau.', 389000.00, 299000.00, NULL, 5, 4, '/uploads/products/6886437ba38b9_1753629563.webp', NULL, 'published', 0, NULL, '', NULL, NULL, 'unisex', 'all_season', 'casual', '', '', 76, '2025-07-27 04:06:19', '2025-07-27 08:19:23', 0, 1, 0, 5),
(10018, 'Áo Thun Cotton Basic Test', 'ao-thun-cotton-basic-test', '', NULL, 'Áo thun cotton cao cấp - Test variant system', 299000.00, 249000.00, NULL, 1, NULL, NULL, NULL, '', 1, NULL, NULL, NULL, NULL, 'unisex', 'all_season', 'casual', NULL, NULL, 0, '2025-07-30 00:03:59', '2025-07-30 00:03:59', 1, 1, 0, 5),
(10022, 'Áo Thun Cotton Basic Test 1753859335', 'ao-thun-cotton-basic-test-1753859335', 'TEST-1753859335', NULL, 'Áo thun cotton cao cấp - Test variant system', 299000.00, 249000.00, NULL, 1, NULL, NULL, NULL, '', 1, NULL, NULL, NULL, NULL, 'unisex', 'all_season', 'casual', NULL, NULL, 0, '2025-07-30 00:08:55', '2025-07-30 00:08:55', 1, 1, 0, 5);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_attributes`
--

DROP TABLE IF EXISTS `product_attributes`;
CREATE TABLE IF NOT EXISTS `product_attributes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tên thuộc tính (Màu sắc, Kích thước, etc.)',
  `slug` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('color','size','material','style') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `sort_order` int DEFAULT '0',
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_attributes_slug_unique` (`slug`),
  KEY `product_attributes_type_index` (`type`),
  KEY `product_attributes_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `product_attributes`
--

INSERT INTO `product_attributes` (`id`, `name`, `slug`, `type`, `description`, `sort_order`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Màu sắc', 'mau-sac', 'color', NULL, 1, 'active', '2025-07-29 23:52:32', '2025-07-29 23:52:32'),
(2, 'Kích thước', 'kich-thuoc', 'size', NULL, 2, 'active', '2025-07-29 23:52:32', '2025-07-29 23:52:32'),
(3, 'Chất liệu', 'chat-lieu', 'material', NULL, 3, 'active', '2025-07-29 23:52:32', '2025-07-29 23:52:32');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_attribute_values`
--

DROP TABLE IF EXISTS `product_attribute_values`;
CREATE TABLE IF NOT EXISTS `product_attribute_values` (
  `id` int NOT NULL AUTO_INCREMENT,
  `attribute_id` int NOT NULL,
  `value` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Giá trị (Đỏ, XL, Cotton, etc.)',
  `slug` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `color_code` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã màu hex cho thuộc tính màu sắc',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Hình ảnh cho giá trị thuộc tính',
  `sort_order` int DEFAULT '0',
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_attribute_values_slug_unique` (`slug`),
  KEY `product_attribute_values_attribute_id_foreign` (`attribute_id`),
  KEY `product_attribute_values_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `product_attribute_values`
--

INSERT INTO `product_attribute_values` (`id`, `attribute_id`, `value`, `slug`, `color_code`, `image`, `sort_order`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Đỏ', 'do', '#FF0000', NULL, 1, 'active', '2025-07-29 23:52:32', '2025-07-29 23:52:32'),
(2, 1, 'Xanh dương', 'xanh-duong', '#0000FF', NULL, 2, 'active', '2025-07-29 23:52:32', '2025-07-29 23:52:32'),
(3, 1, 'Xanh lá', 'xanh-la', '#00FF00', NULL, 3, 'active', '2025-07-29 23:52:32', '2025-07-29 23:52:32'),
(4, 1, 'Vàng', 'vang', '#FFFF00', NULL, 4, 'active', '2025-07-29 23:52:32', '2025-07-29 23:52:32'),
(5, 1, 'Đen', 'den', '#000000', NULL, 5, 'active', '2025-07-29 23:52:32', '2025-07-29 23:52:32'),
(6, 1, 'Trắng', 'trang', '#FFFFFF', NULL, 6, 'active', '2025-07-29 23:52:32', '2025-07-29 23:52:32'),
(7, 2, 'XS', 'xs', NULL, NULL, 1, 'active', '2025-07-29 23:52:32', '2025-07-29 23:52:32'),
(8, 2, 'S', 's', NULL, NULL, 2, 'active', '2025-07-29 23:52:32', '2025-07-29 23:52:32'),
(9, 2, 'M', 'm', NULL, NULL, 3, 'active', '2025-07-29 23:52:32', '2025-07-29 23:52:32'),
(10, 2, 'L', 'l', NULL, NULL, 4, 'active', '2025-07-29 23:52:32', '2025-07-29 23:52:32'),
(11, 2, 'XL', 'xl', NULL, NULL, 5, 'active', '2025-07-29 23:52:32', '2025-07-29 23:52:32'),
(12, 2, 'XXL', 'xxl', NULL, NULL, 6, 'active', '2025-07-29 23:52:32', '2025-07-29 23:52:32');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_images`
--

DROP TABLE IF EXISTS `product_images`;
CREATE TABLE IF NOT EXISTS `product_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `variant_id` int DEFAULT NULL,
  `image_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `alt_text` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int DEFAULT '0',
  `is_primary` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_images_product_id_foreign` (`product_id`),
  KEY `product_images_variant_id_foreign` (`variant_id`),
  KEY `product_images_sort_order_index` (`sort_order`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_reviews`
--

DROP TABLE IF EXISTS `product_reviews`;
CREATE TABLE IF NOT EXISTS `product_reviews` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `order_id` int DEFAULT NULL,
  `rating` tinyint(1) NOT NULL,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `review` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','approved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `helpful_count` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_reviews_product_id_foreign` (`product_id`),
  KEY `product_reviews_user_id_foreign` (`user_id`),
  KEY `product_reviews_order_id_foreign` (`order_id`),
  KEY `product_reviews_rating_index` (`rating`),
  KEY `product_reviews_status_index` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_variants`
--

DROP TABLE IF EXISTS `product_variants`;
CREATE TABLE IF NOT EXISTS `product_variants` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `sku` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'SKU riêng cho variant',
  `variant_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tên variant (Áo thun - Đỏ - XL)',
  `price` decimal(10,2) DEFAULT NULL COMMENT 'Giá riêng cho variant (null = dùng giá gốc)',
  `sale_price` decimal(10,2) DEFAULT NULL COMMENT 'Giá sale riêng cho variant',
  `cost_price` decimal(10,2) DEFAULT NULL COMMENT 'Giá vốn riêng cho variant',
  `stock_quantity` int NOT NULL DEFAULT '0' COMMENT 'Số lượng tồn kho',
  `reserved_quantity` int NOT NULL DEFAULT '0' COMMENT 'Số lượng đã đặt hàng nhưng chưa thanh toán',
  `weight` decimal(8,2) DEFAULT NULL,
  `dimensions` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Hình ảnh đại diện cho variant',
  `gallery` json DEFAULT NULL COMMENT 'Thư viện ảnh cho variant',
  `status` enum('active','inactive','out_of_stock') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `sort_order` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_variants_sku_unique` (`sku`),
  KEY `product_variants_product_id_foreign` (`product_id`),
  KEY `product_variants_status_index` (`status`),
  KEY `product_variants_stock_index` (`stock_quantity`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `product_variants`
--

INSERT INTO `product_variants` (`id`, `product_id`, `sku`, `variant_name`, `price`, `sale_price`, `cost_price`, `stock_quantity`, `reserved_quantity`, `weight`, `dimensions`, `image`, `gallery`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 10018, 'TEST-1-7-10018', 'Áo Thun Test - Đỏ - XS', NULL, NULL, NULL, 48, 0, NULL, NULL, NULL, NULL, 'active', 0, '2025-07-30 00:03:59', '2025-07-30 00:03:59'),
(2, 10018, 'TEST-1-8-10018', 'Áo Thun Test - Đỏ - S', NULL, NULL, NULL, 46, 0, NULL, NULL, NULL, NULL, 'active', 0, '2025-07-30 00:03:59', '2025-07-30 00:03:59'),
(3, 10018, 'TEST-1-9-10018', 'Áo Thun Test - Đỏ - M', NULL, NULL, NULL, 32, 0, NULL, NULL, NULL, NULL, 'active', 0, '2025-07-30 00:03:59', '2025-07-30 00:03:59'),
(4, 10018, 'TEST-2-7-10018', 'Áo Thun Test - Xanh dương - XS', NULL, NULL, NULL, 36, 0, NULL, NULL, NULL, NULL, 'active', 0, '2025-07-30 00:03:59', '2025-07-30 00:03:59'),
(5, 10018, 'TEST-2-8-10018', 'Áo Thun Test - Xanh dương - S', NULL, NULL, NULL, 50, 0, NULL, NULL, NULL, NULL, 'active', 0, '2025-07-30 00:03:59', '2025-07-30 00:03:59'),
(6, 10018, 'TEST-2-9-10018', 'Áo Thun Test - Xanh dương - M', NULL, NULL, NULL, 42, 0, NULL, NULL, NULL, NULL, 'active', 0, '2025-07-30 00:03:59', '2025-07-30 00:03:59'),
(7, 10022, 'TEST-1-7-10022', 'Áo Thun Test - Đỏ - XS', NULL, NULL, NULL, 40, 0, NULL, NULL, NULL, NULL, 'active', 0, '2025-07-30 00:08:55', '2025-07-30 00:08:55'),
(8, 10022, 'TEST-1-8-10022', 'Áo Thun Test - Đỏ - S', NULL, NULL, NULL, 40, 0, NULL, NULL, NULL, NULL, 'active', 0, '2025-07-30 00:08:55', '2025-07-30 00:08:55'),
(9, 10022, 'TEST-1-9-10022', 'Áo Thun Test - Đỏ - M', NULL, NULL, NULL, 46, 0, NULL, NULL, NULL, NULL, 'active', 0, '2025-07-30 00:08:55', '2025-07-30 00:08:55'),
(10, 10022, 'TEST-2-7-10022', 'Áo Thun Test - Xanh dương - XS', NULL, NULL, NULL, 26, 0, NULL, NULL, NULL, NULL, 'active', 0, '2025-07-30 00:08:55', '2025-07-30 00:08:55'),
(11, 10022, 'TEST-2-8-10022', 'Áo Thun Test - Xanh dương - S', NULL, NULL, NULL, 44, 0, NULL, NULL, NULL, NULL, 'active', 0, '2025-07-30 00:08:55', '2025-07-30 00:08:55'),
(12, 10022, 'TEST-2-9-10022', 'Áo Thun Test - Xanh dương - M', NULL, NULL, NULL, 45, 0, NULL, NULL, NULL, NULL, 'active', 0, '2025-07-30 00:08:55', '2025-07-30 00:08:55'),
(20, 4, 'VMN001-XAN-XS-DO', 'Váy Maxi Nữ Hoa Nhí - Xanh dương - XS - Đỏ', NULL, NULL, NULL, 10, 0, NULL, NULL, NULL, NULL, 'active', 0, '2025-08-09 09:47:37', '2025-08-09 09:47:37'),
(21, 4, 'VMN001-DEN-M-DO', 'Váy Maxi Nữ Hoa Nhí - Đen - M - Đỏ', NULL, NULL, NULL, 20, 0, NULL, NULL, NULL, NULL, 'active', 0, '2025-08-09 09:47:49', '2025-08-09 09:47:49'),
(22, 4, 'VMN001-TRA-S-DO', 'Váy Maxi Nữ Hoa Nhí - Trắng - S - Đỏ', NULL, NULL, NULL, 30, 0, NULL, NULL, NULL, NULL, 'active', 0, '2025-08-09 09:48:01', '2025-08-09 09:48:01');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_variant_attributes`
--

DROP TABLE IF EXISTS `product_variant_attributes`;
CREATE TABLE IF NOT EXISTS `product_variant_attributes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `variant_id` int NOT NULL,
  `attribute_value_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_variant_attributes_unique` (`variant_id`,`attribute_value_id`),
  KEY `product_variant_attributes_variant_id_foreign` (`variant_id`),
  KEY `product_variant_attributes_attribute_value_id_foreign` (`attribute_value_id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `product_variant_attributes`
--

INSERT INTO `product_variant_attributes` (`id`, `variant_id`, `attribute_value_id`, `created_at`) VALUES
(1, 1, 1, '2025-07-30 00:03:59'),
(2, 1, 7, '2025-07-30 00:03:59'),
(3, 2, 1, '2025-07-30 00:03:59'),
(4, 2, 8, '2025-07-30 00:03:59'),
(5, 3, 1, '2025-07-30 00:03:59'),
(6, 3, 9, '2025-07-30 00:03:59'),
(7, 4, 2, '2025-07-30 00:03:59'),
(8, 4, 7, '2025-07-30 00:03:59'),
(9, 5, 2, '2025-07-30 00:03:59'),
(10, 5, 8, '2025-07-30 00:03:59'),
(11, 6, 2, '2025-07-30 00:03:59'),
(12, 6, 9, '2025-07-30 00:03:59'),
(13, 7, 1, '2025-07-30 00:08:55'),
(14, 7, 7, '2025-07-30 00:08:55'),
(15, 8, 1, '2025-07-30 00:08:55'),
(16, 8, 8, '2025-07-30 00:08:55'),
(17, 9, 1, '2025-07-30 00:08:55'),
(18, 9, 9, '2025-07-30 00:08:55'),
(19, 10, 2, '2025-07-30 00:08:55'),
(20, 10, 7, '2025-07-30 00:08:55'),
(21, 11, 2, '2025-07-30 00:08:55'),
(22, 11, 8, '2025-07-30 00:08:55'),
(23, 12, 2, '2025-07-30 00:08:55'),
(24, 12, 9, '2025-07-30 00:08:55'),
(25, 13, 1, '2025-07-30 00:28:16'),
(26, 13, 7, '2025-07-30 00:28:16'),
(27, 14, 1, '2025-07-30 00:28:16'),
(28, 14, 8, '2025-07-30 00:28:16'),
(29, 15, 1, '2025-07-30 00:28:16'),
(30, 15, 9, '2025-07-30 00:28:16'),
(31, 16, 2, '2025-07-30 00:28:16'),
(32, 16, 7, '2025-07-30 00:28:16'),
(33, 17, 2, '2025-07-30 00:28:16'),
(34, 17, 8, '2025-07-30 00:28:16'),
(40, 20, 2, '2025-08-09 09:47:37'),
(41, 20, 7, '2025-08-09 09:47:37'),
(42, 20, 1, '2025-08-09 09:47:37'),
(43, 21, 5, '2025-08-09 09:47:49'),
(44, 21, 9, '2025-08-09 09:47:49'),
(45, 21, 1, '2025-08-09 09:47:49'),
(46, 22, 6, '2025-08-09 09:48:01'),
(47, 22, 8, '2025-08-09 09:48:01'),
(48, 22, 1, '2025-08-09 09:48:01');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reviews`
--

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `order_id` int DEFAULT NULL,
  `rating` tinyint(1) NOT NULL,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','approved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `helpful_count` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `reviews_product_id_foreign` (`product_id`),
  KEY `reviews_user_id_foreign` (`user_id`),
  KEY `reviews_order_id_foreign` (`order_id`),
  KEY `reviews_rating_index` (`rating`),
  KEY `reviews_status_index` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `reviews`
--

INSERT INTO `reviews` (`id`, `product_id`, `user_id`, `order_id`, `rating`, `title`, `content`, `status`, `helpful_count`, `created_at`, `updated_at`) VALUES
(5, 4, 1018, NULL, 4, 'gggggggggggggggggggg', 'ggggggggggggggg', 'approved', 3, '2025-08-13 19:08:14', '2025-08-13 19:21:54');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `review_likes`
--

DROP TABLE IF EXISTS `review_likes`;
CREATE TABLE IF NOT EXISTS `review_likes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `review_id` int NOT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_review_user` (`review_id`,`user_id`),
  KEY `review_likes_review_id_foreign` (`review_id`),
  KEY `review_likes_user_id_foreign` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `review_likes`
--

INSERT INTO `review_likes` (`id`, `review_id`, `user_id`, `created_at`) VALUES
(7, 3, 1018, '2025-08-13 18:57:13'),
(9, 4, 1018, '2025-08-13 19:07:57'),
(11, 5, 1018, '2025-08-13 19:08:27'),
(15, 5, 1005, '2025-08-13 19:21:09'),
(17, 5, 1004, '2025-08-13 19:21:54');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `type` enum('string','integer','boolean','json','text') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'string',
  `group` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'general',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`),
  KEY `settings_group_index` (`group`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `type`, `group`, `created_at`, `updated_at`) VALUES
(1, 'site_name', '5S Fashion', 'string', 'general', '2025-07-25 06:10:32', '2025-07-25 06:10:32'),
(2, 'site_description', 'Thời trang hiện đại cho mọi phong cách', 'string', 'general', '2025-07-25 06:10:32', '2025-07-25 06:10:32'),
(3, 'site_logo', 'logo.png', 'string', 'general', '2025-07-25 06:10:32', '2025-07-25 06:10:32'),
(4, 'contact_email', 'contact@5sfashion.com', 'string', 'general', '2025-07-25 06:10:32', '2025-07-25 06:10:32'),
(5, 'contact_phone', '1900-5555', 'string', 'general', '2025-07-25 06:10:32', '2025-07-25 06:10:32'),
(6, 'contact_address', '123 Nguyễn Huệ, Quận 1, TP.HCM', 'string', 'general', '2025-07-25 06:10:32', '2025-07-25 06:10:32'),
(7, 'currency', 'VND', 'string', 'general', '2025-07-25 06:10:32', '2025-07-25 06:10:32'),
(8, 'timezone', 'Asia/Ho_Chi_Minh', 'string', 'general', '2025-07-25 06:10:32', '2025-07-25 06:10:32'),
(9, 'items_per_page', '12', 'integer', 'general', '2025-07-25 06:10:32', '2025-07-25 06:10:32'),
(10, 'enable_reviews', '1', 'boolean', 'features', '2025-07-25 06:10:32', '2025-07-25 06:10:32'),
(11, 'enable_wishlist', '1', 'boolean', 'features', '2025-07-25 06:10:32', '2025-07-25 06:10:32'),
(12, 'enable_coupons', '1', 'boolean', 'features', '2025-07-25 06:10:32', '2025-07-25 06:10:32'),
(13, 'shipping_fee', '30000', 'integer', 'shipping', '2025-07-25 06:10:32', '2025-07-25 06:10:32'),
(14, 'free_shipping_amount', '500000', 'integer', 'shipping', '2025-07-25 06:10:32', '2025-07-25 06:10:32'),
(15, 'tax_rate', '10', 'integer', 'tax', '2025-07-25 06:10:32', '2025-07-26 07:54:11'),
(16, 'smtp_host', 'smtp.gmail.com', 'string', 'email', '2025-07-25 06:10:32', '2025-07-25 06:10:32'),
(17, 'smtp_port', '587', 'integer', 'email', '2025-07-25 06:10:32', '2025-07-25 06:10:32'),
(18, 'email_from_name', '5S Fashion', 'string', 'email', '2025-07-25 06:10:32', '2025-07-25 06:10:32'),
(19, 'email_from_address', 'noreply@5sfashion.com', 'string', 'email', '2025-07-25 06:10:32', '2025-07-25 06:10:32'),
(20, 'facebook_url', 'https://facebook.com/5sfashion', 'string', 'social', '2025-07-25 06:10:32', '2025-07-26 07:54:07'),
(21, 'instagram_url', 'https://instagram.com/5sfashion', 'string', 'social', '2025-07-25 06:10:32', '2025-07-26 07:54:07'),
(22, 'youtube_url', 'https://youtube.com/5sfashion', 'string', 'social', '2025-07-25 06:10:32', '2025-07-26 07:54:07');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `shopping_cart`
--

DROP TABLE IF EXISTS `shopping_cart`;
CREATE TABLE IF NOT EXISTS `shopping_cart` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `session_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_id` int NOT NULL,
  `variant_id` int DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `shopping_cart_user_id_foreign` (`user_id`),
  KEY `shopping_cart_product_id_foreign` (`product_id`),
  KEY `shopping_cart_variant_id_foreign` (`variant_id`),
  KEY `shopping_cart_session_id_index` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `stock_movements`
--

DROP TABLE IF EXISTS `stock_movements`;
CREATE TABLE IF NOT EXISTS `stock_movements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `variant_id` int DEFAULT NULL,
  `type` enum('in','out','adjustment') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` int DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `stock_movements_product_id_foreign` (`product_id`),
  KEY `stock_movements_variant_id_foreign` (`variant_id`),
  KEY `stock_movements_created_by_foreign` (`created_by`),
  KEY `stock_movements_type_index` (`type`),
  KEY `stock_movements_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('admin','customer') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'customer',
  `status` enum('active','inactive','banned') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `email_verify_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reset_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reset_token_expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `birthday` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `google_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_username_unique` (`username`),
  KEY `users_role_index` (`role`),
  KEY `users_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=1042 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `full_name`, `phone`, `address`, `avatar`, `role`, `status`, `email_verified_at`, `email_verify_token`, `last_login_at`, `remember_token`, `reset_token`, `reset_token_expires_at`, `created_at`, `updated_at`, `birthday`, `google_id`) VALUES
(1, 'admin', 'admin@5sfashion.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', '0375099213', NULL, NULL, 'admin', 'active', NULL, NULL, '2025-08-13 16:33:19', NULL, NULL, NULL, '2025-07-25 13:10:32', '2025-08-13 16:33:19', '2025-08-03 15:51:26', NULL),
(1002, 'nguyen_van_a', 'nguyenvana@email.com', '$2y$10$ms36/o7ZhziFtVYPf4bs.eo.PPUbWGPfEAgY3gpM0mTurbLaSPBSS', 'Nguyễn Văn A', '0901234567', 'hello', NULL, 'customer', 'active', NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-25 13:10:32', '2025-08-03 18:30:21', '2025-08-02 17:00:00', NULL),
(1003, 'tran_thi_b', 'tranthib@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Thị B', '0912345678', NULL, NULL, 'customer', 'active', NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-25 13:10:32', '2025-07-25 13:10:32', '2025-08-03 15:51:26', NULL),
(1004, 'le_van_c', 'levanc@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lê Văn C', '0923456789', NULL, NULL, 'customer', 'active', '2025-08-04 18:22:35', NULL, '2025-08-13 19:21:40', NULL, NULL, NULL, '2025-07-25 13:10:32', '2025-08-13 19:21:40', '2025-08-03 15:51:26', NULL),
(1005, 'pham_thi_d', 'phamthid@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Phạm Thị D', '0934567890', NULL, NULL, 'customer', 'active', '2025-08-04 18:22:35', NULL, '2025-08-13 19:10:11', NULL, NULL, NULL, '2025-07-25 13:10:32', '2025-08-13 19:10:11', '2025-08-03 15:51:26', NULL),
(1006, 'datdev', 'datdev@gmail.com', '$2y$10$KLfaI8pXJND4/Ba1P4foL.nbGJMYLXtEPDJrM4wT2CFjpVSc38fZC', 'Nguyễn Tiến Đạt', '0375099213', NULL, NULL, 'admin', 'active', NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-26 15:25:08', '2025-07-26 15:25:08', '2025-08-03 15:51:26', NULL),
(1018, 'Hieu', 'dongochieu333@gmail.com', '$2y$10$zRp4thHpMKd/TtpUkp4HkeKWynXGXlRMQ4NuwmcFnoKwi9gF5qLim', 'Do Ngoc Hieu', '0384946973', 'Hello', 'https://lh3.googleusercontent.com/a/ACg8ocK6ySse4OeWY7oQgMrArPjalBQ5PK6U8_gK8qXu2t9HsxpPxV4S=s96-c', 'customer', 'active', '2025-08-04 18:22:35', NULL, '2025-08-13 21:57:21', NULL, NULL, NULL, '2025-08-04 18:21:37', '2025-08-13 21:57:21', '2025-08-20 17:00:00', '108068036855262487099'),
(1019, 'Dat', 'dat@gmail.com', '$2y$10$gUPE9Y6LvqPisPty6hCzJOy9K2cmj34wtRHcEu6NXdshWxtYIX8Le', 'Nguyen Dat', '0988888888', NULL, NULL, 'customer', 'active', '2025-08-14 07:04:30', NULL, '2025-08-10 08:45:08', NULL, NULL, NULL, '2025-08-10 07:02:35', '2025-08-10 08:45:08', '2025-08-10 07:02:35', NULL),
(1041, 'nha579728', 'nha579728@gmail.com', '$2y$10$62MIlXqHkVDQOljTzYxmCOdZwqBWxBWhecY0DoYcrXtqOTAKiMAzK', 'Nguyễn Hà', NULL, NULL, 'https://lh3.googleusercontent.com/a/ACg8ocJ4c4Q98re2H327cqdT7-EFO_qN1BqGqGNG2vwaM-yd7A-DzQ=s96-c', 'customer', 'active', '2025-08-13 21:58:05', NULL, '2025-08-13 21:58:05', NULL, NULL, NULL, '2025-08-13 21:58:05', '2025-08-13 21:58:05', '2025-08-13 21:58:05', '110964051539598102437');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_coupons`
--

DROP TABLE IF EXISTS `user_coupons`;
CREATE TABLE IF NOT EXISTS `user_coupons` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `coupon_id` int NOT NULL,
  `order_id` int DEFAULT NULL,
  `saved_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `used_at` timestamp NULL DEFAULT NULL,
  `status` enum('saved','used','expired') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'saved',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_coupon_unique` (`user_id`,`coupon_id`),
  KEY `user_coupons_user_id_foreign` (`user_id`),
  KEY `user_coupons_coupon_id_foreign` (`coupon_id`),
  KEY `user_coupons_order_id_foreign` (`order_id`),
  KEY `user_coupons_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `user_coupons`
--

INSERT INTO `user_coupons` (`id`, `user_id`, `coupon_id`, `order_id`, `saved_at`, `used_at`, `status`) VALUES
(32, 1018, 7, 100106, '2025-08-13 11:04:23', '2025-08-13 13:31:56', 'used'),
(33, 1018, 6, 100104, '2025-08-13 11:04:24', '2025-08-13 11:28:24', 'used'),
(34, 1018, 1, 100105, '2025-08-13 13:02:24', '2025-08-13 13:02:45', 'used'),
(40, 1018, 2, NULL, '2025-08-13 14:07:43', NULL, 'expired');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `wishlist`
--

DROP TABLE IF EXISTS `wishlist`;
CREATE TABLE IF NOT EXISTS `wishlist` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `product_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_product` (`user_id`,`product_id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `fk_cart_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cart_variant` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `coupon_usage`
--
ALTER TABLE `coupon_usage`
  ADD CONSTRAINT `coupon_usage_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `coupon_usage_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `coupon_usage_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `customer_addresses`
--
ALTER TABLE `customer_addresses`
  ADD CONSTRAINT `customer_addresses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_variant_id_foreign` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `order_logs`
--
ALTER TABLE `order_logs`
  ADD CONSTRAINT `order_logs_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `order_logs_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
