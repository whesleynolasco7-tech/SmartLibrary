<?php
require_once __DIR__ . '/../includes/auth_check.php';
requireAdmin();

$csrf = generateCSRFToken();
$pageTitle = 'Import from Google Books';
$extraScripts = ['/assets/js/google_import.js'];
include __DIR__ . '/../includes/header.php';
?>

<div class="page-head">
  <div>
    <h1>Import from Google Books</h1>
    <p class="muted">Search the Google Books catalog and import titles directly into your library.</p>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <div class="input-icon search-inline lg">
      <i class="fa-brands fa-google"></i>
      <input type="text" id="gbSearchInput" placeholder="Search by title, author, or ISBN… e.g. 'Harry Potter'">
      <button class="btn btn-primary" id="gbSearchBtn"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
    </div>
  </div>
</div>

<div id="gbResultsContainer" class="gb-results-grid"></div>

<input type="hidden" id="csrfToken" value="<?= e($csrf) ?>">

<?php include __DIR__ . '/../includes/footer.php'; ?>