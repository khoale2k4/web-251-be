<?php
// AboutSection model - thao tác với bảng about_sections (nội dung trang Giới thiệu)

class AboutSection
{
    /** @var PDO */
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Lấy toàn bộ section cho trang Giới thiệu
     */
    public function getAll()
    {
        $sql = "SELECT id, title, subtitle, description, image_url, sort_order, created_at, updated_at
                FROM about_sections
                ORDER BY sort_order ASC, id ASC";

        $stmt = $this->pdo->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        foreach ($rows as &$row) {
            // alias để FE dùng chung với cấu trúc cũ
            if (!isset($row['content'])) {
                $row['content'] = $row['description'];
            }
            // alias sort_order -> display_order để FE hiển thị
            if (!isset($row['display_order'])) {
                $row['display_order'] = $row['sort_order'];
            }
            // nếu chưa có is_active trong DB thì coi như luôn hiển thị
            if (!isset($row['is_active'])) {
                $row['is_active'] = 1;
            }
        }

        return $rows;
    }

    /**
     * Lấy 1 section theo id.
     */
    public function getById($id)
    {
        $sql = "SELECT id, title, subtitle, description, image_url, sort_order, created_at, updated_at
                FROM about_sections
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        if (!isset($row['content'])) {
            $row['content'] = $row['description'];
        }
        if (!isset($row['display_order'])) {
            $row['display_order'] = $row['sort_order'];
        }
        if (!isset($row['is_active'])) {
            $row['is_active'] = 1;
        }

        return $row;
    }

    /**
     * Tạo mới section, trả về id.
     */
    public function create($data)
    {
        $sql = "INSERT INTO about_sections (title, subtitle, description, image_url, sort_order, created_at, updated_at)
                VALUES (:title, :subtitle, :description, :image_url, :sort_order, NOW(), NOW())";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':title'       => $data['title'],
            ':subtitle'    => isset($data['subtitle']) ? $data['subtitle'] : null,
            ':description' => isset($data['description']) ? $data['description'] : '',
            ':image_url'   => isset($data['image_url']) ? $data['image_url'] : null,
            ':sort_order'  => isset($data['sort_order']) ? (int)$data['sort_order'] : 0,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Cập nhật section.
     */
    public function update($id, $data)
    {
        $sql = "UPDATE about_sections
                SET title = :title,
                    subtitle = :subtitle,
                    description = :description,
                    image_url = :image_url,
                    sort_order = :sort_order,
                    updated_at = NOW()
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id'          => $id,
            ':title'       => $data['title'],
            ':subtitle'    => isset($data['subtitle']) ? $data['subtitle'] : null,
            ':description' => isset($data['description']) ? $data['description'] : '',
            ':image_url'   => isset($data['image_url']) ? $data['image_url'] : null,
            ':sort_order'  => isset($data['sort_order']) ? (int)$data['sort_order'] : 0,
        ]);
    }

    /**
     * Xoá section.
     */
    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM about_sections WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
