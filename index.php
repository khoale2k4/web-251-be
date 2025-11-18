<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("Content-Type: application/json");

// Allow multiple origins
$allowedOrigins = [
    'http://localhost',
    'http://localhost:3000',
    'http://localhost:5500',
    'http://localhost:8080',
    'http://127.0.0.1',
    'http://btl-web.test'
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    header("Access-Control-Allow-Origin: *");
}

header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");

ini_set('upload_max_filesize', '20M');
ini_set('post_max_size', '25M');
ini_set('max_execution_time', '300');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Gọi DB config
require_once __DIR__ . '/config/database.php';
$pdo = getPDO();

// Gọi router
require_once __DIR__ . '/routes/routes.php';

// Fix routing
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// For PHP built-in server, use URI directly
// For XAMPP, remove base path
if (strpos($uri, '/web-251-be') !== false) {
    $request = str_replace('/web-251-be', '', $uri);
    $request = str_replace('/index.php', '', $request);
} else {
    $request = $uri;
}

// Nếu không có request path, dùng query string
if ($request === '' || $request === '/') {
    if (isset($_GET['route'])) {
        $request = '/' . $_GET['route'];
    } else {
        $request = '/';
    }
}

routeRequest($request, $pdo);
