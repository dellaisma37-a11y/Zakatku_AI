document.addEventListener('DOMContentLoaded', () => {
  initSidebarToggle();
  initSidebarNav();
  initNotifDropdown();
  initUserDisplayName();
  initChart();
  initModalCatatHarta();
  initAiPanel();
});

function initUserDisplayName() {
  try {
    const fullName = localStorage.getItem('zakaai_full_name')
    if (!fullName) return

    const headerName = document.getElementById('headerUserName')
    const greetingName = document.getElementById('dashboardGreetingName')
    if (headerName) headerName.textContent = fullName
    if (greetingName) greetingName.textContent = fullName
  } catch (err) {
    console.warn('Gagal membaca nama pengguna dari localStorage:', err)
  }
}

/* ---------------- Sidebar collapse / expand ---------------- */
function initSidebarToggle() {
  const shell = document.querySelector('.app-shell');
  const toggleBtn = document.getElementById('btnSidebarToggle');
  if (!shell || !toggleBtn) return;

  const STORAGE_KEY = 'zakaai_sidebar_collapsed';
  const applyState = (collapsed) => {
    shell.classList.toggle('is-collapsed', collapsed);
    toggleBtn.setAttribute('aria-label', collapsed ? 'Buka sidebar' : 'Ciutkan sidebar');
  };

  let saved = false;
  try {
    saved = localStorage.getItem(STORAGE_KEY) === '1';
  } catch (err) {
    saved = false;
  }
  applyState(saved);

  toggleBtn.addEventListener('click', () => {
    const collapsed = !shell.classList.contains('is-collapsed');
    applyState(collapsed);
    try {
      localStorage.setItem(STORAGE_KEY, collapsed ? '1' : '0');
    } catch (err) {
      /* localStorage unavailable, state just won't persist */
    }
  });
}

/* ---------------- Sidebar navigation (highlight active item) ---------------- */
function initSidebarNav() {
  const items = document.querySelectorAll('.nav-item[data-section]');
  items.forEach((item) => {
    item.addEventListener('click', (e) => {
      e.preventDefault();
      items.forEach((i) => i.classList.remove('is-active'));
      item.classList.add('is-active');
      // Di aplikasi nyata, ini akan me-load halaman/section lain via fetch atau routing.
      // Untuk demo, kita cukup tandai menu yang aktif.
    });
  });
}

/* ---------------- Notification dropdown ---------------- */
function initNotifDropdown() {
  const btn = document.getElementById('btnNotif');
  const dropdown = document.getElementById('notifDropdown');

  btn.addEventListener('click', (e) => {
    e.stopPropagation();
    dropdown.classList.toggle('is-open');
  });

  document.addEventListener('click', (e) => {
    if (!dropdown.contains(e.target) && e.target !== btn) {
      dropdown.classList.remove('is-open');
    }
  });
}

/* ---------------- Chart: Tren Pertumbuhan Harta ---------------- */
let wealthChart;

function initChart() {
  const canvas = document.getElementById('wealthChart');
  const ctx = canvas.getContext('2d');

  wealthChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: [],
      datasets: [{
        label: 'Total harta',
        data: [],
        borderColor: '#1f5c4b',
        backgroundColor: 'rgba(31, 92, 75, 0.08)',
        fill: true,
        tension: 0.35,
        pointRadius: 3,
        pointBackgroundColor: '#1f5c4b',
      }],
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: {
        y: {
          ticks: {
            callback: (value) => 'Rp ' + (value / 1000000) + 'jt',
          },
          grid: { color: '#eef1f0' },
        },
        x: { grid: { display: false } },
      },
    },
  });

  loadChartData('6bulan');

  document.getElementById('periodSelect').addEventListener('change', (e) => {
    loadChartData(e.target.value);
  });
}

async function loadChartData(period) {
  try {
    const res = await fetch(`api/get_chart.php?period=${encodeURIComponent(period)}`);
    if (!res.ok) throw new Error('Gagal memuat data chart');
    const data = await res.json();

    wealthChart.data.labels = data.labels;
    wealthChart.data.datasets[0].data = data.values;
    wealthChart.update();
  } catch (err) {
    console.error(err);
  }
}

