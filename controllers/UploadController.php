<?php
require_once __DIR__ . '/../services/UploadService.php';

class UploadController {
    private $service;

    public function __construct() {
        $this->service = new UploadService();
    }

    public function handleRequest($request) {
        if ($request !== "/upload" || $_SERVER["REQUEST_METHOD"] !== "POST") {
            http_response_code(404);
            echo json_encode(["success" => false, "error" => "Not found"]);
            exit;
        }

        if (!isset($_FILES['file'])) {
            http_response_code(400);
            echo json_encode(["success" => false, "error" => "Không nhận được file upload"]);
            exit;
        }

        $folder = $_POST['folder'] ?? $_GET['folder'] ?? 'misc';
        $targetAssets = ($_POST['target'] ?? $_GET['target'] ?? 'assets') === 'assets';

        try {
            $result = $this->service->saveFile($_FILES["file"], $folder, $targetAssets);
            echo json_encode([
                "success" => true,
                "file" => $result['filename'],
                "relativePath" => $result['relativePath'],
                "mime" => $result['mime']
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(["success" => false, "error" => $e->getMessage()]);
        }
        exit;
    }
}
