-- Create site_settings table

CREATE TABLE IF NOT EXISTS `site_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `site_name` varchar(255) DEFAULT 'Shoe Store',
  `site_title` varchar(255) DEFAULT 'Cửa hàng giày dép chất lượng cao',
  `site_description` text DEFAULT NULL,
  `site_keywords` varchar(500) DEFAULT NULL,
  `logo` varchar(500) DEFAULT NULL,
  `favicon` varchar(500) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `youtube` varchar(255) DEFAULT NULL,
  `about_us` text DEFAULT NULL,
  `working_hours` varchar(255) DEFAULT NULL,
  `copyright` varchar(255) DEFAULT '© 2025 Shoe Store. All rights reserved.',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default settings
INSERT INTO `site_settings` (
    site_name, site_title, site_description, site_keywords,
    email, phone, address, copyright
) VALUES (
    'Shoe Store',
    'Cửa hàng giày dép chất lượng cao',
    'Chuyên cung cấp các loại giày thể thao, giày da, boots và sandals chính hãng',
    'giày, giày thể thao, sneakers, boots',
    'contact@shoestore.vn',
    '0123-456-789',
    '123 Nguyễn Huệ, Quận 1, TP.HCM',
    '© 2025 Shoe Store. All rights reserved.'
);
