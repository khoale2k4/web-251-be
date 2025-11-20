<?php

require_once __DIR__ . '/../services/ProductService.php';

class ProductController {
    private $productService;
    
    public function __construct($pdo) {
        $this->productService = new ProductService($pdo);
    }
    
    public function handleRequest($request) {
        $method = $_SERVER['REQUEST_METHOD'];
        
        // GET /products - Lấy danh sách sản phẩm
        if ($request === '/products' && $method === 'GET') {
            $this->getProducts();
            return;
        }
        
        // GET /products/latest - Lấy sản phẩm mới nhất
        if ($request === '/products/latest' && $method === 'GET') {
            $this->getLatestProducts();
            return;
        }
        
        // GET /products/discounted - Lấy sản phẩm giảm giá
        if ($request === '/products/discounted' && $method === 'GET') {
            $this->getDiscountedProducts();
            return;
        }
        
        // GET /products/search - Tìm kiếm sản phẩm
        if ($request === '/products/search' && $method === 'GET') {
            $this->searchProducts();
            return;
        }
        
        // GET /products/category/{id} - Lấy sản phẩm theo category
        if (preg_match('#^/products/category/(\d+)$#', $request, $matches) && $method === 'GET') {
            $categoryId = (int)$matches[1];
            $this->getProductsByCategory($categoryId);
            return;
        }
        
        // GET /products/{id} - Lấy chi tiết sản phẩm
        if (preg_match('#^/products/(\d+)$#', $request, $matches) && $method === 'GET') {
            $productId = (int)$matches[1];
            $this->getProduct($productId);
            return;
        }
        
        // POST /products - Tạo sản phẩm mới
        if ($request === '/products' && $method === 'POST') {
            $this->createProduct();
            return;
        }
        
        // PUT /products/{id} - Cập nhật sản phẩm
        if (preg_match('#^/products/(\d+)$#', $request, $matches) && $method === 'PUT') {
            $productId = (int)$matches[1];
            $this->updateProduct($productId);
            return;
        }
        
        // PUT /products/{id}/stock - Cập nhật stock
        if (preg_match('#^/products/(\d+)/stock$#', $request, $matches) && $method === 'PUT') {
            $productId = (int)$matches[1];
            $this->updateStock($productId);
            return;
        }
        
        // DELETE /products/{id} - Xóa sản phẩm
        if (preg_match('#^/products/(\d+)$#', $request, $matches) && $method === 'DELETE') {
            $productId = (int)$matches[1];
            $this->deleteProduct($productId);
            return;
        }
        
        // Route không tồn tại
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Route not found']);
    }
    
    /**
     * GET /products - Lấy danh sách sản phẩm
     * Query params:
     *   - page: Trang (default 1)
     *   - limit: Số lượng/trang (default 20)
     *   - category_id: Lọc theo danh mục
     *   - search: Tìm kiếm theo tên
     *   - min_price: Giá tối thiểu
     *   - max_price: Giá tối đa
     *   - size: Lọc theo size
     *   - color: Lọc theo màu
     *   - order_by: Sắp xếp (id, name, price, created_at, discount, stock)
     *   - order_dir: Chiều sắp xếp (ASC, DESC)
     */
    private function getProducts() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        
        $filters = [];
        if (isset($_GET['category_id'])) $filters['category_id'] = (int)$_GET['category_id'];
        if (isset($_GET['search'])) $filters['search'] = $_GET['search'];
        if (isset($_GET['min_price'])) $filters['min_price'] = (float)$_GET['min_price'];
        if (isset($_GET['max_price'])) $filters['max_price'] = (float)$_GET['max_price'];
        if (isset($_GET['size'])) $filters['size'] = $_GET['size'];
        if (isset($_GET['color'])) $filters['color'] = $_GET['color'];
        if (isset($_GET['order_by'])) $filters['order_by'] = $_GET['order_by'];
        if (isset($_GET['order_dir'])) $filters['order_dir'] = $_GET['order_dir'];
        
        $result = $this->productService->getProducts($page, $limit, $filters);
        
        if ($result['success']) {
            http_response_code(200);
        } else {
            http_response_code(400);
        }
        
