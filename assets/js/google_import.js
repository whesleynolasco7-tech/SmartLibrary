/**
 * google_import.js — Google Books search & one-click import
 */
document.addEventListener('DOMContentLoaded', () => {
  const base = getBaseUrl();
  const csrf = document.getElementById('csrfToken')?.value;
  const input = document.getElementById('gbSearchInput');
  const btn = document.getElementById('gbSearchBtn');
  const container = document.getElementById('gbResultsContainer');

  async function runSearch() {
    const q = input.value.trim();
    if (!q) return showToast('Enter a search term.', 'error');

    container.innerHTML = '<div class="loader-inline"><i class="fa-solid fa-spinner fa-spin"></i> Searching Google Books…</div>';

    try {
      const res = await fetch(`${base}/api/google_books_search.php?q=${encodeURIComponent(q)}`);
      const data = await res.json();
      if (!data.success || (data.results || []).length === 0) {
        container.innerHTML = `<div class="empty-state"><i class="fa-brands fa-google"></i><p>${data.message || 'No results found.'}</p></div>`;
        return;
      }
      renderResults(data.results);
    } catch (err) {
      container.innerHTML = '<div class="empty-state"><i class="fa-solid fa-wifi"></i><p>Could not reach Google Books API. Check your server\'s internet connection.</p></div>';
    }
  }

  function renderResults(results) {
    const defaultCover = `${base}/assets/images/covers/default-cover.svg`;
    container.innerHTML = results.map((b, i) => `
      <div class="gb-card" data-index="${i}">
        <img src="${b.thumbnail || defaultCover}" alt="">
        <div class="gb-card-body">
          <strong>${escapeHtml(b.title)}</strong>
          <span>${escapeHtml(b.author)}</span>
          <span>${escapeHtml(b.published_date || '')}</span>
          <p>${escapeHtml((b.description || '').substring(0, 110))}${b.description && b.description.length > 110 ? '…' : ''}</p>
        </div>
        <div class="gb-card-footer">
          <button class="btn btn-primary btn-sm btn-block btn-import" data-index="${i}">
            <i class="fa-solid fa-download"></i> Import to Library
          </button>
        </div>
      </div>
    `).join('');

    container.querySelectorAll('.btn-import').forEach(importBtn => {
      importBtn.addEventListener('click', async () => {
        const idx = importBtn.dataset.index;
        const book = results[idx];
        importBtn.disabled = true;
        importBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Importing…';
        try {
          const res = await fetch(`${base}/api/google_books_import.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ ...book, csrf_token: csrf }),
          });
          const data = await res.json();
          if (data.success) {
            showToast(data.message, 'success');
            importBtn.innerHTML = '<i class="fa-solid fa-check"></i> Imported';
            importBtn.classList.remove('btn-primary');
            importBtn.classList.add('btn-outline');
          } else {
            showToast(data.message, 'error');
            importBtn.disabled = false;
            importBtn.innerHTML = '<i class="fa-solid fa-download"></i> Import to Library';
          }
        } catch (err) {
          showToast('Network error during import.', 'error');
          importBtn.disabled = false;
        }
      });
    });
  }

  btn?.addEventListener('click', runSearch);
  input?.addEventListener('keydown', (e) => { if (e.key === 'Enter') runSearch(); });
});