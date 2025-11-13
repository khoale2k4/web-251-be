<?php
require_once __DIR__ . '/../services/UserService.php';

class UserController
{
    private $service;

    public function __construct($pdo)
    {
        $this->service = new UserService($pdo);
    }

    public function handleRequest($request)
    {
        header("Content-Type: application/json");

        // Lấy phương thức HTTP
        $method = $_SERVER["REQUEST_METHOD"];

        try {
            // 1️⃣ Lấy tất cả người dùng
            if ($request === "/users" && $method === "GET") {
                echo json_encode($this->service->getAllUsers());
            }

            // 2️⃣ Lấy người dùng theo ID
            elseif (preg_match("/\/users\/(\d+)/", $request, $matches) && $method === "GET") {
                $id = (int) $matches[1];
                $user = $this->service->getUserById($id);
                echo json_encode($user ?: ["error" => "User not found"]);
            }

            // 3️⃣ Tạo người dùng mới
            elseif ($request === "/users" && $method === "POST") {
                $data = json_decode(file_get_contents("php://input"), true);
                $id = $this->service->createUser(
                    $data["name"] ?? "",
                    $data["email"] ?? "",
                    $data["password"] ?? "",
                    $data["role"] ?? "member"
                );
                echo json_encode(["success" => true, "id" => $id]);
            }

            // 4️⃣ Đăng nhập người dùng
            elseif ($request === "/users/login" && $method === "POST") {
                $data = json_decode(file_get_contents("php://input"), true);
                $email = $data["email"] ?? "";
                $password = $data["password"] ?? "";

                $user = $this->service->login($email, $password);
                echo json_encode([
                    "success" => true,
                    "user" => $user
                ]);
            }

            // 5️⃣ Cập nhật thông tin người dùng
            elseif (preg_match("/\/users\/(\d+)/", $request, $matches) && $method === "PUT") {
                $id = (int) $matches[1];
                $data = json_decode(file_get_contents("php://input"), true);
                $this->service->updateUser($id, $data);
                echo json_encode(["success" => true, "message" => "User updated successfully"]);
            }

            // 6️⃣ Không khớp route nào
            else {
                http_response_code(404);
                echo json_encode(["error" => "Not found"]);
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "error" => $e->getMessage()
            ]);
        }

        exit;
    }
}
