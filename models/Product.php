<?php

class Product {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Tạo sản phẩm mới
     */
    public function create($data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO products (name, description, price, discount, stock, size, color, image, category_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $data['name'],
            $data['description'] ?? null,
            $data['price'],
            $data['discount'] ?? 0,
            $data['stock'] ?? 0,
            $data['size'] ?? null,
            $data['color'] ?? null,
            $data['image'] ?? null,
            $data['category_id'] ?? null
        ]);
        
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Lấy sản phẩm theo ID
     */
    public function getById($id) {
        $sql = "
            SELECT 
                p.*,
                pc.name as category_name,
                (p.price * (100 - p.discount) / 100) as final_price
            FROM products p
            LEFT JOIN product_categories pc ON p.category_id = pc.id
            WHERE p.id = ?
        ";

        error_log("SQL: " . $sql);
        error_log("ID = " . $id);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy tất cả sản phẩm với phân trang và filter
     */
    public function getAll($limit = 20, $offset = 0, $filters = []) {
        $sql = "
            SELECT 
                p.*,
                pc.name as category_name,
                (p.price * (100 - p.discount) / 100) as final_price
            FROM products p
            LEFT JOIN product_categories pc ON p.category_id = pc.id
            WHERE 1=1
        ";

        error_log("SQL (base): " . $sql);

        $params = [];

        // === FILTERS ===
        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = ?";
            $params[] = $filters['category_id'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND p.name LIKE ?";
            $params[] = '%' . $filters['search'] . '%';
        }

        if (isset($filters['min_price'])) {
            $sql .= " AND p.price >= ?";
            $params[] = $filters['min_price'];
        }

        if (isset($filters['max_price'])) {
            $sql .= " AND p.price <= ?";
            $params[] = $filters['max_price'];
        }

        if (!empty($filters['size'])) {
            $sql .= " AND p.size = ?";
            $params[] = $filters['size'];
        }

        if (!empty($filters['color'])) {
            $sql .= " AND p.color = ?";
            $params[] = $filters['color'];
        }

        // === SORT ===
        $orderBy = $filters['order_by'] ?? 'created_at';
        $orderDir = $filters['order_dir'] ?? 'DESC';
        $allowedOrderBy = ['id', 'name', 'price', 'created_at', 'discount', 'stock'];
        $allowedOrderDir = ['ASC', 'DESC'];

        if (in_array($orderBy, $allowedOrderBy) && in_array($orderDir, $allowedOrderDir)) {
            $sql .= " ORDER BY p.$orderBy $orderDir";
        } else {
            $sql .= " ORDER BY p.created_at DESC";
        }

        // === LIMIT & OFFSET (sửa lỗi) ===
        $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;

        error_log("SQL (final): " . $sql);
        error_log("Params: " . json_encode($params));

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    /**
     * Đếm tổng số sản phẩm
     */
    public function count($filters = []) {
        $sql = "SELECT COUNT(*) as total FROM products p WHERE 1=1";
        $params = [];
        
        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = ?";
            $params[] = $filters['category_id'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND p.name LIKE ?";
            $params[] = '%' . $filters['search'] . '%';
        }
        
        if (isset($filters['min_price'])) {
            $sql .= " AND p.price >= ?";
            $params[] = $filters['min_price'];
        }
        
        if (isset($filters['max_price'])) {
            $sql .= " AND p.price <= ?";
            $params[] = $filters['max_price'];
        }
        
        if (!empty($filters['size'])) {
            $sql .= " AND p.size = ?";
            $params[] = $filters['size'];
        }
        
        if (!empty($filters['color'])) {
            $sql .= " AND p.color = ?";
            $params[] = $filters['color'];
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    
    /**
     * Cập nhật sản phẩm
     */
    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        $allowedFields = ['name', 'description', 'price', 'discount', 'stock', 'size', 'color', 'image', 'category_id'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $values[] = $data[$field];
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $id;
        $sql = "UPDATE products SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($values);
    }
    
    /**
     * Xóa sản phẩm
     */
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Lấy sản phẩm theo category
     */
    public function getByCategory($categoryId, $limit = 20, $offset = 0) {
        $stmt = $this->pdo->prepare("
            SELECT 
                p.*,
                pc.name as category_name,
                (p.price * (100 - p.discount) / 100) as final_price
            FROM products p
            LEFT JOIN product_categories pc ON p.category_id = pc.id
            WHERE p.category_id = ?
            ORDER BY p.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$categoryId, $limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lấy sản phẩm mới nhất
     */
    public function getLatest($limit = 10) {
        $stmt = $this->pdo->prepare("
            SELECT 
                p.*,
                pc.name as category_name,
                (p.price * (100 - p.discount) / 100) as final_price
            FROM products p
            LEFT JOIN product_categories pc ON p.category_id = pc.id
            ORDER BY p.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lấy sản phẩm giảm giá
     */
    public function getDiscounted($limit = 20, $offset = 0) {
        $stmt = $this->pdo->prepare("
            SELECT 
                p.*,
                pc.name as category_name,
                (p.price * (100 - p.discount) / 100) as final_price
            FROM products p
            LEFT JOIN product_categories pc ON p.category_id = pc.id
            WHERE p.discount > 0
            ORDER BY p.discount DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Cập nhật stock
     */
    public function updateStock($id, $quantity) {
        $stmt = $this->pdo->prepare("UPDATE products SET stock = ? WHERE id = ?");
        return $stmt->execute([$quantity, $id]);
    }
    
    /**
     * Giảm stock (khi bán hàng)
     */
    public function decreaseStock($id, $quantity) {
        $stmt = $this->pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
        return $stmt->execute([$quantity, $id, $quantity]);
    }
    
    /**
     * Kiểm tra sản phẩm có tồn tại không
     */
    public function exists($id) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
    
    /**
     * Đếm số sản phẩm đang giảm giá
     */
    public function countDiscounted() {
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM products WHERE discount > 0");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
}
