<?php

require_once __DIR__ . '/../services/SiteSettingsService.php';

/**
 * SiteSettingsController - Quản lý thông tin cấu hình trang web
 */
class SiteSettingsController
{
    private $siteSettingsService;

    public function __construct($pdo)
    {
        $this->siteSettingsService = new SiteSettingsService($pdo);
    }

    public function handleRequest($request)
    {
        $method = $_SERVER['REQUEST_METHOD'];
        
        // GET /site-settings - Lấy thông tin settings
        if ($request === '/site-settings' && $method === 'GET') {
            $this->get();
            return;
        }

        // PUT /site-settings - Cập nhật settings (admin only)
        if ($request === '/site-settings' && $method === 'PUT') {
            $this->update();
            return;
        }

        // POST /site-settings/upload - Upload logo/favicon (admin only)
        if ($request === '/site-settings/upload' && $method === 'POST') {
            $this->uploadImage();
            return;
        }

        http_response_code(404);
        echo json_encode(["error" => "Site settings endpoint not found"]);
    }

    /**
     * GET /site-settings - Lấy thông tin settings
     */
    private function get()
    {
        $result = $this->siteSettingsService->getSettings();

        if ($result['success']) {
            http_response_code(200);
        } else {
            http_response_code(500);
        }

        echo json_encode($result);
    }

    /**
     * PUT /site-settings - Cập nhật settings
     */
    private function update()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $result = $this->siteSettingsService->updateSettings($data);

        if ($result['success']) {
            http_response_code(200);
        } else {
            if (isset($result['errors'])) {
                http_response_code(400);
            } else {
                http_response_code(500);
            }
        }

        echo json_encode($result);
    }
}
