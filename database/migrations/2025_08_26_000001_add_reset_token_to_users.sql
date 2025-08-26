-- Migration: add reset_token and reset_token_expires_at to users
-- Run this against your 5s_fashion database (phpMyAdmin or mysql CLI)
ALTER TABLE `users`
  ADD COLUMN `reset_token` VARCHAR(255) NULL AFTER `remember_token`,
  ADD COLUMN `reset_token_expires_at` DATETIME NULL AFTER `reset_token`;