/* ---------------- Modal: Catat Harta Baru ---------------- */
function initModalCatatHarta() {
  const overlay = document.getElementById('modalOverlay');
  const openBtn = document.getElementById('btnCatatHarta');
  const closeBtn = document.getElementById('modalClose');
  const form = document.getElementById('formCatatHarta');

  openBtn.addEventListener('click', () => overlay.classList.add('is-open'));
  closeBtn.addEventListener('click', () => overlay.classList.remove('is-open'));
  overlay.addEventListener('click', (e) => {
    if (e.target === overlay) overlay.classList.remove('is-open');
  });

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    clearFormError(form);

    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Menyimpan...';

    try {
      const res = await fetch('api/add_asset.php', { method: 'POST', body: formData });
      const result = await res.json();

      if (!res.ok) {
        showFormError(form, result.error || 'Terjadi kesalahan, coba lagi.');
        return;
      }

      prependRiwayat(result.entry);
      updateTotals(result.totals);

      form.reset();
      overlay.classList.remove('is-open');
    } catch (err) {
      showFormError(form, 'Tidak dapat terhubung ke server.');
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = 'Simpan';
    }
  });
}

function showFormError(form, message) {
  clearFormError(form);
  const p = document.createElement('p');
  p.className = 'form-error';
  p.textContent = message;
  form.appendChild(p);
}

function clearFormError(form) {
  const existing = form.querySelector('.form-error');
  if (existing) existing.remove();
}

function prependRiwayat(entry) {
  const list = document.getElementById('riwayatList');
  const item = document.createElement('div');
  item.className = 'riwayat-item is-new';
  item.innerHTML = `
    <div class="riwayat-left">
      <span class="riwayat-icon"><i class="${entry.icon_class}"></i></span>
      <div>
        <p class="riwayat-title">${escapeHtml(entry.jenis)}</p>
        <p class="riwayat-date">${escapeHtml(entry.tanggal)} &bull; ${escapeHtml(entry.waktu)}</p>
      </div>
    </div>
    <div class="riwayat-right">
      <p class="riwayat-amount">${escapeHtml(entry.nominal)}</p>
      <span class="${entry.badge_class}">${escapeHtml(entry.status)}</span>
    </div>
  `;
  list.prepend(item);
}

function updateTotals(totals) {
  document.getElementById('totalPerhitunganValue').textContent = totals.total_perhitungan;
  document.getElementById('estimasiZakatValue').textContent = totals.estimasi_zakat;
  document.getElementById('statusNisabValue').textContent = totals.status_label;
}

function escapeHtml(str) {
  const div = document.createElement('div');
  div.textContent = str;
  return div.innerHTML;
}

/* ---------------- AI panel + search bar ---------------- */
function initAiPanel() {
  const panel = document.getElementById('aiPanel');
  const fab = document.getElementById('fabChat');
  const closeBtn = document.getElementById('aiPanelClose');
  const body = document.getElementById('aiPanelBody');
  const searchForm = document.getElementById('aiSearchForm');
  const searchInput = document.getElementById('aiSearchInput');

  fab.addEventListener('click', () => panel.classList.toggle('is-open'));
  closeBtn.addEventListener('click', () => panel.classList.remove('is-open'));

  searchForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const pertanyaan = searchInput.value.trim();
    if (!pertanyaan) return;

    panel.classList.add('is-open');
    addChatMessage(body, pertanyaan, 'user');
    searchInput.value = '';

    await askAi(pertanyaan, body);
  });
}

async function askAi(pertanyaan, body) {
  const loadingMsg = addChatMessage(body, 'Mengetik...', 'bot');

  try {
    const res = await fetch('api/ai_ask.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ pertanyaan }),
    });
    const result = await res.json();
    loadingMsg.textContent = result.jawaban || result.error || 'Maaf, terjadi kesalahan.';
  } catch (err) {
    loadingMsg.textContent = 'Tidak dapat terhubung ke asisten AI saat ini.';
  }
}

function addChatMessage(container, text, role) {
  const p = document.createElement('p');
  p.className = `ai-msg ${role === 'user' ? 'ai-msg-user' : 'ai-msg-bot'}`;
  p.textContent = text;
  container.appendChild(p);
  container.scrollTop = container.scrollHeight;
  return p;
}
