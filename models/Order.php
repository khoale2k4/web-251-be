<?php

class Order {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Tạo đơn hàng mới
     */
    public function createOrder($userId, $totalPrice, $shippingAddress, $paymentMethod, $note = null) {
        $stmt = $this->pdo->prepare("
            INSERT INTO orders (user_id, total_price, shipping_address, payment_method, note, status)
            VALUES (?, ?, ?, ?, ?, 'pending')
        ");
        $stmt->execute([$userId, $totalPrice, $shippingAddress, $paymentMethod, $note]);
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Thêm item vào đơn hàng
     */
    public function addOrderItem($orderId, $productId, $quantity, $price) {
        $stmt = $this->pdo->prepare("
            INSERT INTO order_items (order_id, product_id, quantity, price)
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([$orderId, $productId, $quantity, $price]);
    }
    
    /**
     * Lấy thông tin đơn hàng theo ID
     */
    public function getOrderById($orderId) {
        $stmt = $this->pdo->prepare("
            SELECT 
                o.*,
                u.name as user_name,
                u.email as user_email,
                u.phone as user_phone
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            WHERE o.id = ?
        ");
        $stmt->execute([$orderId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lấy các items của đơn hàng
     */
    public function getOrderItems($orderId) {
        $stmt = $this->pdo->prepare("
            SELECT 
                oi.*,
                p.name as product_name,
                p.image as product_image,
                p.size,
                p.color,
                (oi.quantity * oi.price) as subtotal
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lấy tất cả đơn hàng của user
     */
    public function getOrdersByUserId($userId, $limit = 50, $offset = 0) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM orders 
            WHERE user_id = ? 
            ORDER BY created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$userId, $limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lấy tất cả đơn hàng (admin)
     */
    public function getAllOrders($limit = 50, $offset = 0, $status = null) {
        $limit = (int)$limit;
        $offset = (int)$offset;

        if ($status) {
            $stmt = $this->pdo->prepare("
                SELECT 
                    o.*,
                    u.name as user_name,
                    u.email as user_email
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                WHERE o.status = ?
                ORDER BY o.created_at DESC
                LIMIT $limit OFFSET $offset
            ");
            $stmt->execute([$status]);
        } else {
            $stmt = $this->pdo->prepare("
                SELECT 
                    o.*,
                    u.name as user_name,
                    u.email as user_email
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                ORDER BY o.created_at DESC
                LIMIT $limit OFFSET $offset
            ");
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Cập nhật trạng thái đơn hàng
     */
    public function updateOrderStatus($orderId, $status) {
        $validStatuses = ['pending', 'confirmed', 'shipped', 'completed', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            throw new Exception("Invalid status");
        }
        
        $stmt = $this->pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $orderId]);
    }
    
    /**
     * Cập nhật thông tin đơn hàng
     */
    public function updateOrder($orderId, $data) {
        $fields = [];
        $values = [];
        
        if (isset($data['shipping_address'])) {
            $fields[] = "shipping_address = ?";
            $values[] = $data['shipping_address'];
        }
        if (isset($data['payment_method'])) {
            $fields[] = "payment_method = ?";
            $values[] = $data['payment_method'];
        }
        if (isset($data['note'])) {
            $fields[] = "note = ?";
            $values[] = $data['note'];
        }
        if (isset($data['status'])) {
            $validStatuses = ['pending', 'confirmed', 'shipped', 'completed', 'cancelled'];
            if (in_array($data['status'], $validStatuses)) {
                $fields[] = "status = ?";
                $values[] = $data['status'];
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $orderId;
        $sql = "UPDATE orders SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($values);
    }
    
    /**
     * Xóa đơn hàng (soft delete bằng cách set status = cancelled)
     */
    public function deleteOrder($orderId) {
        return $this->updateOrderStatus($orderId, 'cancelled');
    }
    
    /**
     * Hard delete đơn hàng (xóa thật)
     */
    public function hardDeleteOrder($orderId) {
        $stmt = $this->pdo->prepare("DELETE FROM orders WHERE id = ?");
        return $stmt->execute([$orderId]);
    }
    
    /**
     * Kiểm tra đơn hàng có thuộc về user không
     */
    public function isOrderBelongsToUser($orderId, $userId) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM orders WHERE id = ? AND user_id = ?");
        $stmt->execute([$orderId, $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
    
    /**
     * Đếm tổng số đơn hàng
     */
    public function countOrders($userId = null, $status = null) {
        if ($userId) {
            if ($status) {
                $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM orders WHERE user_id = ? AND status = ?");
                $stmt->execute([$userId, $status]);
            } else {
                $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM orders WHERE user_id = ?");
                $stmt->execute([$userId]);
            }
        } else {
            if ($status) {
                $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM orders WHERE status = ?");
                $stmt->execute([$status]);
            } else {
                $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM orders");
                $stmt->execute();
            }
        }
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }
}
