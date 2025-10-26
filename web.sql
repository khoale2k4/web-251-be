-- ===========================================
--  DATABASE: shoe_store
--  AUTHOR: Tran Khoa & Team
--  DESCRIPTION: Website bán giày - PHP & MySQL
-- ===========================================

-- 1️⃣ Tạo database
CREATE DATABASE IF NOT EXISTS shoe_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE shoe_store;

-- 2️⃣ Bảng người dùng
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'member') DEFAULT 'member',
    avatar VARCHAR(255),
    phone VARCHAR(20),
    status ENUM('active', 'banned') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 3️⃣ Danh mục sản phẩm (ví dụ: sneaker, boot,...)
CREATE TABLE product_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT
);

-- 4️⃣ Sản phẩm
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    discount DECIMAL(5,2) DEFAULT 0,
    stock INT DEFAULT 0,
    size VARCHAR(10),
    color VARCHAR(50),
    image VARCHAR(255),
    category_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES product_categories(id) ON DELETE SET NULL
);

-- 5️⃣ Đơn hàng
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_price DECIMAL(10,2) NOT NULL,
    shipping_address VARCHAR(255),
    payment_method VARCHAR(50),
    note TEXT,
    status ENUM('pending','confirmed','shipped','completed','cancelled') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 6️⃣ Chi tiết đơn hàng
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- 7️⃣ Bài viết / tin tức
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE,
    content TEXT,
    image VARCHAR(255),
    author_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
);

-- 8️⃣ Bình luận / đánh giá
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    comment_type ENUM('product','post') DEFAULT 'product',
    product_id INT NULL,
    post_id INT NULL,
    content TEXT NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

-- 9️⃣ Hỏi đáp (FAQ)
CREATE TABLE faqs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    question VARCHAR(255) NOT NULL,
    answer TEXT,
    status ENUM('pending','answered') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- 🔟 Liên hệ khách hàng
CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(150),
    message TEXT NOT NULL,
    status ENUM('new','read','replied') DEFAULT 'new',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE carts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cart_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);


USE shoe_store;

-- 1️⃣ USERS
INSERT INTO users (name, email, password, role, avatar, phone, status)
VALUES
('Admin', 'admin@shoestore.com', '123456', 'admin', 'admin.png', '0900000001', 'active'),
('Nguyen Van A', 'a@gmail.com', '123456', 'member', 'a.png', '0900000002', 'active'),
('Tran Thi B', 'b@gmail.com', '123456', 'member', 'b.png', '0900000003', 'active'),
('Le Van C', 'c@gmail.com', '123456', 'member', 'c.png', '0900000004', 'banned'),
('Pham Thi D', 'd@gmail.com', '123456', 'member', 'd.png', '0900000005', 'active');

-- 2️⃣ PRODUCT CATEGORIES
INSERT INTO product_categories (name, description)
VALUES
('Sneakers', 'Giày thể thao, phong cách năng động.'),
('Boots', 'Giày cổ cao dành cho mùa đông hoặc thời trang.'),
('Sandals', 'Dép quai hậu, thoải mái cho mùa hè.'),
('Loafers', 'Giày lười da sang trọng.'),
('Running Shoes', 'Giày chuyên dụng cho chạy bộ.');

-- 3️⃣ PRODUCTS
INSERT INTO products (name, description, price, discount, stock, size, color, image, category_id)
VALUES
('Nike Air Force 1', 'Mẫu giày kinh điển của Nike.', 2500000, 10, 20, '42', 'Trắng', 'airforce1.jpg', 1),
('Adidas Ultraboost', 'Giày chạy bộ thoải mái.', 3500000, 5, 15, '41', 'Đen', 'ultraboost.jpg', 5),
('Converse Chuck 70', 'Giày vải cổ điển.', 1800000, 0, 30, '43', 'Trắng', 'chuck70.jpg', 1),
('Dr. Martens 1460', 'Boots da cổ cao huyền thoại.', 4200000, 15, 10, '42', 'Đen', 'martens1460.jpg', 2),
('Vans Old Skool', 'Phong cách skate cực chất.', 1900000, 0, 25, '41', 'Đen trắng', 'vansoldskool.jpg', 1),
('Nike ZoomX Vaporfly', 'Giày chạy hiệu năng cao.', 5500000, 20, 8, '42', 'Xanh', 'vaporfly.jpg', 5),
('Bitis Hunter Street', 'Giày nội địa Việt cực cool.', 950000, 0, 40, '42', 'Xám', 'hunterstreet.jpg', 1),
('Timberland Classic', 'Boot da lộn bền bỉ.', 4800000, 5, 12, '43', 'Nâu', 'timberland.jpg', 2),
('Crocs Classic Sandal', 'Sandals siêu nhẹ.', 800000, 0, 35, '42', 'Xanh lá', 'crocs.jpg', 3),
('Gucci Horsebit Loafer', 'Loafer cao cấp.', 8900000, 10, 5, '42', 'Đen', 'gucci.jpg', 4),
('Puma Suede', 'Giày thời trang cổ điển.', 1600000, 0, 18, '41', 'Xám', 'pumasuede.jpg', 1),
('New Balance 574', 'Phong cách retro.', 2100000, 5, 22, '42', 'Xanh navy', 'nb574.jpg', 1),
('Nike Pegasus 40', 'Giày chạy nhẹ.', 3200000, 0, 10, '42', 'Trắng xanh', 'pegasus.jpg', 5),
('Adidas Stan Smith', 'Sneaker da trắng đơn giản.', 2200000, 0, 20, '42', 'Trắng xanh', 'stansmith.jpg', 1),
('MLB Chunky', 'Giày đế độn phong cách Hàn.', 2800000, 5, 16, '41', 'Kem', 'mlbchunky.jpg', 1);

