<?php
// SiteSettingsController - Quản lý thông tin cấu hình trang web

class SiteSettingsController
{
    private $pdo;
    private $uploadDir;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->uploadDir = __DIR__ . '/../storage/';
    }

    public function handleRequest($request)
    {
        $method = $_SERVER['REQUEST_METHOD'];
        
        // GET /site-settings - Lấy thông tin settings
        if ($request === '/site-settings' && $method === 'GET') {
            $this->get();
            return;
        }

        // PUT /site-settings - Cập nhật settings (admin only)
        if ($request === '/site-settings' && $method === 'PUT') {
            $this->update();
            return;
        }

        // POST /site-settings/upload - Upload logo/favicon (admin only)
        if ($request === '/site-settings/upload' && $method === 'POST') {
            $this->uploadImage();
            return;
        }

        http_response_code(404);
        echo json_encode(["error" => "Site settings endpoint not found"]);
    }

    // Lấy thông tin settings
    private function get()
    {
        try {
            $stmt = $this->pdo->query("SELECT * FROM site_settings ORDER BY id DESC LIMIT 1");
            $settings = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$settings) {
                // Nếu chưa có, tạo mặc định
                $this->createDefault();
                $stmt = $this->pdo->query("SELECT * FROM site_settings ORDER BY id DESC LIMIT 1");
                $settings = $stmt->fetch(PDO::FETCH_ASSOC);
            }

            echo json_encode([
                "success" => true,
                "data" => $settings
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Failed to get settings", "details" => $e->getMessage()]);
        }
    }

    // Cập nhật settings (admin only)
    private function update()
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            // Validation
            $errors = [];
            
            if (isset($data['email']) && !empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = "Email không hợp lệ";
            }
            
            if (isset($data['phone']) && !empty($data['phone'])) {
                $phone = preg_replace('/[^0-9\-\s]/', '', $data['phone']);
                if (strlen($phone) < 10) {
                    $errors['phone'] = "Số điện thoại không hợp lệ";
                }
            }

            if (!empty($errors)) {
                http_response_code(400);
                echo json_encode(["error" => "Validation failed", "details" => $errors]);
                return;
            }

            // Lấy settings hiện tại
            $stmt = $this->pdo->query("SELECT id FROM site_settings ORDER BY id DESC LIMIT 1");
            $current = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$current) {
                http_response_code(404);
                echo json_encode(["error" => "Settings not found. Please initialize first."]);
                return;
            }

            // Build update query
            $allowedFields = [
                'site_name', 'site_title', 'site_description', 'site_keywords',
                'logo', 'favicon', 'email', 'phone', 'address',
                'facebook', 'instagram', 'youtube',
                'about_us', 'working_hours', 'copyright'
            ];

            $updateFields = [];
            $params = [':id' => $current['id']];

            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $updateFields[] = "$field = :$field";
                    $params[":$field"] = htmlspecialchars(trim($data[$field]));
                }
            }

            if (empty($updateFields)) {
                http_response_code(400);
                echo json_encode(["error" => "No valid fields to update"]);
                return;
            }

            $sql = "UPDATE site_settings SET " . implode(", ", $updateFields) . " WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            echo json_encode([
                "success" => true,
                "message" => "Đã cập nhật thông tin trang web"
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Failed to update settings", "details" => $e->getMessage()]);
        }
    }

    // Upload logo/favicon (admin only)
    private function uploadImage()
    {
        try {
            if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                http_response_code(400);
                echo json_encode(["error" => "No file uploaded or upload error"]);
                return;
            }

            $file = $_FILES['image'];
            $type = isset($_POST['type']) ? $_POST['type'] : 'logo'; // logo hoặc favicon

            // Validate type
            if (!in_array($type, ['logo', 'favicon'])) {
                http_response_code(400);
                echo json_encode(["error" => "Invalid type. Must be 'logo' or 'favicon'"]);
                return;
            }

            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/x-icon', 'image/vnd.microsoft.icon'];
            $fileType = mime_content_type($file['tmp_name']);
            
            if (!in_array($fileType, $allowedTypes)) {
                http_response_code(400);
                echo json_encode(["error" => "Invalid file type. Only JPG, PNG, GIF, WEBP, ICO allowed"]);
                return;
            }

            // Validate file size (max 5MB)
            if ($file['size'] > 5 * 1024 * 1024) {
                http_response_code(400);
                echo json_encode(["error" => "File too large. Max 5MB"]);
                return;
            }

            // Create upload directory if not exists
            if (!is_dir($this->uploadDir)) {
                mkdir($this->uploadDir, 0777, true);
            }

            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = time() . '_' . $type . '.' . $extension;
            $filepath = $this->uploadDir . $filename;

            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                http_response_code(500);
                echo json_encode(["error" => "Failed to move uploaded file"]);
                return;
            }

            // Get URL path
            $baseUrl = 'http://localhost/web-251-be-main'; // XAMPP default port
            $imageUrl = $baseUrl . '/storage/' . $filename;

            // Update database
            $stmt = $this->pdo->query("SELECT id FROM site_settings ORDER BY id DESC LIMIT 1");
            $current = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($current) {
                $sql = "UPDATE site_settings SET $type = :image WHERE id = :id";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([':image' => $imageUrl, ':id' => $current['id']]);
            }

            echo json_encode([
                "success" => true,
                "message" => "Upload thành công",
                "data" => [
                    "filename" => $filename,
                    "url" => $imageUrl,
                    "type" => $type
                ]
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Failed to upload image", "details" => $e->getMessage()]);
        }
    }

    // Tạo settings mặc định
    private function createDefault()
    {
        $sql = "INSERT INTO site_settings (
                    site_name, site_title, site_description, site_keywords,
                    email, phone, address, copyright
                ) VALUES (
                    'Shoe Store',
                    'Cửa hàng giày dép chất lượng cao',
                    'Chuyên cung cấp các loại giày thể thao, giày da, boots và sandals chính hãng',
                    'giày, giày thể thao, sneakers, boots',
                    'contact@shoestore.vn',
                    '0123-456-789',
                    '123 Nguyễn Huệ, Quận 1, TP.HCM',
                    '© 2025 Shoe Store. All rights reserved.'
                )";
        $this->pdo->exec($sql);
    }
}

