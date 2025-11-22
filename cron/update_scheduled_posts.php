#!/usr/bin/env php
<?php
/**
 * Cron Job: Cập nhật scheduled posts
 * 
 * Chạy script này mỗi phút hoặc mỗi 5 phút để tự động
 * cập nhật status từ "scheduled" sang "published"
 * 
 * Cách chạy:
 * 1. Manual: php update_scheduled_posts.php
 * 2. Cron (Linux): * * * * * php /path/to/update_scheduled_posts.php
 * 3. Task Scheduler (Windows): Tạo task chạy mỗi 5 phút
 * 4. HTTP Request: GET http://localhost:8000/scheduler/update-scheduled
 */

// Load database config
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../services/PostSchedulerService.php';

try {
    $pdo = getPDO();
    $scheduler = new PostSchedulerService($pdo);
    
    echo "=== Post Scheduler Started ===\n";
    echo "Time: " . date('Y-m-d H:i:s') . "\n\n";
    
    // Cập nhật scheduled posts
    $result = $scheduler->updateScheduledPosts();
    
    if ($result['success']) {
        echo "✓ Success: {$result['message']}\n";
        
        if ($result['updated'] > 0) {
            echo "\nUpdated posts:\n";
            foreach ($result['posts'] as $post) {
                echo "  - ID: {$post['id']} | {$post['title']} | Published at: {$post['published_at']}\n";
            }
        }
    } else {
        echo "✗ Error: {$result['message']}\n";
    }
    
    echo "\n=== Scheduler Finished ===\n";
    
} catch (Exception $e) {
    echo "✗ Fatal Error: " . $e->getMessage() . "\n";
    exit(1);
}
