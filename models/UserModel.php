<?php
class UserModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Lấy tất cả người dùng
    public function getAll()
    {
        $stmt = $this->pdo->query("SELECT id, name, email, role, status, created_at FROM users ORDER BY id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy người dùng theo ID
    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT id, name, email, role, avatar, phone, status, created_at, updated_at FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy người dùng theo email (phục vụ đăng nhập)
    public function getByEmail($email)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Tạo người dùng mới
    public function create($name, $email, $password, $role = 'member')
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO users (name, email, password, role, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$name, $email, $password, $role]);
        return $this->pdo->lastInsertId();
    }

    // Cập nhật thông tin người dùng
    public function update($id, $data)
    {
        $fields = [];
        $values = [];

        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }

        $values[] = $id;

        $sql = "UPDATE users SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($values);
    }

    // Xoá người dùng
    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
