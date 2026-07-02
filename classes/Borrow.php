<?php
/**
 * Borrow class
 * Handles borrowing / returning books and keeps book availability in sync.
 */
class Borrow
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Borrow a book. Returns ['success' => bool, 'message' => string, 'id' => int|null]
     */
    public function borrowBook(int $studentId, int $bookId): array
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare('SELECT available_copies FROM books WHERE id = :id FOR UPDATE');
            $stmt->execute(['id' => $bookId]);
            $book = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$book) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Book not found.'];
            }
            if ($book['available_copies'] < 1) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'No available copies to borrow.'];
            }

            $already = $this->db->prepare(
                "SELECT id FROM borrowing_records WHERE student_id = :sid AND book_id = :bid AND status = 'borrowed'"
            );
            $already->execute(['sid' => $studentId, 'bid' => $bookId]);
            if ($already->fetch()) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'This book is already borrowed by the student.'];
            }

            $borrowDate = date('Y-m-d');
            $dueDate = date('Y-m-d', strtotime('+' . LOAN_PERIOD_DAYS . ' days'));

            $insert = $this->db->prepare(
                'INSERT INTO borrowing_records (student_id, book_id, borrow_date, due_date, status, created_at)
                 VALUES (:sid, :bid, :bdate, :ddate, "borrowed", NOW())'
            );
            $insert->execute(['sid' => $studentId, 'bid' => $bookId, 'bdate' => $borrowDate, 'ddate' => $dueDate]);
            $recordId = (int) $this->db->lastInsertId();

            $update = $this->db->prepare('UPDATE books SET available_copies = available_copies - 1 WHERE id = :id');
            $update->execute(['id' => $bookId]);

            $this->db->commit();
            return ['success' => true, 'message' => 'Book borrowed successfully.', 'id' => $recordId, 'due_date' => $dueDate];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function returnBook(int $recordId): array
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare('SELECT * FROM borrowing_records WHERE id = :id FOR UPDATE');
            $stmt->execute(['id' => $recordId]);
            $record = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$record || !in_array($record['status'], ['borrowed', 'overdue'], true)) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Invalid or already-returned record.'];
            }

            $returnDate = date('Y-m-d');
            $fine = calculateFine($record['due_date'], $returnDate);
            $fineStatus = $fine > 0 ? 'unpaid' : 'none';

            $update = $this->db->prepare(
                'UPDATE borrowing_records
                 SET status = "returned", return_date = :rdate, fine_amount = :fine, fine_status = :fstatus
                 WHERE id = :id'
            );
            $update->execute(['rdate' => $returnDate, 'fine' => $fine, 'fstatus' => $fineStatus, 'id' => $recordId]);

            $bookUpdate = $this->db->prepare(
                'UPDATE books SET available_copies = LEAST(available_copies + 1, total_copies) WHERE id = :id'
            );
            $bookUpdate->execute(['id' => $record['book_id']]);

            $this->db->commit();
            return ['success' => true, 'message' => 'Book returned successfully.', 'fine' => $fine];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function getAll(int $limit = 50): array
    {
        $sql = 'SELECT r.*, b.title, b.author, b.cover_image, u.name AS student_name, s.student_number
                FROM borrowing_records r
                JOIN books b ON b.id = r.book_id
                JOIN students s ON s.id = r.student_id
                JOIN users u ON u.id = s.user_id
                ORDER BY r.borrow_date DESC
                LIMIT :limit';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecent(int $limit = 8): array
    {
        return $this->getAll($limit);
    }

    public function countBorrowed(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM borrowing_records WHERE status = 'borrowed'")->fetchColumn();
    }

    public function countReturned(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM borrowing_records WHERE status = 'returned'")->fetchColumn();
    }

    public function countOverdue(): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM borrowing_records WHERE status = 'borrowed' AND due_date < :today");
        $stmt->execute(['today' => date('Y-m-d')]);
        return (int) $stmt->fetchColumn();
    }

    /** Refresh overdue flags — call before rendering lists */
    public function markOverdue(): void
    {
        $stmt = $this->db->prepare(
            "UPDATE borrowing_records SET status = 'overdue'
             WHERE status = 'borrowed' AND due_date < :today"
        );
        $stmt->execute(['today' => date('Y-m-d')]);
    }
}