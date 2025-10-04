<?php
header("Content-Type: application/json");

// Gọi DB config
require_once __DIR__ . '/config/database.php';
$pdo = getPDO();

// Gọi router
require_once __DIR__ . '/routes/routes.php';

$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
routeRequest($request, $pdo);
