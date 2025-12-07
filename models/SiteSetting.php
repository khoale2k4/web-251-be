<?php

/**
 * SiteSetting Model - Database operations cho site settings
 */
class SiteSetting
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Lấy settings mới nhất
     */
    public function getLatest()
    {
        $stmt = $this->pdo->query("SELECT * FROM site_settings ORDER BY id DESC LIMIT 1");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy settings theo ID
     */
    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM site_settings WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Kiểm tra settings có tồn tại không
     */
    public function exists()
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM site_settings");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    /**
     * Tạo settings mặc định
     */
    public function createDefault()
    {
        $sql = "INSERT INTO site_settings (
                    site_name, site_title, site_description, site_keywords,
                    email, phone, address, copyright
                ) VALUES (
                    'Shoe Store',
                    'Cửa hàng giày dép chất lượng cao',
                    'Chuyên cung cấp các loại giày thể thao, giày da, boots và sandals chính hãng',
                    'giày, giày thể thao, sneakers, boots',
                    'contact@shoestore.vn',
                    '0123-456-789',
                    '123 Nguyễn Huệ, Quận 1, TP.HCM',
                    '© 2025 Shoe Store. All rights reserved.'
                )";
        $this->pdo->exec($sql);
        return $this->pdo->lastInsertId();
    }

    /**
     * Cập nhật settings
     */
    public function update($id, $data)
    {
        $allowedFields = [
            'site_name', 'site_title', 'site_description', 'site_keywords',
            'logo', 'favicon', 'email', 'phone', 'address',
            'facebook', 'instagram', 'youtube',
            'about_us', 'working_hours', 'copyright'
        ];

        $updateFields = [];
        $params = [':id' => $id];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updateFields[] = "$field = :$field";
                $params[":$field"] = htmlspecialchars(trim($data[$field]));
            }
        }

        if (empty($updateFields)) {
            return false;
        }

        $sql = "UPDATE site_settings SET " . implode(", ", $updateFields) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Cập nhật một field (dùng cho logo/favicon)
     */
    public function updateField($id, $field, $value)
    {
        $allowedFields = ['logo', 'favicon'];
        
        if (!in_array($field, $allowedFields)) {
            return false;
        }

        $sql = "UPDATE site_settings SET $field = :value WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':value' => $value, ':id' => $id]);
    }
}
