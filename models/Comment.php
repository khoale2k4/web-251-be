<?php

class Comment
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Lấy danh sách bình luận theo post_id
     */
    public function getByPostId($postId)
    {
        $sql = "SELECT c.*, u.name AS user_name, u.avatar AS user_avatar
            FROM comments c
            LEFT JOIN users u ON c.user_id = u.id
            WHERE c.comment_type = 'post'
                AND c.post_id = ?
            ORDER BY c.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$postId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // ⚠️ đảm bảo fetchAll
    }
    /**
     * Lấy danh sách bình luận theo product_id
     */
    public function getByProductId($productId)
    {
        $sql = "SELECT c.*, u.name AS user_name, u.avatar AS user_avatar
            FROM comments c
            LEFT JOIN users u ON c.user_id = u.id
            WHERE c.comment_type = 'product'
                AND c.product_id = ?
            ORDER BY c.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Thêm bình luận cho bài viết
     */
    public function create($data)
    {
        // Support creating both 'post' and 'product' comments.
        $type = isset($data['comment_type']) && $data['comment_type'] === 'product' ? 'product' : 'post';

        if ($type === 'product') {
            $stmt = $this->pdo->prepare(
                "INSERT INTO comments (user_id, product_id, comment_type, content, rating, created_at) VALUES (?, ?, 'product', ?, ?, NOW())"
            );
            $ok = $stmt->execute([
                $data['user_id'] ?? null,
                $data['product_id'] ?? null,
                $data['content'] ?? null,
                isset($data['rating']) ? $data['rating'] : null
            ]);
            if ($ok) return $this->pdo->lastInsertId();
            return false;
        } else {
            $stmt = $this->pdo->prepare(
                "INSERT INTO comments (user_id, post_id, comment_type, content, rating, created_at) VALUES (?, ?, 'post', ?, ?, NOW())"
            );
            $ok = $stmt->execute([
                $data['user_id'] ?? null,
                $data['post_id'] ?? null,
                $data['content'] ?? null,
                isset($data['rating']) ? $data['rating'] : null
            ]);
            if ($ok) return $this->pdo->lastInsertId();
            return false;
        }
    }

    /**
     * Lấy comment theo id
     */
    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT c.*, u.name AS user_name, u.avatar AS user_avatar FROM comments c LEFT JOIN users u ON c.user_id = u.id WHERE c.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Xoá bình luận
     */
    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM comments WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
