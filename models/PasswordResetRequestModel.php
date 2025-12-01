<?php
class PasswordResetRequestModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Tạo yêu cầu reset password mới
     */
    public function create($userId, $reason = null)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO password_reset_requests (user_id, reason, created_at)
            VALUES (?, ?, NOW())
        ");
        $stmt->execute([$userId, $reason]);
        return $this->pdo->lastInsertId();
    }

    /**
     * Lấy tất cả yêu cầu (cho admin)
     */
    public function getAll($status = null)
    {
        $sql = "
            SELECT 
                prr.*,
                u.name as user_name,
                u.email as user_email,
                admin.name as admin_name
            FROM password_reset_requests prr
            INNER JOIN users u ON prr.user_id = u.id
            LEFT JOIN users admin ON prr.admin_id = admin.id
        ";
        
        if ($status) {
            $sql .= " WHERE prr.status = ?";
            $stmt = $this->pdo->prepare($sql . " ORDER BY prr.created_at DESC");
            $stmt->execute([$status]);
        } else {
            $stmt = $this->pdo->prepare($sql . " ORDER BY prr.created_at DESC");
            $stmt->execute();
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy yêu cầu theo ID
     */
    public function getById($id)
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                prr.*,
                u.name as user_name,
                u.email as user_email
            FROM password_reset_requests prr
            INNER JOIN users u ON prr.user_id = u.id
            WHERE prr.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy yêu cầu của user
     */
    public function getByUserId($userId)
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM password_reset_requests
            WHERE user_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Kiểm tra user có yêu cầu pending không
     */
    public function hasPendingRequest($userId)
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as count
            FROM password_reset_requests
            WHERE user_id = ? AND status = 'pending'
        ");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    /**
     * Duyệt yêu cầu (approve)
     */
    public function approve($requestId, $adminId, $adminNote = null)
    {
        $stmt = $this->pdo->prepare("
            UPDATE password_reset_requests
            SET status = 'approved',
                admin_id = ?,
                admin_note = ?,
                processed_at = NOW()
            WHERE id = ? AND status = 'pending'
        ");
        return $stmt->execute([$adminId, $adminNote, $requestId]);
    }

    /**
     * Từ chối yêu cầu (reject)
     */
    public function reject($requestId, $adminId, $adminNote = null)
    {
        $stmt = $this->pdo->prepare("
            UPDATE password_reset_requests
            SET status = 'rejected',
                admin_id = ?,
                admin_note = ?,
                processed_at = NOW()
            WHERE id = ? AND status = 'pending'
        ");
        return $stmt->execute([$adminId, $adminNote, $requestId]);
    }
}
