<?php
// FAQController - nhóm #2

require_once __DIR__ . '/../services/FAQService.php';

class FAQController
{
    private $faqService;

    public function __construct($pdo)
    {
        $this->faqService = new FAQService($pdo);
    }

    public function handleRequest($request)
    {
        $method = $_SERVER['REQUEST_METHOD'];

        // Preflight cho CORS
        if ($method === 'OPTIONS') {
            http_response_code(204);
            return;
        }

        // GET /faqs/admin - danh sách FAQ cho admin (quản lý)
        if ($request === '/faqs/admin' && $method === 'GET') {
            // Cho phép filter qua query string: status, search, limit
            $result = $this->faqService->getAdminFaqs($_GET);
            echo json_encode($result);
            return;
        }

        // POST /faqs - tạo FAQ mới (admin)
        if ($request === '/faqs' && $method === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true) ?? [];
            $result = $this->faqService->createFaq($data);
            echo json_encode($result);
            return;
        }

        // PUT /faqs/{id} - cập nhật FAQ (admin)
        if (preg_match('#^/faqs/(\\d+)$#', $request, $m) && $method === 'PUT') {
            $id   = (int)$m[1];
            $data = json_decode(file_get_contents('php://input'), true) ?? [];
            $result = $this->faqService->updateFaq($id, $data);
            echo json_encode($result);
            return;
        }

        // DELETE /faqs/{id} - xoá FAQ (admin)
        if (preg_match('#^/faqs/(\\d+)$#', $request, $m) && $method === 'DELETE') {
            $id   = (int)$m[1];
            $result = $this->faqService->deleteFaq($id);
            echo json_encode($result);
            return;
        }

        // GET /faqs - lấy danh sách FAQ public
        if ($request === '/faqs' && $method === 'GET') {
            // Truyền nguyên $_GET vào service để dùng filter (status, search, limit)
            $result = $this->faqService->getPublicFaqs($_GET);
            echo json_encode($result);
            return;
        }

        http_response_code(404);
        echo json_encode(['error' => 'FAQ endpoint not found']);
    }
}
