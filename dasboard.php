<?php
require_once __DIR__ . '/includes/functions.php';

$data = store_read();
$user = $data['user'];
$riwayat = $data['riwayat'];
$insights = $data['insights'];

$totalPerhitungan = array_sum(array_column($riwayat, 'nominal'));
$estimasiZakat = hitung_estimasi_zakat($riwayat);
$statusNisab = cek_status_nisab($totalPerhitungan, $data['nisab_perak']);
$totalKonsultasi = 24; // contoh statis, bisa dihubungkan ke tabel konsultasi AI
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>ZakaAI - Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="app-shell">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>

    <main class="main-content">
        <?php include __DIR__ . '/includes/header.php'; ?>

        <section class="page-head">
            <div>
                <h1>Assalamu'alaikum, <?= htmlspecialchars($user['name']) ?></h1>
                <p class="subtitle">Berikut adalah ringkasan finansial dan kewajiban zakat Anda hari ini.</p>
            </div>
            <button class="btn-primary" id="btnCatatHarta">
                <i class="ti ti-plus"></i> Catat Harta Baru
            </button>
        </section>

        <section class="stat-grid">
            <div class="stat-card stat-highlight">
                <div class="stat-top">
                    <span class="stat-icon"><i class="ti ti-shield-check"></i></span>
                    <span class="badge badge-green"><?= htmlspecialchars($statusNisab['badge']) ?></span>
                </div>
                <p class="stat-label">Status Nisab</p>
                <p class="stat-value" id="statusNisabValue"><?= htmlspecialchars($statusNisab['label']) ?></p>
                <p class="stat-sub">Nisab Perak: <?= format_rupiah($data['nisab_perak']) ?></p>
            </div>

            <div class="stat-card">
                <span class="stat-icon"><i class="ti ti-file-invoice"></i></span>
                <p class="stat-label">Total Perhitungan</p>
                <p class="stat-value" id="totalPerhitunganValue"><?= format_rupiah($totalPerhitungan) ?></p>
                <p class="stat-sub stat-up"><i class="ti ti-trending-up"></i> +12% dari bulan lalu</p>
            </div>

            <div class="stat-card">
                <span class="stat-icon"><i class="ti ti-message-chatbot"></i></span>
                <p class="stat-label">Total Konsultasi AI</p>
                <p class="stat-value"><?= $totalKonsultasi ?> Sesi</p>
                <p class="stat-sub">Terakhir: 2 jam yang lalu</p>
            </div>

            <div class="stat-card stat-dark">
                <span class="stat-icon"><i class="ti ti-wallet"></i></span>
                <p class="stat-label">Estimasi Zakat</p>
                <p class="stat-value" id="estimasiZakatValue"><?= format_rupiah($estimasiZakat) ?></p>
                <p class="stat-sub">Jatuh tempo dlm 12 hari</p>
            </div>
        </section>

        <section class="chart-card">
            <div class="chart-head">
                <div>
                    <h2>Tren Pertumbuhan Harta</h2>
                    <p class="subtitle">Pergerakan aset Anda dalam periode terpilih</p>
                </div>
                <select id="periodSelect" class="period-select">
                    <option value="6bulan" selected>6 Bulan Terakhir</option>
                    <option value="3bulan">3 Bulan Terakhir</option>
                    <option value="1tahun">1 Tahun Terakhir</option>
                </select>
            </div>
            <canvas id="wealthChart" height="90"></canvas>
        </section>

        <section class="bottom-grid">
            <div class="panel">
                <div class="panel-head">
                    <h2>Riwayat Terbaru</h2>
                    <a href="#" id="lihatSemua">Lihat Semua</a>
                </div>
                <div class="riwayat-list" id="riwayatList">
                    <?php foreach ($riwayat as $item): ?>
                    <div class="riwayat-item">
                        <div class="riwayat-left">
                            <span class="riwayat-icon"><i class="<?= kategori_icon_class($item['kategori']) ?>"></i></span>
                            <div>
                                <p class="riwayat-title"><?= htmlspecialchars($item['jenis']) ?></p>
                                <p class="riwayat-date"><?= htmlspecialchars($item['tanggal']) ?> &bull; <?= htmlspecialchars($item['waktu']) ?></p>
                            </div>
                        </div>
                        <div class="riwayat-right">
                            <p class="riwayat-amount"><?= format_rupiah($item['nominal']) ?></p>
                            <span class="<?= kategori_badge_class($item['status']) ?>"><?= htmlspecialchars($item['status']) ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="panel panel-ai">
                <div class="panel-head">
                    <h2><i class="ti ti-sparkles"></i> Insight AI</h2>
                </div>
                <p class="subtitle">Optimalkan keberkahan harta Anda hari ini.</p>

                <div class="insight-list" id="insightList">
                    <?php foreach ($insights as $insight): ?>
                    <div class="insight-item insight-<?= htmlspecialchars($insight['type']) ?>">
                        <p class="insight-title">
                            <i class="ti <?= $insight['type'] === 'tips' ? 'ti-bulb' : 'ti-gavel' ?>"></i>
                            <?= htmlspecialchars($insight['title']) ?>
                        </p>
                        <p class="insight-text">"<?= htmlspecialchars($insight['text']) ?>"</p>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="quick-action-box">
                    <i class="ti ti-bolt"></i>
                    <div>
                        <p class="quick-title">Quick Action</p>
                        <p class="quick-text">Estimasi zakat bulan depan sudah siap. Mau cek sekarang?</p>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>

