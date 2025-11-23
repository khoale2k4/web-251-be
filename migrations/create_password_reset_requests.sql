-- Migration: Tạo bảng password_reset_requests cho admin approval
-- Chạy script này trong phpMyAdmin hoặc MySQL client

USE `web_251`;

-- Tạo bảng password_reset_requests
CREATE TABLE IF NOT EXISTS `password_reset_requests` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `reason` TEXT NULL COMMENT 'Lý do yêu cầu reset password',
    `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending' COMMENT 'Trạng thái yêu cầu',
    `admin_id` INT NULL COMMENT 'ID admin xử lý yêu cầu',
    `admin_note` TEXT NULL COMMENT 'Ghi chú của admin',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `processed_at` DATETIME NULL COMMENT 'Thời gian admin xử lý',
    
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`admin_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_status` (`status`),
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thêm cột để track xem user có cần đổi mật khẩu không
ALTER TABLE `users`
ADD COLUMN `must_change_password` BOOLEAN DEFAULT FALSE COMMENT 'Bắt buộc đổi mật khẩu khi đăng nhập lần sau',
ADD COLUMN `last_password_change` DATETIME NULL COMMENT 'Lần đổi mật khẩu gần nhất';

-- Xóa các cột reset_token không dùng nữa (nếu muốn)
-- ALTER TABLE `users`
-- DROP COLUMN `reset_token`,
-- DROP COLUMN `reset_token_expiry`;

SELECT 'Migration completed successfully!' AS message;
