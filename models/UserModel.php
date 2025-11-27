<?php
class UserModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Lấy danh sách người dùng có phân trang + tìm kiếm
    public function getPaginatedUsers($page = 1, $limit = 10, $search = '', $status = null)
    {
        $offset = max(0, ($page - 1) * $limit);
        $baseQuery = "FROM users WHERE 1=1";
        $params = [];

        if ($search !== '') {
            $baseQuery .= " AND (name LIKE ? OR email LIKE ? OR phone LIKE ?)";
            $like = '%' . $search . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        if ($status !== null) {
            $baseQuery .= " AND status = ?";
            $params[] = $status;
        }

        $countStmt = $this->pdo->prepare("SELECT COUNT(*) " . $baseQuery);
        foreach ($params as $index => $value) {
            $countStmt->bindValue($index + 1, $value);
        }
        $countStmt->execute();
        $total = (int) $countStmt->fetchColumn();

        $statsStmt = $this->pdo->prepare("SELECT status, COUNT(*) as total " . $baseQuery . " GROUP BY status");
        foreach ($params as $index => $value) {
            $statsStmt->bindValue($index + 1, $value);
        }
        $statsStmt->execute();
        $rawStats = $statsStmt->fetchAll(PDO::FETCH_ASSOC);
        $statusCounts = [
            'active' => 0,
            'banned' => 0,
        ];
        foreach ($rawStats as $row) {
            $key = $row['status'];
            if (isset($statusCounts[$key])) {
                $statusCounts[$key] = (int) $row['total'];
            }
        }

        $dataSql = "
            SELECT id, name, email, role, avatar, phone, status, created_at, updated_at
            {$baseQuery}
            ORDER BY created_at DESC
            LIMIT ? OFFSET ?
        ";

        $dataStmt = $this->pdo->prepare($dataSql);
        $position = 1;
        foreach ($params as $value) {
            $dataStmt->bindValue($position++, $value);
        }
        $dataStmt->bindValue($position++, (int) $limit, PDO::PARAM_INT);
        $dataStmt->bindValue($position, (int) $offset, PDO::PARAM_INT);
        $dataStmt->execute();

        return [
            'users' => $dataStmt->fetchAll(PDO::FETCH_ASSOC),
            'total' => $total,
            'stats' => $statusCounts,
        ];
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

    public function updateStatus($id, $status)
    {
        $stmt = $this->pdo->prepare("UPDATE users SET status = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$status, $id]);
        return $stmt->rowCount() > 0;
    }
}
