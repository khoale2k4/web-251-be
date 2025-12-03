<?php
// be/api/about_sections_create.php
header('Content-Type: application/json; charset=utf-8');

// KẾT NỐI DATABASE – dùng chung config/database.php
require_once __DIR__ . '/../config/database.php';
$pdo = getPDO(); // <-- rất quan trọng, để $pdo không còn undefined

try {
    // Đọc JSON từ body
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);

    if (!is_array($data)) {
        throw new Exception('Dữ liệu JSON không hợp lệ');
    }

    $title       = trim($data['title'] ?? '');
    $description = trim($data['description'] ?? '');
    $image_url   = trim($data['image_url'] ?? '');
    $sort_order  = isset($data['sort_order']) ? (int)$data['sort_order'] : 0;
    $is_active   = isset($data['is_active']) ? (int)$data['is_active'] : 1;

    if ($title === '') {
        throw new Exception('Tiêu đề không được để trống');
    }

    // Lưu ý: chỉnh lại tên cột nếu bảng bạn khác (vd: display_order thay vì sort_order)
    $sql = "
        INSERT INTO about_sections (title, description, image_url, sort_order, is_active)
        VALUES (:title, :description, :image_url, :sort_order, :is_active)
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':title'       => $title,
        ':description' => $description,
        ':image_url'   => $image_url,
        ':sort_order'  => $sort_order,
        ':is_active'   => $is_active,
    ]);

    $id = (int)$pdo->lastInsertId();

    echo json_encode([
        'status'  => 'success',
        'message' => 'Tạo section mới thành công',
        'data'    => [
            'id'          => $id,
            'title'       => $title,
            'description' => $description,
            'image_url'   => $image_url,
            'sort_order'  => $sort_order,
            'is_active'   => $is_active,
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}
