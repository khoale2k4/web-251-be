<?php

require_once __DIR__ . '/../services/PostService.php';

class PostController {
    private $postService;

    public function __construct($pdo) {
        $this->postService = new PostService($pdo);
    }

    public function handleRequest($request) {
        $method = $_SERVER['REQUEST_METHOD'];

        // GET /posts?page=1&search=abc&status=published&slug=xxx
        if ($request === '/posts' && $method === 'GET') {
            $page = $_GET['page'] ?? 1;
            $search = $_GET['search'] ?? null;
            $status = $_GET['status'] ?? null;
            $slug = $_GET['slug'] ?? null;
            
            echo json_encode($this->postService->getPosts($page, 10, $search, $status, $slug));
            return;
        }

        // GET /posts/{id}
        if (preg_match('#^/posts/(\\d+)$#', $request, $m) && $method === 'GET') {
            echo json_encode($this->postService->getPost((int)$m[1]));
            return;
        }

        // POST /posts
        if ($request === '/posts' && $method === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            echo json_encode($this->postService->createPost($data));
            return;
        }

        // PUT /posts/{id}
        if (preg_match('#^/posts/(\\d+)$#', $request, $m) && $method === 'PUT') {
            $data = json_decode(file_get_contents('php://input'), true);
            echo json_encode($this->postService->updatePost((int)$m[1], $data));
            return;
        }

        // DELETE /posts/{id}
        if (preg_match('#^/posts/(\\d+)$#', $request, $m) && $method === 'DELETE') {
            echo json_encode($this->postService->deletePost((int)$m[1]));
            return;
        }

        http_response_code(404);
        echo json_encode(['error' => 'API không tồn tại']);
    }
}

?>
