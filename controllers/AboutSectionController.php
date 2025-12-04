<?php
// AboutSectionController - API cho trang Giới thiệu (user & admin)

require_once __DIR__ . '/../services/AboutSectionService.php';

class AboutSectionController
{
    private $service;

    public function __construct($pdo)
    {
        $this->service = new AboutSectionService($pdo);
    }

    public function handleRequest($request)
    {
        $method = $_SERVER['REQUEST_METHOD'];

        // Public: GET /about-sections
        if ($request === '/about-sections' && $method === 'GET') {
            $result = $this->service->getPublicSections();
            $this->jsonResponse(
                $result,
                $result['status'] === 'success' ? 200 : 500
            );
            return;
        }

        // Admin: /admin/about-sections/*
        if (strpos($request, '/admin/about-sections') === 0) {
            // GET /admin/about-sections/list
            if ($request === '/admin/about-sections/list' && $method === 'GET') {
                $result = $this->service->getAdminSections();
                $this->jsonResponse(
                    $result,
                    $result['status'] === 'success' ? 200 : 500
                );
                return;
            }

            // POST /admin/about-sections/create
            if ($request === '/admin/about-sections/create' && $method === 'POST') {
                $payload = $this->getJsonInput();
                $result  = $this->service->createSection($payload);
                $this->jsonResponse(
                    $result,
                    $result['status'] === 'success' ? 200 : 400
                );
                return;
            }

            // POST /admin/about-sections/update
            if ($request === '/admin/about-sections/update' && $method === 'POST') {
                $payload = $this->getJsonInput();
                $id      = isset($payload['id']) ? (int)$payload['id'] : 0;
                $result  = $this->service->updateSection($id, $payload);
                $this->jsonResponse(
                    $result,
                    $result['status'] === 'success' ? 200 : 400
                );
                return;
            }

            // POST /admin/about-sections/delete
            if ($request === '/admin/about-sections/delete' && $method === 'POST') {
                $payload = $this->getJsonInput();
                $id      = isset($payload['id']) ? (int)$payload['id'] : 0;
                $result  = $this->service->deleteSection($id);
                $this->jsonResponse(
                    $result,
                    $result['status'] === 'success' ? 200 : 400
                );
                return;
            }

            // Sai URL/method cho admin
            http_response_code(404);
            echo json_encode([
                'status'  => 'error',
                'message' => 'About sections admin endpoint not found',
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        // Không khớp route nào
        http_response_code(404);
        echo json_encode([
            'status'  => 'error',
            'message' => 'About sections endpoint not found',
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Đọc body JSON, luôn trả về array (có thể rỗng).
     */
    private function getJsonInput()
    {
        $raw = file_get_contents('php://input');
        if ($raw === false || trim($raw) === '') {
            return [];
        }

        $data = json_decode($raw, true);
        if (!is_array($data)) {
            return [];
        }

        return $data;
    }

    private function jsonResponse(array $data, int $code = 200)
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(
            $data,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }
}
