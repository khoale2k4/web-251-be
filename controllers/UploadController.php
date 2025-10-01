<?php
require_once __DIR__ . '/../services/UploadService.php';

class UploadController {
    private $service;

    public function __construct() {
        $this->service = new UploadService();
    }

    public function handleRequest($request) {
        if ($request === "/upload" && $_SERVER["REQUEST_METHOD"] === "POST") {
            try {
                $filename = $this->service->saveFile($_FILES["file"]);
                echo json_encode([
                    "success" => true,
                    "file" => $filename,
                    "url" => "/storage/" . $filename
                ]);
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode(["error" => $e->getMessage()]);
            }
            exit;
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Not found"]);
            exit;
        }
    }
}
