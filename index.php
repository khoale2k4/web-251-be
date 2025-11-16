<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");

ini_set('upload_max_filesize', '20M');
ini_set('post_max_size', '25M');
ini_set('max_execution_time', '300');

$allowedOrigins = [
    "http://localhost:5500",
    "http://127.0.0.1:5500",
    "http://localhost",
];

if (isset($_SERVER["HTTP_ORIGIN"]) && in_array($_SERVER["HTTP_ORIGIN"], $allowedOrigins)) {
    header("Access-Control-Allow-Origin: " . $_SERVER["HTTP_ORIGIN"]);
    header("Access-Control-Allow-Credentials: true");
}

header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Gọi DB config
require_once __DIR__ . '/config/database.php';
$pdo = getPDO();

// Gọi router
require_once __DIR__ . '/routes/routes.php';

// Fix routing cho XAMPP
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request = str_replace('/web-251-be-main', '', $uri);

// Nếu không có request path, dùng query string
if ($request === '' || $request === '/') {
    if (isset($_GET['route'])) {
        $request = '/' . $_GET['route'];
    } else {
        $request = '/';
    }
}

routeRequest($request, $pdo);
