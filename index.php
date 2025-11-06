<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost");
// header("Access-Control-Allow-Origin: http://btl-web.test");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Gọi DB config
require_once __DIR__ . '/config/database.php';
$pdo = getPDO();

// Gọi router
require_once __DIR__ . '/routes/routes.php';

$request = str_replace('/web-251-be', '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
routeRequest($request, $pdo);
