<?php

require_once __DIR__ . '/../services/OrderService.php';

class OrderController {
    private $orderService;
    
    public function __construct($pdo) {
        $this->orderService = new OrderService($pdo);
    }
    
    public function handleRequest($request) {
        $method = $_SERVER['REQUEST_METHOD'];
        
        // POST /orders - Tạo đơn hàng mới
        if ($request === '/orders' && $method === 'POST') {
            $this->createOrder();
            return;
        }
        
        // POST /orders/from-cart - Tạo đơn hàng từ giỏ hàng
        if ($request === '/orders/from-cart' && $method === 'POST') {
            $this->createOrderFromCart();
            return;
        }
        
        // GET /orders - Lấy danh sách đơn hàng
        if ($request === '/orders' && $method === 'GET') {
            $this->getOrders();
            return;
        }
        
        // GET /orders/{id} - Lấy chi tiết đơn hàng
        if (preg_match('#^/orders/(\d+)$#', $request, $matches) && $method === 'GET') {
            $orderId = (int)$matches[1];
            $this->getOrderDetail($orderId);
            return;
        }
        
        // PUT /orders/{id} - Cập nhật đơn hàng
        if (preg_match('#^/orders/(\d+)$#', $request, $matches) && $method === 'PUT') {
            $orderId = (int)$matches[1];
            $this->updateOrder($orderId);
            return;
        }
        
        // PUT /orders/{id}/status - Cập nhật trạng thái đơn hàng
        if (preg_match('#^/orders/(\d+)/status$#', $request, $matches) && $method === 'PUT') {
            $orderId = (int)$matches[1];
            $this->updateOrderStatus($orderId);
            return;
        }
        
        // PUT /orders/{id}/cancel - Hủy đơn hàng
        if (preg_match('#^/orders/(\d+)/cancel$#', $request, $matches) && $method === 'PUT') {
            $orderId = (int)$matches[1];
            $this->cancelOrder($orderId);
            return;
        }
        
        // DELETE /orders/{id} - Xóa đơn hàng (admin)
        if (preg_match('#^/orders/(\d+)$#', $request, $matches) && $method === 'DELETE') {
            $orderId = (int)$matches[1];
            $this->deleteOrder($orderId);
            return;
        }
        
        // Route không tồn tại
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Route not found']);
    }
    
    /**
     * POST /orders - Tạo đơn hàng trực tiếp
     * Body: {
     *   "user_id": 1,
     *   "items": [{"product_id": 1, "quantity": 2, "price": 100000}],
     *   "shipping_address": "123 Street",
     *   "payment_method": "COD",
     *   "note": "Ghi chú"
     * }
     */
    private function createOrder() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $userId = $data['user_id'] ?? null;
        $items = $data['items'] ?? [];
        $shippingAddress = $data['shipping_address'] ?? null;
        $paymentMethod = $data['payment_method'] ?? null;
        $note = $data['note'] ?? null;
        
        if (!$userId || empty($items) || !$shippingAddress || !$paymentMethod) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Thiếu thông tin bắt buộc'
            ]);
            return;
        }
        
        $result = $this->orderService->createOrder(
            $userId,
            $items,
            $shippingAddress,
            $paymentMethod,
            $note
        );
        
        if ($result['success']) {
            http_response_code(201);
        } else {
            http_response_code(400);
        }
        
        echo json_encode($result);
    }
    
    /**
     * POST /orders/from-cart - Tạo đơn hàng từ giỏ hàng
     * Body: {
     *   "user_id": 1,
     *   "shipping_address": "123 Street",
     *   "payment_method": "COD",
     *   "note": "Ghi chú"
     * }
     */
    private function createOrderFromCart() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $userId = $data['user_id'] ?? null;
        $shippingAddress = $data['shipping_address'] ?? null;
        $paymentMethod = $data['payment_method'] ?? null;
        $note = $data['note'] ?? null;
        
        if (!$userId || !$shippingAddress || !$paymentMethod) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Thiếu thông tin bắt buộc'
            ]);
            return;
        }
        
        $result = $this->orderService->createOrderFromCart(
            $userId,
            $shippingAddress,
            $paymentMethod,
            $note
        );
        
        if ($result['success']) {
            http_response_code(201);
        } else {
            http_response_code(400);
        }
        
        echo json_encode($result);
    }
    
    /**
     * GET /orders - Lấy danh sách đơn hàng
     * Query params:
     *   - user_id: ID user (nếu không có thì lấy tất cả - admin)
     *   - page: Trang (default 1)
     *   - limit: Số lượng/trang (default 10)
     *   - status: Lọc theo trạng thái
     */
    private function getOrders() {
        $userId = $_GET['user_id'] ?? null;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $status = $_GET['status'] ?? null;
        
        if ($userId) {
            // Lấy đơn hàng của user cụ thể
            $result = $this->orderService->getUserOrders($userId, $page, $limit, $status);
        } else {
            // Lấy tất cả đơn hàng (admin)
            $result = $this->orderService->getAllOrders($page, $limit, $status);
        }
        
        if ($result['success']) {
            http_response_code(200);
        } else {
            http_response_code(400);
        }
        
        echo json_encode($result);
    }
    
    /**
     * GET /orders/{id} - Lấy chi tiết đơn hàng
     */
    private function getOrderDetail($orderId) {
        $result = $this->orderService->getOrderDetail($orderId);
        
        if ($result['success']) {
            http_response_code(200);
        } else {
            http_response_code(404);
        }
        
        echo json_encode($result);
    }
    
    /**
     * PUT /orders/{id} - Cập nhật thông tin đơn hàng
     * Body: {
     *   "shipping_address": "New address",
     *   "payment_method": "Bank Transfer",
     *   "note": "New note"
     * }
     */
    private function updateOrder($orderId) {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (empty($data)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Không có dữ liệu cập nhật'
            ]);
            return;
        }
        
        $result = $this->orderService->updateOrder($orderId, $data);
        
        if ($result['success']) {
            http_response_code(200);
        } else {
            http_response_code(400);
        }
        
        echo json_encode($result);
    }
    
    /**
     * PUT /orders/{id}/status - Cập nhật trạng thái
     * Body: { "status": "confirmed" }
     */
    private function updateOrderStatus($orderId) {
        $data = json_decode(file_get_contents('php://input'), true);
        $status = $data['status'] ?? null;
        
        if (!$status) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Thiếu trạng thái'
            ]);
            return;
        }
        
        $result = $this->orderService->updateOrderStatus($orderId, $status);
        
        if ($result['success']) {
            http_response_code(200);
        } else {
            http_response_code(400);
        }
        
        echo json_encode($result);
    }
    
    /**
     * PUT /orders/{id}/cancel - Hủy đơn hàng
     * Query: ?user_id=1 (optional)
     */
    private function cancelOrder($orderId) {
        $userId = $_GET['user_id'] ?? null;
        
        $result = $this->orderService->cancelOrder($orderId, $userId);
        
        if ($result['success']) {
            http_response_code(200);
        } else {
            http_response_code(400);
        }
        
        echo json_encode($result);
    }
    
    /**
     * DELETE /orders/{id} - Xóa đơn hàng (admin only)
     */
    private function deleteOrder($orderId) {
        $result = $this->orderService->deleteOrder($orderId);
        
        if ($result['success']) {
            http_response_code(200);
        } else {
            http_response_code(400);
        }
        
        echo json_encode($result);
    }
}
