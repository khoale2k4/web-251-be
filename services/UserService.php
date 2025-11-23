<?php
require_once __DIR__ . '/../models/UserModel.php';

class UserService
{
    private $userModel;

    public function __construct($pdo)
    {
        $this->userModel = new UserModel($pdo);
    }

    // Lấy tất cả người dùng
    public function getAllUsers()
    {
        return $this->userModel->getAll();
    }

    // Lấy người dùng theo ID
    public function getUserById($id)
    {
        return $this->userModel->getById($id);
    }

    // Tạo tài khoản mới
    public function createUser($name, $email, $password, $role = 'member', $phone = null)
    {
        if (empty($name) || empty($email) || empty($password)) {
            throw new Exception("Name, email, and password are required");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }

        // Kiểm tra username (name) đã tồn tại
        $existingUserByUsername = $this->userModel->getByUsername($name);
        if ($existingUserByUsername) {
            throw new Exception("Username already exists");
        }

        // Kiểm tra email đã tồn tại
        $existingUserByEmail = $this->userModel->getByEmail($email);
        if ($existingUserByEmail) {
            throw new Exception("Email already exists");
        }

        // Mã hoá mật khẩu
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        return $this->userModel->create($name, $email, $hashedPassword, $role, $phone);
    }

    // Đăng nhập (hỗ trợ username hoặc email)
    public function login($usernameOrEmail, $password)
    {
        if (empty($usernameOrEmail) || empty($password)) {
            throw new Exception("Username/Email and password are required");
        }

        // Thử đăng nhập bằng email trước
        $user = $this->userModel->getByEmail($usernameOrEmail);
        
        // Nếu không tìm thấy bằng email, thử tìm bằng username
        if (!$user) {
            $user = $this->userModel->getByUsername($usernameOrEmail);
        }

        if (!$user) {
            throw new Exception("Invalid credentials");
        }

        if (!password_verify($password, $user['password'])) {
            throw new Exception("Invalid credentials");
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

}
