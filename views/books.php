<?php
require_once __DIR__ . '/../includes/auth_check.php';

$bookModel = new Book();
$categories = $bookModel->getCategories();

$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 10;
$books = $bookModel->getAll($perPage, ($page - 1) * $perPage);
$totalBooks = $bookModel->count();
$totalPages = max(1, (int) ceil($totalBooks / $perPage));

$csrf = generateCSRFToken();
$pageTitle = 'Book Catalog';
$extraScripts = ['/assets/js/books.js'];
include __DIR__ . '/../includes/header.php';
?>

<div class="page-head">
  <div>
    <h1>Book Catalog</h1>
    <p class="muted"><?= $totalBooks ?> books in the library</p>
  </div>
  <?php if (isAdmin()): ?>
  <div class="page-head-actions">
    <button class="btn btn-primary" id="btnAddBook"><i class="fa-solid fa-plus"></i> Add Book</button>
  </div>
  <?php endif; ?>
</div>

<div class="card">
  <div class="card-header wrap-header">
    <div class="input-icon search-inline">
      <i class="fa-solid fa-magnifying-glass"></i>
      <input type="text" id="bookSearchInput" placeholder="Search by title, author, ISBN, category, publisher…">
    </div>
    <select id="categoryFilter" class="select-input">
      <option value="">All Categories</option>
      <?php foreach ($categories as $cat): ?>
        <option value="<?= e($cat['name']) ?>"><?= e($cat['name']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="card-body no-pad">
    <table class="data-table" id="booksTable">
      <thead>
        <tr>
          <th>Cover</th>
          <th>Title</th>
          <th>Author</th>
          <th>Category</th>
          <th>Copies</th>
          <th>Status</th>
          <th style="width:120px">Actions</th>
        </tr>
      </thead>
      <tbody id="booksTableBody">
        <?php foreach ($books as $book):
          $status = $book['available_copies'] > 0 ? 'available' : 'borrowed'; ?>
          <tr data-id="<?= $book['id'] ?>">
            <td><img class="thumb" src="<?= $book['cover_image'] ? UPLOAD_COVER_URL . e($book['cover_image']) : DEFAULT_COVER ?>" alt=""></td>
            <td>
              <a class="table-title-link" href="<?= BASE_URL ?>/views/book_details.php?id=<?= $book['id'] ?>"><?= e($book['title']) ?></a>
              <div class="muted small"><?= e($book['isbn']) ?></div>
            </td>
            <td><?= e($book['author']) ?></td>
            <td><?= e($book['category_name'] ?? '—') ?></td>
            <td><?= (int)$book['available_copies'] ?> / <?= (int)$book['total_copies'] ?></td>
            <td><?= statusBadge($status) ?></td>
            <td class="actions-cell">
              <a href="<?= BASE_URL ?>/views/book_details.php?id=<?= $book['id'] ?>" class="icon-btn sm" title="View"><i class="fa-regular fa-eye"></i></a>
              <?php if (isAdmin()): ?>
              <button class="icon-btn sm btn-edit-book" data-id="<?= $book['id'] ?>" title="Edit"><i class="fa-regular fa-pen-to-square"></i></button>
              <button class="icon-btn sm danger btn-delete-book" data-id="<?= $book['id'] ?>" data-title="<?= e($book['title']) ?>" title="Delete"><i class="fa-regular fa-trash-can"></i></button>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php if (empty($books)): ?>
      <div class="empty-state"><i class="fa-solid fa-book"></i><p>No books yet. Add your first book to get started.</p></div>
    <?php endif; ?>
  </div>
  <div class="card-footer pagination">
    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
      <a href="?page=<?= $p ?>" class="page-btn <?= $p === $page ? 'active' : '' ?>"><?= $p ?></a>
    <?php endfor; ?>
  </div>
</div>

<!-- Add / Edit Book Modal -->
<div class="modal-backdrop" id="bookModalBackdrop">
  <div class="modal">
    <div class="modal-header">
      <h3 id="bookModalTitle"><i class="fa-solid fa-book-medical"></i> Add New Book</h3>
      <button class="icon-btn" id="closeBookModal"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <form id="bookForm" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
      <input type="hidden" name="book_id" id="bookId">
      <div class="modal-body">
        <div class="form-row">
          <div class="form-group">
            <label>Title *</label>
            <input type="text" name="title" id="f_title" required maxlength="255">
          </div>
          <div class="form-group">
            <label>Author *</label>
            <input type="text" name="author" id="f_author" required maxlength="255">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>ISBN</label>
            <input type="text" name="isbn" id="f_isbn" maxlength="20">
          </div>
          <div class="form-group">
            <label>Publisher</label>
            <input type="text" name="publisher" id="f_publisher" maxlength="255">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Category</label>
            <select name="category_id" id="f_category">
              <option value="">— Select —</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= e($cat['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Year Published</label>
            <input type="number" name="year_published" id="f_year" min="1000" max="2100">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Language</label>
            <input type="text" name="language" id="f_language" value="English" maxlength="50">
          </div>
          <div class="form-group">
            <label>Total Copies *</label>
            <input type="number" name="total_copies" id="f_copies" min="1" value="1" required>
          </div>
        </div>
        <div class="form-group">
          <label>Tags / Keywords <span class="muted small">(comma-separated, used for recommendations)</span></label>
          <input type="text" name="tags" id="f_tags" placeholder="e.g. fiction, adventure, classic">
        </div>
        <div class="form-group">
          <label>Description</label>
          <textarea name="description" id="f_description" rows="4" maxlength="2000"></textarea>
        </div>
        <div class="form-group">
          <label>Book Cover</label>
          <input type="file" name="cover_image" id="f_cover" accept="image/*">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" id="cancelBookForm">Cancel</button>
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save Book</button>
      </div>
    </form>
  </div>
</div>

<!-- Delete confirmation -->
<div class="modal-backdrop" id="confirmDeleteBackdrop">
  <div class="modal modal-sm">
    <div class="modal-body center-content">
      <div class="modal-icon danger"><i class="fa-solid fa-triangle-exclamation"></i></div>
      <h3>Delete this book?</h3>
      <p class="muted" id="confirmDeleteText">This action cannot be undone.</p>
    </div>
    <div class="modal-footer center-content">
      <button class="btn btn-outline" id="cancelDelete">Cancel</button>
      <button class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>