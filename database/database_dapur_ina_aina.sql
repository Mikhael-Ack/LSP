-- ==========================================================
-- DATABASE: db_dapur_ina_aina
-- Sistem POS & Manajemen Operasional Restoran "Dapur Ina Aina"
-- Uji Kompetensi Keahlian (UKK) Rekayasa Perangkat Lunak
-- ==========================================================

CREATE DATABASE IF NOT EXISTS db_dapur_ina_aina;
USE db_dapur_ina_aina;

-- ----------------------------------------------------------
-- 1. TABEL: users
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'kasir') NOT NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ----------------------------------------------------------
-- 2. TABEL: kategori
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS kategori (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ----------------------------------------------------------
-- 3. TABEL: produk
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS produk (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    kategori_id INT(11) NOT NULL,
    nama_produk VARCHAR(100) NOT NULL,
    harga INT(11) NOT NULL,
    stok INT(11) NOT NULL DEFAULT 0,
    status ENUM('Tersedia', 'Habis') NOT NULL DEFAULT 'Tersedia',
    deskripsi TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ----------------------------------------------------------
-- 4. TABEL: pesanan
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS pesanan (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    no_pesanan VARCHAR(20) NOT NULL UNIQUE,
    no_meja VARCHAR(10) NOT NULL,
    tanggal DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status_pesanan ENUM('Draft', 'Billed', 'Paid', 'Cancelled') NOT NULL DEFAULT 'Draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ----------------------------------------------------------
-- 5. TABEL: detail_pesanan
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS detail_pesanan (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    pesanan_id INT(11) NOT NULL,
    produk_id INT(11) NOT NULL,
    jumlah INT(11) NOT NULL,
    harga_satuan INT(11) NOT NULL,
    subtotal INT(11) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON DELETE CASCADE,
    FOREIGN KEY (produk_id) REFERENCES produk(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ----------------------------------------------------------
-- 6. TABEL: billing
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS billing (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    pesanan_id INT(11) NOT NULL UNIQUE,
    no_tagihan VARCHAR(30) NOT NULL UNIQUE,
    total_bayar INT(11) NOT NULL,
    pajak INT(11) NOT NULL,
    grand_total INT(11) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ----------------------------------------------------------
-- 7. TABEL: pembayaran
-- Mendukung 2 metode utama: Tunai & Non-Tunai (Midtrans Sandbox)
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS pembayaran (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    billing_id INT(11) NOT NULL UNIQUE,
    metode VARCHAR(30) NOT NULL DEFAULT 'Tunai',
    uang_dibayar INT(11) NOT NULL,
    kembalian INT(11) NOT NULL DEFAULT 0,
    no_referensi VARCHAR(50) NULL,
    tanggal_bayar TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (billing_id) REFERENCES billing(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ==========================================================
-- DATA AWAL CONTOH (USERS & SEED MENU)
-- ==========================================================

-- Akun Petugas Default
INSERT INTO users (id, nama, username, password, role) VALUES 
(1, 'Administrator Ina', 'admin', 'admin123', 'admin'),
(2, 'Kasir Aina', 'kasir', 'kasir123', 'kasir')
ON DUPLICATE KEY UPDATE nama=VALUES(nama);

-- Kategori Menu
INSERT INTO kategori (id, nama_kategori) VALUES 
(1, 'Makanan Utama'),
(2, 'Appetizer & Camilan'),
(3, 'Minuman')
ON DUPLICATE KEY UPDATE nama_kategori=VALUES(nama_kategori);

-- Data Produk
INSERT INTO produk (id, kategori_id, nama_produk, harga, stok, status, deskripsi) VALUES 
(1, 1, 'Nasi Liwet Komplit Dapur Ina', 35000, 30, 'Tersedia', 'Nasi liwet wangi khas Sunda disajikan dengan ayam goreng, tahu, tempe, lalapan dan sambal terasi.'),
(2, 1, 'Ayam Bakar Madu Spesial', 28000, 25, 'Tersedia', 'Ayam bakar dengan lumuran madu manis gurih meresap sampai ke tulang.'),
(3, 1, 'Bebek Goreng Kremes', 38000, 20, 'Tersedia', 'Bebek empuk bumbu rempah dengan kremesan garing renyah.'),
(4, 2, 'Tahu Gejrot Cirebon', 12000, 40, 'Tersedia', 'Tahu pong garing dengan kuah asam manis pedas.'),
(5, 2, 'Tempe Mendoan Anget', 15000, 50, 'Tersedia', 'Tempe mendoan disajikan dengan kecap rawit pedas.'),
(6, 3, 'Es Teh Manis Melati', 6000, 100, 'Tersedia', 'Teh melati seduh segar dengan gula tebu asli.'),
(7, 3, 'Es Jeruk Peras Segar', 10000, 50, 'Tersedia', 'Jeruk peras alami segar dingin.'),
(8, 3, 'Kopi Susu Gula Aren', 15000, 40, 'Tersedia', 'Espresso dengan susu segar dan gula aren alami.')
ON DUPLICATE KEY UPDATE nama_produk=VALUES(nama_produk);
