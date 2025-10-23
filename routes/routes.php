<?php

require_once __DIR__ . '/../controllers/HealthCheckController.php';
require_once __DIR__ . '/../controllers/UploadController.php';
require_once __DIR__ . '/../controllers/UserController.php';
require_once __DIR__ . '/../controllers/CartController.php';
require_once __DIR__ . '/../controllers/OrderController.php';
require_once __DIR__ . '/../controllers/ProductController.php';
require_once __DIR__ . '/../controllers/PostController.php';
require_once __DIR__ . '/../controllers/CommentController.php';

function routeRequest($request, $pdo)
{
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

    // Product
    if (str_starts_with($request, "/products")) {
        $controller = new ProductController($pdo);
        $controller->handleRequest($request);
        return;
    }

    // Cart
    if (str_starts_with($request, "/carts")) {
        $controller = new CartController($pdo);
        $controller->handleRequest($request);
        return;
    }

    // Order
    if (str_starts_with($request, "/orders")) {
        $controller = new OrderController($pdo);
        $controller->handleRequest($request);
        return;
    }

    // Post
    if (str_starts_with($request, "/posts")) {
        $controller = new PostController($pdo);
        $controller->handleRequest($request);
        return;
    }

    // Comments cho bài viết
    if (str_starts_with($request, "/comments")) {
        $controller = new CommentController($pdo);
        $controller->handleRequest($request);
        return;
    }

    // Comments cho sản phẩm
    if (str_starts_with($request, "/product-comments")) {
        $controller = new CommentController($pdo);
        $controller->handleRequest($request);
        return;
    }

    // Không khớp controller nào
    http_response_code(404);
    echo json_encode(["error" => "Not found"]);
}
