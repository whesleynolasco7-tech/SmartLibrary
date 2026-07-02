/**
 * borrow.js — Borrow modal, borrow/return actions, similarity matching widget
 */
document.addEventListener('DOMContentLoaded', () => {
  const base = getBaseUrl();
  const csrfEl = document.getElementById('csrfToken');
  const csrf = csrfEl ? csrfEl.value : '';

  // ---------- Borrowing page: open modal ----------
  const borrowModal = document.getElementById('borrowModalBackdrop');
  document.getElementById('btnOpenBorrow')?.addEventListener('click', () => borrowModal.classList.add('open'));
  document.getElementById('closeBorrowModal')?.addEventListener('click', () => borrowModal.classList.remove('open'));
  document.getElementById('cancelBorrow')?.addEventListener('click', () => borrowModal.classList.remove('open'));
  borrowModal?.addEventListener('click', (e) => { if (e.target === borrowModal) borrowModal.classList.remove('open'); });

  if (new URLSearchParams(location.search).get('action') === 'borrow') {
    borrowModal?.classList.add('open');
  }

  document.getElementById('confirmBorrowBtn')?.addEventListener('click', async () => {
    const bookId = document.getElementById('borrowBookSelect')?.value;
    const studentSelect = document.getElementById('borrowStudentSelect');
    const studentId = studentSelect ? studentSelect.value : document.getElementById('myStudentId')?.value;

    if (!bookId) return showToast('Please select a book.', 'error');
    if (studentSelect && !studentId) return showToast('Please select a student.', 'error');

    await doBorrow(bookId, studentId);
  });

  // ---------- Book details page: borrow this book ----------
  document.getElementById('btnBorrowThis')?.addEventListener('click', async (e) => {
    const bookId = e.currentTarget.dataset.bookId;
    const studentId = document.getElementById('myStudentId')?.value;
    await doBorrow(bookId, studentId);
  });

  async function doBorrow(bookId, studentId) {
    try {
      const res = await fetch(`${base}/api/borrow_book.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ book_id: bookId, student_id: studentId, csrf_token: csrf }),
      });
      const data = await res.json();
      if (data.success) {
        showToast(data.message + (data.due_date ? ` Due: ${data.due_date}` : ''), 'success');
        setTimeout(() => location.reload(), 900);
      } else {
        showToast(data.message, 'error');
      }
    } catch (err) {
      showToast('Network error. Please try again.', 'error');
    }
  }

  // ---------- Return book buttons ----------
  document.querySelectorAll('.btn-return').forEach(btn => {
    btn.addEventListener('click', async () => {
      const recordId = btn.dataset.id;
      btn.disabled = true;
      btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
      try {
        const res = await fetch(`${base}/api/return_book.php`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ record_id: recordId, csrf_token: csrf }),
        });
        const data = await res.json();
        if (data.success) {
          showToast(data.message + (data.fine > 0 ? ` Fine: ₱${data.fine.toFixed(2)}` : ''), 'success');
          setTimeout(() => location.reload(), 900);
        } else {
          showToast(data.message, 'error');
          btn.disabled = false;
          btn.innerHTML = '<i class="fa-solid fa-rotate-left"></i> Return';
        }
      } catch (err) {
        showToast('Network error. Please try again.', 'error');
        btn.disabled = false;
      }
    });
  });

  // ---------- Similarity matching widget (book_details.php) ----------
  const similarContainer = document.getElementById('similarBooksContainer');
  const bookDetailId = document.getElementById('bookDetailId')?.value;

  if (similarContainer && bookDetailId) {
    fetch(`${base}/api/get_similar_books.php?book_id=${bookDetailId}`)
      .then(res => res.json())
      .then(data => {
        const results = data.results || [];
        if (results.length === 0) {
          similarContainer.innerHTML = '<p class="muted">No similar books found yet.</p>';
          return;
        }
        similarContainer.innerHTML = results.map(b => `
          <a href="${b.detail_url}" class="book-card">
            <img src="${b.cover_url}" alt="">
            <div class="book-card-body">
              <strong>${escapeHtml(b.title)}</strong>
              <span>${escapeHtml(b.author)}</span>
              <span class="tag sm">${b.similarity}% similar</span>
            </div>
          </a>
        `).join('');
      })
      .catch(() => {
        similarContainer.innerHTML = '<p class="muted">Could not load similar books.</p>';
      });
  }
});