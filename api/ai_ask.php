<?php
require_once __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$body = json_decode(file_get_contents('php://input'), true);
$pertanyaan = trim($body['pertanyaan'] ?? '');

if ($pertanyaan === '') {
    http_response_code(422);
    echo json_encode(['error' => 'Pertanyaan tidak boleh kosong.']);
    exit;
}

$data = store_read();
$riwayat = $data['riwayat'];
$totalPerhitungan = array_sum(array_column($riwayat, 'nominal'));
$estimasiZakat = hitung_estimasi_zakat($riwayat);
$statusNisab = cek_status_nisab($totalPerhitungan, $data['nisab_perak']);

// Catatan: ini adalah responder rule-based sebagai placeholder.
// Untuk jawaban AI yang sesungguhnya, ganti blok ini dengan panggilan
// ke API model bahasa (mis. Claude API) dan kirimkan $pertanyaan + konteks di atas.
$q = mb_strtolower($pertanyaan);

if (str_contains($q, 'nisab')) {
    $jawaban = "Nisab perak saat ini adalah " . format_rupiah($data['nisab_perak']) .
        ". Total harta Anda yang tercatat adalah " . format_rupiah($totalPerhitungan) .
        ", jadi status Anda saat ini: {$statusNisab['label']}.";
} elseif (str_contains($q, 'zakat maal') || str_contains($q, 'zakat')) {
    $jawaban = "Berdasarkan total harta tercatat " . format_rupiah($totalPerhitungan) .
        ", estimasi zakat maal Anda adalah " . format_rupiah($estimasiZakat) .
        " (2.5% dari harta yang sudah mencapai nisab dan haul).";
} elseif (str_contains($q, 'kripto')) {
    $jawaban = "Aset kripto termasuk harta yang wajib diperhitungkan dalam zakat maal jika nilainya sudah melewati nisab dan dimiliki selama satu haul (1 tahun hijriah).";
} else {
    $jawaban = "Terima kasih atas pertanyaannya. Saat ini status Anda: {$statusNisab['label']}, dengan estimasi zakat " .
        format_rupiah($estimasiZakat) . ". Coba tanyakan hal spesifik seperti \"berapa nisab saya\" atau \"apakah kripto kena zakat\".";
}

echo json_encode(['jawaban' => $jawaban]);
