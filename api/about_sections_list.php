<?php
// 1. Trả về dữ liệu dạng JSON
header('Content-Type: application/json; charset=utf-8');

// 2. Cấu hình kết nối database
$host    = '127.0.0.1';   // hoặc 'localhost'
$dbName  = 'shoe_store';  // tên database trong phpMyAdmin
$user    = 'root';        // user mặc định của XAMPP
$pass    = '';            // mật khẩu (thường để trống trên local)
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbName;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // bật exception khi lỗi
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // trả mảng dạng key => value
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // 3. Kết nối tới database
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // 4. Nếu lỗi, trả JSON báo lỗi
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Không kết nối được database',
        'detail'  => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 5. Câu lệnh SELECT dữ liệu
$sql = "
    SELECT 
        id,
        title,
        subtitle,
        is_active,
        display_order,
        content,
        description,
        image_url,
        sort_order,
        created_at,
        updated_at
    FROM about_sections
    ORDER BY sort_order ASC
";

// 6. Thực thi query và lấy tất cả dữ liệu
$stmt = $pdo->query($sql);
$rows = $stmt->fetchAll();

// 7. Trả dữ liệu về dạng JSON
echo json_encode([
    'status' => 'success',
    'count'  => count($rows),
    'data'   => $rows
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

exit;