-- 4️⃣ ORDERS
INSERT INTO orders (user_id, total_price, shipping_address, payment_method, note, status)
VALUES
(2, 3700000, '123 Lê Lợi, Q.1, TP.HCM', 'COD', 'Giao buổi sáng', 'completed'),
(3, 4200000, '45 Trần Phú, Q.5, TP.HCM', 'VNPay', '', 'shipped'),
(2, 2500000, '12 Nguyễn Huệ, Q.1, TP.HCM', 'Momo', '', 'pending'),
(5, 4800000, '88 Hai Bà Trưng, TP.HCM', 'COD', 'Khách thân thiết', 'confirmed'),
(3, 5500000, '99 Lý Thường Kiệt, TP.HCM', 'VNPay', 'Giao nhanh', 'cancelled');

-- 5️⃣ ORDER ITEMS
INSERT INTO order_items (order_id, product_id, quantity, price)
VALUES
(1, 1, 1, 2250000),
(1, 5, 1, 1450000),
(2, 4, 1, 3570000),
(3, 3, 1, 1800000),
(3, 7, 1, 950000),
(4, 8, 1, 4560000),
(5, 6, 1, 4400000);

-- 6️⃣ POSTS
INSERT INTO posts (title, slug, content, image, author_id)
VALUES
('Top 5 đôi sneaker hot nhất 2025', 'top-5-sneaker-2025', 'Bài viết giới thiệu các mẫu sneaker được yêu thích.', 'sneakerhot.jpg', 1),
('Cách chọn size giày chuẩn', 'chon-size-giay', 'Hướng dẫn chọn size phù hợp cho mọi loại chân.', 'sizeguide.jpg', 1),
('Bí quyết bảo quản giày da', 'bao-quan-giay-da', 'Giữ giày luôn như mới với các mẹo đơn giản.', 'baogiay.jpg', 1),
('Top giày chạy tốt nhất', 'giay-chay-tot-nhat', 'Các mẫu giày giúp bạn đạt hiệu suất cao.', 'running.jpg', 1),
('Mix giày với outfit cực đẹp', 'mix-giay-outfit', 'Gợi ý phối đồ với giày thời trang.', 'mixgiay.jpg', 1);

-- 7️⃣ COMMENTS
INSERT INTO comments (user_id, comment_type, product_id, post_id, content, rating)
VALUES
(2, 'product', 1, NULL, 'Giày đẹp, mang êm.', 5),
(3, 'product', 3, NULL, 'Giá hơi cao nhưng chất lượng tốt.', 4),
(5, 'post', NULL, 1, 'Bài viết rất hữu ích.', 5),
(4, 'product', 7, NULL, 'Giày bình thường.', 3),
(2, 'post', NULL, 2, 'Thông tin chính xác và rõ ràng.', 5);

-- 8️⃣ FAQ
INSERT INTO faqs (user_id, question, answer, status)
VALUES
(2, 'Shop có giao hàng toàn quốc không?', 'Có, shop giao hàng toàn quốc qua GHTK và J&T.', 'answered'),
(3, 'Có thể đổi size không?', 'Được đổi size trong vòng 7 ngày nếu chưa sử dụng.', 'answered'),
(5, 'Có bảo hành không?', NULL, 'pending');

-- 9️⃣ CONTACTS
INSERT INTO contacts (name, email, phone, subject, message, status)
VALUES
('Nguyen Van A', 'a@gmail.com', '0900000002', 'Hỏi về đơn hàng #1', 'Khi nào giao ạ?', 'read'),
('Tran Thi B', 'b@gmail.com', '0900000003', 'Phản hồi sản phẩm', 'Giày rất đẹp.', 'replied'),
('Le Van C', 'c@gmail.com', '0900000004', 'Đổi hàng', 'Tôi muốn đổi sang size 43.', 'new');

-- 🔟 CARTS
INSERT INTO carts (user_id) VALUES (2), (3), (5);

-- 🛒 CART ITEMS
INSERT INTO cart_items (cart_id, product_id, quantity)
VALUES
(1, 1, 1),
(1, 3, 2),
(2, 4, 1),
(2, 5, 1),
(3, 2, 1),
(3, 7, 1);
