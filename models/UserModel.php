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

    // Lấy người dùng theo username
    public function getByUsername($username)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE name = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Tạo người dùng mới
    public function create($name, $email, $password, $role = 'member', $phone = null)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO users (name, email, password, role, phone, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$name, $email, $password, $role, $phone]);
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

    // ============ PASSWORD RESET METHODS ============

    /**
     * Reset password về default và bắt buộc đổi mật khẩu
     * 
     * @param int $userId ID người dùng
     * @param string $defaultPassword Password mặc định (đã hash)
     * @return bool
     */
    public function resetToDefaultPassword($userId, $defaultPassword)
    {
        $stmt = $this->pdo->prepare("
            UPDATE users 
            SET password = ?,
                must_change_password = TRUE,
                last_password_change = NOW(),
                updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$defaultPassword, $userId]);
    }

    /**
     * Cập nhật mật khẩu mới (sau khi user đổi)
     * 
     * @param int $userId ID người dùng
     * @param string $hashedPassword Mật khẩu đã hash
     * @return bool
     */
    public function updatePassword($userId, $hashedPassword)
    {
        $stmt = $this->pdo->prepare("
            UPDATE users 
            SET password = ?,
                must_change_password = FALSE,
                last_password_change = NOW(),
                updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$hashedPassword, $userId]);
    }

    /**
     * Kiểm tra user có bắt buộc đổi mật khẩu không
     * 
     * @param int $userId ID người dùng
     * @return bool
     */
    public function mustChangePassword($userId)
    {
        $stmt = $this->pdo->prepare("
            SELECT must_change_password FROM users WHERE id = ?
        ");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (bool)$result['must_change_password'] : false;
    }
}
