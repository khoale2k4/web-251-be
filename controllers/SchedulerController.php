<?php

require_once __DIR__ . '/../services/PostSchedulerService.php';

/**
 * SchedulerController
 * Xử lý các request liên quan đến scheduled posts
 */
class SchedulerController
{
    private $schedulerService;

    public function __construct($pdo)
    {
        $this->schedulerService = new PostSchedulerService($pdo);
    }

    /**
     * Xử lý request
     */
    public function handleRequest($request)
    {
        // Chỉ cho phép GET request
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            return;
        }

        // Route: /scheduler/update-scheduled
        if ($request === '/scheduler/update-scheduled') {
            $this->updateScheduledPosts();
            return;
        }

        // Route: /scheduler/upcoming
        if ($request === '/scheduler/upcoming') {
            $this->getUpcomingPosts();
            return;
        }

        // 404
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Not found']);
    }

    /**
     * Cập nhật scheduled posts đã đến giờ
     */
    private function updateScheduledPosts()
    {
        $result = $this->schedulerService->updateScheduledPosts();
        
        if ($result['success']) {
            http_response_code(200);
        } else {
            http_response_code(500);
        }
        
        echo json_encode($result);
    }

    /**
     * Lấy danh sách scheduled posts sắp tới
     */
    private function getUpcomingPosts()
    {
        $hours = isset($_GET['hours']) ? (int)$_GET['hours'] : 24;
        $result = $this->schedulerService->getUpcomingScheduledPosts($hours);
        
        if ($result['success']) {
            http_response_code(200);
        } else {
            http_response_code(500);
        }
        
        echo json_encode($result);
    }
}
