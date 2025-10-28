<?php

require_once __DIR__ . '/../models/ProductCategory.php';

class ProductCategoryService {
    private $categoryModel;
    
    public function __construct($pdo) {
        $this->categoryModel = new ProductCategory($pdo);
    }
    
    /**
     * Tạo danh mục mới
     */
    public function createCategory($data) {
        try {
            // Validate required fields
            if (empty($data['name'])) {
                return [
                    'success' => false,
                    'message' => 'Thiếu tên danh mục'
                ];
            }
            
            // Kiểm tra tên đã tồn tại chưa
            if ($this->categoryModel->existsByName($data['name'])) {
                return [
                    'success' => false,
                    'message' => 'Tên danh mục đã tồn tại'
                ];
            }
            
            $categoryId = $this->categoryModel->create(
                $data['name'],
                $data['description'] ?? null
            );
            
            $category = $this->categoryModel->getById($categoryId);
            
            return [
                'success' => true,
                'message' => 'Tạo danh mục thành công',
                'data' => $category
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể tạo danh mục: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Lấy chi tiết danh mục
     */
    public function getCategory($id) {
        try {
            $category = $this->categoryModel->getById($id);
            
            if (!$category) {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy danh mục'
                ];
            }
            
            // Thêm số lượng sản phẩm
            $category['product_count'] = $this->categoryModel->getProductCount($id);
            
            return [
                'success' => true,
                'data' => $category
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể lấy thông tin danh mục: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Lấy danh sách danh mục
     */
    public function getCategories($page = 1, $limit = 100, $withProductCount = false) {
        try {
            $offset = ($page - 1) * $limit;
            
            if ($withProductCount) {
                $categories = $this->categoryModel->getAllWithProductCount($limit, $offset);
            } else {
                $categories = $this->categoryModel->getAll($limit, $offset);
            }
            
            $total = $this->categoryModel->count();
            
            return [
                'success' => true,
                'data' => [
                    'categories' => $categories,
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
                'message' => 'Không thể lấy danh sách danh mục: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Cập nhật danh mục
     */
    public function updateCategory($id, $data) {
        try {
            // Kiểm tra danh mục có tồn tại không
            if (!$this->categoryModel->exists($id)) {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy danh mục'
                ];
            }
            
            // Validate name nếu có
            if (isset($data['name']) && empty($data['name'])) {
                return [
                    'success' => false,
                    'message' => 'Tên danh mục không được để trống'
                ];
            }
            
            // Kiểm tra tên đã tồn tại chưa (trừ chính nó)
            if (isset($data['name']) && $this->categoryModel->existsByName($data['name'], $id)) {
                return [
                    'success' => false,
                    'message' => 'Tên danh mục đã tồn tại'
                ];
            }
            
            // Lấy thông tin hiện tại
            $currentCategory = $this->categoryModel->getById($id);
            
            $name = $data['name'] ?? $currentCategory['name'];
            $description = isset($data['description']) ? $data['description'] : $currentCategory['description'];
            
            $this->categoryModel->update($id, $name, $description);
            $category = $this->categoryModel->getById($id);
            
            return [
                'success' => true,
                'message' => 'Cập nhật danh mục thành công',
                'data' => $category
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể cập nhật danh mục: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Xóa danh mục
     */
    public function deleteCategory($id) {
        try {
            if (!$this->categoryModel->exists($id)) {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy danh mục'
                ];
            }
            
            // Kiểm tra xem có sản phẩm nào đang dùng danh mục này không
            $productCount = $this->categoryModel->getProductCount($id);
            if ($productCount > 0) {
                return [
                    'success' => false,
                    'message' => "Không thể xóa danh mục có {$productCount} sản phẩm. Vui lòng xóa hoặc chuyển sản phẩm sang danh mục khác trước."
                ];
            }
            
            $this->categoryModel->delete($id);
            
            return [
                'success' => true,
                'message' => 'Xóa danh mục thành công'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể xóa danh mục: ' . $e->getMessage()
            ];
        }
    }
}

