<?php
// ContactController - Quản lý liên hệ từ khách hàng

class ContactController
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function handleRequest($request)
    {
        $method = $_SERVER['REQUEST_METHOD'];
        
        // GET /contacts - Lấy danh sách contacts (có phân trang)
        if ($request === '/contacts' && $method === 'GET') {
            $this->getAll();
            return;
        }

        // GET /contacts/{id} - Lấy chi tiết 1 contact
        if (preg_match('#^/contacts/(\d+)$#', $request, $matches) && $method === 'GET') {
            $this->getById((int)$matches[1]);
            return;
        }

        // POST /contacts - Tạo contact mới (từ form user)
        if ($request === '/contacts' && $method === 'POST') {
            $this->create();
            return;
        }

        // PUT /contacts/{id} - Cập nhật trạng thái contact
        if (preg_match('#^/contacts/(\d+)$#', $request, $matches) && $method === 'PUT') {
            $this->updateStatus((int)$matches[1]);
            return;
        }

        // DELETE /contacts/{id} - Xóa contact
        if (preg_match('#^/contacts/(\d+)$#', $request, $matches) && $method === 'DELETE') {
            $this->delete((int)$matches[1]);
            return;
        }

        // GET /contacts/stats - Lấy thống kê
        if ($request === '/contacts/stats' && $method === 'GET') {
            $this->getStats();
            return;
        }

        http_response_code(404);
        echo json_encode(["error" => "Contact endpoint not found"]);
    }

    // Lấy danh sách contacts với phân trang
    private function getAll()
    {
        try {
            // Lấy tham số phân trang
            $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            $limit = isset($_GET['limit']) ? max(1, min(100, (int)$_GET['limit'])) : 10;
            $offset = ($page - 1) * $limit;

            // Lấy filter theo status
            $status = isset($_GET['status']) ? $_GET['status'] : null;

            // Lấy tìm kiếm
            $search = isset($_GET['search']) ? trim($_GET['search']) : null;

            // Build query
            $whereConditions = [];
            $params = [];

            if ($status && in_array($status, ['new', 'read', 'replied'])) {
                $whereConditions[] = "status = :status";
                $params[':status'] = $status;
            }

            if ($search) {
                $whereConditions[] = "(name LIKE :search OR email LIKE :search OR subject LIKE :search OR message LIKE :search)";
                $params[':search'] = "%$search%";
            }

            $whereClause = $whereConditions ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

            // Đếm tổng số
            $countSql = "SELECT COUNT(*) as total FROM contacts $whereClause";
            $countStmt = $this->pdo->prepare($countSql);
            $countStmt->execute($params);
            $total = (int)$countStmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Lấy data
            $sql = "SELECT * FROM contacts $whereClause ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
            $stmt = $this->pdo->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            
            $stmt->execute();
            $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                "success" => true,
                "data" => $contacts,
                "pagination" => [
                    "page" => $page,
                    "limit" => $limit,
                    "total" => $total,
                    "total_pages" => ceil($total / $limit)
                ]
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Failed to get contacts", "details" => $e->getMessage()]);
        }
    }

    // Lấy chi tiết 1 contact
    private function getById($id)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM contacts WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $contact = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$contact) {
                http_response_code(404);
                echo json_encode(["error" => "Contact not found"]);
                return;
            }

            echo json_encode([
                "success" => true,
                "data" => $contact
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Failed to get contact", "details" => $e->getMessage()]);
        }
    }

    // Tạo contact mới (từ form user)
    private function create()
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            // Validation
            $errors = [];
            
            if (empty($data['name']) || strlen($data['name']) < 2) {
                $errors['name'] = "Tên phải có ít nhất 2 ký tự";
            }
            
            if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = "Email không hợp lệ";
            }
            
            if (!empty($data['phone'])) {
                $phone = preg_replace('/[^0-9]/', '', $data['phone']);
                if (strlen($phone) < 10 || strlen($phone) > 11) {
                    $errors['phone'] = "Số điện thoại không hợp lệ (10-11 số)";
                }
            }
            
            if (empty($data['message']) || strlen($data['message']) < 10) {
                $errors['message'] = "Tin nhắn phải có ít nhất 10 ký tự";
            }

            if (!empty($errors)) {
                http_response_code(400);
                echo json_encode(["error" => "Validation failed", "details" => $errors]);
                return;
            }

            // Insert
            $sql = "INSERT INTO contacts (name, email, phone, subject, message, status, created_at) 
                    VALUES (:name, :email, :phone, :subject, :message, 'new', NOW())";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':name' => htmlspecialchars(trim($data['name'])),
                ':email' => strtolower(trim($data['email'])),
                ':phone' => isset($data['phone']) ? preg_replace('/[^0-9]/', '', $data['phone']) : null,
                ':subject' => isset($data['subject']) ? htmlspecialchars(trim($data['subject'])) : null,
                ':message' => htmlspecialchars(trim($data['message']))
            ]);

            $contactId = $this->pdo->lastInsertId();

            http_response_code(201);
            echo json_encode([
                "success" => true,
                "message" => "Đã gửi liên hệ thành công. Chúng tôi sẽ phản hồi sớm nhất!",
                "data" => ["id" => $contactId]
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Failed to create contact", "details" => $e->getMessage()]);
        }
    }

    // Cập nhật trạng thái contact (admin only)
    private function updateStatus($id)
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            // Validation
            if (empty($data['status']) || !in_array($data['status'], ['new', 'read', 'replied'])) {
                http_response_code(400);
                echo json_encode(["error" => "Invalid status. Must be: new, read, or replied"]);
                return;
            }

            // Kiểm tra contact có tồn tại không
            $stmt = $this->pdo->prepare("SELECT id FROM contacts WHERE id = :id");
            $stmt->execute([':id' => $id]);
            if (!$stmt->fetch()) {
                http_response_code(404);
                echo json_encode(["error" => "Contact not found"]);
                return;
            }

            // Update
            $sql = "UPDATE contacts SET status = :status WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':status' => $data['status'],
                ':id' => $id
            ]);

            echo json_encode([
                "success" => true,
                "message" => "Đã cập nhật trạng thái"
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Failed to update contact", "details" => $e->getMessage()]);
        }
    }

    // Xóa contact (admin only)
    private function delete($id)
    {
        try {
            // Kiểm tra contact có tồn tại không
            $stmt = $this->pdo->prepare("SELECT id FROM contacts WHERE id = :id");
            $stmt->execute([':id' => $id]);
            if (!$stmt->fetch()) {
                http_response_code(404);
                echo json_encode(["error" => "Contact not found"]);
                return;
            }

            // Delete
            $stmt = $this->pdo->prepare("DELETE FROM contacts WHERE id = :id");
            $stmt->execute([':id' => $id]);

            echo json_encode([
                "success" => true,
                "message" => "Đã xóa liên hệ"
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Failed to delete contact", "details" => $e->getMessage()]);
        }
    }

    // Lấy thống kê (admin only)
    private function getStats()
    {
        try {
            $sql = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) as new_count,
                        SUM(CASE WHEN status = 'read' THEN 1 ELSE 0 END) as read_count,
                        SUM(CASE WHEN status = 'replied' THEN 1 ELSE 0 END) as replied_count
                    FROM contacts";
            
            $stmt = $this->pdo->query($sql);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode([
                "success" => true,
                "data" => $stats
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Failed to get stats", "details" => $e->getMessage()]);
        }
    }
}
