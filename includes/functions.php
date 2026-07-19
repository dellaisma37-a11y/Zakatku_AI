<?php
// Path ke "database" sederhana (file JSON).
define('STORE_PATH', __DIR__ . '/../data/store.json');

/**
 * Membaca seluruh data dari store.json
 */
function store_read(): array {
    $raw = file_get_contents(STORE_PATH);
    $data = json_decode($raw, true);
    return $data ?: [];
}

/**
 * Menyimpan seluruh data ke store.json
 */
function store_write(array $data): void {
    file_put_contents(STORE_PATH, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

/**
 * Format angka menjadi format Rupiah, mis. 3212500 -> "Rp 3.212.500"
 */
function format_rupiah(int $amount): string {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

/**
 * Menghitung total nilai harta dari seluruh riwayat yang berkategori aset
 * (emas, maal, tabungan) untuk dijadikan dasar estimasi zakat (2.5%).
 */
function hitung_estimasi_zakat(array $riwayat): int {
    $total = 0;
    foreach ($riwayat as $item) {
        $total += $item['nominal'];
    }
    // Estimasi zakat maal sederhana: 2.5% dari total harta tercatat.
    return (int) round($total * 0.025);
}

/**
 * Menentukan status nisab: sudah wajib zakat atau belum,
 * dibandingkan dengan nisab perak yang berlaku.
 */
function cek_status_nisab(int $totalHarta, int $nisabPerak): array {
    if ($totalHarta >= $nisabPerak) {
        return ['label' => 'Wajib Zakat', 'badge' => 'Mencapai Nisab', 'status' => 'wajib'];
    }
    return ['label' => 'Belum Wajib', 'badge' => 'Belum Nisab', 'status' => 'belum'];
}

/**
 * Ikon kecil (emoji-like initial) per kategori riwayat, dipetakan ke kelas CSS.
 */
function kategori_icon_class(string $kategori): string {
    return match ($kategori) {
        'emas' => 'ti ti-diamond',
        'tabungan' => 'ti ti-building-bank',
        'maal' => 'ti ti-building',
        default => 'ti ti-receipt',
    };
}

function kategori_badge_class(string $status): string {
    return match ($status) {
        'Tersimpan' => 'badge badge-green',
        'Terverifikasi' => 'badge badge-blue',
        default => 'badge',
    };
}
