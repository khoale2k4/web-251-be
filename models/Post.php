<?php

class Post
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Lấy tất cả bài viết (hỗ trợ tìm kiếm theo title, phân trang, filter status, slug)
     */
    public function getAll($limit = 20, $offset = 0, $search = null, $status = null, $slug = null)
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

        // Filter by search
        if (!empty($search)) {
            $sql .= " AND p.title LIKE ?";
            $params[] = "%$search%";
        }

        // Filter by status
        if (!empty($status)) {
            $sql .= " AND p.status = ?";
            $params[] = $status;
        }

        // Filter by slug
        if (!empty($slug)) {
            $sql .= " AND p.slug = ?";
            $params[] = $slug;
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
            INSERT INTO posts (title, slug, content, excerpt, image, author_id, status, published_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['title'],
            $data['slug'] ?? null,
            $data['content'] ?? null,
            $data['excerpt'] ?? null,
            $data['image'] ?? null,
            $data['author_id'] ?? null,
            $data['status'] ?? 'draft',
            $data['published_at'] ?? null
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
            SET title = ?, slug = ?, content = ?, excerpt = ?, image = ?, author_id = ?, status = ?, published_at = ?, updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['title'],
            $data['slug'] ?? null,
            $data['content'] ?? null,
            $data['excerpt'] ?? null,
            $data['image'] ?? null,
            $data['author_id'] ?? null,
            $data['status'] ?? 'draft',
            $data['published_at'] ?? null,
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

    public function existsByTitle($title, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) AS total FROM posts WHERE LOWER(title) = LOWER(?)";
        $params = [$title];
        if ($excludeId !== null) {
            $sql .= " AND id <> ?";
            $params[] = $excludeId;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($result['total'] ?? 0) > 0;
    }

    public function existsBySlug($slug, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) AS total FROM posts WHERE slug = ?";
        $params = [$slug];
        if ($excludeId !== null) {
            $sql .= " AND id <> ?";
            $params[] = $excludeId;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($result['total'] ?? 0) > 0;
    }
}
