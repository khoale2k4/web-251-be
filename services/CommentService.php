<?php

require_once __DIR__ . '/../models/Comment.php';

class CommentService {
    private $commentModel;

    public function __construct($pdo) {
        $this->commentModel = new Comment($pdo);
    }

    /**
     * Lấy danh sách bình luận theo post_id
     */
    public function getComments($postId) {
        try {
            $comments = $this->commentModel->getByPostId($postId);
            return ['success' => true, 'data' => $comments];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Không thể lấy bình luận: ' . $e->getMessage()];
        }
    }

    /**
     * Tạo bình luận mới
     */
    public function createComment($data) {
        try {
            if (empty($data['content']) || strlen(trim($data['content'])) < 3) {
                return ['success' => false, 'message' => 'Bình luận quá ngắn'];
            }

            // Chống spam (ví dụ 1 user không gửi >1 bình luận giống nhau trong 30 giây)
            // -> Có thể thêm kiểm tra bằng bảng log hoặc session nếu cần sau này.

            $this->commentModel->create($data);
            return ['success' => true, 'message' => 'Gửi bình luận thành công'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Không thể thêm bình luận: ' . $e->getMessage()];
        }
    }

    /**
     * Xoá bình luận (ẩn hoặc xóa thật)
     */
    public function deleteComment($id) {
        try {
            // Nếu muốn "ẩn" thay vì xóa, có thể thêm cột `is_hidden` trong DB
            $this->commentModel->delete($id);
            return ['success' => true, 'message' => 'Đã xoá bình luận'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi khi xoá bình luận: ' . $e->getMessage()];
        }
    }
}

?>
