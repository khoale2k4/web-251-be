<?php
// User model

class UserModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Lấy tất cả người dùng
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM users ORDER BY id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy người dùng theo ID
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT id, name, email, created_at FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Thêm người dùng mới
    public function create($name, $email) {
        $stmt = $this->pdo->prepare("INSERT INTO users (name, email, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$name, $email]);
        return $this->pdo->lastInsertId();
    }

    // Xóa người dùng
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
