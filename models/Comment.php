<?php

class Comment {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Lấy danh sách bình luận theo post_id
     */
    public function getByPostId($postId) {
        $sql = "
            SELECT 
                c.*, 
                u.name AS user_name, 
                u.avatar AS user_avatar 
            FROM comments c
            LEFT JOIN users u ON c.user_id = u.id
            WHERE c.post_id = ? AND c.comment_type = 'post'
            ORDER BY c.created_at DESC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$postId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Thêm bình luận cho bài viết
     */
    public function create($data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO comments (user_id, post_id, comment_type, content, rating)
            VALUES (?, ?, 'post', ?, NULL)
        ");
        return $stmt->execute([
            $data['user_id'],
            $data['post_id'],
            $data['content']
        ]);
    }

    /**
     * Xoá bình luận
     */
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM comments WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

?>
