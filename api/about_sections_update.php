<?php
// API cập nhật 1 dòng trong bảng about_sections
// Dữ liệu nhận dạng JSON từ JS

header('Content-Type: application/json; charset=utf-8');

// --- KẾT NỐI DATABASE y hệt about_sections_list.php ---
$host    = '127.0.0.1';
$dbName  = 'shoe_store';
$user    = 'root';
$pass    = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbName;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Không kết nối được database',
        'detail'  => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// --- XỬ LÝ INPUT ---
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => 'JSON không hợp lệ',
        'raw'     => $raw,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$id = isset($data['id']) ? (int)$data['id'] : 0;
if ($id <= 0) {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => 'ID không hợp lệ',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// CÁC TRƯỜNG CHO BẢNG about_sections
// Dựa đúng SELECT trong about_sections_list.php:
$title         = trim($data['title']        ?? '');
$subtitle      = trim($data['subtitle']     ?? '');         // nếu FE không dùng thì để rỗng
$content       = trim($data['content']      ?? '');         // nếu FE không dùng thì để rỗng
$description   = trim($data['description']  ?? '');
$image_url     = trim($data['image_url']    ?? '');
$display_order = (int)($data['sort_order']  ?? 0);
$is_active     = (int)($data['is_active']   ?? 1);

if ($title === '') {
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Tiêu đề không được để trống',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $sql = "
        UPDATE about_sections
        SET
            title         = :title,
            subtitle      = :subtitle,
            content       = :content,
            description   = :description,
            image_url     = :image_url,
            display_order = :display_order,
            is_active     = :is_active
        WHERE id = :id
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':title'         => $title,
        ':subtitle'      => $subtitle,
        ':content'       => $content,
        ':description'   => $description,
        ':image_url'     => $image_url,
        ':display_order' => $display_order,
        ':is_active'     => $is_active,
        ':id'            => $id,
    ]);

    echo json_encode([
        'status'  => 'success',
        'message' => 'Cập nhật section thành công',
        'data'    => [
            'id'            => $id,
            'title'         => $title,
            'subtitle'      => $subtitle,
            'content'       => $content,
            'description'   => $description,
            'image_url'     => $image_url,
            'display_order' => $display_order,
            'is_active'     => $is_active,
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Lỗi khi cập nhật dữ liệu',
        'detail'  => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}
