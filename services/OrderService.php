<?php

require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Cart.php';

class OrderService {
    private $orderModel;
    private $cartModel;
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->orderModel = new Order($pdo);
        $this->cartModel = new Cart($pdo);
    }
    
    /**
     * Tạo đơn hàng từ giỏ hàng
     */
    public function createOrderFromCart($userId, $shippingAddress, $paymentMethod, $note = null) {
        try {
            $this->pdo->beginTransaction();
            
            // Lấy giỏ hàng và items
            $cart = $this->cartModel->getOrCreateCart($userId);
            $cartItems = $this->cartModel->getCartItems($cart['id']);
            
            if (empty($cartItems)) {
                throw new Exception("Giỏ hàng trống");
            }
            
            // Tính tổng tiền
            $totalPrice = 0;
            foreach ($cartItems as $item) {
                $totalPrice += $item['subtotal'];
            }
            
            // Tạo đơn hàng
            $orderId = $this->orderModel->createOrder(
                $userId, 
                $totalPrice, 
                $shippingAddress, 
                $paymentMethod, 
                $note
            );
            
            // Thêm các items vào đơn hàng
            foreach ($cartItems as $item) {
                $this->orderModel->addOrderItem(
                    $orderId,
                    $item['product_id'],
                    $item['quantity'],
                    $item['final_price']
                );
            }
            
            // Xóa giỏ hàng sau khi đặt hàng thành công
            $this->cartModel->clearCart($cart['id']);
            
            $this->pdo->commit();
            
            // Lấy thông tin đơn hàng vừa tạo
            $order = $this->getOrderDetail($orderId);
            
            return [
                'success' => true,
                'message' => 'Đặt hàng thành công',
                'data' => $order['data']
            ];
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return [
                'success' => false,
                'message' => 'Không thể tạo đơn hàng: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Tạo đơn hàng trực tiếp (không qua giỏ hàng)
     */
    public function createOrder($userId, $items, $shippingAddress, $paymentMethod, $note = null) {
        try {
            $this->pdo->beginTransaction();
            
            if (empty($items)) {
                throw new Exception("Danh sách sản phẩm trống");
            }
            
            // Tính tổng tiền
            $totalPrice = 0;
            foreach ($items as $item) {
                if (!isset($item['product_id'], $item['quantity'], $item['price'])) {
                    throw new Exception("Thiếu thông tin sản phẩm");
                }
                $totalPrice += $item['quantity'] * $item['price'];
            }
            
            // Tạo đơn hàng
            $orderId = $this->orderModel->createOrder(
                $userId,
                $totalPrice,
                $shippingAddress,
                $paymentMethod,
                $note
            );
            
            // Thêm các items
            foreach ($items as $item) {
                $this->orderModel->addOrderItem(
                    $orderId,
                    $item['product_id'],
                    $item['quantity'],
                    $item['price']
                );
            }
            
            $this->pdo->commit();
            
            // Lấy thông tin đơn hàng vừa tạo
            $order = $this->getOrderDetail($orderId);
            
            return [
                'success' => true,
                'message' => 'Tạo đơn hàng thành công',
                'data' => $order['data']
            ];
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return [
                'success' => false,
                'message' => 'Không thể tạo đơn hàng: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Lấy chi tiết đơn hàng
     */
    public function getOrderDetail($orderId) {
        try {
            $order = $this->orderModel->getOrderById($orderId);
            
            if (!$order) {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy đơn hàng'
                ];
            }
            
            $items = $this->orderModel->getOrderItems($orderId);
            
            return [
                'success' => true,
                'data' => [
                    'order' => $order,
                    'items' => $items
                ]
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể lấy thông tin đơn hàng: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Lấy danh sách đơn hàng của user
     */
    public function getUserOrders($userId, $page = 1, $limit = 10, $status = null) {
        try {
            $offset = ($page - 1) * $limit;
            $orders = $this->orderModel->getOrdersByUserId($userId, $limit, $offset);
            $total = $this->orderModel->countOrders($userId, $status);
            
            return [
                'success' => true,
                'data' => [
                    'orders' => $orders,
                    'pagination' => [
                        'page' => $page,
                        'limit' => $limit,
                        'total' => $total,
                        'total_pages' => ceil($total / $limit)
                    ]
                ]
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể lấy danh sách đơn hàng: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Lấy tất cả đơn hàng (admin)
     */
    public function getAllOrders($page = 1, $limit = 10, $status = null) {
        try {
            $offset = ($page - 1) * $limit;
            $orders = $this->orderModel->getAllOrders($limit, $offset, $status);
            $total = $this->orderModel->countOrders(null, $status);
            
            return [
                'success' => true,
                'data' => [
                    'orders' => $orders,
                    'pagination' => [
                        'page' => $page,
                        'limit' => $limit,
                        'total' => $total,
                        'total_pages' => ceil($total / $limit)
                    ]
                ]
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể lấy danh sách đơn hàng: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Cập nhật trạng thái đơn hàng
     */
    public function updateOrderStatus($orderId, $status) {
        try {
            $order = $this->orderModel->getOrderById($orderId);
            if (!$order) {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy đơn hàng'
                ];
            }
            
            $this->orderModel->updateOrderStatus($orderId, $status);
            
            return [
                'success' => true,
                'message' => 'Cập nhật trạng thái đơn hàng thành công',
                'data' => [
                    'order_id' => $orderId,
                    'status' => $status
                ]
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể cập nhật trạng thái: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Cập nhật thông tin đơn hàng
     */
    public function updateOrder($orderId, $data) {
        try {
            $order = $this->orderModel->getOrderById($orderId);
            if (!$order) {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy đơn hàng'
                ];
            }
            
            // Chỉ cho phép cập nhật nếu đơn hàng đang ở trạng thái pending
            if ($order['status'] !== 'pending') {
                return [
                    'success' => false,
                    'message' => 'Chỉ có thể cập nhật đơn hàng đang chờ xử lý'
                ];
            }
            
            $this->orderModel->updateOrder($orderId, $data);
            
            return [
                'success' => true,
                'message' => 'Cập nhật đơn hàng thành công'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể cập nhật đơn hàng: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Hủy đơn hàng
     */
    public function cancelOrder($orderId, $userId = null) {
        try {
            $order = $this->orderModel->getOrderById($orderId);
            if (!$order) {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy đơn hàng'
                ];
            }
            
            // Kiểm tra quyền nếu có userId
            if ($userId && !$this->orderModel->isOrderBelongsToUser($orderId, $userId)) {
                return [
                    'success' => false,
                    'message' => 'Bạn không có quyền hủy đơn hàng này'
                ];
            }
            
            // Chỉ cho phép hủy nếu đơn hàng chưa shipped
            if (in_array($order['status'], ['shipped', 'completed'])) {
                return [
                    'success' => false,
                    'message' => 'Không thể hủy đơn hàng đã giao hoặc hoàn thành'
                ];
            }
            
            $this->orderModel->deleteOrder($orderId);
            
            return [
                'success' => true,
                'message' => 'Hủy đơn hàng thành công'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể hủy đơn hàng: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Xóa đơn hàng (hard delete - admin only)
     */
    public function deleteOrder($orderId) {
        try {
            $order = $this->orderModel->getOrderById($orderId);
            if (!$order) {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy đơn hàng'
                ];
            }
            
            $this->orderModel->hardDeleteOrder($orderId);
            
            return [
                'success' => true,
                'message' => 'Xóa đơn hàng thành công'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể xóa đơn hàng: ' . $e->getMessage()
            ];
        }
    }
}
