<?php
require_once __DIR__ . '/../models/PasswordResetRequestModel.php';
require_once __DIR__ . '/../models/UserModel.php';

class PasswordResetService
{
    private $requestModel;
    private $userModel;
    private $pdo;
    const DEFAULT_PASSWORD = '12345678';

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->requestModel = new PasswordResetRequestModel($pdo);
        $this->userModel = new UserModel($pdo);
    }

    /**
     * Tạo yêu cầu reset password
     */
    public function createResetRequest($email, $reason = null)
    {
        if (empty($email)) {
            throw new Exception("Email is required");
        }

        // Tìm user
        $user = $this->userModel->getByEmail($email);
        
        if (!$user) {
            // Không tiết lộ email không tồn tại
            return [
                'success' => true,
                'message' => 'Yêu cầu của bạn đã được gửi. Vui lòng chờ admin xử lý.'
            ];
        }

        // Kiểm tra có yêu cầu pending không
        if ($this->requestModel->hasPendingRequest($user['id'])) {
            throw new Exception("Bạn đã có yêu cầu đang chờ xử lý. Vui lòng đợi admin phản hồi.");
        }

        // Tạo yêu cầu mới
        $requestId = $this->requestModel->create($user['id'], $reason);

        return [
            'success' => true,
            'message' => 'Yêu cầu reset password đã được gửi. Admin sẽ xem xét và phản hồi sớm.',
            'request_id' => $requestId
        ];
    }

    /**
     * Lấy tất cả yêu cầu (cho admin)
     */
    public function getAllRequests($status = null)
    {
        return $this->requestModel->getAll($status);
    }

    /**
     * Lấy yêu cầu của user
     */
    public function getUserRequests($userId)
    {
        return $this->requestModel->getByUserId($userId);
    }

    /**
     * Duyệt yêu cầu (admin)
     */
    public function approveRequest($requestId, $adminId, $adminNote = null)
    {
        // Lấy thông tin request
        $request = $this->requestModel->getById($requestId);
        
        if (!$request) {
            throw new Exception("Yêu cầu không tồn tại");
        }

        if ($request['status'] !== 'pending') {
            throw new Exception("Yêu cầu đã được xử lý");
        }

        // Bắt đầu transaction
        $this->pdo->beginTransaction();

        try {
            // Approve request
            $this->requestModel->approve($requestId, $adminId, $adminNote);

            // Reset password về default
            $defaultPasswordHash = password_hash(self::DEFAULT_PASSWORD, PASSWORD_BCRYPT);
            $this->userModel->resetToDefaultPassword($request['user_id'], $defaultPasswordHash);

            $this->pdo->commit();

            return [
                'success' => true,
                'message' => "Yêu cầu đã được duyệt. Mật khẩu của user đã được reset về: " . self::DEFAULT_PASSWORD
            ];

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Từ chối yêu cầu (admin)
     */
    public function rejectRequest($requestId, $adminId, $adminNote = null)
    {
        $request = $this->requestModel->getById($requestId);
        
        if (!$request) {
            throw new Exception("Yêu cầu không tồn tại");
        }

        if ($request['status'] !== 'pending') {
            throw new Exception("Yêu cầu đã được xử lý");
        }

        $this->requestModel->reject($requestId, $adminId, $adminNote);

        return [
            'success' => true,
            'message' => 'Yêu cầu đã bị từ chối'
        ];
    }

    /**
     * Đổi mật khẩu (sau khi user login với default password)
     */
    public function changePassword($userId, $currentPassword, $newPassword)
    {
        if (empty($newPassword)) {
            throw new Exception("Mật khẩu mới không được để trống");
        }

        if (strlen($newPassword) < 6) {
            throw new Exception("Mật khẩu phải có ít nhất 6 ký tự");
        }

        // Lấy user
        $user = $this->userModel->getById($userId);
        
        if (!$user) {
            throw new Exception("User không tồn tại");
        }

        // Verify current password
        $fullUser = $this->userModel->getByEmail($user['email']);
        if (!password_verify($currentPassword, $fullUser['password'])) {
            throw new Exception("Mật khẩu hiện tại không đúng");
        }

        // Không cho đổi trùng mật khẩu cũ
        if (password_verify($newPassword, $fullUser['password'])) {
            throw new Exception("Mật khẩu mới không được trùng với mật khẩu hiện tại");
        }

        // Update password
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $this->userModel->updatePassword($userId, $hashedPassword);

        return [
            'success' => true,
            'message' => 'Mật khẩu đã được thay đổi thành công'
        ];
    }
}
