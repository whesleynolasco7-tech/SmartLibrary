<?php
/**
 * Recommendation class
 * Implements a Content-Based Filtering recommender and a Similarity Matching
 * algorithm (weighted Jaccard-style comparison over category, author,
 * publisher, keywords/tags and description).
 */
class Recommendation
{
    private PDO $db;

    private const WEIGHTS = [
        'category'    => 0.35,
        'author'      => 0.25,
        'keywords'    => 0.25,
        'publisher'   => 0.05,
        'description' => 0.10,
    ];

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    private function tokenize(?string $text): array
    {
        if (!$text) return [];

        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\s]/', ' ', $text);

        $words = preg_split('/\s+/', trim($text));

        $stopwords = [
            'the','a','an','and','or','of','to','in',
            'is','for','on','with','by','this','that'
        ];

        return array_values(array_diff(array_filter($words), $stopwords));
    }

    private function jaccard(array $a, array $b): float
    {
        if (empty($a) && empty($b)) {
            return 0;
        }

        $intersection = count(array_intersect(array_unique($a), array_unique($b)));
        $union = count(array_unique(array_merge($a, $b)));

        return $union > 0 ? $intersection / $union : 0;
    }

    public function similarityScore(array $bookA, array $bookB): float
    {
        $score = 0;

        if (
            !empty($bookA['category_id']) &&
            $bookA['category_id'] == $bookB['category_id']
        ) {
            $score += self::WEIGHTS['category'];
        }

        $authorSim = $this->jaccard(
            $this->tokenize($bookA['author'] ?? ''),
            $this->tokenize($bookB['author'] ?? '')
        );

        $score += self::WEIGHTS['author'] * $authorSim;

        $tagSim = $this->jaccard(
            $this->tokenize($bookA['tags'] ?? ''),
            $this->tokenize($bookB['tags'] ?? '')
        );

        $score += self::WEIGHTS['keywords'] * $tagSim;

        if (
            !empty($bookA['publisher']) &&
            strcasecmp($bookA['publisher'], $bookB['publisher'] ?? '') == 0
        ) {
            $score += self::WEIGHTS['publisher'];
        }

        $descSim = $this->jaccard(
            $this->tokenize($bookA['description'] ?? ''),
            $this->tokenize($bookB['description'] ?? '')
        );

        $score += self::WEIGHTS['description'] * $descSim;

        return round($score * 100, 1);
    }

    public function getSimilarBooks(int $bookId, int $topN = 5): array
    {
        $stmt = $this->db->prepare("SELECT * FROM books WHERE id = ?");
        $stmt->execute([$bookId]);
        $target = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$target) {
            return [];
        }

        $stmt = $this->db->prepare("SELECT * FROM books WHERE id != ?");
        $stmt->execute([$bookId]);
        $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $results = [];

        foreach ($books as $book) {

            $score = $this->similarityScore($target, $book);

            if ($score > 0) {
                $book['similarity'] = $score;
                $results[] = $book;
            }
        }

        usort($results, fn($a, $b) => $b['similarity'] <=> $a['similarity']);

        return array_slice($results, 0, $topN);
    }

    public function getPersonalizedRecommendations(int $studentId, int $limit = 8): array
    {
        $stmt = $this->db->prepare("
            SELECT DISTINCT b.*
            FROM borrowing_records br
            JOIN books b ON br.book_id = b.id
            WHERE br.student_id = ?
        ");

        $stmt->execute([$studentId]);

        $history = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($history)) {
            return $this->getPopularBooks($limit);
        }

        $borrowedIds = array_column($history, 'id');

        $placeholders = implode(',', array_fill(0, count($borrowedIds), '?'));

        $sql = "SELECT * FROM books WHERE id NOT IN ($placeholders)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($borrowedIds);

        $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $recommendations = [];

        foreach ($books as $book) {

            $best = 0;

            foreach ($history as $pastBook) {

                $best = max(
                    $best,
                    $this->similarityScore($pastBook, $book)
                );
            }

            if ($best > 0) {
                $book['similarity'] = $best;
                $recommendations[] = $book;
            }
        }

        usort(
            $recommendations,
            fn($a, $b) => $b['similarity'] <=> $a['similarity']
        );

        return array_slice($recommendations, 0, $limit);
    }

    /**
     * THIS IS THE MISSING FUNCTION
     */
    public function getRecommendations(int $studentId, int $limit = 8): array
    {
        return $this->getPersonalizedRecommendations($studentId, $limit);
    }

    public function getPopularBooks(int $limit = 8): array
    {
        $stmt = $this->db->prepare("
            SELECT b.*, COUNT(br.id) AS borrow_count
            FROM books b
            LEFT JOIN borrowing_records br
                ON b.id = br.book_id
            GROUP BY b.id
            ORDER BY borrow_count DESC, b.created_at DESC
            LIMIT ?
        ");

        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecentlyBorrowed(int $limit = 8): array
    {
        $stmt = $this->db->prepare("
            SELECT DISTINCT b.*, MAX(br.borrow_date) AS last_borrowed
            FROM borrowing_records br
            JOIN books b ON br.book_id = b.id
            GROUP BY b.id
            ORDER BY last_borrowed DESC
            LIMIT ?
        ");

        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cacheSimilarity(int $bookA, int $bookB, float $score): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO similarity_scores
            (book_id_a, book_id_b, score, computed_at)
            VALUES (?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE
            score = VALUES(score),
            computed_at = NOW()
        ");

        $stmt->execute([
            $bookA,
            $bookB,
            $score
        ]);
    }
}