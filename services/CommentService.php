<?php

require_once __DIR__ . '/../models/Comment.php';

class CommentService
{
    private $commentModel;
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->commentModel = new Comment($pdo);
    }

    /**
     * Lấy danh sách bình luận theo post_id
     */
    public function getCommentsByPost($postId)
    {
        try {
            $comments = $this->commentModel->getByPostId($postId);
            return ['success' => true, 'data' => $comments];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Không thể lấy bình luận: ' . $e->getMessage()];
        }
    }
    /**
     * Lấy danh sách bình luận theo product_id
     */
    public function getCommentsByProduct($productId)
    {
        try {
            $comments = $this->commentModel->getByProductId($productId);
            return ['success' => true, 'data' => $comments];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Không thể lấy bình luận sản phẩm: ' . $e->getMessage()];
        }
    }



    /**
     * Tạo bình luận mới
     */
    public function createComment($data)
    {
        try {
            if (empty($data['content']) || strlen(trim($data['content'])) < 3) {
                return ['success' => false, 'message' => 'Bình luận quá ngắn'];
            }

            // Chống spam (ví dụ 1 user không gửi >1 bình luận giống nhau trong 30 giây)
            // -> Có thể thêm kiểm tra bằng bảng log hoặc session nếu cần sau này.

            // Server-side validation: user must exist
            require_once __DIR__ . '/../models/UserModel.php';
            $userModel = new UserModel($this->pdo);
            if (empty($data['user_id']) || !$userModel->getById((int)$data['user_id'])) {
                return ['success' => false, 'message' => 'User không tồn tại'];
            }

            // If product comment, ensure product exists
            if (isset($data['comment_type']) && $data['comment_type'] === 'product') {
                require_once __DIR__ . '/../models/Product.php';
                $productModel = new Product($this->pdo);
                if (empty($data['product_id']) || !$productModel->exists((int)$data['product_id'])) {
                    return ['success' => false, 'message' => 'Product không tồn tại'];
                }
            }

            // If post comment, ensure post exists
            if (!isset($data['comment_type']) || $data['comment_type'] === 'post') {
                require_once __DIR__ . '/../models/Post.php';
                $postModel = new Post($this->pdo);
                if (empty($data['post_id']) || !$postModel->getById((int)$data['post_id'])) {
                    return ['success' => false, 'message' => 'Post không tồn tại'];
                }
            }

            $insertId = $this->commentModel->create($data);
            if ($insertId) {
                $created = $this->commentModel->getById($insertId);
                // Normalize fields so post_id/product_id/rating/comment_type always exist
                $created['post_id'] = array_key_exists('post_id', $created) ? $created['post_id'] : null;
                $created['product_id'] = array_key_exists('product_id', $created) ? $created['product_id'] : null;
                $created['rating'] = array_key_exists('rating', $created) ? $created['rating'] : null;
                $created['comment_type'] = array_key_exists('comment_type', $created) ? $created['comment_type'] : null;

                return ['success' => true, 'message' => 'Gửi bình luận thành công', 'data' => $created];
            }
            return ['success' => false, 'message' => 'Không thể lưu bình luận'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Không thể thêm bình luận: ' . $e->getMessage()];
        }
    }

    /**
     * Xoá bình luận (ẩn hoặc xóa thật)
     */
    public function deleteComment($id)
    {
        try {
            // Nếu muốn "ẩn" thay vì xóa, có thể thêm cột `is_hidden` trong DB
            $this->commentModel->delete($id);
            return ['success' => true, 'message' => 'Đã xoá bình luận'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi khi xoá bình luận: ' . $e->getMessage()];
        }
    }
}
