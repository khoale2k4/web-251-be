<?php
// AboutSectionService - nghiệp vụ cho trang Giới thiệu

require_once __DIR__ . '/../models/AboutSection.php';

class AboutSectionService
{
    private $model;

    public function __construct($pdo)
    {
        $this->model = new AboutSection($pdo);
    }

    /**
     * Lấy dữ liệu hiển thị public cho trang About.
     */
    public function getPublicSections()
    {
        try {
            $sections = $this->model->getAll();

            return [
                'status' => 'success',
                'data'   => $sections,
            ];
        } catch (Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Không thể tải nội dung trang giới thiệu',
                'error'   => $e->getMessage(),
            ];
        }
    }

    /**
     * Lấy danh sách cho màn hình admin.
     * Hiện tại giống public, tách riêng để sau này dễ mở rộng filter.
     */
    public function getAdminSections()
    {
        return $this->getPublicSections();
    }

    /**
     * Tạo mới 1 section.
     */
    public function createSection($payload)
    {
        try {
            $title = isset($payload['title']) ? trim($payload['title']) : '';
            if ($title === '') {
                throw new Exception('Tiêu đề không được để trống');
            }

            $data = [
                'title'       => $title,
                'subtitle'    => isset($payload['subtitle']) ? trim($payload['subtitle']) : null,
                'description' => isset($payload['description']) ? trim($payload['description']) : '',
                'image_url'   => isset($payload['image_url']) ? trim($payload['image_url']) : null,
                'sort_order'  => isset($payload['sort_order']) ? (int)$payload['sort_order'] : 0,
            ];

            $id   = $this->model->create($data);
            $item = $this->model->getById($id);

            return [
                'status'  => 'success',
                'message' => 'Tạo section thành công',
                'data'    => $item,
            ];
        } catch (Exception $e) {
            return [
                'status'  => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Cập nhật 1 section.
     */
    public function updateSection($id, $payload)
    {
        try {
            $id = (int)$id;
            if ($id <= 0) {
                throw new Exception('ID không hợp lệ');
            }

            $title = isset($payload['title']) ? trim($payload['title']) : '';
            if ($title === '') {
                throw new Exception('Tiêu đề không được để trống');
            }

            $data = [
                'title'       => $title,
                'subtitle'    => isset($payload['subtitle']) ? trim($payload['subtitle']) : null,
                'description' => isset($payload['description']) ? trim($payload['description']) : '',
                'image_url'   => isset($payload['image_url']) ? trim($payload['image_url']) : null,
                'sort_order'  => isset($payload['sort_order']) ? (int)$payload['sort_order'] : 0,
            ];

            $ok = $this->model->update($id, $data);
            if (!$ok) {
                throw new Exception('Không tìm thấy section để cập nhật');
            }

            $item = $this->model->getById($id);

            return [
                'status'  => 'success',
                'message' => 'Cập nhật section thành công',
                'data'    => $item,
            ];
        } catch (Exception $e) {
            return [
                'status'  => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Xoá 1 section.
     */
    public function deleteSection($id)
    {
        try {
            $id = (int)$id;
            if ($id <= 0) {
                throw new Exception('ID không hợp lệ');
            }

            $ok = $this->model->delete($id);
            if (!$ok) {
                throw new Exception('Không tìm thấy section để xoá');
            }

            return [
                'status'  => 'success',
                'message' => 'Xoá section thành công',
            ];
        } catch (Exception $e) {
            return [
                'status'  => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }
}
