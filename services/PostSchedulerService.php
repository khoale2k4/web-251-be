<?php

/**
 * PostSchedulerService
 * Tự động cập nhật trạng thái bài viết từ "scheduled" sang "published"
 * khi đến thời gian xuất bản
 */
class PostSchedulerService
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Cập nhật tất cả bài viết scheduled đã đến giờ xuất bản
     * @return array Kết quả cập nhật
     */
    public function updateScheduledPosts()
    {
        try {
            // Tìm tất cả bài viết có status = 'scheduled' và published_at <= NOW()
            $query = "
                SELECT id, title, published_at 
                FROM posts 
                WHERE status = 'scheduled' 
                AND published_at IS NOT NULL 
                AND published_at <= NOW()
            ";
            
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($posts)) {
                return [
                    'success' => true,
                    'message' => 'Không có bài viết nào cần cập nhật',
                    'updated' => 0,
                    'posts' => []
                ];
            }

            // Cập nhật status thành 'published'
            $updateQuery = "
                UPDATE posts 
                SET status = 'published', updated_at = NOW() 
                WHERE status = 'scheduled' 
                AND published_at IS NOT NULL 
                AND published_at <= NOW()
            ";
            
            $updateStmt = $this->pdo->prepare($updateQuery);
            $updateStmt->execute();
            $updatedCount = $updateStmt->rowCount();

            return [
                'success' => true,
                'message' => "Đã cập nhật {$updatedCount} bài viết",
                'updated' => $updatedCount,
                'posts' => $posts
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Lỗi khi cập nhật: ' . $e->getMessage(),
                'updated' => 0
            ];
        }
    }

    /**
     * Lấy danh sách bài viết scheduled sắp được xuất bản
     * @param int $hours Số giờ tới (mặc định 24h)
     * @return array
     */
    public function getUpcomingScheduledPosts($hours = 24)
    {
        try {
            $query = "
                SELECT id, title, status, published_at, author_id
                FROM posts 
                WHERE status = 'scheduled' 
                AND published_at IS NOT NULL 
                AND published_at > NOW() 
                AND published_at <= DATE_ADD(NOW(), INTERVAL :hours HOUR)
                ORDER BY published_at ASC
            ";
            
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':hours', $hours, PDO::PARAM_INT);
            $stmt->execute();
            
            return [
                'success' => true,
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ];
        }
    }
}
