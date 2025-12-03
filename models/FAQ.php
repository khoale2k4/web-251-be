<?php
// FAQ model - thao tác với bảng faqs

class FAQ
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Lấy danh sách FAQ với các filter đơn giản:
     *  - status: 'pending' | 'answered'
     *  - search: tìm trong question / answer
     *  - limit: số lượng bản ghi
     */
    public function getAll(array $options = []): array
    {
        $status = $options['status'] ?? null;
        $search = $options['search'] ?? null;
        $limit  = isset($options['limit']) ? (int)$options['limit'] : null;

        $sql = "SELECT id, user_id, question, answer, status, created_at
                FROM faqs
                WHERE 1 = 1";

        $params = [];

        if ($status) {
            $sql .= " AND status = :status";
            $params[':status'] = $status;
        }

        if ($search) {
            $sql .= " AND (question LIKE :search OR answer LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY created_at DESC";

        if ($limit && $limit > 0) {
            $sql .= " LIMIT :limit";
        }

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        if ($limit && $limit > 0) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy 1 FAQ theo id
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT id, user_id, question, answer, status, created_at
            FROM faqs
            WHERE id = :id
        ");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /**
     * Tạo FAQ mới
     */
    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO faqs (user_id, question, answer, status, created_at)
            VALUES (:user_id, :question, :answer, :status, NOW())
        ");

        if (isset($data['user_id']) && $data['user_id'] !== null) {
            $stmt->bindValue(':user_id', (int)$data['user_id'], PDO::PARAM_INT);
        } else {
            $stmt->bindValue(':user_id', null, PDO::PARAM_NULL);
        }

        $stmt->bindValue(':question', $data['question']);
        $stmt->bindValue(':answer', $data['answer'] ?? null);
        $stmt->bindValue(':status', $data['status'] ?? 'pending');

        $stmt->execute();
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Cập nhật FAQ theo id
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE faqs
            SET question = :question,
                answer   = :answer,
                status   = :status
            WHERE id = :id
        ");

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':question', $data['question']);
        $stmt->bindValue(':answer', $data['answer'] ?? null);
        $stmt->bindValue(':status', $data['status'] ?? 'pending');

        return $stmt->execute();
    }

    /**
     * Xoá FAQ theo id
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM faqs WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
