-- Add email verification fields to users table
ALTER TABLE `users`
ADD COLUMN `email_verify_token` VARCHAR(100) NULL DEFAULT NULL AFTER `email_verified_at`;
-- You may want to clear/reset tokens after verification for security.
