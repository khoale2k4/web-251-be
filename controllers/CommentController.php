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

        if (preg_match('#^/product-comments#', $request)) {
            $type = 'product';
        } else {
            $type = 'post';
        }

        switch ($method) {
            case 'GET':
                $filterId = $_GET['id'] ?? null;
                $comments = $this->service->getAll($type, $filterId);
                echo json_encode($comments);
                break;

            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                if (empty($data['content'])) {
                    http_response_code(400);
                    echo json_encode(["error" => "Nội dung bình luận không được để trống"]);
                    return;
                }
                $data['comment_type'] = $type;
                $id = $this->service->create($data);
                echo json_encode(["message" => "Đã thêm bình luận", "id" => $id]);
                break;

            case 'DELETE':
                $id = $_GET['id'] ?? null;
                if (!$id) {
                    http_response_code(400);
                    echo json_encode(["error" => "Thiếu ID bình luận"]);
                    return;
                }
                $this->service->delete($id);
                echo json_encode(["message" => "Đã xóa bình luận"]);
                break;

            default:
                http_response_code(405);
                echo json_encode(["error" => "Phương thức không được hỗ trợ"]);
                break;
        }
    }
}
