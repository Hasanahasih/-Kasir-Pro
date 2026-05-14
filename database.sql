-- ============================================
-- KasirPRO - Database Schema MySQL
-- ============================================

CREATE DATABASE IF NOT EXISTS kasirpro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE kasirpro;

-- Tabel Kategori
CREATE TABLE kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    icon VARCHAR(50) DEFAULT '🏷️',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Produk
CREATE TABLE produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kategori_id INT,
    nama VARCHAR(200) NOT NULL,
    harga DECIMAL(12,2) NOT NULL,
    stok INT DEFAULT 0,
    gambar VARCHAR(255),
    barcode VARCHAR(100),
    aktif TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id)
);

-- Tabel Pelanggan
CREATE TABLE pelanggan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(200) NOT NULL,
    telepon VARCHAR(20),
    email VARCHAR(100),
    alamat TEXT,
    poin INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Transaksi
CREATE TABLE transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor_transaksi VARCHAR(50) UNIQUE NOT NULL,
    pelanggan_id INT,
    kasir_id INT,
    subtotal DECIMAL(12,2) NOT NULL,
    diskon DECIMAL(12,2) DEFAULT 0,
    pajak DECIMAL(12,2) DEFAULT 0,
    total DECIMAL(12,2) NOT NULL,
    bayar DECIMAL(12,2) NOT NULL,
    kembalian DECIMAL(12,2) DEFAULT 0,
    metode_bayar ENUM('tunai','debit','kredit','qris','transfer') DEFAULT 'tunai',
    status ENUM('selesai','batal','pending') DEFAULT 'selesai',
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pelanggan_id) REFERENCES pelanggan(id)
);

-- Tabel Detail Transaksi
CREATE TABLE detail_transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaksi_id INT NOT NULL,
    produk_id INT NOT NULL,
    nama_produk VARCHAR(200) NOT NULL,
    harga DECIMAL(12,2) NOT NULL,
    qty INT NOT NULL,
    diskon DECIMAL(12,2) DEFAULT 0,
    subtotal DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (transaksi_id) REFERENCES transaksi(id),
    FOREIGN KEY (produk_id) REFERENCES produk(id)
);

-- Tabel Pengguna / Kasir
CREATE TABLE pengguna (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(200) NOT NULL,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','kasir','supervisor') DEFAULT 'kasir',
    aktif TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Pengaturan Toko
CREATE TABLE pengaturan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kunci VARCHAR(100) UNIQUE NOT NULL,
    nilai TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ============================================
-- DATA AWAL (SEED DATA)
-- ============================================

INSERT INTO kategori (nama, icon) VALUES
('Makanan', '🍔'),
('Minuman', '🥤'),
('Snack', '🍿'),
('Dessert', '🍰'),
('Paket', '📦');

INSERT INTO pengguna (nama, username, password, role) VALUES
('Administrator', 'admin', '$2y$10$hashed_password_here', 'admin'),
('Kasir 1', 'kasir1', '$2y$10$hashed_password_here', 'kasir');

INSERT INTO pengaturan (kunci, nilai) VALUES
('nama_toko', 'KasirPRO Store'),
('alamat_toko', 'Jl. Contoh No. 123, Jakarta'),
('telepon_toko', '021-12345678'),
('pajak_persen', '11'),
('mata_uang', 'IDR'),
('struk_footer', 'Terima kasih telah berbelanja!'),
('logo_toko', '');

INSERT INTO produk (kategori_id, nama, harga, stok) VALUES
(1, 'Nasi Goreng Special', 35000, 100),
(1, 'Mie Goreng Seafood', 38000, 100),
(1, 'Ayam Bakar', 45000, 50),
(1, 'Soto Ayam', 28000, 80),
(2, 'Es Teh Manis', 8000, 200),
(2, 'Jus Alpukat', 18000, 100),
(2, 'Kopi Hitam', 12000, 150),
(2, 'Mineral Water', 5000, 300),
(3, 'Keripik Singkong', 12000, 100),
(3, 'Pisang Goreng', 15000, 80),
(4, 'Es Krim Coklat', 20000, 60),
(4, 'Pudding Susu', 15000, 70);
