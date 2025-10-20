<?php

class Cart {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Lấy giỏ hàng của user (hoặc tạo mới nếu chưa có)
     */
    public function getOrCreateCart($userId) {
        // Tìm cart hiện tại
        $stmt = $this->pdo->prepare("SELECT * FROM carts WHERE user_id = ?");
        $stmt->execute([$userId]);
        $cart = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$cart) {
            // Tạo cart mới
            $stmt = $this->pdo->prepare(
                "INSERT INTO carts (user_id) VALUES (?)"
            );
            $stmt->execute([$userId]);
            $cartId = $this->pdo->lastInsertId();
            
            return [
                'id' => $cartId,
                'user_id' => $userId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }
        
        return $cart;
    }
    
    /**
     * Lấy tất cả items trong giỏ hàng kèm thông tin sản phẩm
     */
    public function getCartItems($cartId) {
        $stmt = $this->pdo->prepare("
            SELECT 
                ci.id,
                ci.cart_id,
                ci.product_id,
                ci.quantity,
                p.name as product_name,
                p.price,
                p.discount,
                p.image,
                p.stock,
                p.size,
                p.color,
                (p.price * (100 - p.discount) / 100) as final_price,
                (p.price * (100 - p.discount) / 100 * ci.quantity) as subtotal
            FROM cart_items ci
            JOIN products p ON ci.product_id = p.id
            WHERE ci.cart_id = ?
        ");
        $stmt->execute([$cartId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    public function addItem($cartId, $productId, $quantity = 1) {
        // Kiểm tra xem sản phẩm đã có trong giỏ chưa
        $stmt = $this->pdo->prepare(
            "SELECT * FROM cart_items WHERE cart_id = ? AND product_id = ?"
        );
        $stmt->execute([$cartId, $productId]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existing) {
            // Cập nhật số lượng
            $newQuantity = $existing['quantity'] + $quantity;
            $stmt = $this->pdo->prepare(
                "UPDATE cart_items SET quantity = ? WHERE id = ?"
            );
            $stmt->execute([$newQuantity, $existing['id']]);
            return $existing['id'];
        } else {
            // Thêm mới
            $stmt = $this->pdo->prepare(
                "INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, ?)"
            );
            $stmt->execute([$cartId, $productId, $quantity]);
            return $this->pdo->lastInsertId();
        }
    }
    
    /**
     * Cập nhật số lượng sản phẩm trong giỏ
     */
    public function updateItemQuantity($cartItemId, $quantity) {
        if ($quantity <= 0) {
            // Nếu số lượng <= 0, xóa luôn
            return $this->removeItem($cartItemId);
        }
        
        $stmt = $this->pdo->prepare(
            "UPDATE cart_items SET quantity = ? WHERE id = ?"
        );
        return $stmt->execute([$quantity, $cartItemId]);
    }
    
    /**
     * Xóa sản phẩm khỏi giỏ hàng
     */
    public function removeItem($cartItemId) {
        $stmt = $this->pdo->prepare("DELETE FROM cart_items WHERE id = ?");
        return $stmt->execute([$cartItemId]);
    }
    
    /**
     * Xóa toàn bộ giỏ hàng
     */
    public function clearCart($cartId) {
        $stmt = $this->pdo->prepare("DELETE FROM cart_items WHERE cart_id = ?");
        return $stmt->execute([$cartId]);
    }
    
    /**
     * Lấy tổng giá trị giỏ hàng
     */
    public function getCartTotal($cartId) {
        $stmt = $this->pdo->prepare("
            SELECT 
                SUM(p.price * (100 - p.discount) / 100 * ci.quantity) as total
            FROM cart_items ci
            JOIN products p ON ci.product_id = p.id
            WHERE ci.cart_id = ?
        ");
        $stmt->execute([$cartId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
    
    /**
     * Kiểm tra xem cart item có thuộc về cart không
     */
    public function isCartItemBelongsToCart($cartItemId, $cartId) {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) as count FROM cart_items WHERE id = ? AND cart_id = ?"
        );
        $stmt->execute([$cartItemId, $cartId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
}
