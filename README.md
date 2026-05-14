# ⚡ KasirPRO — Sistem Kasir Modern

Aplikasi Point of Sale (POS) modern berbasis web yang siap pakai untuk usaha makanan, retail, dan toko umum.

---

## 🚀 Fitur Lengkap

- ✅ **Kasir (POS)** — Antarmuka kasir cepat dengan grid produk & keranjang real-time
- ✅ **Manajemen Produk** — Tambah, edit, hapus produk dengan kategori
- ✅ **Riwayat Transaksi** — Lihat semua transaksi + export CSV
- ✅ **Dashboard & Laporan** — Grafik penjualan, statistik omzet, produk terlaris
- ✅ **Cetak Struk** — Print receipt langsung dari browser
- ✅ **Numpad Digital** — Input pembayaran mudah tanpa keyboard
- ✅ **4 Metode Bayar** — Tunai, Debit, QRIS, Transfer
- ✅ **Kelola Stok** — Stok otomatis berkurang setiap transaksi
- ✅ **Pajak Otomatis** — Konfigurasi pajak (default 11% PPN)
- ✅ **Simpan ke MySQL** — Backend PHP siap pakai

---

## 📁 Struktur File

```
kasir-pro/
├── index.html      ← Frontend utama (buka di browser)
├── api.php         ← Backend API PHP + MySQL
├── database.sql    ← Skema database MySQL
└── README.md       ← Panduan ini
```

---

## ⚙️ Cara Install

### Mode Demo (Tanpa Server)
Cukup buka `index.html` di browser — langsung jalan!

### Mode Produksi (Dengan MySQL)

#### 1. Persyaratan Server
- PHP 8.0+
- MySQL 5.7+ / MariaDB 10.3+
- Web server (Apache / Nginx / XAMPP / Laragon)

#### 2. Setup Database
```sql
-- Buka phpMyAdmin atau MySQL CLI, jalankan:
source /path/to/database.sql
```

#### 3. Konfigurasi Backend
Edit file `api.php`, sesuaikan:
```php
define('DB_HOST', 'localhost');   // Host database
define('DB_USER', 'root');        // Username MySQL
define('DB_PASS', '');            // Password MySQL
define('DB_NAME', 'kasirpro');    // Nama database
```

#### 4. Hubungkan Frontend ke Backend
Di `index.html`, ganti konstanta API_URL:
```javascript
const API_URL = 'http://localhost/kasir-pro/api.php';
```

Kemudian uncomment baris fetch di fungsi `init()`, `prosesBayar()`, dll.

#### 5. Akses Aplikasi
```
http://localhost/kasir-pro/index.html
```

---

## 🔗 Endpoint API

| Method | URL | Keterangan |
|--------|-----|------------|
| GET | `api.php?action=produk` | Daftar produk |
| POST | `api.php?action=produk` | Tambah produk |
| PUT | `api.php?action=produk` | Edit produk |
| DELETE | `api.php?action=produk&id=1` | Hapus produk |
| GET | `api.php?action=kategori` | Daftar kategori |
| GET | `api.php?action=transaksi` | Riwayat transaksi |
| POST | `api.php?action=transaksi` | Buat transaksi baru |
| GET | `api.php?action=laporan` | Laporan penjualan |
| GET | `api.php?action=pengaturan` | Pengaturan toko |
| POST | `api.php?action=pengaturan` | Update pengaturan |

---

## 💡 Tips Penggunaan

- **Scan Barcode**: Hubungkan scanner barcode USB, otomatis terdeteksi di kolom pencarian
- **Shortcut**: Gunakan numpad di panel pembayaran untuk input cepat
- **Export**: Klik tombol "Export CSV" di menu Riwayat untuk laporan spreadsheet
- **Struk**: Sambungkan printer thermal 58mm/80mm via USB untuk cetak struk

---

## 🎨 Kustomisasi

Ubah nama toko dan warna di bagian `:root` CSS:
```css
:root {
  --accent: #f5a623;    /* Warna utama */
  --accent2: #ff6b35;   /* Warna aksen */
}
```

---

## 📄 Lisensi

Bebas digunakan untuk keperluan komersial maupun pribadi.
Dibuat dengan ❤️ menggunakan HTML, CSS, JavaScript & PHP.

---

*KasirPRO v1.0 — Sistem Kasir Modern untuk UMKM Indonesia*
