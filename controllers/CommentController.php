<?php

require_once __DIR__ . '/../services/CommentService.php';

class CommentController
{
    private $commentService;

    public function __construct($pdo)
    {
        $this->commentService = new CommentService($pdo);
    }

    public function handleRequest($request)
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $query = $_GET;

        if ($method === 'GET') {
            if (str_starts_with($request, '/product-comments') && isset($query['product_id'])) {
                $response = $this->commentService->getCommentsByProduct($query['product_id']);
            } elseif (str_starts_with($request, '/comments') && isset($query['post_id'])) {
                $response = $this->commentService->getCommentsByPost($query['post_id']);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Thiếu tham số post_id hoặc product_id']);
                return;
            }

            echo json_encode($response);
            return;
        }

        if ($method === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $response = $this->commentService->createComment($data);
            echo json_encode($response);
            return;
        }

        if ($method === 'DELETE' && preg_match("#^/comments/(\d+)$#", $request, $matches)) {
            $id = $matches[1];
            $response = $this->commentService->deleteComment($id);
            echo json_encode($response);
            return;
        }

        http_response_code(404);
        echo json_encode(['error' => 'Not found']);
    }
}
