<?php
require_once __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$kategori = trim($_POST['kategori'] ?? '');
$jenis = trim($_POST['jenis'] ?? '');
$nominal = (int) ($_POST['nominal'] ?? 0);

if ($jenis === '' || $nominal <= 0 || !in_array($kategori, ['emas', 'tabungan', 'maal'], true)) {
    http_response_code(422);
    echo json_encode(['error' => 'Data tidak valid. Pastikan jenis dan nominal terisi dengan benar.']);
    exit;
}

$data = store_read();

$newId = count($data['riwayat']) > 0
    ? max(array_column($data['riwayat'], 'id')) + 1
    : 1;

$entry = [
    'id' => $newId,
    'jenis' => $jenis,
    'kategori' => $kategori,
    'nominal' => $nominal,
    'tanggal' => date('d F Y'),
    'waktu' => date('H:i') . ' WIB',
    'status' => 'Tersimpan',
];

// Entri terbaru ditaruh paling atas.
array_unshift($data['riwayat'], $entry);
store_write($data);

$totalPerhitungan = array_sum(array_column($data['riwayat'], 'nominal'));
$estimasiZakat = hitung_estimasi_zakat($data['riwayat']);
$statusNisab = cek_status_nisab($totalPerhitungan, $data['nisab_perak']);

echo json_encode([
    'entry' => [
        'jenis' => $entry['jenis'],
        'kategori' => $entry['kategori'],
        'nominal' => format_rupiah($entry['nominal']),
        'tanggal' => $entry['tanggal'],
        'waktu' => $entry['waktu'],
        'status' => $entry['status'],
        'icon_class' => kategori_icon_class($entry['kategori']),
        'badge_class' => kategori_badge_class($entry['status']),
    ],
    'totals' => [
        'total_perhitungan' => format_rupiah($totalPerhitungan),
        'estimasi_zakat' => format_rupiah($estimasiZakat),
        'status_label' => $statusNisab['label'],
        'status_badge' => $statusNisab['badge'],
    ],
]);
