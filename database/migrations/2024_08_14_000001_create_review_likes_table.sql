-- Tạo bảng lưu trữ likes của đánh giá
CREATE TABLE IF NOT EXISTS `review_likes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `review_id` int NOT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_review_user` (`review_id`, `user_id`),
  KEY `review_likes_review_id_foreign` (`review_id`),
  KEY `review_likes_user_id_foreign` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
