<?php

require_once __DIR__ . '/../models/Cart.php';

class CartService {
    private $cartModel;
    
    public function __construct($pdo) {
        $this->cartModel = new Cart($pdo);
    }

    public function getAllCarts() {
        try {
        $cart = $this->cartModel->getAllCarts();
            
            return [
                'success' => true,
                'data' => [
                    'cart' => $cart,
                ]
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể lấy giỏ hàng: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Lấy giỏ hàng của user kèm theo các items
     */
    public function getUserCart($userId) {
        try {
            $cart = $this->cartModel->getOrCreateCart($userId);
            $items = $this->cartModel->getCartItems($cart['id']);
            $total = $this->cartModel->getCartTotal($cart['id']);
            
            return [
                'success' => true,
                'data' => [
                    'cart' => $cart,
                    'items' => $items,
                    'total' => (float)$total,
                    'item_count' => count($items)
                ]
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể lấy giỏ hàng: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    public function addToCart($userId, $productId, $quantity = 1) {
        try {
            // Validate quantity
            if ($quantity < 1) {
                return [
                    'success' => false,
                    'message' => 'Số lượng phải lớn hơn 0'
                ];
            }
            
            $cart = $this->cartModel->getOrCreateCart($userId);
            $itemId = $this->cartModel->addItem($cart['id'], $productId, $quantity);
            
            // Lấy thông tin giỏ hàng sau khi thêm
            $items = $this->cartModel->getCartItems($cart['id']);
            $total = $this->cartModel->getCartTotal($cart['id']);
            
            return [
                'success' => true,
                'message' => 'Đã thêm sản phẩm vào giỏ hàng',
                'data' => [
                    'cart_item_id' => $itemId,
                    'items' => $items,
                    'total' => (float)$total,
                    'item_count' => count($items)
                ]
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể thêm vào giỏ hàng: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Cập nhật số lượng sản phẩm trong giỏ
     */
    public function updateCartItem($userId, $cartItemId, $quantity) {
        try {
            $cart = $this->cartModel->getOrCreateCart($userId);
            
            // Kiểm tra quyền sở hữu
            if (!$this->cartModel->isCartItemBelongsToCart($cartItemId, $cart['id'])) {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy sản phẩm trong giỏ hàng của bạn'
                ];
            }
            
            $this->cartModel->updateItemQuantity($cartItemId, $quantity);
            
            // Lấy thông tin giỏ hàng sau khi cập nhật
            $items = $this->cartModel->getCartItems($cart['id']);
            $total = $this->cartModel->getCartTotal($cart['id']);
            
            return [
                'success' => true,
                'message' => 'Đã cập nhật giỏ hàng',
                'data' => [
                    'items' => $items,
                    'total' => (float)$total,
                    'item_count' => count($items)
                ]
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể cập nhật giỏ hàng: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Xóa sản phẩm khỏi giỏ hàng
     */
    public function removeFromCart($userId, $cartItemId) {
        try {
            $cart = $this->cartModel->getOrCreateCart($userId);
            
            // Kiểm tra quyền sở hữu
            if (!$this->cartModel->isCartItemBelongsToCart($cartItemId, $cart['id'])) {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy sản phẩm trong giỏ hàng của bạn'
                ];
            }
            
            $this->cartModel->removeItem($cartItemId);
            
            // Lấy thông tin giỏ hàng sau khi xóa
            $items = $this->cartModel->getCartItems($cart['id']);
            $total = $this->cartModel->getCartTotal($cart['id']);
            
            return [
                'success' => true,
                'message' => 'Đã xóa sản phẩm khỏi giỏ hàng',
                'data' => [
                    'items' => $items,
                    'total' => (float)$total,
                    'item_count' => count($items)
                ]
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể xóa khỏi giỏ hàng: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Xóa toàn bộ giỏ hàng
     */
    public function clearCart($userId) {
        try {
            $cart = $this->cartModel->getOrCreateCart($userId);
            $this->cartModel->clearCart($cart['id']);
            
            return [
                'success' => true,
                'message' => 'Đã xóa toàn bộ giỏ hàng',
                'data' => [
                    'items' => [],
                    'total' => 0,
                    'item_count' => 0
                ]
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể xóa giỏ hàng: ' . $e->getMessage()
            ];
        }
    }
}
