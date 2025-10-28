<?php
require_once __DIR__ . '/../models/UserModel.php';

class UserService {
    private $userModel;

    public function __construct($pdo) {
        // Khởi tạo model với PDO
        $this->userModel = new UserModel($pdo);
    }

    public function getAllUsers() {
        return $this->userModel->getAll();
    }

    public function getUserById($id) {
        return $this->userModel->getById($id);
    }

    public function createUser($name, $email) {
        // Validate logic trước khi tạo
        if (empty($name) || empty($email)) {
            throw new Exception("Name and email are required");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }

        // Gọi model để thêm user
        return $this->userModel->create($name, $email);
    }
}
