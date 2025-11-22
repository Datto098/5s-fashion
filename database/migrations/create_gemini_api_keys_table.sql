-- Migration for Gemini API Keys Management
-- Create table to store and manage Gemini API keys
-- zone Fashion E-commerce Platform

CREATE TABLE `gemini_api_keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'Tên gợi nhớ cho key',
  `api_key` varchar(500) NOT NULL COMMENT 'Gemini API key',
  `status` enum('active','inactive','error') NOT NULL DEFAULT 'active' COMMENT 'Trạng thái key (active/inactive/error)',
  `usage_count` int(11) NOT NULL DEFAULT 0 COMMENT 'Số lần sử dụng',
  `daily_limit` int(11) NULL DEFAULT 1000 COMMENT 'Giới hạn request/ngày (0 = không giới hạn)',
  `monthly_limit` int(11) NULL DEFAULT 30000 COMMENT 'Giới hạn request/tháng (0 = không giới hạn)',
  `current_daily_usage` int(11) NOT NULL DEFAULT 0 COMMENT 'Số request đã dùng hôm nay',
  `current_monthly_usage` int(11) NOT NULL DEFAULT 0 COMMENT 'Số request đã dùng tháng này',
  `last_used_at` datetime NULL COMMENT 'Lần cuối sử dụng',
  `last_test_at` datetime NULL COMMENT 'Lần cuối test key',
  `last_test_status` enum('success','failed','pending') NULL COMMENT 'Kết quả test cuối',
  `last_error_message` text NULL COMMENT 'Thông báo lỗi cuối (nếu có)',
  `notes` text NULL COMMENT 'Ghi chú về key',
  `created_by` int(11) NOT NULL COMMENT 'ID admin tạo key',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_api_key` (`api_key`),
  KEY `idx_status` (`status`),
  KEY `idx_created_by` (`created_by`),
  KEY `idx_last_used` (`last_used_at`),
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng quản lý API keys cho Gemini';

-- Insert default API key if not exists
INSERT INTO `gemini_api_keys` (`name`, `api_key`, `status`, `notes`, `created_by`) 
SELECT 
    'Default Gemini Key',
    'AIzaSyA_YOUR_DEFAULT_KEY_HERE',
    'active',
    'Key mặc định cho hệ thống chatbot',
    1
WHERE NOT EXISTS (
    SELECT 1 FROM `gemini_api_keys` WHERE `name` = 'Default Gemini Key'
);

-- Create indexes for performance
ALTER TABLE `gemini_api_keys` 
ADD INDEX `idx_status_active` (`status`, `created_at`),
ADD INDEX `idx_daily_usage` (`current_daily_usage`, `daily_limit`),
ADD INDEX `idx_monthly_usage` (`current_monthly_usage`, `monthly_limit`);