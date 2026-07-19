# ZakaAI Dashboard (PHP + JavaScript)

Implementasi dashboard ZakaAI sesuai desain, dengan backend **PHP** dan interaktivitas **JavaScript** (fetch/AJAX) di frontend. Catatan: brief menyebut "Java", tapi karena konteksnya web dashboard, kode ini dibuat dengan PHP (backend) + JavaScript (frontend) — kombinasi standar untuk membuat halaman seperti ini interaktif. Kalau yang dimaksud betulan bahasa Java (misalnya untuk backend Spring Boot atau aplikasi Android), beri tahu saja dan saya buatkan versi itu.

## Struktur folder

```
zakaai-dashboard/
├── index.php                 # Halaman dashboard utama (render awal dari PHP)
├── includes/
│   ├── functions.php         # Helper: baca/tulis data, format rupiah, hitung nisab & zakat
│   ├── sidebar.php           # Partial sidebar navigasi
│   └── header.php            # Partial header (search AI, notifikasi, profil)
├── api/
│   ├── get_chart.php         # GET  -> data grafik sesuai periode (6bulan/3bulan/1tahun)
│   ├── add_asset.php         # POST -> tambah harta baru, update total & riwayat
│   └── ai_ask.php            # POST -> jawaban asisten AI (rule-based, siap diganti LLM asli)
├── data/
│   └── store.json            # "Database" sederhana berbasis file JSON
└── assets/
    ├── css/style.css         # Styling sesuai desain (sidebar hijau tua, kartu, badge, dsb)
    └── js/app.js              # Semua interaktivitas: chart, modal, dropdown, chat AI
```

## Fitur interaktif yang sudah jalan

1. **Grafik "Tren Pertumbuhan Harta"** — dropdown periode (6 bulan / 3 bulan / 1 tahun) memanggil `api/get_chart.php` via `fetch()` dan menggambar ulang chart dengan Chart.js, tanpa reload halaman.
2. **Tombol "Catat Harta Baru"** — membuka modal, submit form via `fetch()` ke `api/add_asset.php`, lalu:
   - Menyimpan entri baru ke `data/store.json`
   - Menambahkan baris baru di "Riwayat Terbaru" secara langsung (dengan animasi highlight)
   - Memperbarui angka di kartu "Total Perhitungan", "Estimasi Zakat", dan "Status Nisab" secara real-time
3. **Search bar "Tanya AI tentang Zakat Maal..."** dan **tombol chat mengambang** — mengirim pertanyaan ke `api/ai_ask.php`, jawaban muncul di panel chat.
4. **Dropdown notifikasi** — toggle buka/tutup, otomatis tertutup saat klik di luar area.
5. **Sidebar** — highlight menu aktif saat diklik.

## Cara menjalankan

Butuh PHP 8+ terpasang di komputer kamu.

```bash
cd zakaai-dashboard
php -S localhost:8000
```

Lalu buka `http://localhost:8000` di browser.

## Menghubungkan ke AI sungguhan

`api/ai_ask.php` saat ini memakai jawaban rule-based sederhana (placeholder) supaya project bisa langsung dicoba tanpa API key. Untuk jawaban AI yang sesungguhnya, ganti bagian di dalam `ai_ask.php` dengan pemanggilan ke API model bahasa pilihanmu (misalnya Claude API), kirimkan `$pertanyaan` beserta data finansial pengguna sebagai konteks, lalu kembalikan hasilnya sebagai `jawaban`.

## Menghubungkan ke database sungguhan

Saat ini data disimpan di `data/store.json` (cocok untuk prototipe/demo). Untuk produksi, ganti isi `includes/functions.php` (`store_read()` dan `store_write()`) dengan query ke MySQL/PostgreSQL menggunakan PDO, tanpa perlu mengubah struktur `index.php` atau file API lainnya.