        echo json_encode($result);
    }
    
    /**
     * GET /products/{id} - Lấy chi tiết sản phẩm
     */
    private function getProduct($id) {
        $result = $this->productService->getProduct($id);
        
        if ($result['success']) {
            http_response_code(200);
        } else {
            http_response_code(404);
        }
        
        echo json_encode($result);
    }
    
    /**
     * POST /products - Tạo sản phẩm mới
     * Body: {
     *   "name": "Nike Air Max",
     *   "description": "Mô tả sản phẩm",
     *   "price": 1500000,
     *   "discount": 10,
     *   "stock": 50,
     *   "size": "42",
     *   "color": "Black",
     *   "image": "path/to/image.jpg",
     *   "category_id": 1
     * }
     */
    private function createProduct() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (empty($data)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Thiếu dữ liệu'
            ]);
            return;
        }
        
        $result = $this->productService->createProduct($data);
        
        if ($result['success']) {
            http_response_code(201);
        } else {
            http_response_code(400);
        }
        
        echo json_encode($result);
    }
    
    /**
     * PUT /products/{id} - Cập nhật sản phẩm
     * Body: { "name": "New name", "price": 2000000, ... }
     */
    private function updateProduct($id) {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (empty($data)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Không có dữ liệu cập nhật'
            ]);
            return;
        }
        
        $result = $this->productService->updateProduct($id, $data);
        
        if ($result['success']) {
            http_response_code(200);
        } else {
            http_response_code(400);
        }
        
        echo json_encode($result);
    }
    
    /**
     * DELETE /products/{id} - Xóa sản phẩm
     */
    private function deleteProduct($id) {
        $result = $this->productService->deleteProduct($id);
        
        if ($result['success']) {
            http_response_code(200);
        } else {
            http_response_code(400);
        }
        
        echo json_encode($result);
    }
    
    /**
     * GET /products/category/{id} - Lấy sản phẩm theo category
     * Query: ?page=1&limit=20
     */
    private function getProductsByCategory($categoryId) {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        
        $result = $this->productService->getProductsByCategory($categoryId, $page, $limit);
        
        if ($result['success']) {
            http_response_code(200);
        } else {
            http_response_code(400);
        }
        
        echo json_encode($result);
    }
    
    /**
     * GET /products/latest - Lấy sản phẩm mới nhất
     * Query: ?limit=10
     */
    private function getLatestProducts() {
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        
        $result = $this->productService->getLatestProducts($limit);
        
        if ($result['success']) {
            http_response_code(200);
        } else {
            http_response_code(400);
        }
        
        echo json_encode($result);
    }
    
    /**
     * GET /products/discounted - Lấy sản phẩm giảm giá
     * Query: ?page=1&limit=20
     */
    private function getDiscountedProducts() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        
        $result = $this->productService->getDiscountedProducts($page, $limit);
        
        if ($result['success']) {
            http_response_code(200);
        } else {
            http_response_code(400);
        }
        
        echo json_encode($result);
    }
    
    /**
     * GET /products/search - Tìm kiếm sản phẩm
     * Query: ?q=nike&page=1&limit=20
     */
    private function searchProducts() {
        $keyword = $_GET['q'] ?? '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        
        if (empty($keyword)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Thiếu từ khóa tìm kiếm'
            ]);
            return;
        }
        
        $result = $this->productService->searchProducts($keyword, $page, $limit);
        
        if ($result['success']) {
            http_response_code(200);
        } else {
            http_response_code(400);
        }
        
        echo json_encode($result);
    }
    
    /**
     * PUT /products/{id}/stock - Cập nhật stock
     * Body: { "quantity": 100 }
     */
    private function updateStock($id) {
        $data = json_decode(file_get_contents('php://input'), true);
        $quantity = $data['quantity'] ?? null;
        
        if ($quantity === null) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Thiếu thông tin quantity'
            ]);
            return;
        }
        
        $result = $this->productService->updateStock($id, $quantity);
        
        if ($result['success']) {
            http_response_code(200);
        } else {
            http_response_code(400);
        }
        
        echo json_encode($result);
    }
}
