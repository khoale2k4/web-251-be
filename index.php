<?php

header("Content-Type: application/json");

// Gọi DB config
require_once __DIR__ . '/config/database.php';
$pdo = getPDO();

require_once __DIR__ . '/controllers/UploadController.php';
require_once __DIR__ . '/controllers/UserController.php';
require_once __DIR__ . '/controllers/HealthCheckController.php';

// Lấy URL request
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($request === "/favicon.ico") {
    exit;
}

// Chuyển cho controller xử lý
$healthCheckController = new HealthCheckController();
$healthCheckController->handleRequest($request);
$uploadController = new UploadController();
$uploadController->handleRequest($request);
$controller = new UserController($pdo);
$controller->handleRequest($request);
