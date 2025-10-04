<?php
// router.php

require_once __DIR__ . './../controllers/HealthCheckController.php';
require_once __DIR__ . './../controllers/UploadController.php';
require_once __DIR__ . './../controllers/UserController.php';

function routeRequest($request, $pdo) {
    // favicon -> bỏ qua
    if ($request === "/favicon.ico") {
        exit;
    }

    // Health check
    if ($request === "/" && $_SERVER["REQUEST_METHOD"] === "GET") {
        $controller = new HealthCheckController();
        $controller->handleRequest($request);
        return;
    }

    // Upload
    if (str_starts_with($request, "/upload")) {
        $controller = new UploadController();
        $controller->handleRequest($request);
        return;
    }

    // User
    if (str_starts_with($request, "/users")) {
        $controller = new UserController($pdo);
        $controller->handleRequest($request);
        return;
    }

    // Không khớp controller nào
    http_response_code(404);
    echo json_encode(["error" => "Not found"]);
}
