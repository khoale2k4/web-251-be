/*
 Navicat Premium Dump SQL

 Source Server         : shoe_store
 Source Server Type    : MySQL
 Source Server Version : 100432 (10.4.32-MariaDB)
 Source Host           : localhost:3306
 Source Schema         : shoe_store

 Target Server Type    : MySQL
 Target Server Version : 100432 (10.4.32-MariaDB)
 File Encoding         : 65001

 Date: 21/11/2025 23:02:27
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for cart_items
-- ----------------------------
DROP TABLE IF EXISTS `cart_items`;
CREATE TABLE `cart_items`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `cart_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `cart_id`(`cart_id` ASC) USING BTREE,
  INDEX `product_id`(`product_id` ASC) USING BTREE,
  CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cart_items
-- ----------------------------
INSERT INTO `cart_items` VALUES (1, 1, 4, 1);
INSERT INTO `cart_items` VALUES (2, 2, 2, 1);

-- ----------------------------
-- Table structure for carts
-- ----------------------------
DROP TABLE IF EXISTS `carts`;
CREATE TABLE `carts`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `created_at` datetime NULL DEFAULT current_timestamp(),
  `updated_at` datetime NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `user_id`(`user_id` ASC) USING BTREE,
  CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of carts
-- ----------------------------
INSERT INTO `carts` VALUES (1, 9, '2025-10-19 21:06:27', '2025-10-20 21:06:27');
INSERT INTO `carts` VALUES (2, 10, '2025-10-18 21:06:27', '2025-10-19 21:06:27');

-- ----------------------------
-- Table structure for comments
-- ----------------------------
DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NULL DEFAULT NULL,
  `comment_type` enum('product','post') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'product',
  `product_id` int NULL DEFAULT NULL,
  `post_id` int NULL DEFAULT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating` int NULL DEFAULT NULL,
  `status` enum('pending','approved','hidden','spam') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'approved',
  `created_at` datetime NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `user_id`(`user_id` ASC) USING BTREE,
  INDEX `product_id`(`product_id` ASC) USING BTREE,
  INDEX `post_id`(`post_id` ASC) USING BTREE,
  INDEX `idx_comments_type_created`(`comment_type` ASC, `created_at` ASC) USING BTREE,
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 54 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of comments
-- ----------------------------
INSERT INTO `comments` VALUES (1, 3, 'product', 1, NULL, 'Very comfortable and light.', 5, 'approved', '2025-10-11 21:06:27', '2025-11-12 20:13:58');
INSERT INTO `comments` VALUES (2, 6, 'product', 3, NULL, 'Good value for the price.', 4, 'approved', '2025-10-15 21:06:27', '2025-11-12 20:13:58');
INSERT INTO `comments` VALUES (3, 4, 'post', NULL, 1, 'Great collection! The colors are lovely.', 4, 'approved', '2025-10-16 21:06:27', '2025-11-12 20:13:58');
INSERT INTO `comments` VALUES (4, 3, 'product', 2, NULL, 'Lorem', 4, 'approved', '2025-10-21 18:05:55', '2025-11-12 20:13:58');
INSERT INTO `comments` VALUES (5, 3, 'post', NULL, 1, 'Bình luận thử', 5, 'approved', '2025-10-21 18:53:20', '2025-11-12 20:13:58');
INSERT INTO `comments` VALUES (6, 3, 'post', NULL, 2, 'Bình luận thử 2', 1, 'approved', '2025-10-21 18:53:45', '2025-11-12 20:13:58');
INSERT INTO `comments` VALUES (8, 3, 'product', 1, NULL, 'Bình luận thử', 4, 'approved', '2025-10-21 23:01:22', '2025-11-12 20:13:58');
INSERT INTO `comments` VALUES (9, 3, 'product', 1, NULL, 'Bình luận thử', 4, 'approved', '2025-10-21 23:02:04', '2025-11-12 20:13:58');
INSERT INTO `comments` VALUES (10, 3, 'post', NULL, 2, 'Bình luận thử', 4, 'approved', '2025-10-21 23:08:28', '2025-11-12 20:13:58');
INSERT INTO `comments` VALUES (11, 3, 'post', NULL, 1, 'Bình luận thử', 4, 'approved', '2025-10-21 23:08:30', '2025-11-12 20:13:58');
INSERT INTO `comments` VALUES (12, 3, 'post', NULL, 1, 'Bình luận thử', 4, 'approved', '2025-10-21 23:08:34', '2025-11-12 20:13:58');
INSERT INTO `comments` VALUES (33, 2, 'product', 1, NULL, 'Giày rất nhẹ và thoáng khí, đi chạy bộ rất thoải mái!', 5, 'approved', '2025-11-16 08:30:00', '2025-11-16 08:30:00');
INSERT INTO `comments` VALUES (34, 4, 'product', 1, NULL, 'Chất lượng tốt, giá hơi cao nhưng xứng đáng.', 4, 'approved', '2025-11-17 10:15:00', '2025-11-17 10:15:00');
INSERT INTO `comments` VALUES (35, 7, 'product', 1, NULL, 'Đế giày êm ái, thiết kế đẹp mắt. Rất hài lòng!', 5, 'approved', '2025-11-18 14:20:00', '2025-11-18 14:20:00');
INSERT INTO `comments` VALUES (36, 3, 'product', 2, NULL, 'Boot da thật, chất lượng cao cấp. Đi rất bền!', 5, 'approved', '2025-11-16 09:45:00', '2025-11-16 09:45:00');
INSERT INTO `comments` VALUES (37, 6, 'product', 2, NULL, 'Giày đẹp nhưng hơi nặng, phù hợp mùa đông.', 4, 'approved', '2025-11-17 15:30:00', '2025-11-17 15:30:00');
INSERT INTO `comments` VALUES (38, 9, 'product', 3, NULL, 'Dép rất thoải mái, giá rẻ mà chất lượng tốt!', 5, 'approved', '2025-11-16 11:00:00', '2025-11-16 11:00:00');
INSERT INTO `comments` VALUES (39, 10, 'product', 3, NULL, 'Đi biển rất ổn, không trơn trượt.', 4, 'approved', '2025-11-18 16:45:00', '2025-11-18 16:45:00');
INSERT INTO `comments` VALUES (40, 2, 'product', 4, NULL, 'Giày phong cách, đi trong thành phố rất hợp!', 5, 'approved', '2025-11-17 08:20:00', '2025-11-17 08:20:00');
INSERT INTO `comments` VALUES (41, 4, 'product', 4, NULL, 'Màu xám rất dễ phối đồ, form dáng đẹp.', 4, 'approved', '2025-11-18 13:10:00', '2025-11-18 13:10:00');
INSERT INTO `comments` VALUES (42, 6, 'product', 4, NULL, 'Giá tốt, chất lượng ổn. Sẽ giới thiệu cho bạn bè!', 5, 'approved', '2025-11-19 10:30:00', '2025-11-19 10:30:00');
INSERT INTO `comments` VALUES (44, 9, 'post', NULL, 1, 'Các mẫu giày trong bộ sưu tập đều rất trendy!', NULL, 'approved', '2025-11-17 11:30:00', '2025-11-17 11:30:00');
INSERT INTO `comments` VALUES (45, 3, 'post', NULL, 2, 'Hướng dẫn rất chi tiết, cảm ơn admin đã chia sẻ!', NULL, 'approved', '2025-11-16 10:15:00', '2025-11-16 10:15:00');
INSERT INTO `comments` VALUES (46, 10, 'post', NULL, 2, 'Mình đã thử và boot da sạch bóng như mới!', NULL, 'approved', '2025-11-18 14:45:00', '2025-11-18 14:45:00');
INSERT INTO `comments` VALUES (47, 2, 'post', NULL, 5, 'Bài viết rất hữu ích, giúp mình cập nhật xu hướng mới nhất!', NULL, 'approved', '2025-11-17 09:20:00', '2025-11-17 09:20:00');
INSERT INTO `comments` VALUES (48, 6, 'post', NULL, 5, 'Top 10 này đều là những mẫu giày đỉnh cao!', NULL, 'approved', '2025-11-18 15:10:00', '2025-11-18 15:10:00');
INSERT INTO `comments` VALUES (50, 7, 'post', NULL, 6, 'Đọc xong mình tự tin hơn khi mua giày rồi!', NULL, 'approved', '2025-11-19 08:50:00', '2025-11-19 08:50:00');
INSERT INTO `comments` VALUES (53, 1, 'post', NULL, 5, 'Quá đỉnh', NULL, 'approved', '2025-11-21 19:26:32', '2025-11-21 19:26:32');

-- ----------------------------
-- Table structure for contacts
-- ----------------------------
DROP TABLE IF EXISTS `contacts`;
CREATE TABLE `contacts`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `subject` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('new','read','replied') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'new',
  `created_at` datetime NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of contacts
-- ----------------------------
INSERT INTO `contacts` VALUES (1, 'Customer A', 'customer.a@example.com', '0900123456', 'Order inquiry', 'I have a question about my order #123.', 'new', '2025-10-17 21:06:27');
INSERT INTO `contacts` VALUES (2, 'Customer B', 'customer.b@example.com', NULL, 'Product question', 'Does the Urban Sneak come in wide sizes?', 'read', '2025-10-19 21:06:27');

-- ----------------------------
-- Table structure for faqs
-- ----------------------------
DROP TABLE IF EXISTS `faqs`;
CREATE TABLE `faqs`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NULL DEFAULT NULL,
  `question` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `status` enum('pending','answered') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'pending',
  `created_at` datetime NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `user_id`(`user_id` ASC) USING BTREE,
  CONSTRAINT `faqs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of faqs
-- ----------------------------
INSERT INTO `faqs` VALUES (1, NULL, 'What is the return policy?', 'You can return items within 14 days in original condition.', 'answered', '2025-09-20 21:06:27');
INSERT INTO `faqs` VALUES (2, 7, 'Do you ship internationally?', 'Yes, we ship to selected countries. Check shipping page for details.', 'pending', '2025-10-18 21:06:27');

-- ----------------------------
-- Table structure for order_items
-- ----------------------------
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NULL DEFAULT 1,
  `price` decimal(10, 2) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `order_id`(`order_id` ASC) USING BTREE,
  INDEX `product_id`(`product_id` ASC) USING BTREE,
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of order_items
-- ----------------------------
INSERT INTO `order_items` VALUES (1, 1, 1, 2, 120.00);
INSERT INTO `order_items` VALUES (2, 2, 3, 1, 25.00);

-- ----------------------------
-- Table structure for orders
-- ----------------------------
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NULL DEFAULT NULL,
  `total_price` decimal(10, 2) NOT NULL,
  `shipping_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `payment_method` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `status` enum('pending','confirmed','shipped','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'pending',
  `created_at` datetime NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `user_id`(`user_id` ASC) USING BTREE,
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of orders
-- ----------------------------
INSERT INTO `orders` VALUES (1, 3, 240.00, '123 Le Loi, District 1, HCMC', 'credit_card', NULL, 'completed', '2025-10-13 21:06:27');
INSERT INTO `orders` VALUES (2, 6, 25.00, '45 Nguyen Hue, District 3', 'cod', 'Please deliver between 9-12', 'pending', '2025-10-18 21:06:27');

-- ----------------------------
-- Table structure for posts
-- ----------------------------
DROP TABLE IF EXISTS `posts`;
CREATE TABLE `posts`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `excerpt` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `author_id` int NULL DEFAULT NULL,
  `status` enum('draft','scheduled','published','archived') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_at` datetime NULL DEFAULT current_timestamp(),
  `updated_at` datetime NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP,
  `published_at` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `slug`(`slug` ASC) USING BTREE,
  INDEX `author_id`(`author_id` ASC) USING BTREE,
  INDEX `idx_posts_status_created`(`status` ASC, `created_at` ASC) USING BTREE,
  FULLTEXT INDEX `idx_posts_search`(`title`, `content`, `excerpt`),
  CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 18 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of posts
-- ----------------------------
INSERT INTO `posts` VALUES (1, 'New Fall Collection', 'new-fall-collection', 'Announcing our new fall shoe collection...', 'Announcing our new fall shoe collection...', 'news/fall.jpg', 2, 'published', '2025-09-30 21:06:27', '2025-11-12 20:13:58', '2025-09-30 21:06:27');
INSERT INTO `posts` VALUES (2, 'How to Clean Leather Boots', 'clean-leather-boots', 'Step by step guide to clean and maintain leather boots...', 'Step by step guide to clean and maintain leather boots...', NULL, 8, 'published', '2025-10-08 21:06:27', '2025-11-12 20:13:58', '2025-10-08 21:06:27');
INSERT INTO `posts` VALUES (4, 'New shoe', 'post-4', 'string', 'string', NULL, NULL, 'published', '2025-10-21 11:11:26', '2025-11-12 20:13:58', '2025-10-21 11:11:26');
INSERT INTO `posts` VALUES (5, 'Top 10 Xu Hướng Giày Sneaker 2025', 'top-10-xu', '<p>Khám phá những mẫu sneaker hot nhất được giới trẻ yêu thích trong năm 2025.</p>', 'Khám phá những mẫu sneaker hot nhất được giới trẻ yêu thích trong năm 2025.', 'news/sneaker-trends-2025.jpg', 2, 'published', '2025-10-22 23:25:56', '2025-11-21 15:38:24', '2025-11-21 15:38:00');
INSERT INTO `posts` VALUES (6, 'Cách Phân Biệt Giày Chính Hãng Và Giả', 'phan-biet-giay-chinh-hang', '<p>Hướng dẫn chi tiết giúp bạn nhận biết giày chính hãng so với hàng giả, tránh bị lừa.</p><p><br></p>', 'Hướng dẫn chi tiết giúp bạn nhận biết giày chính hãng so với hàng giả, tránh bị lừa.', 'news/authentic-check.jpg', 3, 'archived', '2025-10-22 23:25:56', '2025-11-21 15:46:17', '2025-10-21 05:25:00');
INSERT INTO `posts` VALUES (7, '5 Mẹo Giữ Giày Trắng Luôn Sạch Như Mới', 'giu-giay-trang-sach', '<p>Bí quyết giúp đôi giày trắng của bạn luôn sáng bóng dù đi cả năm.</p>', 'Bí quyết giúp đôi giày trắng của bạn luôn sáng bóng dù đi cả năm.', 'news/clean-white-shoes.jpg', 4, 'published', '2025-10-22 23:25:56', '2025-11-20 22:55:40', '2025-10-22 23:25:56');
INSERT INTO `posts` VALUES (8, 'Lịch Sử Thương Hiệu Nike: Từ Zero Đến Huyền Thoại', 'lich-su-nike', '<p>Câu chuyện đầy cảm hứng về hành trình phát triển của Nike – biểu tượng thể thao toàn cầu.</p>', 'Câu chuyện đầy cảm hứng về hành trình phát triển của Nike – biểu tượng thể thao toàn cầu.', 'news/nike-history.jpg', 5, 'published', '2025-10-22 23:25:56', '2025-11-21 18:33:23', '2025-11-21 18:29:00');
INSERT INTO `posts` VALUES (9, 'Adidas vs Nike: Cuộc Đua Công Nghệ Đỉnh Cao', 'adidas-vs-nike', 'So sánh công nghệ và thiết kế giữa hai ông lớn trong ngành thể thao.', 'So sánh công nghệ và thiết kế giữa hai ông lớn trong ngành thể thao.', 'news/adidas-vs-nike.jpg', 2, 'published', '2025-10-22 23:25:56', '2025-11-12 20:13:58', '2025-10-22 23:25:56');
INSERT INTO `posts` VALUES (10, 'Cách Lựa Chọn Giày Chạy Bộ Phù Hợp Với Dáng Chân', 'chon-giay-chay-bo', 'Giày chạy bộ phù hợp giúp cải thiện hiệu suất và giảm chấn thương khi luyện tập.', 'Giày chạy bộ phù hợp giúp cải thiện hiệu suất và giảm chấn thương khi luyện tập.', 'news/running-shoes.jpg', 3, 'published', '2025-10-22 23:25:56', '2025-11-12 20:13:58', '2025-10-22 23:25:56');
INSERT INTO `posts` VALUES (11, 'Top 7 Đôi Giày Nam Đáng Mua Dưới 2 Triệu', 'giay-nam-duoi-2-trieu', 'Danh sách những đôi giày chất lượng cao với mức giá hợp lý dành cho nam giới.', 'Danh sách những đôi giày chất lượng cao với mức giá hợp lý dành cho nam giới.', 'news/mens-shoes-under-2m.jpg', 4, 'published', '2025-10-22 23:25:56', '2025-11-12 20:13:58', '2025-10-22 23:25:56');
INSERT INTO `posts` VALUES (12, 'Phối Đồ Với Giày Sneaker: Đơn Giản Mà Chất', 'phoi-do-voi-sneaker', 'Gợi ý cách phối đồ với sneaker để bạn luôn nổi bật và cá tính.', 'Gợi ý cách phối đồ với sneaker để bạn luôn nổi bật và cá tính.', 'news/sneaker-outfit.jpg', 5, 'published', '2025-10-22 23:25:56', '2025-11-12 20:13:58', '2025-10-22 23:25:56');
INSERT INTO `posts` VALUES (13, 'Làm Sao Để Giày Không Bị Hôi Khi Đi Lâu', 'meo-giay-khong-hoi', 'Mẹo nhỏ giúp đôi giày của bạn luôn thơm tho và dễ chịu suốt cả ngày.', 'Mẹo nhỏ giúp đôi giày của bạn luôn thơm tho và dễ chịu suốt cả ngày.', 'news/deodorize-shoes.jpg', 3, 'published', '2025-10-22 23:25:56', '2025-11-12 20:13:58', '2025-10-22 23:25:56');
INSERT INTO `posts` VALUES (14, 'Giày Cổ Cao Hay Cổ Thấp: Nên Chọn Loại Nào?', 'giay-co-cao-hay-thap', 'Phân tích ưu nhược điểm của giày cổ cao và cổ thấp để bạn chọn đúng loại phù hợp.', 'Phân tích ưu nhược điểm của giày cổ cao và cổ thấp để bạn chọn đúng loại phù hợp.', 'news/high-vs-low-top.jpg', 2, 'published', '2025-10-22 23:25:56', '2025-11-12 20:13:58', '2025-10-22 23:25:56');
INSERT INTO `posts` VALUES (15, 'Hôm nay trời đệp quáaaaa', 'hom-nay-troi-dep-quaaaaa', '<p>Lorem issue</p>', 'Lorem issue', NULL, 1, 'draft', '2025-11-04 16:17:07', '2025-11-21 07:47:55', '2025-11-04 09:17:00');
INSERT INTO `posts` VALUES (17, 'Bla', 'bla', '<p><strong><img src=\"../../assets/uploads/news/c719e1d591e0c805ebbc4edb059a100c.jpg\">Bla oh no </strong><em>jednjnj ựndnn</em></p>', 'bla bla', NULL, 1, 'draft', '2025-11-21 07:31:49', '2025-11-21 15:50:15', '2025-11-06 00:36:00');

-- ----------------------------
-- Table structure for product_categories
-- ----------------------------
DROP TABLE IF EXISTS `product_categories`;
CREATE TABLE `product_categories`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of product_categories
-- ----------------------------
INSERT INTO `product_categories` VALUES (1, 'Sneakers', 'Casual and athletic sneakers');
INSERT INTO `product_categories` VALUES (2, 'Boots', 'Leather and winter boots');
INSERT INTO `product_categories` VALUES (3, 'Sandals', 'Open-toe summer sandals');

-- ----------------------------
-- Table structure for products
-- ----------------------------
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `price` decimal(10, 2) NOT NULL,
  `discount` decimal(5, 2) NULL DEFAULT 0.00,
  `stock` int NULL DEFAULT 0,
  `size` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `color` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `category_id` int NULL DEFAULT NULL,
  `created_at` datetime NULL DEFAULT current_timestamp(),
  `updated_at` datetime NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `category_id`(`category_id` ASC) USING BTREE,
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of products
-- ----------------------------
INSERT INTO `products` VALUES (1, 'AirFlex Runner', 'Lightweight running sneaker', 120.00, 10.00, 50, '42', 'White', 'products/airflex.jpg', 1, '2025-09-10 21:06:27', '2025-10-19 21:06:27');
INSERT INTO `products` VALUES (2, 'Commuter Boot', 'Durable leather boot for daily wear', 180.00, 0.00, 20, '43', 'Brown', 'products/commuter_boot.jpg', 2, '2025-08-21 21:06:27', '2025-10-18 21:06:27');
INSERT INTO `products` VALUES (3, 'Beach Sandal', 'Comfortable rubber sandal', 25.00, 5.00, 100, '40-45', 'Black', 'products/beach_sandal.jpg', 3, '2025-10-10 21:06:27', '2025-10-19 21:06:27');
INSERT INTO `products` VALUES (4, 'Urban Sneak', 'Stylish city sneaker', 95.00, 15.00, 30, '41', 'Gray', 'products/urban_sneak.jpg', 1, '2025-10-05 21:06:27', '2025-10-19 21:06:27');

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','member') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'member',
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `status` enum('active','banned') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'active',
  `created_at` datetime NULL DEFAULT current_timestamp(),
  `updated_at` datetime NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `email`(`email` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 'Admin User', 'admin@example.com', 'Password123!', 'admin', NULL, '0123456789', 'active', '2025-10-20 21:06:27', '2025-10-20 21:06:27');
INSERT INTO `users` VALUES (2, 'Tran Khoa', 'tkhoa@example.com', 'Password123!', 'member', 'avatars/khoa.jpg', '0987654321', 'active', '2025-09-20 21:06:27', '2025-10-19 21:06:27');
INSERT INTO `users` VALUES (3, 'Nguyen An', 'anan@example.com', 'Password123!', 'member', NULL, '0911222333', 'active', '2025-09-05 21:06:27', '2025-10-18 21:06:27');
INSERT INTO `users` VALUES (4, 'Le Thi B', 'lethi.b@example.com', 'Password123!', 'member', 'avatars/lethi.jpg', '0909988776', 'active', '2025-08-21 21:06:27', '2025-10-17 21:06:27');
INSERT INTO `users` VALUES (5, 'Pham C', 'pham.c@example.com', 'Password123!', 'member', NULL, '0900111222', 'banned', '2025-07-22 21:06:27', '2025-10-10 21:06:27');
INSERT INTO `users` VALUES (6, 'Guest One', 'guest1@example.com', 'Password123!', 'member', NULL, NULL, 'active', '2025-10-10 21:06:27', '2025-10-15 21:06:27');
INSERT INTO `users` VALUES (7, 'Merchant', 'merchant@example.com', 'Password123!', 'member', 'avatars/merchant.png', '0933222111', 'active', '2025-06-22 21:06:27', '2025-09-30 21:06:27');
INSERT INTO `users` VALUES (8, 'Support', 'support@example.com', 'Password123!', 'admin', NULL, '0911999888', 'active', '2025-10-15 21:06:27', '2025-10-19 21:06:27');
INSERT INTO `users` VALUES (9, 'Test User', 'testuser@example.com', 'Password123!', 'member', NULL, '0944333222', 'active', '2025-10-19 21:06:27', '2025-10-20 21:06:27');
INSERT INTO `users` VALUES (10, 'Alice Nguyen', 'alice.nguyen@example.com', 'Password123!', 'member', 'avatars/alice.png', '0966554433', 'active', '2025-10-18 21:06:27', '2025-10-19 21:06:27');

SET FOREIGN_KEY_CHECKS = 1;

-- 
CREATE TABLE `about_sections` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `sort_order` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `about_sections`
--

INSERT INTO `about_sections` (`id`, `title`, `subtitle`, `description`, `image_url`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Câu chuyện thương hiệu', 'Giày tốt cho mọi bước chân', 'Mô tả ngắn...', '/uploads/about/story.jpg', 1, '2025-12-01 21:44:56', '2025-12-01 21:44:56'),
(2, 'Giá trị cốt lõi', 'Chất lượng – Thoải mái – Phong cách', 'Mô tả ngắn...', '/uploads/about/core-values.jpg', 2, '2025-12-01 21:44:56', '2025-12-01 21:44:56'),
(3, 'Đội ngũ & dịch vụ', 'Đồng hành cùng khách hàng', 'Mô tả ngắn...', '/uploads/about/team.jpg', 3, '2025-12-01 21:44:56', '2025-12-01 21:44:56');