<button class="fab-chat" id="fabChat" aria-label="Buka asisten AI">
    <i class="ti ti-robot"></i>
</button>

<!-- Modal: Catat Harta Baru -->
<div class="modal-overlay" id="modalOverlay">
    <div class="modal-box">
        <div class="modal-head">
            <h3>Catat Harta Baru</h3>
            <button id="modalClose" aria-label="Tutup"><i class="ti ti-x"></i></button>
        </div>
        <form id="formCatatHarta">
            <label>Jenis harta
                <select name="kategori" required>
                    <option value="emas">Zakat Emas</option>
                    <option value="tabungan">Saldo Tabungan</option>
                    <option value="maal">Zakat Maal</option>
                </select>
            </label>
            <label>Keterangan
                <input type="text" name="jenis" placeholder="Contoh: Perhitungan Zakat Emas" required>
            </label>
            <label>Nominal (Rp)
                <input type="number" name="nominal" min="0" step="1000" placeholder="Contoh: 850000" required>
            </label>
            <button type="submit" class="btn-primary btn-full">Simpan</button>
        </form>
    </div>
</div>

<!-- Panel: Asisten AI (dipicu oleh search bar & tombol chat mengambang) -->
<div class="ai-panel" id="aiPanel">
    <div class="ai-panel-head">
        <p><i class="ti ti-sparkles"></i> Asisten AI ZakaAI</p>
        <button id="aiPanelClose" aria-label="Tutup"><i class="ti ti-x"></i></button>
    </div>
    <div class="ai-panel-body" id="aiPanelBody">
        <p class="ai-msg ai-msg-bot">Assalamu'alaikum! Tanyakan apa saja seputar zakat, nisab, atau kondisi harta Anda.</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script src="assets/js/app.js"></script>
<!-- Code injected by live-server -->
<script>
	// <![CDATA[  <-- For SVG support
	if ('WebSocket' in window) {
		(function () {
			function refreshCSS() {
				var sheets = [].slice.call(document.getElementsByTagName("link"));
				var head = document.getElementsByTagName("head")[0];
				for (var i = 0; i < sheets.length; ++i) {
					var elem = sheets[i];
					var parent = elem.parentElement || head;
					parent.removeChild(elem);
					var rel = elem.rel;
					if (elem.href && typeof rel != "string" || rel.length == 0 || rel.toLowerCase() == "stylesheet") {
						var url = elem.href.replace(/(&|\?)_cacheOverride=\d+/, '');
						elem.href = url + (url.indexOf('?') >= 0 ? '&' : '?') + '_cacheOverride=' + (new Date().valueOf());
					}
					parent.appendChild(elem);
				}
			}
			var protocol = window.location.protocol === 'http:' ? 'ws://' : 'wss://';
			var address = protocol + window.location.host + window.location.pathname + '/ws';
			var socket = new WebSocket(address);
			socket.onmessage = function (msg) {
				if (msg.data == 'reload') window.location.reload();
				else if (msg.data == 'refreshcss') refreshCSS();
			};
			if (sessionStorage && !sessionStorage.getItem('IsThisFirstTime_Log_From_LiveServer')) {
				console.log('Live reload enabled.');
				sessionStorage.setItem('IsThisFirstTime_Log_From_LiveServer', true);
			}
		})();
	}
	else {
		console.error('Upgrade your browser. This Browser is NOT supported WebSocket for Live-Reloading.');
	}
	// ]]>
</script>
</body>
</html>
