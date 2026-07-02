<?php
require_once __DIR__ . '/../includes/auth_check.php';

$pageTitle = 'Recommendations';

$recommendation = new Recommendation();

if (isAdmin()) {
    // Popular books for librarians/admins
    $books = $recommendation->getPopularBooks(20);
} else {
    // Personalized recommendations for students
    $books = $recommendation->getRecommendations($_SESSION['user_id'], 20);
}

include __DIR__ . '/../includes/header.php';
?>

<div class="page-head">
    <div>
        <h1><i class="fa-solid fa-wand-magic-sparkles"></i> Recommendations</h1>

        <?php if (isAdmin()): ?>

            <p class="muted">
                View the most recommended and popular books in your library.
                Use this information to improve the collection.
            </p>

        <?php else: ?>

            <p class="muted">
                These books are recommended for you based on your borrowing
                history and interests.
            </p>

        <?php endif; ?>
    </div>
</div>

<?php if (isAdmin()): ?>

<div class="stat-grid">

    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fa-solid fa-book"></i>
        </div>

        <div>
            <span class="stat-value"><?= count($books) ?></span>
            <span class="stat-label">Popular Books</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fa-solid fa-chart-line"></i>
        </div>

        <div>
            <span class="stat-value">Library</span>
            <span class="stat-label">Recommendation Analytics</span>
        </div>
    </div>

</div>

<?php endif; ?>

<div class="card">

    <div class="card-header">

        <h3>

            <?php if (isAdmin()): ?>

                Most Recommended Books

            <?php else: ?>

                Books Recommended For You

            <?php endif; ?>

        </h3>

    </div>

    <div class="card-body">

        <?php if (empty($books)): ?>

            <div class="empty-state">

                <i class="fa-solid fa-book-open"></i>

                <h3>No recommendations available.</h3>

            </div>

        <?php else: ?>

            <div class="book-grid">

                <?php foreach ($books as $book): ?>

                    <a href="<?= BASE_URL ?>/views/book_details.php?id=<?= $book['id'] ?>" class="book-card">

                        <img
                            src="<?= !empty($book['cover_image']) ? UPLOAD_COVER_URL . e($book['cover_image']) : DEFAULT_COVER ?>"
                            alt="<?= e($book['title']) ?>"
                        >

                        <div class="book-card-body">

                            <strong><?= e($book['title']) ?></strong>

                            <span><?= e($book['author']) ?></span>

                            <?php if (!empty($book['category'])): ?>

                                <small><?= e($book['category']) ?></small>

                            <?php endif; ?>

                        </div>

                    </a>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>

</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>