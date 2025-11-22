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
     * Lấy danh sách bài viết (có tìm kiếm, phân trang, filter status & slug)
     */
    public function getPosts($page = 1, $limit = 10, $search = null, $status = null, $slug = null)
    {
        try {
            $offset = ($page - 1) * $limit;
            $data = $this->postModel->getAll($limit, $offset, $search, $status, $slug);

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

            $title = trim($data['title']);
            if ($this->postModel->existsByTitle($title)) {
                return ['success' => false, 'message' => 'Tiêu đề đã tồn tại'];
            }

            // Tạo slug (nếu chưa có)
            $slug = $data['slug'] ?? null;
            if (!$slug) {
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
            }
            if ($slug && $this->postModel->existsBySlug($slug)) {
                // tránh slug trùng, thêm hậu tố thời gian
                $slug .= '-' . time();
            }

            $payload = [
                'title' => $title,
                'slug' => $slug,
                'content' => $data['content'],
                'excerpt' => $data['excerpt'] ?? null,
                'image' => $data['image'] ?? null,
                'author_id' => $data['author_id'] ?? null,
                'status' => $data['status'] ?? 'draft',
                'published_at' => $data['published_at'] ?? null,
            ];

            $postId = $this->postModel->create($payload);
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
            $titleChanged = isset($data['title']) && trim($data['title']) !== '' && trim($data['title']) !== $existingPost['title'];
            $slugChanged = isset($data['slug']) && trim($data['slug']) !== '' && trim($data['slug']) !== ($existingPost['slug'] ?? '');

            if ($titleChanged && $this->postModel->existsByTitle(trim($data['title']), $id)) {
                return ['success' => false, 'message' => 'Tiêu đề đã tồn tại'];
            }

            $slugToUse = $existingPost['slug'] ?? null;

            if ($slugChanged) {
                $slugCandidate = trim($data['slug']);
                if ($slugCandidate === '') {
                    $slugToUse = null;
                } else {
                    if ($this->postModel->existsBySlug($slugCandidate, $id)) {
                        return ['success' => false, 'message' => 'Slug đã tồn tại'];
                    }
                    $slugToUse = $slugCandidate;
                }
            } elseif ($titleChanged && !$slugToUse) {
                $generated = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', trim($data['title']))));
                if ($generated && $this->postModel->existsBySlug($generated, $id)) {
                    $generated .= '-' . time();
                }
                $slugToUse = $generated;
            }

            $updateData = [
                'title'   => isset($data['title']) && trim($data['title']) !== ''
                    ? trim($data['title'])
                    : $existingPost['title'],
                'slug'    => $slugToUse,
                'content' => isset($data['content']) && trim($data['content']) !== ''
                    ? trim($data['content'])
                    : $existingPost['content'],
                'excerpt' => array_key_exists('excerpt', $data) && trim((string)$data['excerpt']) !== ''
                    ? trim($data['excerpt'])
                    : $existingPost['excerpt'],
                'image'   => isset($data['image']) && trim($data['image']) !== ''
                    ? trim($data['image'])
                    : $existingPost['image'],
                'author_id' => isset($data['author_id']) && !empty($data['author_id'])
                    ? (int)$data['author_id']
                    : $existingPost['author_id'],
                'status' => isset($data['status']) && trim($data['status']) !== ''
                    ? trim($data['status'])
                    : ($existingPost['status'] ?? 'draft'),
                'published_at' => array_key_exists('published_at', $data)
                    ? $data['published_at']
                    : $existingPost['published_at']
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
