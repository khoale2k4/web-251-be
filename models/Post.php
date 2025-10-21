<?php

class Post
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Lấy tất cả bài viết (hỗ trợ tìm kiếm theo title, phân trang)
     */
    public function getAll($limit = 20, $offset = 0, $search = null)
    {
        $sql = "
        SELECT 
            p.*, 
            u.name AS author_name 
        FROM posts p
        LEFT JOIN users u ON p.author_id = u.id
        WHERE 1=1
    ";

        $params = [];

        if (!empty($search)) {
            $sql .= " AND p.title LIKE ?";
            $params[] = "%$search%";
        }

        // ✅ Ép kiểu để tránh SQL injection
        $limit = (int)$limit;
        $offset = (int)$offset;
        $sql .= " ORDER BY p.created_at DESC LIMIT $limit OFFSET $offset";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Lấy chi tiết bài viết theo ID
     */
    public function getById($id)
    {
        $sql = "
            SELECT 
                p.*, 
                u.name AS author_name 
            FROM posts p
            LEFT JOIN users u ON p.author_id = u.id
            WHERE p.id = ?
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Tạo bài viết mới
     */
    public function create($data)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO posts (title, slug, content, image, author_id)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['title'],
            $data['slug'] ?? null,
            $data['content'] ?? null,
            $data['image'] ?? null,
            $data['author_id'] ?? null
        ]);

        return $this->pdo->lastInsertId();
    }

    /**
     * Cập nhật bài viết
     */
    public function update($id, $data)
    {
        $stmt = $this->pdo->prepare("
            UPDATE posts
            SET title = ?, slug = ?, content = ?, image = ?, author_id = ?, updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['title'],
            $data['slug'] ?? null,
            $data['content'] ?? null,
            $data['image'] ?? null,
            $data['author_id'] ?? null,
            $id
        ]);
    }

    /**
     * Xoá bài viết
     */
    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM posts WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
