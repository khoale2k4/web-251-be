<?php
require_once __DIR__ . '/../services/UserService.php';

class UserController {
    private $service;

    public function __construct($pdo) {
        $this->service = new UserService($pdo);
    }

    public function handleRequest($request) {
        if ($request === "/users" && $_SERVER["REQUEST_METHOD"] === "GET") {
            echo json_encode($this->service->getAllUsers());
        } elseif (preg_match("/\/users\/(\d+)/", $request, $matches) && $_SERVER["REQUEST_METHOD"] === "GET") {
            $id = (int)$matches[1];
            echo json_encode($this->service->getUserById($id) ?: ["error" => "User not found"]);
        } elseif ($request === "/users" && $_SERVER["REQUEST_METHOD"] === "POST") {
            $data = json_decode(file_get_contents("php://input"), true);
            $id = $this->service->createUser($data["name"], $data["email"]);
            echo json_encode(["success" => true, "id" => $id]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Not found"]);
        }
        exit;
    }
}
