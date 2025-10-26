<?php

class ProductCategory {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Tạo danh mục mới
     */
    public function create($name, $description = null) {
        $stmt = $this->pdo->prepare("
            INSERT INTO product_categories (name, description)
            VALUES (?, ?)
        ");
        $stmt->execute([$name, $description]);
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Lấy danh mục theo ID
     */
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM product_categories WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lấy tất cả danh mục
     */
    public function getAll($limit = 100, $offset = 0) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM product_categories 
            ORDER BY name ASC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lấy danh mục kèm số lượng sản phẩm
     */
    public function getAllWithProductCount($limit = 100, $offset = 0) {
        $limit = (int) $limit;
        $offset = (int) $offset;
        
        $stmt = $this->pdo->prepare("
            SELECT 
                pc.*,
                COUNT(p.id) as product_count
            FROM product_categories pc
            LEFT JOIN products p ON pc.id = p.category_id
            GROUP BY pc.id
            ORDER BY pc.name ASC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Đếm tổng số danh mục
     */
    public function count() {
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM product_categories");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    
    /**
     * Cập nhật danh mục
     */
    public function update($id, $name, $description = null) {
        $stmt = $this->pdo->prepare("
            UPDATE product_categories 
            SET name = ?, description = ?
            WHERE id = ?
        ");
        return $stmt->execute([$name, $description, $id]);
    }
    
    /**
     * Xóa danh mục
     */
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM product_categories WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Kiểm tra danh mục có tồn tại không
     */
    public function exists($id) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM product_categories WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
    
    /**
     * Kiểm tra tên danh mục đã tồn tại chưa
     */
    public function existsByName($name, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM product_categories WHERE name = ? AND id != ?");
            $stmt->execute([$name, $excludeId]);
        } else {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM product_categories WHERE name = ?");
            $stmt->execute([$name]);
        }
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
    
    /**
     * Đếm số sản phẩm trong danh mục
     */
    public function getProductCount($id) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM products WHERE category_id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }
}

