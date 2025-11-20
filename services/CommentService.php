<?php
require_once __DIR__ . '/../models/Comment.php';

class CommentService {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Lấy tất cả bình luận từ mới nhất đến cũ nhất (hoặc lọc theo type và filterId)
    public function getAll($type = null, $filterId = null) {
        // Query lấy tất cả bình luận với thông tin user, product và post
        $query = "SELECT 
                    c.*, 
                    u.name AS user_name, 
                    u.avatar AS user_avatar, 
                    pr.name AS product_name,
                    ps.title AS post_title
                  FROM comments c
                  LEFT JOIN users u ON c.user_id = u.id
                  LEFT JOIN products pr ON c.product_id = pr.id
                  LEFT JOIN posts ps ON c.post_id = ps.id
                  WHERE 1=1";
        
        // Lọc theo type nếu có
        if ($type === 'product') {
            $query .= " AND c.comment_type = 'product'";
            if ($filterId) {
                $query .= " AND c.product_id = :filterId";
            }
        } elseif ($type === 'post') {
            $query .= " AND c.comment_type = 'post'";
            if ($filterId) {
                $query .= " AND c.post_id = :filterId";
            }
        }
        // Nếu $type = null hoặc rỗng, lấy tất cả không lọc theo type
        
        // Sắp xếp từ mới nhất đến cũ nhất
        $query .= " ORDER BY c.created_at DESC";

        $stmt = $this->pdo->prepare($query);
        if ($filterId && ($type === 'product' || $type === 'post')) {
            $stmt->bindValue(':filterId', $filterId, PDO::PARAM_INT);
        }
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM comments WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getDetailedById($id) {
        $stmt = $this->pdo->prepare("
            SELECT 
                c.*,
                u.name AS user_name,
                ps.title AS post_title,
                pr.name AS product_name
            FROM comments c
            LEFT JOIN users u ON c.user_id = u.id
            LEFT JOIN posts ps ON c.post_id = ps.id
            LEFT JOIN products pr ON c.product_id = pr.id
            WHERE c.id = ?
            LIMIT 1
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Tạo bình luận mới
    public function create($data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO comments (user_id, comment_type, product_id, post_id, content, rating, created_at)
            VALUES (:user_id, :comment_type, :product_id, :post_id, :content, :rating, NOW())
        ");
        $stmt->execute([
            ':user_id' => $data['user_id'],
            ':comment_type' => $data['comment_type'],
            ':product_id' => $data['product_id'] ?? null,
            ':post_id' => $data['post_id'] ?? null,
            ':content' => $data['content'],
            ':rating' => $data['rating'] ?? null,
        ]);
        return $this->pdo->lastInsertId();
    }

    // Xóa bình luận
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM comments WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
