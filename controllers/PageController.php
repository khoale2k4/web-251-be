<?php
// PageController - quản lý nội dung trang tĩnh (home/about)

require_once __DIR__ . '/../services/PageService.php';
require_once __DIR__ . '/../utils/ResponseHelper.php';

class PageController
{
    private $service;

    public function __construct($pdo)
    {
        $this->service = new PageService($pdo);
    }

    public function handleRequest($request)
    {
        $method = $_SERVER['REQUEST_METHOD'];

        // Chuẩn hóa path cho đẹp: /page-contents và /page-contents/ coi như giống nhau
        $path = rtrim($request, '/');
        if ($path === '') {
            $path = '/';
        }

        // GET /page-contents  - lấy nội dung trang
        // PUT /page-contents  - cập nhật nội dung trang
        if ($path === '/page-contents') {
            if ($method === 'GET') {
                $this->handleGetPageContents();
                return;
            }

            if ($method === 'PUT') {
                $this->handleUpdatePageContents();
                return;
            }

            ResponseHelper::error('Method not allowed', 405);
            return;
        }

        // Có thể thêm các route khác cho Page ở đây sau này

        http_response_code(404);
        echo json_encode(["error" => "Page endpoint not found"]);
    }

    private function handleGetPageContents()
    {
        try {
            $data = $this->service->getPageContents();
            ResponseHelper::success($data, 'Loaded page contents');
        } catch (Exception $e) {
            ResponseHelper::error('Server error', 500, [
                'details' => $e->getMessage(),
            ]);
        }
    }

    private function handleUpdatePageContents()
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true) ?: [];
            $data = $this->service->updatePageContents($input);
            ResponseHelper::success($data, 'Đã lưu nội dung trang');
        } catch (Exception $e) {
            ResponseHelper::error('Server error', 500, [
                'details' => $e->getMessage(),
            ]);
        }
    }
}
