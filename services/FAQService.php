<?php
// FAQService - nghiệp vụ cho FAQ (nhóm #2)

require_once __DIR__ . '/../models/FAQ.php';

class FAQService
{
    private $faqModel;

    public function __construct($pdo)
    {
        $this->faqModel = new FAQ($pdo);
    }

    /**
     * Lấy FAQ cho phía public (user)
     *  - Mặc định: chỉ lấy status = 'answered'
     *  - Hỗ trợ filter: status, search, limit
     */
    public function getPublicFaqs(array $queryParams = []): array
    {
        try {
            $filters = [];

            // Mặc định chỉ show câu đã trả lời
            $status = $queryParams['status'] ?? 'answered';
            if ($status) {
                $filters['status'] = $status;
            }

            if (!empty($queryParams['search'])) {
                $filters['search'] = trim($queryParams['search']);
            }

            if (!empty($queryParams['limit']) && is_numeric($queryParams['limit'])) {
                $filters['limit'] = (int)$queryParams['limit'];
            } else {
                $filters['limit'] = 50;
            }

            $faqs = $this->faqModel->getAll($filters);

            return [
                'success' => true,
                'data'    => $faqs,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể tải danh sách FAQ',
                'error'   => $e->getMessage(),
            ];
        }
    }

    /**
     * Lấy FAQ cho phía admin.
     *  - Mặc định: lấy tất cả (pending + answered)
     *  - Có thể filter qua query string: status, search, limit
     */
    public function getAdminFaqs(array $queryParams = []): array
    {
        try {
            $filters = [];

            // status: nếu truyền 'pending' hoặc 'answered' thì filter
            // nếu 'all' hoặc rỗng thì lấy hết
            if (!empty($queryParams['status']) && $queryParams['status'] !== 'all') {
                $filters['status'] = $queryParams['status'];
            }

            if (!empty($queryParams['search'])) {
                $filters['search'] = trim($queryParams['search']);
            }

            if (!empty($queryParams['limit']) && is_numeric($queryParams['limit'])) {
                $filters['limit'] = (int)$queryParams['limit'];
            } else {
                $filters['limit'] = 200;
            }

            $faqs = $this->faqModel->getAll($filters);

            return [
                'success' => true,
                'data'    => $faqs,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể tải danh sách FAQ (admin)',
                'error'   => $e->getMessage(),
            ];
        }
    }

    /**
     * Thêm FAQ mới (admin)
     */
    public function createFaq(array $data): array
    {
        try {
            $question = isset($data['question']) ? trim($data['question']) : '';

            if ($question === '') {
                return [
                    'success' => false,
                    'message' => 'Vui lòng nhập câu hỏi',
                ];
            }

            $payload = [
                'user_id'  => $data['user_id'] ?? null,
                'question' => $question,
                'answer'   => $data['answer'] ?? null,
                'status'   => $data['status'] ??
                    (empty($data['answer']) ? 'pending' : 'answered'),
            ];

            $id  = $this->faqModel->create($payload);
            $faq = $this->faqModel->getById($id);

            return [
                'success' => true,
                'message' => 'Thêm câu hỏi thành công',
                'data'    => $faq,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể thêm FAQ',
                'error'   => $e->getMessage(),
            ];
        }
    }

    /**
     * Cập nhật FAQ (admin)
     */
    public function updateFaq(int $id, array $data): array
    {
        try {
            $existing = $this->faqModel->getById($id);
            if (!$existing) {
                return [
                    'success' => false,
                    'message' => 'FAQ không tồn tại',
                ];
            }

            $question = isset($data['question']) ? trim($data['question']) : '';
            if ($question === '') {
                return [
                    'success' => false,
                    'message' => 'Vui lòng nhập câu hỏi',
                ];
            }

            $payload = [
                'question' => $question,
                'answer'   => $data['answer'] ?? null,
                'status'   => $data['status'] ?? $existing['status'],
            ];

            $this->faqModel->update($id, $payload);
            $faq = $this->faqModel->getById($id);

            return [
                'success' => true,
                'message' => 'Cập nhật FAQ thành công',
                'data'    => $faq,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể cập nhật FAQ',
                'error'   => $e->getMessage(),
            ];
        }
    }

    /**
     * Xoá FAQ (admin)
     */
    public function deleteFaq(int $id): array
    {
        try {
            $existing = $this->faqModel->getById($id);
            if (!$existing) {
                return [
                    'success' => false,
                    'message' => 'FAQ không tồn tại',
                ];
            }

            $this->faqModel->delete($id);

            return [
                'success' => true,
                'message' => 'Xoá FAQ thành công',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể xoá FAQ',
                'error'   => $e->getMessage(),
            ];
        }
    }
}
