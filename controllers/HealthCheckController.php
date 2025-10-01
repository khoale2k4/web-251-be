<?php

class HealthCheckController {
    public function handleRequest($request) {
        if ($request === "/" && $_SERVER["REQUEST_METHOD"] === "GET") {
            echo json_encode([
                "success" => true,
            ]);
            exit;
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Not found"]);
            exit;
        }
    }
}
