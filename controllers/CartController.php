<?php

require_once __DIR__ . '/../services/CartService.php';

class CartController {
    private $cartService;
    
    public function __construct($pdo) {
        $this->cartService = new CartService($pdo);
    }
    
    public function handleRequest($request) {
        $method = $_SERVER['REQUEST_METHOD'];
        
        // GET /carts - Lấy giỏ hàng của user hiện tại
        if ($request === '/carts' && $method === 'GET') {
            $this->getCart();
            return;
        }
        
        // POST /carts/items - Thêm sản phẩm vào giỏ
        if ($request === '/carts/items' && $method === 'POST') {
            $this->addToCart();
            return;
        }
        
        // PUT /carts/items/{id} - Cập nhật số lượng sản phẩm
        if (preg_match('#^/carts/items/(\d+)$#', $request, $matches) && $method === 'PUT') {
            $cartItemId = (int)$matches[1];
            $this->updateCartItem($cartItemId);
            return;
        }
        
        // DELETE /carts/items/{id} - Xóa sản phẩm khỏi giỏ
        if (preg_match('#^/carts/items/(\d+)$#', $request, $matches) && $method === 'DELETE') {
            $cartItemId = (int)$matches[1];
            $this->removeFromCart($cartItemId);
            return;
        }
        
        // DELETE /carts - Xóa toàn bộ giỏ hàng
        if ($request === '/carts' && $method === 'DELETE') {
            $this->clearCart();
            return;
        }
        
        // Route không tồn tại
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Route not found']);
    }
    
    /**
     * GET /carts - Lấy giỏ hàng
     */
    private function getCart() {
        // Lấy user_id từ query hoặc session
        // Ở đây tôi giả sử user_id được truyền qua query param hoặc từ authentication middleware
        $userId = $_GET['user_id'] ?? null;
        
        if (!$userId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Thiếu user_id']);
            return;
        }
        
        $result = $this->cartService->getUserCart($userId);
        
        if ($result['success']) {
            http_response_code(200);
        } else {
            http_response_code(400);
        }
        
        echo json_encode($result);
    }
    
    /**
     * POST /carts/items - Thêm sản phẩm vào giỏ
     * Body: { "user_id": 1, "product_id": 1, "quantity": 2 }
     */
    private function addToCart() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $userId = $data['user_id'] ?? null;
        $productId = $data['product_id'] ?? null;
        $quantity = $data['quantity'] ?? 1;
        
        if (!$userId || !$productId) {
            http_response_code(400);
            echo json_encode([
                'success' => false, 
                'message' => 'Thiếu thông tin user_id hoặc product_id'
            ]);
            return;
        }
        
        $result = $this->cartService->addToCart($userId, $productId, $quantity);
        
        if ($result['success']) {
            http_response_code(200);
        } else {
            http_response_code(400);
        }
        
        echo json_encode($result);
    }
    
    /**
     * PUT /carts/items/{id} - Cập nhật số lượng
     * Body: { "user_id": 1, "quantity": 3 }
     */
    private function updateCartItem($cartItemId) {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $userId = $data['user_id'] ?? null;
        $quantity = $data['quantity'] ?? null;
        
        if (!$userId || $quantity === null) {
            http_response_code(400);
            echo json_encode([
                'success' => false, 
                'message' => 'Thiếu thông tin user_id hoặc quantity'
            ]);
            return;
        }
        
        $result = $this->cartService->updateCartItem($userId, $cartItemId, $quantity);
        
        if ($result['success']) {
            http_response_code(200);
        } else {
            http_response_code(400);
        }
        
        echo json_encode($result);
    }
    
    /**
     * DELETE /carts/items/{id} - Xóa sản phẩm
     * Query: ?user_id=1
     */
    private function removeFromCart($cartItemId) {
        $userId = $_GET['user_id'] ?? null;
        
        if (!$userId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Thiếu user_id']);
            return;
        }
        
        $result = $this->cartService->removeFromCart($userId, $cartItemId);
        
        if ($result['success']) {
            http_response_code(200);
        } else {
            http_response_code(400);
        }
        
        echo json_encode($result);
    }
    
    /**
     * DELETE /carts - Xóa toàn bộ giỏ hàng
     * Query: ?user_id=1
     */
    private function clearCart() {
        $userId = $_GET['user_id'] ?? null;
        
        if (!$userId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Thiếu user_id']);
            return;
        }
        
        $result = $this->cartService->clearCart($userId);
        
        if ($result['success']) {
            http_response_code(200);
        } else {
            http_response_code(400);
        }
        
        echo json_encode($result);
    }
}
