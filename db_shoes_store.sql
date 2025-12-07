-- MySQL dump 10.13  Distrib 9.4.0, for macos26.0 (arm64)
--
-- Host: 127.0.0.1    Database: shoe_store
-- ------------------------------------------------------
-- Server version	9.4.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `about_sections`
--

DROP TABLE IF EXISTS `about_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `about_sections` (
  `id` int NOT NULL,
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtitle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` tinyint NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `about_sections`
--

LOCK TABLES `about_sections` WRITE;
/*!40000 ALTER TABLE `about_sections` DISABLE KEYS */;
INSERT INTO `about_sections` VALUES (1,'Câu chuyện thương hiệu','Giày tốt cho mọi bước chân','Mô tả ngắn...','/uploads/story.jpg',1,'2025-12-01 21:44:56','2025-12-01 21:44:56'),(2,'Giá trị cốt lõi','Chất lượng – Thoải mái – Phong cách','Mô tả ngắn...','/uploads/core-values.jpg',2,'2025-12-01 21:44:56','2025-12-01 21:44:56'),(3,'Đội ngũ & dịch vụ','Đồng hành cùng khách hàng','Mô tả ngắn...','/uploads/team.jpg',3,'2025-12-01 21:44:56','2025-12-01 21:44:56');
/*!40000 ALTER TABLE `about_sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cart_items`
--

DROP TABLE IF EXISTS `cart_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cart_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cart_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `cart_id` (`cart_id`) USING BTREE,
  KEY `product_id` (`product_id`) USING BTREE,
  CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart_items`
--

LOCK TABLES `cart_items` WRITE;
/*!40000 ALTER TABLE `cart_items` DISABLE KEYS */;
INSERT INTO `cart_items` VALUES (1,1,4,1),(2,2,2,1),(7,3,5,1);
/*!40000 ALTER TABLE `cart_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carts`
--

DROP TABLE IF EXISTS `carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `user_id` (`user_id`) USING BTREE,
  CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carts`
--

LOCK TABLES `carts` WRITE;
/*!40000 ALTER TABLE `carts` DISABLE KEYS */;
INSERT INTO `carts` VALUES (1,9,'2025-10-19 21:06:27','2025-10-20 21:06:27'),(2,10,'2025-10-18 21:06:27','2025-10-19 21:06:27'),(3,1,'2025-12-04 09:38:07','2025-12-04 09:38:07'),(4,4,'2025-12-04 09:48:57','2025-12-04 09:48:57');
/*!40000 ALTER TABLE `carts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `comment_type` enum('product','post') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'product',
  `product_id` int DEFAULT NULL,
  `post_id` int DEFAULT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating` int DEFAULT NULL,
  `status` enum('pending','approved','hidden','spam') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'approved',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `user_id` (`user_id`) USING BTREE,
  KEY `product_id` (`product_id`) USING BTREE,
  KEY `post_id` (`post_id`) USING BTREE,
  KEY `idx_comments_type_created` (`comment_type`,`created_at`) USING BTREE,
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` VALUES (1,3,'product',1,NULL,'Very comfortable and light.',5,'approved','2025-10-11 21:06:27','2025-11-12 20:13:58'),(2,6,'product',3,NULL,'Good value for the price.',4,'approved','2025-10-15 21:06:27','2025-11-12 20:13:58'),(3,4,'post',NULL,1,'Great collection! The colors are lovely.',4,'approved','2025-10-16 21:06:27','2025-11-12 20:13:58'),(4,3,'product',2,NULL,'Lorem',4,'approved','2025-10-21 18:05:55','2025-11-12 20:13:58'),(5,3,'post',NULL,1,'Bình luận thử',5,'approved','2025-10-21 18:53:20','2025-11-12 20:13:58'),(6,3,'post',NULL,2,'Bình luận thử 2',1,'approved','2025-10-21 18:53:45','2025-11-12 20:13:58'),(8,3,'product',1,NULL,'Bình luận thử',4,'approved','2025-10-21 23:01:22','2025-11-12 20:13:58'),(9,3,'product',1,NULL,'Bình luận thử',4,'approved','2025-10-21 23:02:04','2025-11-12 20:13:58'),(10,3,'post',NULL,2,'Bình luận thử',4,'approved','2025-10-21 23:08:28','2025-11-12 20:13:58'),(11,3,'post',NULL,1,'Bình luận thử',4,'approved','2025-10-21 23:08:30','2025-11-12 20:13:58'),(12,3,'post',NULL,1,'Bình luận thử',4,'approved','2025-10-21 23:08:34','2025-11-12 20:13:58'),(33,2,'product',1,NULL,'Giày rất nhẹ và thoáng khí, đi chạy bộ rất thoải mái!',5,'approved','2025-11-16 08:30:00','2025-11-16 08:30:00'),(34,4,'product',1,NULL,'Chất lượng tốt, giá hơi cao nhưng xứng đáng.',4,'approved','2025-11-17 10:15:00','2025-11-17 10:15:00'),(35,7,'product',1,NULL,'Đế giày êm ái, thiết kế đẹp mắt. Rất hài lòng!',5,'approved','2025-11-18 14:20:00','2025-11-18 14:20:00'),(36,3,'product',2,NULL,'Boot da thật, chất lượng cao cấp. Đi rất bền!',5,'approved','2025-11-16 09:45:00','2025-11-16 09:45:00'),(37,6,'product',2,NULL,'Giày đẹp nhưng hơi nặng, phù hợp mùa đông.',4,'approved','2025-11-17 15:30:00','2025-11-17 15:30:00'),(38,9,'product',3,NULL,'Dép rất thoải mái, giá rẻ mà chất lượng tốt!',5,'approved','2025-11-16 11:00:00','2025-11-16 11:00:00'),(39,10,'product',3,NULL,'Đi biển rất ổn, không trơn trượt.',4,'approved','2025-11-18 16:45:00','2025-11-18 16:45:00'),(40,2,'product',4,NULL,'Giày phong cách, đi trong thành phố rất hợp!',5,'approved','2025-11-17 08:20:00','2025-11-17 08:20:00'),(41,4,'product',4,NULL,'Màu xám rất dễ phối đồ, form dáng đẹp.',4,'approved','2025-11-18 13:10:00','2025-11-18 13:10:00'),(42,6,'product',4,NULL,'Giá tốt, chất lượng ổn. Sẽ giới thiệu cho bạn bè!',5,'approved','2025-11-19 10:30:00','2025-11-19 10:30:00'),(44,9,'post',NULL,1,'Các mẫu giày trong bộ sưu tập đều rất trendy!',NULL,'approved','2025-11-17 11:30:00','2025-11-17 11:30:00'),(45,3,'post',NULL,2,'Hướng dẫn rất chi tiết, cảm ơn admin đã chia sẻ!',NULL,'approved','2025-11-16 10:15:00','2025-11-16 10:15:00'),(46,10,'post',NULL,2,'Mình đã thử và boot da sạch bóng như mới!',NULL,'approved','2025-11-18 14:45:00','2025-11-18 14:45:00'),(47,2,'post',NULL,5,'Bài viết rất hữu ích, giúp mình cập nhật xu hướng mới nhất!',NULL,'approved','2025-11-17 09:20:00','2025-11-17 09:20:00'),(48,6,'post',NULL,5,'Top 10 này đều là những mẫu giày đỉnh cao!',NULL,'approved','2025-11-18 15:10:00','2025-11-18 15:10:00'),(50,7,'post',NULL,6,'Đọc xong mình tự tin hơn khi mua giày rồi!',NULL,'approved','2025-11-19 08:50:00','2025-11-19 08:50:00'),(53,1,'post',NULL,5,'Quá đỉnh',NULL,'approved','2025-11-21 19:26:32','2025-11-21 19:26:32'),(54,1,'product',7,NULL,'Uar gif z\n',3,'approved','2025-12-04 14:36:11','2025-12-04 14:36:11');
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contacts`
--

DROP TABLE IF EXISTS `contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contacts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('new','read','replied') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'new',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contacts`
--

LOCK TABLES `contacts` WRITE;
/*!40000 ALTER TABLE `contacts` DISABLE KEYS */;
INSERT INTO `contacts` VALUES (1,'Customer A','customer.a@example.com','0900123456','Order inquiry','I have a question about my order #123.','new','2025-10-17 21:06:27'),(2,'Customer B','customer.b@example.com',NULL,'Product question','Does the Urban Sneak come in wide sizes?','read','2025-10-19 21:06:27');
/*!40000 ALTER TABLE `contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `faqs`
--

DROP TABLE IF EXISTS `faqs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `faqs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `question` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','answered') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `user_id` (`user_id`) USING BTREE,
  CONSTRAINT `faqs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `faqs`
--

LOCK TABLES `faqs` WRITE;
/*!40000 ALTER TABLE `faqs` DISABLE KEYS */;
INSERT INTO `faqs` VALUES (1,NULL,'What is the return policy?','You can return items within 14 days in original condition.','answered','2025-09-20 21:06:27'),(2,7,'Do you ship internationally?','Yes, we ship to selected countries. Check shipping page for details.','pending','2025-10-18 21:06:27');
/*!40000 ALTER TABLE `faqs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int DEFAULT '1',
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `order_id` (`order_id`) USING BTREE,
  KEY `product_id` (`product_id`) USING BTREE,
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (1,1,1,2,120.00),(2,2,3,1,25.00);
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `shipping_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','confirmed','shipped','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `user_id` (`user_id`) USING BTREE,
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,3,240.00,'123 Le Loi, District 1, HCMC','credit_card',NULL,'completed','2025-10-13 21:06:27'),(2,6,25.00,'45 Nguyen Hue, District 3','cod','Please deliver between 9-12','pending','2025-10-18 21:06:27');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `page_contents`
--

DROP TABLE IF EXISTS `page_contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `page_contents` (
  `id` int NOT NULL AUTO_INCREMENT,
  `home_hero_title` varchar(255) NOT NULL DEFAULT '',
  `home_hero_subtitle` varchar(255) NOT NULL DEFAULT '',
  `home_hero_button_text` varchar(100) NOT NULL DEFAULT '',
  `home_hero_button_link` varchar(255) NOT NULL DEFAULT '',
  `home_hero_image` varchar(255) NOT NULL DEFAULT '',
  `home_intro_title` varchar(255) NOT NULL DEFAULT '',
  `home_intro_text` text,
  `about_title` varchar(255) NOT NULL DEFAULT '',
  `about_content` text,
  `about_image` varchar(255) NOT NULL DEFAULT '',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `page_contents`
--

LOCK TABLES `page_contents` WRITE;
/*!40000 ALTER TABLE `page_contents` DISABLE KEYS */;
INSERT INTO `page_contents` VALUES (1,'','','','','','','','','','','2025-11-18 09:58:37','2025-11-18 09:58:37'),(2,'','','','','','','','Hello','123','[&quot;/storage/1763078881_sp.png&quot;,&quot;/storage/logo-8.png&quot;,&quot;/storage/no-img-13.jpg&quot;]','2025-11-18 09:58:47','2025-11-26 08:55:25');
/*!40000 ALTER TABLE `page_contents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `posts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `excerpt` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `author_id` int DEFAULT NULL,
  `status` enum('draft','scheduled','published','archived') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `published_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `slug` (`slug`) USING BTREE,
  KEY `author_id` (`author_id`) USING BTREE,
  KEY `idx_posts_status_created` (`status`,`created_at`) USING BTREE,
  FULLTEXT KEY `idx_posts_search` (`title`,`content`,`excerpt`),
  CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES (1,'New Fall Collection','new-fall-collection','Announcing our new fall shoe collection...','Announcing our new fall shoe collection...','news/fall.jpg',2,'published','2025-09-30 21:06:27','2025-11-12 20:13:58','2025-09-30 21:06:27'),(2,'How to Clean Leather Boots','clean-leather-boots','Step by step guide to clean and maintain leather boots...','Step by step guide to clean and maintain leather boots...',NULL,8,'published','2025-10-08 21:06:27','2025-11-12 20:13:58','2025-10-08 21:06:27'),(4,'New shoe','post-4','string','string',NULL,NULL,'published','2025-10-21 11:11:26','2025-11-12 20:13:58','2025-10-21 11:11:26'),(5,'Top 10 Xu Hướng Giày Sneaker 2025','top-10-xu','<p>Khám phá những mẫu sneaker hot nhất được giới trẻ yêu thích trong năm 2025.</p>','Khám phá những mẫu sneaker hot nhất được giới trẻ yêu thích trong năm 2025.','news/sneaker-trends-2025.jpg',2,'published','2025-10-22 23:25:56','2025-11-21 15:38:24','2025-11-21 15:38:00'),(6,'Cách Phân Biệt Giày Chính Hãng Và Giả','phan-biet-giay-chinh-hang','<p>Hướng dẫn chi tiết giúp bạn nhận biết giày chính hãng so với hàng giả, tránh bị lừa.</p><p><br></p>','Hướng dẫn chi tiết giúp bạn nhận biết giày chính hãng so với hàng giả, tránh bị lừa.','news/authentic-check.jpg',3,'archived','2025-10-22 23:25:56','2025-11-21 15:46:17','2025-10-21 05:25:00'),(7,'5 Mẹo Giữ Giày Trắng Luôn Sạch Như Mới','giu-giay-trang-sach','<p>Bí quyết giúp đôi giày trắng của bạn luôn sáng bóng dù đi cả năm.</p>','Bí quyết giúp đôi giày trắng của bạn luôn sáng bóng dù đi cả năm.','news/clean-white-shoes.jpg',4,'published','2025-10-22 23:25:56','2025-11-20 22:55:40','2025-10-22 23:25:56'),(8,'Lịch Sử Thương Hiệu Nike: Từ Zero Đến Huyền Thoại','lich-su-nike','<p>Câu chuyện đầy cảm hứng về hành trình phát triển của Nike – biểu tượng thể thao toàn cầu.</p>','Câu chuyện đầy cảm hứng về hành trình phát triển của Nike – biểu tượng thể thao toàn cầu.','news/nike-history.jpg',5,'published','2025-10-22 23:25:56','2025-11-21 18:33:23','2025-11-21 18:29:00'),(9,'Adidas vs Nike: Cuộc Đua Công Nghệ Đỉnh Cao','adidas-vs-nike','So sánh công nghệ và thiết kế giữa hai ông lớn trong ngành thể thao.','So sánh công nghệ và thiết kế giữa hai ông lớn trong ngành thể thao.','news/adidas-vs-nike.jpg',2,'published','2025-10-22 23:25:56','2025-11-12 20:13:58','2025-10-22 23:25:56'),(10,'Cách Lựa Chọn Giày Chạy Bộ Phù Hợp Với Dáng Chân','chon-giay-chay-bo','Giày chạy bộ phù hợp giúp cải thiện hiệu suất và giảm chấn thương khi luyện tập.','Giày chạy bộ phù hợp giúp cải thiện hiệu suất và giảm chấn thương khi luyện tập.','news/running-shoes.jpg',3,'published','2025-10-22 23:25:56','2025-11-12 20:13:58','2025-10-22 23:25:56'),(11,'Top 7 Đôi Giày Nam Đáng Mua Dưới 2 Triệu','giay-nam-duoi-2-trieu','Danh sách những đôi giày chất lượng cao với mức giá hợp lý dành cho nam giới.','Danh sách những đôi giày chất lượng cao với mức giá hợp lý dành cho nam giới.','news/mens-shoes-under-2m.jpg',4,'published','2025-10-22 23:25:56','2025-11-12 20:13:58','2025-10-22 23:25:56'),(12,'Phối Đồ Với Giày Sneaker: Đơn Giản Mà Chất','phoi-do-voi-sneaker','Gợi ý cách phối đồ với sneaker để bạn luôn nổi bật và cá tính.','Gợi ý cách phối đồ với sneaker để bạn luôn nổi bật và cá tính.','news/sneaker-outfit.jpg',5,'published','2025-10-22 23:25:56','2025-11-12 20:13:58','2025-10-22 23:25:56'),(13,'Làm Sao Để Giày Không Bị Hôi Khi Đi Lâu','meo-giay-khong-hoi','Mẹo nhỏ giúp đôi giày của bạn luôn thơm tho và dễ chịu suốt cả ngày.','Mẹo nhỏ giúp đôi giày của bạn luôn thơm tho và dễ chịu suốt cả ngày.','news/deodorize-shoes.jpg',3,'published','2025-10-22 23:25:56','2025-11-12 20:13:58','2025-10-22 23:25:56'),(14,'Giày Cổ Cao Hay Cổ Thấp: Nên Chọn Loại Nào?','giay-co-cao-hay-thap','Phân tích ưu nhược điểm của giày cổ cao và cổ thấp để bạn chọn đúng loại phù hợp.','Phân tích ưu nhược điểm của giày cổ cao và cổ thấp để bạn chọn đúng loại phù hợp.','news/high-vs-low-top.jpg',2,'published','2025-10-22 23:25:56','2025-11-12 20:13:58','2025-10-22 23:25:56'),(15,'Hôm nay trời đệp quáaaaa','hom-nay-troi-dep-quaaaaa','<p>Lorem issue</p>','Lorem issue',NULL,1,'draft','2025-11-04 16:17:07','2025-11-21 07:47:55','2025-11-04 09:17:00'),(17,'Bla','bla','<p><strong><img src=\"../../assets/uploads/news/c719e1d591e0c805ebbc4edb059a100c.jpg\">Bla oh no </strong><em>jednjnj ựndnn</em></p>','bla bla',NULL,1,'draft','2025-11-21 07:31:49','2025-11-21 15:50:15','2025-11-06 00:36:00');
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_categories`
--

DROP TABLE IF EXISTS `product_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_categories`
--

LOCK TABLES `product_categories` WRITE;
/*!40000 ALTER TABLE `product_categories` DISABLE KEYS */;
INSERT INTO `product_categories` VALUES (1,'Sneakers','Casual and athletic sneakers'),(2,'Boots','Leather and winter boots'),(3,'Sandals','Open-toe summer sandals');
/*!40000 ALTER TABLE `product_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) NOT NULL,
  `discount` decimal(5,2) DEFAULT '0.00',
  `stock` int DEFAULT '0',
  `size` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `category_id` (`category_id`) USING BTREE,
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'AirFlex Runner','Lightweight running sneaker',120.00,10.00,50,'42','#2d5e8f','products/airflex.jpg',1,'2025-09-10 21:06:27','2025-12-04 15:22:50'),(2,'Commuter Boot','Durable leather boot for daily wear',180.00,0.00,20,'43','#2d5e8f','products/commuter_boot.jpg',2,'2025-08-21 21:06:27','2025-12-04 15:22:50'),(3,'Beach Sandal','Comfortable rubber sandal',25.00,5.00,100,'40-45','#2d5e8f','products/beach_sandal.jpg',3,'2025-10-10 21:06:27','2025-12-04 15:22:50'),(4,'Urban Sneak','Stylish city sneaker',95.00,15.00,30,'41','#2d5e8f','products/urban_sneak.jpg',1,'2025-10-05 21:06:27','2025-12-04 15:22:50'),(5,'Giayf 1','hehe',123333.00,12.00,2,'40-45','#2d5e8f','/storage/a-nh-ma-n-hi-nh-2025-11-26-lu-c-12-35-01.png',3,'2025-12-04 09:52:24','2025-12-04 14:54:32'),(6,'jkashdkjadbkjabsdkjasbdkjasbkdabnskjcnaskcnaksnkjdbakjsdbaksjdkasjhdajsd','hehe',25.00,2.00,1,'123','#2d5e8f','/storage/non-luoi-trai-water-7-500x500-u-o-c-xo-a-ne-n-2.png',1,'2025-12-04 09:52:46','2025-12-04 15:22:50'),(7,'jkashdkjadbkjabsdkjasbdkjasbkdabnskjcnaskcnaksnkjdbakjsdbaksjdkasjhdajsd','hehe',25.00,2.00,1,'123','#2d5e8f','/storage/non-luoi-trai-water-7-500x500-u-o-c-xo-a-ne-n-2.png',1,'2025-12-04 09:52:46','2025-12-04 15:22:50'),(8,'Giayf 1','hehe',123333.00,12.00,2,'40-45','#2d5e8f','http://localhost:8000//storage/a-nh-ma-n-hi-nh-2025-11-26-lu-c-12-35-u-o-c-xo-a-ne-n-01-2.png',3,'2025-12-04 09:52:24','2025-12-04 15:22:50'),(9,'Urban Sneak','Stylish city sneaker',95.00,15.00,30,'41','#2d5e8f','products/urban_sneak.jpg',1,'2025-10-05 21:06:27','2025-12-04 15:22:50'),(10,'Beach Sandal','Comfortable rubber sandal',25.00,5.00,100,'40-45','#2d5e8f','products/beach_sandal.jpg',3,'2025-10-10 21:06:27','2025-12-04 15:22:50'),(11,'Commuter Boot','Durable leather boot for daily wear',180.00,0.00,20,'43','#2d5e8f','/storage/a-nh-ma-n-hi-nh-2025-10-11-lu-c-13-49-47-2.png',2,'2025-08-21 21:06:27','2025-12-04 15:39:21'),(12,'AirFlex Runner','Lightweight running sneaker',120.00,10.00,50,'42','#2d5e8f','products/airflex.jpg',1,'2025-09-10 21:06:27','2025-12-04 15:22:50'),(13,'AirFlex Runner','Lightweight running sneaker',120.00,10.00,50,'42','#2d5e8f','products/airflex.jpg',1,'2025-09-10 21:06:27','2025-12-04 15:22:50'),(14,'Commuter Boot','Durable leather boot for daily wear',180.00,0.00,20,'43','#2d5e8f','products/commuter_boot.jpg',2,'2025-08-21 21:06:27','2025-12-04 15:22:50'),(15,'Beach Sandal','Comfortable rubber sandal',25.00,5.00,100,'40-45','#2d5e8f','products/beach_sandal.jpg',3,'2025-10-10 21:06:27','2025-12-04 15:22:50'),(16,'Urban Sneak','Stylish city sneaker',95.00,15.00,30,'41','#2d5e8f','products/urban_sneak.jpg',1,'2025-10-05 21:06:27','2025-12-04 15:22:50'),(17,'Giayf 1','hehe',123333.00,12.00,2,'40-45','#2d5e8f','/storage/a-nh-ma-n-hi-nh-2025-11-26-lu-c-12-35-u-o-c-xo-a-ne-n-01-2.png',3,'2025-12-04 09:52:24','2025-12-04 15:22:50'),(18,'jkashdkjadbkjabsdkjasbdkjasbkdabnskjcnaskcnaksnkjdbakjsdbaksjdkasjhdajsd','hehe',25.00,2.00,1,'123','#2d5e8f','/storage/non-luoi-trai-water-7-500x500-u-o-c-xo-a-ne-n-2.png',1,'2025-12-04 09:52:46','2025-12-04 15:22:50'),(19,'jkashdkjadbkjabsdkjasbdkjasbkdabnskjcnaskcnaksnkjdbakjsdbaksjdkasjhdajsd','hehe',25.00,2.00,1,'123','#2d5e8f','/storage/non-luoi-trai-water-7-500x500-u-o-c-xo-a-ne-n-2.png',1,'2025-12-04 09:52:46','2025-12-04 15:22:50'),(20,'Giayf 1','hehe',123333.00,12.00,2,'40-45','#2d5e8f','/storage/non-luoi-trai-water-7-500x500-1.jpg',3,'2025-12-04 09:52:24','2025-12-04 15:22:50'),(21,'Urban Sneak','Stylish city sneaker',95.00,15.00,30,'41','#2d5e8f','products/urban_sneak.jpg',1,'2025-10-05 21:06:27','2025-12-04 15:22:50'),(22,'Beach Sandal','Comfortable rubber sandal',25.00,5.00,100,'40-45','#2d5e8f','products/beach_sandal.jpg',3,'2025-10-10 21:06:27','2025-12-04 15:22:50'),(23,'Commuter Boot','Durable leather boot for daily wear',180.00,0.00,20,'43','#2d5e8f','products/commuter_boot.jpg',2,'2025-08-21 21:06:27','2025-12-04 15:22:50'),(24,'AirFlex Runner','Lightweight running sneaker',120.00,10.00,50,'42','#2d5e8f','products/airflex.jpg',1,'2025-09-10 21:06:27','2025-12-04 15:22:50');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `site_settings`
--

DROP TABLE IF EXISTS `site_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `site_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Shoe Store',
  `site_title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Cửa hàng giày dép',
  `site_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `site_keywords` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `favicon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `facebook` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instagram` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `youtube` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `about_us` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `working_hours` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `copyright` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_settings`
--

LOCK TABLES `site_settings` WRITE;
/*!40000 ALTER TABLE `site_settings` DISABLE KEYS */;
INSERT INTO `site_settings` VALUES (1,'Shoe Storek','Cửa hàng giày dép chất lượng cao','Chuyên cung cấp các loại giày thể thao, giày da, boots và sandals chính hãng với giá tốt nhất.','giày, giày thể thao, sneakers, boots, giày da, giày chạy bộ','/storage/a-nh-ma-n-hi-nh-2025-11-26-lu-c-12-35-u-o-c-xo-a-ne-n-01-1.png','/storage/non-luoi-trai-water-7-500x500.jpg','contact@shoestore.vn','0123-456-789','123 Nguyễn Huệ, Quận 1, TP.HCM, Việt Nam','https://facebook.com/shoestore','https://instagram.com/shoestore','https://youtube.com/shoestore','Shoe Store là cửa hàng chuyên cung cấp các loại giày chất lượng cao từ các thương hiệu nổi tiếng. Chúng tôi cam kết mang đến cho khách hàng những sản phẩm chính hãng, đa dạng mẫu mã với giá cả hợp lý.','Thứ 2 - Thứ 7: 8:00 - 21:00 | Chủ nhật: 9:00 - 20:00','© 2025 Shoe Store. All rights reserved.','2025-11-28 12:41:56');
/*!40000 ALTER TABLE `site_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','member') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'member',
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','banned') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `email` (`email`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin User','admin@example.com','$2y$12$3nBR4M6U5hGc6mkmUB9FVOTUVRL.kenV4bMIu9JmHBxKp6B8dSTD2','admin','/storage/1761529335_Ảnh màn hình 2025-10-11 lúc 13.49.47.png','0123456789','active','2025-10-20 21:06:27','2025-12-04 12:59:20'),(2,'Tran Khoa','tkhoa@example.com','$2y$12$3nBR4M6U5hGc6mkmUB9FVOTUVRL.kenV4bMIu9JmHBxKp6B8dSTD2','member','/storage/1761529335_Ảnh màn hình 2025-10-11 lúc 13.49.47.png','0987654321','active','2025-09-20 21:06:27','2025-12-04 12:59:20'),(3,'Nguyen An','anan@example.com','$2y$12$3nBR4M6U5hGc6mkmUB9FVOTUVRL.kenV4bMIu9JmHBxKp6B8dSTD2','member','/storage/1761529335_Ảnh màn hình 2025-10-11 lúc 13.49.47.png','0911222333','active','2025-09-05 21:06:27','2025-12-04 12:59:20'),(4,'Le Thi B','lethi.b@example.com','$2y$12$3nBR4M6U5hGc6mkmUB9FVOTUVRL.kenV4bMIu9JmHBxKp6B8dSTD2','member','/storage/1761529335_Ảnh màn hình 2025-10-11 lúc 13.49.47.png','0909988776','active','2025-08-21 21:06:27','2025-12-04 12:59:20'),(5,'Pham C','pham.c@example.com','$2y$12$3nBR4M6U5hGc6mkmUB9FVOTUVRL.kenV4bMIu9JmHBxKp6B8dSTD2','member','/storage/1761529335_Ảnh màn hình 2025-10-11 lúc 13.49.47.png','0900111222','banned','2025-07-22 21:06:27','2025-12-04 12:59:20'),(6,'Guest One','guest1@example.com','$2y$12$3nBR4M6U5hGc6mkmUB9FVOTUVRL.kenV4bMIu9JmHBxKp6B8dSTD2','member','/storage/1761529335_Ảnh màn hình 2025-10-11 lúc 13.49.47.png',NULL,'active','2025-10-10 21:06:27','2025-12-04 12:59:20'),(7,'Merchant','merchant@example.com','$2y$12$3nBR4M6U5hGc6mkmUB9FVOTUVRL.kenV4bMIu9JmHBxKp6B8dSTD2','member','/storage/1761529335_Ảnh màn hình 2025-10-11 lúc 13.49.47.png','0933222111','active','2025-06-22 21:06:27','2025-12-04 12:59:20'),(8,'Support','support@example.com','$2y$12$3nBR4M6U5hGc6mkmUB9FVOTUVRL.kenV4bMIu9JmHBxKp6B8dSTD2','admin','/storage/1761529335_Ảnh màn hình 2025-10-11 lúc 13.49.47.png','0911999888','active','2025-10-15 21:06:27','2025-12-04 12:59:20'),(9,'Test User','testuser@example.com','$2y$12$3nBR4M6U5hGc6mkmUB9FVOTUVRL.kenV4bMIu9JmHBxKp6B8dSTD2','member','/storage/1761529335_Ảnh màn hình 2025-10-11 lúc 13.49.47.png','0944333222','active','2025-10-19 21:06:27','2025-12-04 12:59:20'),(10,'Alice Nguyen','alice.nguyen@example.com','$2y$12$3nBR4M6U5hGc6mkmUB9FVOTUVRL.kenV4bMIu9JmHBxKp6B8dSTD2','member','/storage/1761529335_Ảnh màn hình 2025-10-11 lúc 13.49.47.png','0966554433','active','2025-10-18 21:06:27','2025-12-04 12:59:20');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-04 19:39:04
