<?php
require_once __DIR__ . '/../models/UserModel.php';

class UserService
{
    private $userModel;

    public function __construct($pdo)
    {
        $this->userModel = new UserModel($pdo);
    }

    // Lấy tất cả người dùng (hỗ trợ phân trang, tìm kiếm)
    public function getAllUsers($page = 1, $limit = 10, $filters = [])
    {
        $page = max(1, (int) $page);
        $limit = max(1, min(50, (int) $limit));
        $search = isset($filters['search']) ? trim($filters['search']) : '';
        $status = isset($filters['status']) ? strtolower(trim($filters['status'])) : null;

        if ($status !== null && $status !== '' && !in_array($status, ['active', 'banned'])) {
            throw new Exception("Invalid status filter");
        }

        $result = $this->userModel->getPaginatedUsers(
            $page,
            $limit,
            $search,
            $status !== '' ? $status : null
        );

        $totalPages = $limit > 0 ? (int) ceil($result['total'] / $limit) : 1;

        return [
            'success' => true,
            'data' => $result['users'],
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $result['total'],
                'total_pages' => max(1, $totalPages)
            ],
            'stats' => $result['stats'] ?? null
        ];
    }

    // Lấy người dùng theo ID
    public function getUserById($id)
    {
        return $this->userModel->getById($id);
    }

    // Tạo tài khoản mới
    public function createUser($name, $email, $password, $role = 'member')
    {
        if (empty($email) || empty($password)) {
            throw new Exception("Email, and password are required");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }

        // Mã hoá mật khẩu
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        return $this->userModel->create($name, $email, $hashedPassword, $role);
    }

    // Đăng nhập
    public function login($email, $password)
    {
        if (empty($email) || empty($password)) {
            throw new Exception("Email and password are required");
        }

        $user = $this->userModel->getByEmail($email);
        if (!$user) {
            throw new Exception("User not found");
        }

        if (!password_verify($password, $user['password'])) {
            throw new Exception("Invalid password");
        }

        if ($user['status'] === 'banned') {
            throw new Exception("User account is banned");
        }

        // Không trả password ra ngoài
        unset($user['password']);
        return $user;
    }

    // Cập nhật thông tin người dùng
    public function updateUser($id, $data)
    {
        // Chỉ cho phép cập nhật 1 số trường
        $allowedFields = ['name', 'email', 'avatar', 'phone', 'password', 'status', 'role'];
        $updateData = [];

        foreach ($allowedFields as $field) {
            if (isset($data[$field]) && $data[$field] !== '') {
                if ($field === 'password') {
                    $updateData[$field] = password_hash($data[$field], PASSWORD_BCRYPT);
                } else {
                    $updateData[$field] = $data[$field];
                }
            }
        }

        if (empty($updateData)) {
            throw new Exception("No valid fields to update");
        }

        return $this->userModel->update($id, $updateData);
    }

    public function updateUserStatus($id, $status)
    {
        $status = strtolower(trim($status ?? ''));
        $allowed = ['active', 'banned'];

        if (!in_array($status, $allowed)) {
            throw new Exception("Invalid status value");
        }

        $updated = $this->userModel->updateStatus($id, $status);
        if (!$updated) {
            throw new Exception("User not found or status unchanged");
        }

        return [
            'success' => true,
            'message' => 'Cập nhật trạng thái thành công',
            'data' => [
                'id' => (int) $id,
                'status' => $status
            ]
        ];
    }
}
