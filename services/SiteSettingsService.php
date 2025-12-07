<?php

require_once __DIR__ . '/../models/SiteSetting.php';

/**
 * SiteSettingsService - Business logic và validation cho site settings
 */
class SiteSettingsService
{
    private $siteSettingModel;
    private $uploadDir;

    public function __construct($pdo)
    {
        $this->siteSettingModel = new SiteSetting($pdo);
        $this->uploadDir = __DIR__ . '/../storage/';
    }

    /**
     * Lấy thông tin settings
     * Tự động tạo default nếu chưa có
     */
    public function getSettings()
    {
        try {
            $settings = $this->siteSettingModel->getLatest();

            if (!$settings) {
                // Nếu chưa có, tạo mặc định
                $this->siteSettingModel->createDefault();
                $settings = $this->siteSettingModel->getLatest();
            }

            return [
                'success' => true,
                'data' => $settings
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể lấy thông tin settings: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Cập nhật settings
     */
    public function updateSettings($data)
    {
        try {
            // Validation
            $errors = $this->validateSettings($data);
            
            if (!empty($errors)) {
                return [
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $errors
                ];
            }

            // Lấy settings hiện tại
            $current = $this->siteSettingModel->getLatest();

            if (!$current) {
                return [
                    'success' => false,
                    'message' => 'Settings not found. Please initialize first.'
                ];
            }

            // Cập nhật
            $result = $this->siteSettingModel->update($current['id'], $data);

            if ($result === false) {
                return [
                    'success' => false,
                    'message' => 'No valid fields to update'
                ];
            }

            return [
                'success' => true,
                'message' => 'Đã cập nhật thông tin trang web'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể cập nhật settings: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Validate settings data
     */
    private function validateSettings($data)
    {
        $errors = [];

        // Validate email
        if (isset($data['email']) && !empty($data['email'])) {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Email không hợp lệ';
            }
        }

        // Validate phone
        if (isset($data['phone']) && !empty($data['phone'])) {
            $phone = preg_replace('/[^0-9\-\s]/', '', $data['phone']);
            if (strlen($phone) < 10) {
                $errors['phone'] = 'Số điện thoại không hợp lệ';
            }
        }

        return $errors;
    }
}
