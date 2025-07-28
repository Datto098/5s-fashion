-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 28, 2025 at 07:41 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `5s_fashion`
--

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

DROP TABLE IF EXISTS `banners`;
CREATE TABLE IF NOT EXISTS `banners` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtitle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_text` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` enum('hero','sidebar','footer','popup') COLLATE utf8mb4_unicode_ci DEFAULT 'hero',
  `sort_order` int DEFAULT '0',
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
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
-- Dumping data for table `banners`
--

INSERT INTO `banners` (`id`, `title`, `subtitle`, `image`, `mobile_image`, `link_url`, `link_text`, `position`, `sort_order`, `status`, `start_date`, `end_date`, `created_at`, `updated_at`) VALUES
(1, 'Bộ Sưu Tập Mùa Hè 2025', 'Khám phá xu hướng thời trang mới nhất', 'banner-summer-2025.jpg', NULL, '/collections/summer-2025', 'Khám Phá Ngay', 'hero', 1, 'active', '2025-07-25 13:10:32', '2025-09-23 13:10:32', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(2, 'Sale Up To 50%', 'Giảm giá sốc cuối tháng', 'banner-sale-50.jpg', NULL, '/sale', 'Mua Ngay', 'hero', 2, 'active', '2025-07-25 13:10:32', '2025-08-09 13:10:32', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(3, 'Thời Trang Công Sở', 'Lịch lãm và chuyên nghiệp', 'banner-office-wear.jpg', NULL, '/categories/cong-so', 'Xem Thêm', 'hero', 3, 'active', '2025-07-25 13:10:32', '2025-08-24 13:10:32', '2025-07-25 13:10:32', '2025-07-25 13:10:32');

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
CREATE TABLE IF NOT EXISTS `brands` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `brands_slug_unique` (`slug`),
  KEY `brands_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brands`
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
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` int DEFAULT NULL,
  `sort_order` int DEFAULT '0',
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`),
  KEY `categories_parent_id_foreign` (`parent_id`),
  KEY `categories_status_index` (`status`),
  KEY `categories_sort_order_index` (`sort_order`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
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
-- Table structure for table `coupons`
--

DROP TABLE IF EXISTS `coupons`;
CREATE TABLE IF NOT EXISTS `coupons` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `type` enum('percentage','fixed_amount') COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `minimum_amount` decimal(10,2) DEFAULT NULL,
  `maximum_discount` decimal(10,2) DEFAULT NULL,
  `usage_limit` int DEFAULT NULL,
  `used_count` int DEFAULT '0',
  `user_limit` int DEFAULT NULL,
  `valid_from` timestamp NULL DEFAULT NULL,
  `valid_until` timestamp NULL DEFAULT NULL,
  `status` enum('active','inactive','expired') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
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
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `name`, `description`, `type`, `value`, `minimum_amount`, `maximum_discount`, `usage_limit`, `used_count`, `user_limit`, `valid_from`, `valid_until`, `status`, `is_featured`, `created_at`, `updated_at`) VALUES
(1, 'WELCOME10', 'Chào mừng khách hàng mới', 'Giảm 10% cho đơn hàng đầu tiên', 'percentage', 10.00, 200000.00, NULL, 100, 0, NULL, '2025-07-25 13:10:32', '2025-08-24 13:10:32', 'active', 0, '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(2, 'FREESHIP', 'Miễn phí vận chuyển', 'Miễn phí ship cho đơn từ 500k', 'fixed_amount', 30000.00, 500000.00, NULL, 200, 0, NULL, '2025-07-25 13:10:32', '2025-09-23 13:10:32', 'active', 0, '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(3, 'SALE20', 'Giảm giá 20%', 'Giảm 20% cho đơn hàng trên 1 triệu', 'percentage', 20.00, 1000000.00, NULL, 50, 0, NULL, '2025-07-25 13:10:32', '2025-08-09 13:10:32', 'active', 0, '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(4, 'SUMMER50', 'Ưu đãi mùa hè', 'Giảm 50k cho đơn hàng mùa hè', 'fixed_amount', 50000.00, 300000.00, NULL, 150, 0, NULL, '2025-07-25 13:10:32', '2025-09-08 13:10:32', 'active', 0, '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(5, 'NEW11', 'NEW11', 'NEW11', 'percentage', 10.00, 10000.00, 20000.00, 10, 0, 1, '2025-07-28 06:13:00', '2025-07-30 06:13:00', 'active', 0, '2025-07-28 06:13:48', '2025-07-28 06:13:48'),
(6, 'WELCOME20', 'Gi?m 20% cho khách hàng m?i', 'Chào m?ng b?n ??n v?i 5S Fashion! Gi?m 20% cho ??n hàng ??u tiên.', 'percentage', 20.00, 200000.00, 80000.00, 100, 0, NULL, '2025-07-28 06:29:57', '2025-08-27 06:29:57', 'active', 1, '2025-07-28 06:29:57', '2025-07-28 06:29:57'),
(7, 'SAVE50K', 'Gi?m 50.000?', 'Gi?m ngay 50.000? cho ??n hàng t? 400.000?', 'fixed_amount', 50000.00, 400000.00, NULL, 200, 0, NULL, '2025-07-28 06:30:13', '2025-08-27 06:30:13', 'active', 1, '2025-07-28 06:30:13', '2025-07-28 06:30:13');

-- --------------------------------------------------------

--
-- Table structure for table `coupon_usage`
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
-- Table structure for table `customer_addresses`
--

DROP TABLE IF EXISTS `customer_addresses`;
CREATE TABLE IF NOT EXISTS `customer_addresses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_line_1` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address_line_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'Vietnam',
  `is_default` tinyint(1) DEFAULT '0',
  `type` enum('home','office','other') COLLATE utf8mb4_unicode_ci DEFAULT 'home',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `customer_addresses_user_id_foreign` (`user_id`),
  KEY `customer_addresses_is_default_index` (`is_default`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer_addresses`
--

INSERT INTO `customer_addresses` (`id`, `user_id`, `name`, `phone`, `address_line_1`, `address_line_2`, `city`, `state`, `postal_code`, `country`, `is_default`, `type`, `created_at`, `updated_at`) VALUES
(1, 1002, 'Nguyễn Văn A', '0901234567', '123 Nguyễn Huệ, Quận 1', NULL, 'TP.HCM', 'TP.HCM', NULL, 'Vietnam', 1, 'home', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(2, 1002, 'Nguyễn Văn A', '0901234567', '456 Lê Văn Sỹ, Quận 3', NULL, 'TP.HCM', 'TP.HCM', NULL, 'Vietnam', 0, 'office', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(3, 1003, 'Trần Thị B', '0912345678', '789 Cách Mạng Tháng 8, Quận 10', NULL, 'TP.HCM', 'TP.HCM', NULL, 'Vietnam', 1, 'home', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(4, 1004, 'Lê Văn C', '0923456789', '321 Nguyễn Trãi, Quận 5', NULL, 'TP.HCM', 'TP.HCM', NULL, 'Vietnam', 1, 'home', '2025-07-25 13:10:32', '2025-07-25 13:10:32');

-- --------------------------------------------------------

--
-- Table structure for table `newsletter_subscriptions`
--

DROP TABLE IF EXISTS `newsletter_subscriptions`;
CREATE TABLE IF NOT EXISTS `newsletter_subscriptions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive','unsubscribed') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `subscribed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `unsubscribed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `newsletter_subscriptions_email_unique` (`email`),
  KEY `newsletter_subscriptions_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `newsletter_subscriptions`
--

INSERT INTO `newsletter_subscriptions` (`id`, `email`, `name`, `status`, `subscribed_at`, `unsubscribed_at`) VALUES
(1, 'john.doe@email.com', 'John Doe', 'active', '2025-07-25 13:10:32', NULL),
(2, 'jane.smith@email.com', 'Jane Smith', 'active', '2025-07-25 13:10:32', NULL),
(3, 'peter.parker@email.com', 'Peter Parker', 'active', '2025-07-25 13:10:32', NULL),
(4, 'mary.jane@email.com', 'Mary Jane', 'active', '2025-07-25 13:10:32', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `order_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `shipping_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled','refunded') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `payment_method` enum('cod','bank_transfer','vnpay','momo','zalopay','credit_card') COLLATE utf8mb4_unicode_ci DEFAULT 'cod',
  `payment_status` enum('pending','paid','failed','refunded') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `shipping_address` json NOT NULL,
  `billing_address` json DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `admin_notes` text COLLATE utf8mb4_unicode_ci,
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
) ENGINE=InnoDB AUTO_INCREMENT=100003 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_code`, `customer_name`, `customer_email`, `customer_phone`, `subtotal`, `tax_amount`, `shipping_amount`, `discount_amount`, `total_amount`, `status`, `payment_method`, `payment_status`, `shipping_address`, `billing_address`, `notes`, `admin_notes`, `shipped_at`, `delivered_at`, `created_at`, `updated_at`) VALUES
(100000, 1002, 'ORD-001', 'Nguyễn Văn A', 'nguyenvana@email.com', '0901234567', 358000.00, 0.00, 30000.00, 0.00, 388000.00, 'processing', 'cod', 'pending', '{\"city\": \"TP.HCM\", \"name\": \"Nguyễn Văn A\", \"ward\": \"Phường Bến Nghé\", \"phone\": \"0901234567\", \"address\": \"123 Nguyễn Huệ, Quận 1\", \"district\": \"Quận 1\"}', NULL, 'Giao hàng giờ hành chính', 'Cập nhật từ admin interface', NULL, NULL, '2025-07-20 13:10:32', '2025-07-26 14:48:13'),
(100001, 1003, 'ORD-002', 'Trần Thị B', 'tranthib@email.com', '0912345678', 448000.00, 0.00, 30000.00, 0.00, 478000.00, 'pending', 'vnpay', 'paid', '{\"city\": \"TP.HCM\", \"name\": \"Trần Thị B\", \"ward\": \"Phường 6\", \"phone\": \"0912345678\", \"address\": \"456 Lê Lợi, Quận 3\", \"district\": \"Quận 3\"}', NULL, '', 'Cập nhật từ admin interface', NULL, NULL, '2025-07-23 13:10:32', '2025-07-26 14:45:07'),
(100002, 1004, 'ORD-003', 'Lê Văn C', 'levanc@email.com', '0923456789', 599000.00, 0.00, 0.00, 0.00, 599000.00, 'pending', 'momo', 'refunded', '{\"city\": \"TP.HCM\", \"name\": \"Lê Văn C\", \"ward\": \"Phường 8\", \"phone\": \"0923456789\", \"address\": \"789 Nguyễn Trãi, Quận 5\", \"district\": \"Quận 5\"}', NULL, 'Miễn phí ship đơn trên 500k', 'Cập nhật từ admin interface', '2025-07-26 13:43:19', '2025-07-26 13:43:23', '2025-07-24 13:10:32', '2025-07-26 14:45:04');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `variant_id` int DEFAULT NULL,
  `product_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_sku` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_logs`
--

DROP TABLE IF EXISTS `order_logs`;
CREATE TABLE IF NOT EXISTS `order_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `status_from` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_to` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `order_logs_order_id_foreign` (`order_id`),
  KEY `order_logs_created_by_foreign` (`created_by`),
  KEY `order_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_logs`
--

INSERT INTO `order_logs` (`id`, `order_id`, `status_from`, `status_to`, `notes`, `created_by`, `created_at`) VALUES
(1, 100002, NULL, 'pending', 'Đơn hàng được tạo thành công', 1, '2025-01-20 03:30:00'),
(2, 100002, 'pending', 'processing', 'Đang xử lý đơn hàng', 1, '2025-01-20 07:15:00'),
(3, 100002, 'processing', 'confirmed', 'Test confirmed status', 1, '2025-07-26 13:22:07'),
(4, 100002, '', 'confirmed', 'Test confirmed status', 1, '2025-07-26 13:25:10'),
(5, 100002, '', 'processing', 'Cập nhật từ admin interface', 1, '2025-07-26 13:35:36'),
(6, 100002, 'processing', 'shipped', 'Cập nhật từ admin interface', 1, '2025-07-26 13:42:27'),
(7, 100002, 'shipped', 'delivered', 'Cập nhật từ admin interface', 1, '2025-07-26 13:42:31'),
(8, 100002, 'delivered', 'confirmed', 'Cập nhật từ admin interface', 1, '2025-07-26 13:42:37'),
(9, 100002, '', 'confirmed', 'Cập nhật từ admin interface', 1, '2025-07-26 13:42:43'),
(10, 100002, '', 'processing', 'Cập nhật từ admin interface', 1, '2025-07-26 13:42:51'),
(11, 100002, 'processing', 'cancelled', 'Cập nhật từ admin interface', 1, '2025-07-26 13:42:57'),
(12, 100002, 'cancelled', 'pending', 'Cập nhật từ admin interface', 1, '2025-07-26 13:43:10'),
(13, 100002, 'pending', 'processing', 'Cập nhật từ admin interface', 1, '2025-07-26 13:43:15'),
(14, 100002, 'processing', 'shipped', 'Cập nhật từ admin interface', 1, '2025-07-26 13:43:19'),
(15, 100002, 'shipped', 'delivered', 'Cập nhật từ admin interface', 1, '2025-07-26 13:43:23'),
(16, 100002, 'delivered', 'cancelled', 'Cập nhật từ admin interface', 1, '2025-07-26 13:43:29'),
(17, 100002, 'cancelled', 'pending', 'Cập nhật từ admin interface', 1, '2025-07-26 13:47:25'),
(18, 100002, 'pending', 'cancelled', 'Cập nhật từ admin interface', 1, '2025-07-26 13:47:28'),
(19, 100002, 'cancelled', 'confirmed', 'Cập nhật từ admin interface', 1, '2025-07-26 13:47:40'),
(20, 100002, '', 'confirmed', 'Cập nhật từ admin interface', 1, '2025-07-26 13:53:39'),
(21, 100002, '', 'processing', 'Cập nhật từ admin interface', 1, '2025-07-26 13:53:43'),
(22, 100002, 'processing', 'pending', 'Cập nhật từ admin interface', 1, '2025-07-26 14:45:04'),
(23, 100001, 'shipped', 'pending', 'Cập nhật từ admin interface', 1, '2025-07-26 14:45:07'),
(24, 100000, 'delivered', 'pending', 'Cập nhật từ admin interface', 1, '2025-07-26 14:45:10'),
(25, 100000, 'pending', 'processing', 'Cập nhật từ admin interface', 1, '2025-07-26 14:47:58');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` timestamp NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `password_reset_tokens_email_index` (`email`),
  KEY `password_reset_tokens_token_index` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_description` text COLLATE utf8mb4_unicode_ci,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) NOT NULL,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `cost_price` decimal(10,2) DEFAULT NULL,
  `category_id` int NOT NULL,
  `brand_id` int DEFAULT NULL,
  `featured_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gallery` json DEFAULT NULL,
  `status` enum('draft','published','out_of_stock') COLLATE utf8mb4_unicode_ci DEFAULT 'draft',
  `featured` tinyint(1) DEFAULT '0',
  `weight` decimal(8,2) DEFAULT NULL,
  `dimensions` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `material` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `care_instructions` text COLLATE utf8mb4_unicode_ci,
  `gender` enum('nam','nu','unisex') COLLATE utf8mb4_unicode_ci DEFAULT 'unisex',
  `season` enum('spring','summer','fall','winter','all_season') COLLATE utf8mb4_unicode_ci DEFAULT 'all_season',
  `style` enum('casual','formal','sport','vintage','modern') COLLATE utf8mb4_unicode_ci DEFAULT 'casual',
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `views` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
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
) ENGINE=InnoDB AUTO_INCREMENT=10018 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `slug`, `sku`, `short_description`, `description`, `price`, `sale_price`, `cost_price`, `category_id`, `brand_id`, `featured_image`, `gallery`, `status`, `featured`, `weight`, `dimensions`, `material`, `care_instructions`, `gender`, `season`, `style`, `meta_title`, `meta_description`, `views`, `created_at`, `updated_at`) VALUES
(3, 'Áo Sơ Mi Nữ Trắng Công Sở', 'ao-so-mi-nam-trang-3', 'ASN001', 'Áo sơ mi nữ trắng thanh lịch cho công sở', 'Áo sơ mi nữ màu trắng với thiết kế thanh lịch, phù hợp cho môi trường công sở. Chất liệu polyester cao cấp, ít nhăn.', 459000.00, 0.00, NULL, 3, 3, '/uploads/products/68863ea9486ac_1753628329.webp', NULL, 'published', 1, NULL, '', NULL, NULL, 'unisex', 'all_season', 'casual', '', '', 89, '2025-07-27 11:06:19', '2025-07-27 14:58:49'),
(4, 'Váy Maxi Nữ Hoa Nhí', 'quan-kaki-nam-4', 'VMN001', 'Váy maxi nữ họa tiết hoa nhí dễ thương', 'Váy maxi nữ với họa tiết hoa nhí dễ thương, phù hợp cho dạo phố và du lịch. Chất liệu voan mềm mại, thoáng mát.', 599000.00, 399000.00, NULL, 5, 4, '/uploads/products/6886434083834_1753629504.webp', NULL, 'published', 1, NULL, '', NULL, NULL, 'unisex', 'all_season', 'casual', '', '', 112, '2025-07-27 11:06:19', '2025-07-27 15:18:24'),
(5, 'Quần Âu Nữ Ống Suông', 'ao-hoodie-nam-5', 'QAN001', 'Quần âu nữ ống suông thanh lịch', 'Quần âu nữ ống suông với thiết kế thanh lịch, phù hợp cho công sở và những dịp trang trọng.', 729000.00, 0.00, NULL, 4, 5, '/uploads/products/6886434ae7a37_1753629514.webp', NULL, 'published', 0, NULL, '', NULL, NULL, 'unisex', 'all_season', 'casual', '', '', 67, '2025-07-27 11:06:19', '2025-07-27 15:18:34'),
(6, 'Áo Khoác Nam Bomber', 'ao-thun-nu-crop-6', 'AKN001', 'Áo khoác nam style bomber thời trang', 'Áo khoác nam style bomber với thiết kế trẻ trung, năng động. Chất liệu polyester chống gió, giữ ấm tốt.', 899000.00, 699000.00, NULL, 1, 1, '/uploads/products/6886435667f65_1753629526.webp', NULL, 'published', 1, NULL, '', NULL, NULL, 'unisex', 'all_season', 'casual', '', '', 234, '2025-07-27 11:06:19', '2025-07-27 15:18:46'),
(7, 'Đầm Nữ Midi Cổ V', 'quan-jean-nu-skinny-7', 'DAN001', 'Đầm nữ midi cổ V thanh lịch', 'Đầm nữ midi với thiết kế cổ V thanh lịch, phù hợp cho những buổi hẹn hò và dự tiệc.', 759000.00, 559000.00, NULL, 5, 3, '/uploads/products/6886436366223_1753629539.webp', NULL, 'published', 1, NULL, '', NULL, NULL, 'unisex', 'all_season', 'casual', '', '', 145, '2025-07-27 11:06:19', '2025-07-27 15:18:59'),
(8, 'Áo Polo Nam Premium', 'ao-kieu-nu-hoa-tiet-8', 'APN001', 'Áo polo nam chất liệu premium cao cấp', 'Áo polo nam với chất liệu premium, thiết kế tinh tế. Phù hợp cho cả môi trường công sở và leisure.', 549000.00, 0.00, NULL, 1, 2, '/uploads/products/688643718e0ea_1753629553.webp', NULL, 'published', 0, NULL, '', NULL, NULL, 'unisex', 'all_season', 'casual', '', '', 98, '2025-07-27 11:06:19', '2025-07-27 15:19:13'),
(9, 'Chân Váy Nữ Xòe', 'chan-vay-nu-xoe-9', 'CVN001', 'Chân váy nữ xòe dễ thương', 'Chân váy nữ xòe với thiết kế dễ thương, trẻ trung. Dễ phối đồ với nhiều loại áo khác nhau.', 389000.00, 299000.00, NULL, 5, 4, '/uploads/products/6886437ba38b9_1753629563.webp', NULL, 'published', 0, NULL, '', NULL, NULL, 'unisex', 'all_season', 'casual', '', '', 76, '2025-07-27 11:06:19', '2025-07-27 15:19:23');

-- --------------------------------------------------------

--
-- Table structure for table `product_attributes`
--

DROP TABLE IF EXISTS `product_attributes`;
CREATE TABLE IF NOT EXISTS `product_attributes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `attribute_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attribute_value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_attributes_product_id_foreign` (`product_id`),
  KEY `product_attributes_name_index` (`attribute_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

DROP TABLE IF EXISTS `product_images`;
CREATE TABLE IF NOT EXISTS `product_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `variant_id` int DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alt_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int DEFAULT '0',
  `is_primary` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_images_product_id_foreign` (`product_id`),
  KEY `product_images_variant_id_foreign` (`variant_id`),
  KEY `product_images_sort_order_index` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_reviews`
--

DROP TABLE IF EXISTS `product_reviews`;
CREATE TABLE IF NOT EXISTS `product_reviews` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `order_id` int DEFAULT NULL,
  `rating` tinyint(1) NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `review` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `helpful_count` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_reviews_product_id_foreign` (`product_id`),
  KEY `product_reviews_user_id_foreign` (`user_id`),
  KEY `product_reviews_order_id_foreign` (`order_id`),
  KEY `product_reviews_rating_index` (`rating`),
  KEY `product_reviews_status_index` (`status`)
) ;

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

DROP TABLE IF EXISTS `product_variants`;
CREATE TABLE IF NOT EXISTS `product_variants` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `sku` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color_code` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price_adjustment` decimal(10,2) DEFAULT '0.00',
  `stock_quantity` int DEFAULT '0',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_variants_sku_unique` (`sku`),
  KEY `product_variants_product_id_foreign` (`product_id`),
  KEY `product_variants_color_index` (`color`),
  KEY `product_variants_size_index` (`size`),
  KEY `product_variants_status_index` (`status`),
  KEY `product_variants_product_color_size` (`product_id`,`color`,`size`),
  KEY `product_variants_stock_status` (`stock_quantity`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `order_id` int DEFAULT NULL,
  `rating` tinyint(1) NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `helpful_count` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `reviews_product_id_foreign` (`product_id`),
  KEY `reviews_user_id_foreign` (`user_id`),
  KEY `reviews_order_id_foreign` (`order_id`),
  KEY `reviews_rating_index` (`rating`),
  KEY `reviews_status_index` (`status`)
) ;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` longtext COLLATE utf8mb4_unicode_ci,
  `type` enum('string','integer','boolean','json','text') COLLATE utf8mb4_unicode_ci DEFAULT 'string',
  `group` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'general',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`),
  KEY `settings_group_index` (`group`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `type`, `group`, `created_at`, `updated_at`) VALUES
(1, 'site_name', '5S Fashion', 'string', 'general', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(2, 'site_description', 'Thời trang hiện đại cho mọi phong cách', 'string', 'general', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(3, 'site_logo', 'logo.png', 'string', 'general', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(4, 'contact_email', 'contact@5sfashion.com', 'string', 'general', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(5, 'contact_phone', '1900-5555', 'string', 'general', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(6, 'contact_address', '123 Nguyễn Huệ, Quận 1, TP.HCM', 'string', 'general', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(7, 'currency', 'VND', 'string', 'general', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(8, 'timezone', 'Asia/Ho_Chi_Minh', 'string', 'general', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(9, 'items_per_page', '12', 'integer', 'general', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(10, 'enable_reviews', '1', 'boolean', 'features', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(11, 'enable_wishlist', '1', 'boolean', 'features', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(12, 'enable_coupons', '1', 'boolean', 'features', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(13, 'shipping_fee', '30000', 'integer', 'shipping', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(14, 'free_shipping_amount', '500000', 'integer', 'shipping', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(15, 'tax_rate', '10', 'integer', 'tax', '2025-07-25 13:10:32', '2025-07-26 14:54:11'),
(16, 'smtp_host', 'smtp.gmail.com', 'string', 'email', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(17, 'smtp_port', '587', 'integer', 'email', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(18, 'email_from_name', '5S Fashion', 'string', 'email', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(19, 'email_from_address', 'noreply@5sfashion.com', 'string', 'email', '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(20, 'facebook_url', 'https://facebook.com/5sfashion', 'string', 'social', '2025-07-25 13:10:32', '2025-07-26 14:54:07'),
(21, 'instagram_url', 'https://instagram.com/5sfashion', 'string', 'social', '2025-07-25 13:10:32', '2025-07-26 14:54:07'),
(22, 'youtube_url', 'https://youtube.com/5sfashion', 'string', 'social', '2025-07-25 13:10:32', '2025-07-26 14:54:07');

-- --------------------------------------------------------

--
-- Table structure for table `shopping_cart`
--

DROP TABLE IF EXISTS `shopping_cart`;
CREATE TABLE IF NOT EXISTS `shopping_cart` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `session_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
-- Table structure for table `stock_movements`
--

DROP TABLE IF EXISTS `stock_movements`;
CREATE TABLE IF NOT EXISTS `stock_movements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `variant_id` int DEFAULT NULL,
  `type` enum('in','out','adjustment') COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` int DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
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
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_data` json DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('admin','customer') COLLATE utf8mb4_unicode_ci DEFAULT 'customer',
  `status` enum('active','inactive','banned') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reset_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reset_token_expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_index` (`role`),
  KEY `users_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=1007 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `full_name`, `phone`, `address_data`, `avatar`, `role`, `status`, `email_verified_at`, `last_login_at`, `remember_token`, `reset_token`, `reset_token_expires_at`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@5sfashion.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', '0375099213', NULL, NULL, 'admin', 'active', NULL, NULL, NULL, NULL, NULL, '2025-07-25 13:10:32', '2025-07-26 15:23:35'),
(1002, 'nguyen_van_a', 'nguyenvana@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyễn Văn A', '0901234567', NULL, NULL, 'customer', 'active', NULL, NULL, NULL, NULL, NULL, '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(1003, 'tran_thi_b', 'tranthib@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Thị B', '0912345678', NULL, NULL, 'customer', 'active', NULL, NULL, NULL, NULL, NULL, '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(1004, 'le_van_c', 'levanc@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lê Văn C', '0923456789', NULL, NULL, 'customer', 'active', NULL, NULL, NULL, NULL, NULL, '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(1005, 'pham_thi_d', 'phamthid@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Phạm Thị D', '0934567890', NULL, NULL, 'customer', 'active', NULL, NULL, NULL, NULL, NULL, '2025-07-25 13:10:32', '2025-07-25 13:10:32'),
(1006, 'datdev', 'datdev@gmail.com', '$2y$10$KLfaI8pXJND4/Ba1P4foL.nbGJMYLXtEPDJrM4wT2CFjpVSc38fZC', 'Nguyễn Tiến Đạt', '0375099213', NULL, NULL, 'admin', 'active', NULL, NULL, NULL, NULL, NULL, '2025-07-26 15:25:08', '2025-07-26 15:25:08');

-- --------------------------------------------------------

--
-- Table structure for table `user_coupons`
--

DROP TABLE IF EXISTS `user_coupons`;
CREATE TABLE IF NOT EXISTS `user_coupons` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `coupon_id` int NOT NULL,
  `saved_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `used_at` timestamp NULL DEFAULT NULL,
  `status` enum('saved','used','expired') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'saved',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_coupon_unique` (`user_id`,`coupon_id`),
  KEY `user_coupons_user_id_foreign` (`user_id`),
  KEY `user_coupons_coupon_id_foreign` (`coupon_id`),
  KEY `user_coupons_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_coupons`
--

INSERT INTO `user_coupons` (`id`, `user_id`, `coupon_id`, `saved_at`, `used_at`, `status`) VALUES
(1, 1, 6, '2025-07-28 07:40:03', NULL, 'saved'),
(2, 1, 7, '2025-07-28 07:40:20', NULL, 'saved');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `product_id`, `created_at`, `updated_at`) VALUES
(1, 1, 6, '2025-07-27 18:57:29', '2025-07-27 18:57:29'),
(2, 1, 4, '2025-07-28 04:15:44', '2025-07-28 04:15:44');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `coupon_usage`
--
ALTER TABLE `coupon_usage`
  ADD CONSTRAINT `coupon_usage_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `coupon_usage_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `coupon_usage_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `customer_addresses`
--
ALTER TABLE `customer_addresses`
  ADD CONSTRAINT `customer_addresses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_variant_id_foreign` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_logs`
--
ALTER TABLE `order_logs`
  ADD CONSTRAINT `order_logs_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `order_logs_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_attributes`
--
ALTER TABLE `product_attributes`
  ADD CONSTRAINT `product_attributes_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_images_variant_id_foreign` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD CONSTRAINT `product_reviews_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `product_reviews_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `product_variants_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `reviews_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `shopping_cart`
--
ALTER TABLE `shopping_cart`
  ADD CONSTRAINT `shopping_cart_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `shopping_cart_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `shopping_cart_variant_id_foreign` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `stock_movements_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_movements_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_movements_variant_id_foreign` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_coupons`
--
ALTER TABLE `user_coupons`
  ADD CONSTRAINT `user_coupons_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_coupons_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
