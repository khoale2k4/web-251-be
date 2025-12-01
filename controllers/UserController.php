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
            if ($request === "/users" && $method === "GET") {
                $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
                $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
                $search = $_GET['search'] ?? '';
                $status = $_GET['status'] ?? null;

                $result = $this->service->getAllUsers($page, $limit, [
                    'search' => $search,
                    'status' => $status
                ]);

                http_response_code(200);
                echo json_encode($result);
            }

            elseif (preg_match("/\/users\/(\d+)/", $request, $matches) && $method === "GET") {
                $id = (int) $matches[1];
                $user = $this->service->getUserById($id);
                echo json_encode($user ?: ["error" => "User not found"]);
            }

            elseif ($request === "/users" && $method === "POST") {
                $data = json_decode(file_get_contents("php://input"), true);
                $id = $this->service->createUser(
                    $data["name"] ?? "",
                    $data["email"] ?? "",
                    $data["password"] ?? "",
                    $data["role"] ?? "member",
                    $data["phone"] ?? null
                );
                echo json_encode(["success" => true, "id" => $id]);
            }

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

            elseif (preg_match("/\/users\/(\d+)\/status/", $request, $matches) && $method === "PATCH") {
                $id = (int) $matches[1];
                $data = json_decode(file_get_contents("php://input"), true);
                $status = $data["status"] ?? null;

                $result = $this->service->updateUserStatus($id, $status);
                http_response_code(200);
                echo json_encode($result);
            }

            elseif (preg_match("/\/users\/(\d+)/", $request, $matches) && $method === "PUT") {
                $id = (int) $matches[1];
                $data = json_decode(file_get_contents("php://input"), true);
                $this->service->updateUser($id, $data);
                echo json_encode(["success" => true, "message" => "User updated successfully"]);
            }

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
