/**
 * main.js — shared behaviors: sidebar toggle, user menu, toasts, global live search
 */
const BASE_URL = document.querySelector('link[href*="style.css"]')
  ? document.querySelector('link[href*="style.css"]').href.split('/assets/')[0]
  : '';

// ---------- Toasts ----------
function showToast(message, type = 'info') {
  const container = document.getElementById('toastContainer');
  if (!container) return;
  const icons = { success: 'fa-circle-check', error: 'fa-circle-xmark', info: 'fa-circle-info' };
  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  toast.innerHTML = `<i class="fa-solid ${icons[type] || icons.info}"></i><span>${message}</span>`;
  container.appendChild(toast);
  setTimeout(() => {
    toast.style.transition = 'opacity .3s';
    toast.style.opacity = '0';
    setTimeout(() => toast.remove(), 300);
  }, 3200);
}

// ---------- Sidebar toggle (mobile) ----------
document.addEventListener('DOMContentLoaded', () => {
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebarOverlay');
  const toggle = document.getElementById('sidebarToggle');

  toggle?.addEventListener('click', () => {
    sidebar.classList.toggle('open');
    overlay.classList.toggle('open');
  });
  overlay?.addEventListener('click', () => {
    sidebar.classList.remove('open');
    overlay.classList.remove('open');
  });

  // ---------- User menu ----------
  const userToggle = document.getElementById('userMenuToggle');
  const userMenu = document.getElementById('userMenu');
  userToggle?.addEventListener('click', (e) => {
    e.stopPropagation();
    userMenu.classList.toggle('open');
  });
  document.addEventListener('click', () => userMenu?.classList.remove('open'));

  // ---------- Global live search ----------
  const searchInput = document.getElementById('globalSearch');
  const resultsBox = document.getElementById('globalSearchResults');
  let debounceTimer;

  searchInput?.addEventListener('input', () => {
    clearTimeout(debounceTimer);
    const q = searchInput.value.trim();
    if (q.length === 0) {
      resultsBox.classList.remove('open');
      return;
    }
    debounceTimer = setTimeout(() => runGlobalSearch(q), 250);
  });

  searchInput?.addEventListener('focus', () => {
    if (searchInput.value.trim().length > 0) resultsBox.classList.add('open');
  });

  document.addEventListener('click', (e) => {
    if (resultsBox && !resultsBox.contains(e.target) && e.target !== searchInput) {
      resultsBox.classList.remove('open');
    }
  });

  async function runGlobalSearch(q) {
    try {
      const res = await fetch(`${getBaseUrl()}/api/search_books.php?q=${encodeURIComponent(q)}`);
      const data = await res.json();
      renderSearchResults(data.results || []);
    } catch (err) {
      console.error(err);
    }
  }

  function renderSearchResults(results) {
    if (!resultsBox) return;
    if (results.length === 0) {
      resultsBox.innerHTML = `<div class="search-empty"><i class="fa-regular fa-face-frown"></i> No books found.</div>`;
    } else {
      resultsBox.innerHTML = results.map(b => `
        <a class="search-result-item" href="${b.detail_url}">
          <img src="${b.cover_url}" alt="">
          <div>
            <div class="sr-title">${escapeHtml(b.title)}</div>
            <div class="sr-sub">${escapeHtml(b.author)} · ${b.available_copies}/${b.total_copies} available</div>
          </div>
        </a>
      `).join('');
    }
    resultsBox.classList.add('open');
  }
});

function getBaseUrl() {
  // derive from stylesheet path already computed in header
  const link = document.querySelector('link[href*="/assets/css/style.css"]');
  if (link) return link.getAttribute('href').split('/assets/')[0];
  return '';
}

function escapeHtml(str) {
  const div = document.createElement('div');
  div.textContent = str ?? '';
  return div.innerHTML;
}