<?php
require_once __DIR__ . '/../services/CommentService.php';

class CommentController
{
    private $service;

    public function __construct($pdo)
    {
        $this->service = new CommentService($pdo);
    }

    public function handleRequest($request)
    {
        $method = $_SERVER['REQUEST_METHOD'];

        $isProductRoute = preg_match('#^/product-comments#', $request);
        $type = $isProductRoute ? 'product' : 'post';

        switch ($method) {
            case 'GET':
                if (preg_match('#^/(product-comments|comments)/(\d+)$#', $request, $matches)) {
                    $comment = $this->service->getDetailedById((int)$matches[2]);
                    if (!$comment) {
                        http_response_code(404);
                        echo json_encode(["success" => false, "message" => "Không tìm thấy bình luận"]);
                        return;
                    }
                    echo json_encode(["success" => true, "data" => $comment]);
                    return;
                }

                $filterId = null;
                if ($type === 'post') {
                    $filterId = $_GET['post_id'] ?? $_GET['id'] ?? null;
                } else {
                    $filterId = $_GET['product_id'] ?? $_GET['id'] ?? null;
                }

                $comments = $this->service->getAll($type, $filterId);
                echo json_encode([
                    "success" => true,
                    "data" => $comments
                ]);
                break;

            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                if (empty($data['content'])) {
                    http_response_code(400);
                    echo json_encode(["success" => false, "error" => "Nội dung bình luận không được để trống"]);
                    return;
                }
                $data['comment_type'] = $type;
                $id = $this->service->create($data);
                echo json_encode(["success" => true, "message" => "Đã thêm bình luận", "id" => $id]);
                break;

            case 'DELETE':
                $id = $_GET['id'] ?? null;
                if (!$id && preg_match('#^/(product-comments|comments)/(\d+)$#', $request, $matches)) {
                    $id = $matches[2];
                }
                if (!$id) {
                    http_response_code(400);
                    echo json_encode(["error" => "Thiếu ID bình luận"]);
                    return;
                }
                $this->service->delete($id);
                echo json_encode(["success" => true, "message" => "Đã xóa bình luận"]);
                break;

            default:
                http_response_code(405);
                echo json_encode(["success" => false, "error" => "Phương thức không được hỗ trợ"]);
                break;
        }
    }
}
