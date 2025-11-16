<?php
// Page model - quản lý nội dung trang (home/about) trong bảng page_contents

class Page
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Lấy bản ghi mới nhất trong page_contents.
     * Nếu chưa có row nào, trả về null.
     */
    public function getLatestPageContents()
    {
        $stmt = $this->pdo->query("SELECT * FROM page_contents ORDER BY id DESC LIMIT 1");
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Tạo một bản ghi rỗng mặc định trong page_contents.
     */
    public function createEmptyPageContents()
    {
        $sql = "
            INSERT INTO page_contents (
                home_hero_title,
                home_hero_subtitle,
                home_hero_button_text,
                home_hero_button_link,
                home_hero_image,
                home_intro_title,
                home_intro_text,
                about_title,
                about_content,
                about_image
            ) VALUES ('','','','','','','','','','')
        ";
        $this->pdo->exec($sql);
    }

    /**
     * Đảm bảo trong bảng page_contents luôn có ít nhất 1 row.
     */
    public function ensurePageContentsExists()
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) AS total FROM page_contents");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ((int)$row['total'] === 0) {
            $this->createEmptyPageContents();
        }
    }

    /**
     * Cập nhật nội dung bản ghi mới nhất.
     * $data là array có key trùng tên cột (home_hero_title, about_content, ...)
     */
    public function updateLatestPageContents(array $data)
    {
        // đảm bảo có ít nhất 1 row
        $this->ensurePageContentsExists();

        $stmt = $this->pdo->query("SELECT id FROM page_contents ORDER BY id DESC LIMIT 1");
        $current = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$current) {
            return; // rất hiếm khi tới đây vì ensurePageContentsExists() đã tạo row
        }

        $fields = [
            'home_hero_title',
            'home_hero_subtitle',
            'home_hero_button_text',
            'home_hero_button_link',
            'home_hero_image',
            'home_intro_title',
            'home_intro_text',
            'about_title',
            'about_content',
            'about_image',
        ];

        $setParts = [];
        $params = [];

        foreach ($fields as $field) {
            $setParts[] = "$field = :$field";
            $params[":$field"] = $data[$field] ?? '';
        }

        $setParts[] = "updated_at = NOW()";
        $params[':id'] = $current['id'];

        $sql = "UPDATE page_contents SET " . implode(", ", $setParts) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }
}
