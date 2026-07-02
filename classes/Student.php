<?php
/**
 * Student class
 */
class Student
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll(): array
    {
        $sql = 'SELECT s.*, u.email, u.name
                FROM students s
                JOIN users u ON u.id = s.user_id
                ORDER BY u.name ASC';
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countActive(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM students WHERE status = 'active'")->fetchColumn();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT s.*, u.email, u.name
             FROM students s JOIN users u ON u.id = s.user_id
             WHERE s.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function findByUserId(int $userId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT s.*, u.email, u.name
             FROM students s JOIN users u ON u.id = s.user_id
             WHERE s.user_id = :uid'
        );
        $stmt->execute(['uid' => $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function create(int $userId, array $data): int
    {
        $sql = 'INSERT INTO students (user_id, student_number, course, year_level, contact_number, profile_picture, status, created_at)
                VALUES (:user_id, :student_number, :course, :year_level, :contact_number, :profile_picture, "active", NOW())';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id'         => $userId,
            'student_number'  => $data['student_number'],
            'course'          => $data['course'] ?? null,
            'year_level'      => $data['year_level'] ?? null,
            'contact_number'  => $data['contact_number'] ?? null,
            'profile_picture' => $data['profile_picture'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = 'UPDATE students SET course = :course, year_level = :year_level, contact_number = :contact_number';
        $params = [
            'id'             => $id,
            'course'         => $data['course'] ?? null,
            'year_level'     => $data['year_level'] ?? null,
            'contact_number' => $data['contact_number'] ?? null,
        ];
        if (!empty($data['profile_picture'])) {
            $sql .= ', profile_picture = :profile_picture';
            $params['profile_picture'] = $data['profile_picture'];
        }
        $sql .= ' WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function borrowingHistory(int $studentId): array
    {
        $sql = 'SELECT r.*, b.title, b.author, b.cover_image
                FROM borrowing_records r
                JOIN books b ON b.id = r.book_id
                WHERE r.student_id = :sid
                ORDER BY r.borrow_date DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['sid' => $studentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Determine a student's favorite categories based on borrowing frequency */
    public function favoriteCategories(int $studentId, int $limit = 5): array
    {
        $sql = 'SELECT c.id, c.name, COUNT(*) AS total
                FROM borrowing_records r
                JOIN books b ON b.id = r.book_id
                JOIN categories c ON c.id = b.category_id
                WHERE r.student_id = :sid
                GROUP BY c.id
                ORDER BY total DESC
                LIMIT :limit';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':sid', $studentId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function activeLoansCount(int $studentId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM borrowing_records WHERE student_id = :sid AND status = 'borrowed'");
        $stmt->execute(['sid' => $studentId]);
        return (int) $stmt->fetchColumn();
    }
}