<?php
// PageService - nhóm 1, 2 (nội dung tĩnh)
// PageService - xử lý logic nội dung trang (home/about)

require_once __DIR__ . '/../models/Page.php';

class PageService
{
    private $pageModel;

    public function __construct($pdo)
    {
        $this->pageModel = new Page($pdo);
    }

    /**
     * Lấy nội dung trang (home/about).
     * Nếu chưa có, tự tạo 1 row rỗng trước rồi trả về.
     */
    public function getPageContents()
    {
        $this->pageModel->ensurePageContentsExists();
        return $this->pageModel->getLatestPageContents();
    }

    /**
     * Cập nhật nội dung trang từ input JSON.
     * Trả về row sau khi update.
     */
    public function updatePageContents(array $input)
    {
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

        $data = [];
        foreach ($fields as $field) {
            $value = isset($input[$field]) ? trim($input[$field]) : '';
            // sanitize giống trong page-contents.php cũ
            $data[$field] = htmlspecialchars(
                $value,
                ENT_QUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            );
        }

        $this->pageModel->updateLatestPageContents($data);

        // Lấy lại dữ liệu sau khi update
        return $this->pageModel->getLatestPageContents();
    }
}
