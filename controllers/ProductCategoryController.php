<?php

require_once __DIR__ . '/../services/ProductCategoryService.php';

class ProductCategoryController {
    private $categoryService;
    
    public function __construct($pdo) {
        $this->categoryService = new ProductCategoryService($pdo);
    }
    
    public function handleRequest($request) {
        $method = $_SERVER['REQUEST_METHOD'];
        
        // GET /categories - Lấy danh sách danh mục
        if ($request === '/categories' && $method === 'GET') {
            $this->getCategories();
            return;
        }
        
        // GET /categories/{id} - Lấy chi tiết danh mục
        if (preg_match('#^/categories/(\d+)$#', $request, $matches) && $method === 'GET') {
            $categoryId = (int)$matches[1];
            $this->getCategory($categoryId);
            return;
        }
        
        // POST /categories - Tạo danh mục mới
        if ($request === '/categories' && $method === 'POST') {
            $this->createCategory();
            return;
        }
        
        // PUT /categories/{id} - Cập nhật danh mục
        if (preg_match('#^/categories/(\d+)$#', $request, $matches) && $method === 'PUT') {
            $categoryId = (int)$matches[1];
            $this->updateCategory($categoryId);
            return;
        }
        
        // DELETE /categories/{id} - Xóa danh mục
        if (preg_match('#^/categories/(\d+)$#', $request, $matches) && $method === 'DELETE') {
            $categoryId = (int)$matches[1];
            $this->deleteCategory($categoryId);
            return;
        }
        
        // Route không tồn tại
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Route not found']);
    }
    
    /**
     * GET /categories - Lấy danh sách danh mục
     * Query params:
     *   - page: Trang (default 1)
     *   - limit: Số lượng/trang (default 100)
     *   - with_count: Có lấy số lượng sản phẩm không (true/false, default false)
     */
    private function getCategories() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
        $withProductCount = isset($_GET['with_count']) && $_GET['with_count'] === 'true';
        
        $result = $this->categoryService->getCategories($page, $limit, $withProductCount);
        
        if ($result['success']) {
            http_response_code(200);
        } else {
            http_response_code(400);
        }
        
        echo json_encode($result);
    }
    
    /**
     * GET /categories/{id} - Lấy chi tiết danh mục
     */
    private function getCategory($id) {
        $result = $this->categoryService->getCategory($id);
        
        if ($result['success']) {
            http_response_code(200);
        } else {
            http_response_code(404);
        }
        
        echo json_encode($result);
    }
    
    /**
     * POST /categories - Tạo danh mục mới
     * Body: {
     *   "name": "Sneakers",
     *   "description": "Giày thể thao"
     * }
     */
    private function createCategory() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (empty($data)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Thiếu dữ liệu'
            ]);
            return;
        }
        
        $result = $this->categoryService->createCategory($data);
        
        if ($result['success']) {
            http_response_code(201);
        } else {
            http_response_code(400);
        }
        
        echo json_encode($result);
    }
    
    /**
     * PUT /categories/{id} - Cập nhật danh mục
     * Body: {
     *   "name": "New name",
     *   "description": "New description"
     * }
     */
    private function updateCategory($id) {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (empty($data)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Không có dữ liệu cập nhật'
            ]);
            return;
        }
        
        $result = $this->categoryService->updateCategory($id, $data);
        
        if ($result['success']) {
            http_response_code(200);
        } else {
            http_response_code(400);
        }
        
        echo json_encode($result);
    }
    
    /**
     * DELETE /categories/{id} - Xóa danh mục
     */
    private function deleteCategory($id) {
        $result = $this->categoryService->deleteCategory($id);
        
        if ($result['success']) {
            http_response_code(200);
        } else {
            http_response_code(400);
        }
        
        echo json_encode($result);
    }
}

