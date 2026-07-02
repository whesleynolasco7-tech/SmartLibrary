<?php
/**
 * Book class
 * Encapsulates all book-catalog related database operations.
 */
class Book
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll(int $limit = 0, int $offset = 0): array
    {
        $sql = 'SELECT b.*, c.name AS category_name
                FROM books b
                LEFT JOIN categories c ON b.category_id = c.id
                ORDER BY b.created_at DESC';
        if ($limit > 0) {
            $sql .= ' LIMIT :limit OFFSET :offset';
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM books')->fetchColumn();
    }

    public function countAvailable(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM books WHERE available_copies > 0')->fetchColumn();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT b.*, c.name AS category_name
             FROM books b LEFT JOIN categories c ON b.category_id = c.id
             WHERE b.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO books
            (isbn, title, author, publisher, category_id, description, year_published,
             language, total_copies, available_copies, cover_image, tags, created_at)
            VALUES
            (:isbn, :title, :author, :publisher, :category_id, :description, :year_published,
             :language, :total_copies, :available_copies, :cover_image, :tags, NOW())';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'isbn'            => $data['isbn'] ?? null,
            'title'           => $data['title'],
            'author'          => $data['author'],
            'publisher'       => $data['publisher'] ?? null,
            'category_id'     => $data['category_id'] ?: null,
            'description'     => $data['description'] ?? null,
            'year_published'  => $data['year_published'] ?: null,
            'language'        => $data['language'] ?? 'English',
            'total_copies'    => $data['total_copies'] ?? 1,
            'available_copies'=> $data['available_copies'] ?? ($data['total_copies'] ?? 1),
            'cover_image'     => $data['cover_image'] ?? null,
            'tags'            => $data['tags'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = 'UPDATE books SET
            isbn = :isbn, title = :title, author = :author, publisher = :publisher,
            category_id = :category_id, description = :description, year_published = :year_published,
            language = :language, total_copies = :total_copies';
        $params = [
            'id'              => $id,
            'isbn'            => $data['isbn'] ?? null,
            'title'           => $data['title'],
            'author'          => $data['author'],
            'publisher'       => $data['publisher'] ?? null,
            'category_id'     => $data['category_id'] ?: null,
            'description'     => $data['description'] ?? null,
            'year_published'  => $data['year_published'] ?: null,
            'language'        => $data['language'] ?? 'English',
            'total_copies'    => $data['total_copies'] ?? 1,
        ];
        if (!empty($data['cover_image'])) {
            $sql .= ', cover_image = :cover_image';
            $params['cover_image'] = $data['cover_image'];
        }
        $sql .= ' WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM books WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    /** Live search across title, author, category, ISBN, publisher */
    public function search(string $term, int $limit = 30): array
    {
        $like = '%' . $term . '%';
        $sql = 'SELECT b.*, c.name AS category_name
                FROM books b LEFT JOIN categories c ON b.category_id = c.id
                WHERE b.title LIKE :t1 OR b.author LIKE :t2 OR b.isbn LIKE :t3
                   OR b.publisher LIKE :t4 OR c.name LIKE :t5
                ORDER BY b.title ASC
                LIMIT :limit';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':t1', $like);
        $stmt->bindValue(':t2', $like);
        $stmt->bindValue(':t3', $like);
        $stmt->bindValue(':t4', $like);
        $stmt->bindValue(':t5', $like);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function decrementAvailable(int $id): bool
    {
        $stmt = $this->db->prepare('UPDATE books SET available_copies = available_copies - 1 WHERE id = :id AND available_copies > 0');
        return $stmt->execute(['id' => $id]);
    }

    public function incrementAvailable(int $id): bool
    {
        $stmt = $this->db->prepare('UPDATE books SET available_copies = LEAST(available_copies + 1, total_copies) WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function getCategories(): array
    {
        return $this->db->query('SELECT * FROM categories ORDER BY name ASC')->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPopular(int $limit = 6): array
    {
        $sql = 'SELECT b.*, COUNT(r.id) AS borrow_count
                FROM books b
                LEFT JOIN borrowing_records r ON r.book_id = b.id
                GROUP BY b.id
                ORDER BY borrow_count DESC, b.created_at DESC
                LIMIT :limit';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTrendingCategories(int $limit = 5): array
    {
        $sql = 'SELECT c.name, COUNT(r.id) AS total
                FROM borrowing_records r
                JOIN books b ON b.id = r.book_id
                JOIN categories c ON c.id = b.category_id
                GROUP BY c.id
                ORDER BY total DESC
                LIMIT :limit';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}