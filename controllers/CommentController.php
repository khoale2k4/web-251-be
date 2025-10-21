<?php

require_once __DIR__ . '/../services/CommentService.php';

class CommentController {
    private $commentService;

    public function __construct($pdo) {
        $this->commentService = new CommentService($pdo);
    }

    public function handleRequest($request) {
        $method = $_SERVER['REQUEST_METHOD'];

        // GET /comments?post_id=...
        if ($request === '/comments' && $method === 'GET') {
            $postId = $_GET['post_id'] ?? null;
            if (!$postId) {
                echo json_encode(['success' => false, 'message' => 'Thiếu post_id']);
                return;
            }
            echo json_encode($this->commentService->getComments($postId));
            return;
        }

        // DELETE /comments/{id}
        if (preg_match('#^/comments/(\\d+)$#', $request, $m) && $method === 'DELETE') {
            echo json_encode($this->commentService->deleteComment((int)$m[1]));
            return;
        }

        http_response_code(404);
        echo json_encode(['error' => 'API không tồn tại']);
    }
}

?>
