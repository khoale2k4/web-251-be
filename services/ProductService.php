<?php

require_once __DIR__ . '/../models/Product.php';

class ProductService {
    private $productModel;
    
    public function __construct($pdo) {
        $this->productModel = new Product($pdo);
    }
    
    /**
     * Tạo sản phẩm mới
     */
    public function createProduct($data) {
        try {
            // Validate required fields
            if (empty($data['name']) || !isset($data['price'])) {
                return [
                    'success' => false,
                    'message' => 'Thiếu thông tin bắt buộc (name, price)'
                ];
            }
            
            // Validate price
            if ($data['price'] < 0) {
                return [
                    'success' => false,
                    'message' => 'Giá sản phẩm phải >= 0'
                ];
            }
            
            // Validate discount
            if (isset($data['discount']) && ($data['discount'] < 0 || $data['discount'] > 100)) {
                return [
                    'success' => false,
                    'message' => 'Giảm giá phải từ 0-100%'
                ];
            }
            
            $productId = $this->productModel->create($data);
            $product = $this->productModel->getById($productId);
            
            return [
                'success' => true,
                'message' => 'Tạo sản phẩm thành công',
                'data' => $product
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể tạo sản phẩm: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Lấy chi tiết sản phẩm
     */
    public function getProduct($id) {
        try {
            $product = $this->productModel->getById($id);
            
            if (!$product) {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy sản phẩm'
                ];
            }
            
            return [
                'success' => true,
                'data' => $product
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể lấy thông tin sản phẩm: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Lấy danh sách sản phẩm
     */
    public function getProducts($page = 1, $limit = 20, $filters = []) {
        try {
            $offset = ($page - 1) * $limit;
            $products = $this->productModel->getAll($limit, $offset, $filters);
            $total = $this->productModel->count($filters);
            
            return [
                'success' => true,
                'data' => [
                    'products' => $products,
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
                'message' => 'Không thể lấy danh sách sản phẩm: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Cập nhật sản phẩm
     */
    public function updateProduct($id, $data) {
        try {
            // Kiểm tra sản phẩm có tồn tại không
            if (!$this->productModel->exists($id)) {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy sản phẩm'
                ];
            }
            
            // Validate price nếu có
            if (isset($data['price']) && $data['price'] < 0) {
                return [
                    'success' => false,
                    'message' => 'Giá sản phẩm phải >= 0'
                ];
            }
            
            // Validate discount nếu có
            if (isset($data['discount']) && ($data['discount'] < 0 || $data['discount'] > 100)) {
                return [
                    'success' => false,
                    'message' => 'Giảm giá phải từ 0-100%'
                ];
            }
            
            $this->productModel->update($id, $data);
            $product = $this->productModel->getById($id);
            
            return [
                'success' => true,
                'message' => 'Cập nhật sản phẩm thành công',
                'data' => $product
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể cập nhật sản phẩm: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Xóa sản phẩm
     */
    public function deleteProduct($id) {
        try {
            if (!$this->productModel->exists($id)) {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy sản phẩm'
                ];
            }
            
            $this->productModel->delete($id);
            
            return [
                'success' => true,
                'message' => 'Xóa sản phẩm thành công'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể xóa sản phẩm: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Lấy sản phẩm theo category
     */
    public function getProductsByCategory($categoryId, $page = 1, $limit = 20) {
        try {
            $offset = ($page - 1) * $limit;
            $products = $this->productModel->getByCategory($categoryId, $limit, $offset);
            
            // Count với filter category
            $total = $this->productModel->count(['category_id' => $categoryId]);
            
            return [
                'success' => true,
                'data' => [
                    'products' => $products,
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
                'message' => 'Không thể lấy sản phẩm theo danh mục: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Lấy sản phẩm mới nhất
     */
    public function getLatestProducts($limit = 10) {
        try {
            $products = $this->productModel->getLatest($limit);
            
            return [
                'success' => true,
                'data' => $products
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể lấy sản phẩm mới: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Lấy sản phẩm đang giảm giá
     */
    public function getDiscountedProducts($page = 1, $limit = 20) {
        try {
            $offset = ($page - 1) * $limit;
            $products = $this->productModel->getDiscounted($limit, $offset);
            $total = $this->productModel->countDiscounted();
            
            return [
                'success' => true,
                'data' => [
                    'products' => $products,
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
                'message' => 'Không thể lấy sản phẩm giảm giá: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Cập nhật stock sản phẩm
     */
    public function updateStock($id, $quantity) {
        try {
            if (!$this->productModel->exists($id)) {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy sản phẩm'
                ];
            }
            
            if ($quantity < 0) {
                return [
                    'success' => false,
                    'message' => 'Số lượng phải >= 0'
                ];
            }
            
            $this->productModel->updateStock($id, $quantity);
            $product = $this->productModel->getById($id);
            
            return [
                'success' => true,
                'message' => 'Cập nhật tồn kho thành công',
                'data' => $product
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể cập nhật tồn kho: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Tìm kiếm sản phẩm
     */
    public function searchProducts($keyword, $page = 1, $limit = 20) {
        try {
            $offset = ($page - 1) * $limit;
            $filters = ['search' => $keyword];
            
            $products = $this->productModel->getAll($limit, $offset, $filters);
            $total = $this->productModel->count($filters);
            
            return [
                'success' => true,
                'data' => [
                    'products' => $products,
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
                'message' => 'Không thể tìm kiếm sản phẩm: ' . $e->getMessage()
            ];
        }
    }
}
