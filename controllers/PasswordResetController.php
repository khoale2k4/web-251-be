<?php
require_once __DIR__ . '/../services/PasswordResetService.php';

class PasswordResetController
{
    private $service;

    public function __construct($pdo)
    {
        $this->service = new PasswordResetService($pdo);
    }

    public function handleRequest($request, $method)
    {
        header("Content-Type: application/json");

        try {
            // 1️⃣ Tạo yêu cầu reset password (public)
            if ($request === "/password-reset/request" && $method === "POST") {
                $data = json_decode(file_get_contents("php://input"), true);
                $email = $data["email"] ?? "";
                $reason = $data["reason"] ?? null;
                
                $result = $this->service->createResetRequest($email, $reason);
                echo json_encode($result);
            }

            // 2️⃣ Lấy yêu cầu của user hiện tại
            elseif ($request === "/password-reset/my-requests" && $method === "GET") {
                $userId = $_GET['user_id'] ?? null;
                
                if (!$userId) {
                    throw new Exception("User ID is required");
                }
                
                $requests = $this->service->getUserRequests($userId);
                echo json_encode([
                    'success' => true,
                    'requests' => $requests
                ]);
            }

            // 3️⃣ Lấy tất cả yêu cầu (admin only)
            elseif ($request === "/password-reset/all" && $method === "GET") {
                $status = $_GET['status'] ?? null;
                $requests = $this->service->getAllRequests($status);
                
                echo json_encode([
                    'success' => true,
                    'requests' => $requests
                ]);
            }

            // 4️⃣ Duyệt yêu cầu (admin only)
            elseif ($request === "/password-reset/approve" && $method === "POST") {
                $data = json_decode(file_get_contents("php://input"), true);
                $requestId = $data["request_id"] ?? null;
                $adminId = $data["admin_id"] ?? null;
                $adminNote = $data["admin_note"] ?? null;
                
                if (!$requestId || !$adminId) {
                    throw new Exception("Request ID and Admin ID are required");
                }
                
                $result = $this->service->approveRequest($requestId, $adminId, $adminNote);
                echo json_encode($result);
            }

            // 5️⃣ Từ chối yêu cầu (admin only)
            elseif ($request === "/password-reset/reject" && $method === "POST") {
                $data = json_decode(file_get_contents("php://input"), true);
                $requestId = $data["request_id"] ?? null;
                $adminId = $data["admin_id"] ?? null;
                $adminNote = $data["admin_note"] ?? null;
                
                if (!$requestId || !$adminId) {
                    throw new Exception("Request ID and Admin ID are required");
                }
                
                $result = $this->service->rejectRequest($requestId, $adminId, $adminNote);
                echo json_encode($result);
            }

            // 6️⃣ Đổi mật khẩu (user sau khi login với default password)
            elseif ($request === "/password-reset/change-password" && $method === "POST") {
                $data = json_decode(file_get_contents("php://input"), true);
                $userId = $data["user_id"] ?? null;
                $currentPassword = $data["current_password"] ?? "";
                $newPassword = $data["new_password"] ?? "";
                
                if (!$userId) {
                    throw new Exception("User ID is required");
                }
                
                $result = $this->service->changePassword($userId, $currentPassword, $newPassword);
                echo json_encode($result);
            }

            // 7️⃣ Không khớp route nào
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
