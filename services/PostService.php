<?php

require_once __DIR__ . '/../models/Post.php';

class PostService
{
    private $postModel;

    public function __construct($pdo)
    {
        $this->postModel = new Post($pdo);
    }

    /**
     * Lấy danh sách bài viết (có tìm kiếm & phân trang)
     */
    public function getPosts($page = 1, $limit = 10, $search = null)
    {
        try {
            $offset = ($page - 1) * $limit;
            $data = $this->postModel->getAll($limit, $offset, $search);

            return [
                'success' => true,
                'data' => $data
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể lấy danh sách bài viết: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Lấy chi tiết bài viết theo ID
     */
    public function getPost($id)
    {
        $post = $this->postModel->getById($id);

        if (!$post) {
            return ['success' => false, 'message' => 'Không tìm thấy bài viết'];
        }

        return ['success' => true, 'data' => $post];
    }

    /**
     * Tạo bài viết mới
     */
    public function createPost($data)
    {
        try {
            // Validate cơ bản
            if (empty($data['title']) || empty($data['content'])) {
                return ['success' => false, 'message' => 'Vui lòng nhập tiêu đề và nội dung'];
            }

            // Kiểm tra trùng tiêu đề
            $existing = $this->postModel->getAll(1, 0, $data['title']);
            if (!empty($existing)) {
                return ['success' => false, 'message' => 'Tiêu đề đã tồn tại'];
            }

            // Tạo slug (nếu chưa có)
            $data['slug'] = $data['slug'] ?? strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['title'])));

            $postId = $this->postModel->create($data);
            $newPost = $this->postModel->getById($postId);

            return [
                'success' => true,
                'message' => 'Thêm bài viết thành công',
                'data' => $newPost
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi khi thêm bài viết: ' . $e->getMessage()];
        }
    }

    /**
     * Cập nhật bài viết
     */
    public function updatePost($id, $data)
    {
        try {
            // Lấy bài viết hiện tại
            $existingPost = $this->postModel->getById($id);
            if (!$existingPost) {
                return ['success' => false, 'message' => 'Không tìm thấy bài viết'];
            }

            // Giữ lại dữ liệu cũ nếu request không có field tương ứng
            $updateData = [
                'title'   => isset($data['title']) && trim($data['title']) !== ''
                    ? trim($data['title'])
                    : $existingPost['title'],
                'content' => isset($data['content']) && trim($data['content']) !== ''
                    ? trim($data['content'])
                    : $existingPost['content'],
                'image'   => isset($data['image']) && trim($data['image']) !== ''
                    ? trim($data['image'])
                    : $existingPost['image'],
                'author_id' => isset($data['author_id']) && !empty($data['author_id'])
                    ? (int)$data['author_id']
                    : $existingPost['author_id']
            ];

            // Cập nhật vào DB
            $this->postModel->update($id, $updateData);

            // Lấy lại bài viết sau khi cập nhật
            $updatedPost = $this->postModel->getById($id);

            return [
                'success' => true,
                'message' => 'Cập nhật bài viết thành công',
                'data' => $updatedPost
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi khi cập nhật: ' . $e->getMessage()];
        }
    }


    /**
     * Xóa bài viết
     */
    public function deletePost($id)
    {
        try {
            $this->postModel->delete($id);
            return ['success' => true, 'message' => 'Xoá bài viết thành công'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Lỗi khi xoá bài viết: ' . $e->getMessage()];
        }
    }
}
