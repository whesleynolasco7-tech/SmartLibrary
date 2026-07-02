/**
 * books.js — Book Catalog: add/edit/delete modals, live search/filter, pagination-safe AJAX
 */
document.addEventListener('DOMContentLoaded', () => {
  const base = getBaseUrl();
  const bookModal = document.getElementById('bookModalBackdrop');
  const bookForm = document.getElementById('bookForm');
  const bookModalTitle = document.getElementById('bookModalTitle');
  const deleteModal = document.getElementById('confirmDeleteBackdrop');
  let deleteTargetId = null;

  // ---------- Open "Add Book" ----------
  function openAddModal() {
    bookForm.reset();
    document.getElementById('bookId').value = '';
    document.getElementById('f_language').value = 'English';
    bookModalTitle.innerHTML = '<i class="fa-solid fa-book-medical"></i> Add New Book';
    bookModal.classList.add('open');
  }
  document.getElementById('btnAddBook')?.addEventListener('click', openAddModal);

  // auto-open if ?action=add in URL
  if (new URLSearchParams(location.search).get('action') === 'add') {
    openAddModal();
  }

  document.getElementById('closeBookModal')?.addEventListener('click', () => bookModal.classList.remove('open'));
  document.getElementById('cancelBookForm')?.addEventListener('click', () => bookModal.classList.remove('open'));
  bookModal?.addEventListener('click', (e) => { if (e.target === bookModal) bookModal.classList.remove('open'); });

  // ---------- Edit Book ----------
  document.querySelectorAll('.btn-edit-book').forEach(btn => {
    btn.addEventListener('click', async () => {
      const id = btn.dataset.id;
      try {
        const res = await fetch(`${base}/api/get_book.php?id=${id}`);
        const data = await res.json();
        if (!data.success) return showToast(data.message, 'error');
        const b = data.book;
        bookForm.reset();
        document.getElementById('bookId').value = b.id;
        document.getElementById('f_title').value = b.title || '';
        document.getElementById('f_author').value = b.author || '';
        document.getElementById('f_isbn').value = b.isbn || '';
        document.getElementById('f_publisher').value = b.publisher || '';
        document.getElementById('f_category').value = b.category_id || '';
        document.getElementById('f_year').value = b.year_published || '';
        document.getElementById('f_language').value = b.language || 'English';
        document.getElementById('f_copies').value = b.total_copies || 1;
        document.getElementById('f_tags').value = b.tags || '';
        document.getElementById('f_description').value = b.description || '';
        bookModalTitle.innerHTML = '<i class="fa-regular fa-pen-to-square"></i> Edit Book';
        bookModal.classList.add('open');
      } catch (err) {
        showToast('Failed to load book details.', 'error');
      }
    });
  });

  // ---------- Save (Add/Edit) ----------
  bookForm?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const submitBtn = bookForm.querySelector('button[type=submit]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving…';

    try {
      const formData = new FormData(bookForm);
      const res = await fetch(`${base}/api/save_book.php`, { method: 'POST', body: formData });
      const data = await res.json();
      if (data.success) {
        showToast(data.message, 'success');
        setTimeout(() => location.reload(), 700);
      } else {
        showToast(data.message || 'Failed to save book.', 'error');
      }
    } catch (err) {
      showToast('Network error. Please try again.', 'error');
    } finally {
      submitBtn.disabled = false;
      submitBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Book';
    }
  });

  // ---------- Delete ----------
  document.querySelectorAll('.btn-delete-book').forEach(btn => {
    btn.addEventListener('click', () => {
      deleteTargetId = btn.dataset.id;
      document.getElementById('confirmDeleteText').textContent =
        `Delete "${btn.dataset.title}"? This action cannot be undone.`;
      deleteModal.classList.add('open');
    });
  });
  document.getElementById('cancelDelete')?.addEventListener('click', () => deleteModal.classList.remove('open'));
  deleteModal?.addEventListener('click', (e) => { if (e.target === deleteModal) deleteModal.classList.remove('open'); });

  document.getElementById('confirmDeleteBtn')?.addEventListener('click', async () => {
    if (!deleteTargetId) return;
    const csrf = document.querySelector('input[name=csrf_token]').value;
    try {
      const res = await fetch(`${base}/api/delete_book.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: deleteTargetId, csrf_token: csrf }),
      });
      const data = await res.json();
      if (data.success) {
        showToast(data.message, 'success');
        document.querySelector(`tr[data-id="${deleteTargetId}"]`)?.remove();
      } else {
        showToast(data.message, 'error');
      }
    } catch (err) {
      showToast('Network error. Please try again.', 'error');
    } finally {
      deleteModal.classList.remove('open');
      deleteTargetId = null;
    }
  });

  // ---------- Live table filter (title/author/isbn/category/publisher) + category dropdown ----------
  const searchInput = document.getElementById('bookSearchInput');
  const categoryFilter = document.getElementById('categoryFilter');
  const tableBody = document.getElementById('booksTableBody');
  let searchTimer;

  async function filterBooks() {
    const q = searchInput.value.trim();
    try {
      const res = await fetch(`${base}/api/search_books.php?q=${encodeURIComponent(q)}`);
      const data = await res.json();
      let rows = data.results || [];
      const cat = categoryFilter.value;
      if (cat) rows = rows.filter(r => r.category_name === cat);
      renderRows(rows);
    } catch (err) {
      console.error(err);
    }
  }

  function renderRows(rows) {
    if (rows.length === 0) {
      tableBody.innerHTML = `<tr><td colspan="7"><div class="empty-state small"><i class="fa-solid fa-magnifying-glass"></i><p>No books match your search.</p></div></td></tr>`;
      return;
    }
    tableBody.innerHTML = rows.map(b => `
      <tr data-id="${b.id}">
        <td><img class="thumb" src="${b.cover_url}" alt=""></td>
        <td>
          <a class="table-title-link" href="${b.detail_url}">${escapeHtml(b.title)}</a>
          <div class="muted small">${escapeHtml(b.isbn || '')}</div>
        </td>
        <td>${escapeHtml(b.author)}</td>
        <td>${escapeHtml(b.category_name || '—')}</td>
        <td>${b.available_copies} / ${b.total_copies}</td>
        <td>${b.available_copies > 0 ? '<span class="badge badge-success">Available</span>' : '<span class="badge badge-warning">Borrowed</span>'}</td>
        <td><a href="${b.detail_url}" class="icon-btn sm" title="View"><i class="fa-regular fa-eye"></i></a></td>
      </tr>
    `).join('');
  }

  searchInput?.addEventListener('input', () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(filterBooks, 250);
  });
  categoryFilter?.addEventListener('change', filterBooks);
});