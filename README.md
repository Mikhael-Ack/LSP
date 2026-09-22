# Dapur Ina Aina

Sistem Point of Sales (POS) & Manajemen Operasional Restoran Terintegrasi.  
Dikembangkan untuk Uji Kompetensi Keahlian (UKK) Rekayasa Perangkat Lunak.

![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v4.0-38B2AC?style=flat-square&logo=tailwind-css&logoColor=white)
![Midtrans](https://img.shields.io/badge/Midtrans-Snap_SDK-0052CC?style=flat-square&logo=cashapp&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=flat-square&logo=mysql&logoColor=white)
![Playwright](https://img.shields.io/badge/Playwright-29_E2E_Passed-2EAD33?style=flat-square&logo=playwright&logoColor=white)

---

## Ringkasan Proyek

**Dapur Ina Aina** adalah sistem informasi manajemen restoran dan kasir berbasis web yang dirancang untuk mengelola seluruh rantai operasional rumah makan secara terpadu. Sistem ini mencakup etalase katalog menu publik, manajemen pemesanan meja dan Point of Sales (POS), pelunasan kasir multi-metode (Tunai dan Non-Tunai Midtrans Payment Gateway), pelacakan inventaris stok bahan secara real-time, serta pelaporan keuangan omzet eksekutif siap cetak bagi manajemen restoran.

Sistem dibangun di atas arsitektur MVC (Model-View-Controller) dengan Laravel 11, antarmuka modern responsif menggunakan Tailwind CSS, dan diverifikasi menggunakan pengujian otomatis End-to-End (E2E) berbasis Playwright.

---

## Fitur Utama

### 1. Katalog Menu & Landing Page Publik
* Etalase menu interaktif dengan filter kategori (Makanan Utama, Camilan, Minuman, Paket Hemat).
* Desain responsif di seluruh resolusi layar (desktop, tablet, mobile).
* Navigasi langsung menuju modul autentikasi petugas.

### 2. Autentikasi & Kontrol Akses Berbasis Peran (RBAC)
* **Kasir (`kasir`)**: Menangani transaksi pemesanan meja, penerbitan tagihan, pelunasan pembayaran nota, serta manajemen penambahan menu dan stok.
* **Administrator (`admin`)**: Akses penuh ke seluruh modul, monitoring inventaris stok, serta rekapitulasi laporan penjualan dan omzet.

### 3. Point of Sales (POS) Kasir & Validasi Terintegrasi
* Antarmuka pemilihan menu terstruktur berbasis kartu katalog dan keranjang belanja dinamis.
* Validasi pemesanan kondisional in-app (mencegah penerbitan tagihan jika keranjang kosong atau nomor meja belum terisi).
* Perhitungan otomatis subtotal pesanan dan Pajak Restoran (PB1 10%).
* Penerbitan nomor referensi tagihan unik (`BILL-YYYYMMDD-XXXX`).

### 4. Pelunasan Pembayaran (Multi-Method Checkout)
* **Metode Tunai (Cash)**:
  * Input nominal uang tunai yang diterima dengan tombol denominasi cepat (Uang Pas, 50K, 100K, 200K).
  * Perhitungan uang kembalian instan dan validasi nominal minimum.
* **Metode Non-Tunai (Midtrans Snap SDK)**:
  * Integrasi resmi pop-up modal `window.snap.pay`.
  * Mendukung Virtual Account (BCA, BNI, BRI, Permata, Mandiri Bill), QRIS (GoPay/ShopeePay), dan E-Wallet.
  * Auto-polling status transaksi dan auto-redirect ke nota struk resmi saat pelunasan terkonfirmasi.

### 5. Cetak Nota Struk Pembayaran
* Format struk standar kasir kasual siap cetak langsung (Thermal Receipt Printer).
* Rincian nomor meja, kasir bertugas, item pesanan, pajak PB1, uang dibayar, dan kembalian.

### 6. Kontrol Stok Realtime & Manajemen Menu
* Form modal penambahan dan pengeditan menu (nama, kategori, harga, stok awal, status, deskripsi).
* Pengurangan stok fisik otomatis saat pesanan dinyatakan lunas.
* Penyesuaian status ketersediaan menjadi `Habis` secara otomatis jika stok mencapai 0.

### 7. Laporan Penjualan Eksekutif
* Format laporan akuntansi resmi dengan Kop Surat Restoran Dapur Ina Aina.
* 4 kartu ringkasan eksekutif: Total Omzet Penjualan, Penerimaan Tunai, Penerimaan Non-Tunai, dan Estimasi Setoran Pajak PB1 10%.
* Tabel transaksi berformat presisi tinggi (`whitespace-nowrap`) tanpa teks terpotong.
* Lembar pengesahan tanda tangan Manajer Operasional dan Petugas Kasir pada mode cetak dokumen (`Ctrl + P`).

---

## Struktur Direktori Proyek

```plaintext
Project/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AdminController.php      # Controller laporan eksekutif & kelola stok menu
│   │   │   ├── AuthController.php       # Controller login, logout, & role switching
│   │   │   ├── Controller.php           # Base controller
│   │   │   └── PosController.php        # Controller kasir POS, validasi tagihan, & pembayaran
│   │   └── Middleware/
│   │       ├── RestoAdminMiddleware.php # Proteksi rute khusus Administrator
│   │       └── RestoAuthMiddleware.php  # Proteksi rute kasir & admin terautentikasi
│   ├── Models/
│   │   ├── Billing.php                  # Model tagihan dan grand total
│   │   ├── DetailPesanan.php            # Model rincian item pesanan
│   │   ├── Kategori.php                 # Model relasi kategori menu
│   │   ├── Pembayaran.php               # Model pelunasan transaksi dan metode bayar
│   │   ├── Pesanan.php                  # Model pesanan meja pelanggan
│   │   ├── Produk.php                   # Model produk menu dan stok fisik
│   │   └── User.php                     # Model akun petugas dan peran
│   └── Services/
│       └── MidtransService.php          # Integrasi Midtrans Snap SDK & Sandbox API
├── config/
│   ├── midtrans.php                     # Konfigurasi Server Key, Client Key, & Environment Midtrans
│   └── ...
├── database/
│   ├── migrations/                      # 7 file skema migrasi tabel database
│   ├── seeders/
│   │   ├── DatabaseSeeder.php           # Seeder database utama
│   │   └── RestoSeeder.php              # Seeder data awal akun dan katalog menu
│   └── database_dapur_ina_aina.sql      # Dump SQL siap import
├── resources/
│   └── views/
│       ├── admin/
│       │   ├── laporan.blade.php        # Halaman laporan omzet akuntansi eksekutif
│       │   └── stok.blade.php           # Halaman manajemen katalog menu & stok
│       ├── auth/
│       │   └── login.blade.php          # Halaman autentikasi petugas
│       ├── components/
│       │   └── icon.blade.php           # Komponen SVG UI modular
│       ├── layouts/
│       │   └── app.blade.php            # Master layout template antarmuka
│       ├── pos/
│       │   ├── billing.blade.php        # Modul pembayaran, pop-up Snap, & nota struk
│       │   └── index.blade.php          # Modul kasir POS katalog & validasi keranjang
│       └── welcome.blade.php            # Landing page publik interaktif
├── routes/
│   └── web.php                          # Deklarasi seluruh endpoint routing aplikasi
├── tests/
│   └── e2e/
│       └── tests_e2e_playwright.cjs     # Suite pengujian otomatis E2E Playwright
├── tests_e2e_playwright.cjs             # Runner pengujian otomatis root
├── .env.example                         # Template konfigurasi environment
├── package.json                         # Dependensi Vite, Tailwind, & script runner
└── README.md                            # Dokumentasi teknis proyek
```

---

## Persyaratan Sistem

* **PHP** >= 8.2 (ekstensi aktif: `pdo_mysql`, `mbstring`, `openssl`, `curl`)
* **Composer** >= 2.0
* **MySQL / MariaDB** (Port default: `3306`)
* **Node.js** >= 18.x & **NPM**

---

## Panduan Instalasi

### 1. Clone Repository
```bash
git clone https://github.com/Mikhael-Ack/LSP.git
cd LSP
```

### 2. Instalasi Dependensi
```bash
composer install
npm install
```

### 3. Konfigurasi Environment
Salin file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
Sesuaikan konfigurasi database dan kredensial Midtrans di dalam `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_dapur_ina_aina
DB_USERNAME=root
DB_PASSWORD=

# Kredensial Midtrans Sandbox
MIDTRANS_SERVER_KEY=SB-Mid-server-DemoRestoInaAina2026
MIDTRANS_CLIENT_KEY=SB-Mid-client-DemoRestoInaAina2026
MIDTRANS_IS_PRODUCTION=false
```

Generate application encryption key:
```bash
php artisan key:generate
```

### 4. Migrasi & Seeding Database
Pastikan layanan database MySQL telah aktif, lalu jalankan:
```bash
php artisan migrate --seed
```
*Catatan:* Tersedia juga file dump `database/database_dapur_ina_aina.sql` yang dapat langsung diimpor melalui phpMyAdmin / MySQL CLI.

### 5. Menjalankan Server Aplikasi
Jalankan compiler aset frontend dan HTTP server pada terminal terpisah:

**Terminal 1 (Vite Asset Bundler):**
```bash
npm run dev
```

**Terminal 2 (Laravel Development Server):**
```bash
php artisan serve
```

Akses sistem pada peramban: `http://127.0.0.1:8000`

---

## Akun Pengguna Bawaan (Default Credentials)

| Peran (Role) | Username | Password | Ruang Lingkup Hak Akses |
|---|---|---|---|
| **Administrator** | `admin` | `admin123` | Akses POS, Billing, Manajemen Menu/Stok, Laporan Rekapitulasi Omzet |
| **Kasir** | `kasir` | `kasir123` | Akses POS, Billing, Pelunasan Transaksi, Tambah Menu Baru & Stok |

---

## Pengujian Otomatis (Automated E2E Testing)

Sistem telah dilengkapi dengan test suite Playwright otomatis mencakup **29 skenario pengujian E2E**:
* Autentikasi petugas Kasir & Administrator
* Validasi modal in-app keranjang kosong dan nomor meja kosong di POS
* Pemesanan menu, perhitungan pajak PB1 10%, dan pelunasan Tunai
* Pemesanan dan integrasi pembayaran Non-Tunai Midtrans Snap Sandbox
* Otorisasi kasir dalam manajemen katalog menu dan sinkronisasi stok realtime
* Operasi CRUD penuh Administrator (tambah, perbarui harga/stok, hapus menu)
* Rekapitulasi laporan penjualan dan statistik omzet Tunai vs Non-Tunai

Eksekusi pengujian dengan perintah:
```bash
npm test
```
atau:
```bash
npm run test:e2e
```

---

## Lisensi
Proyek ini dikembangkan untuk kebutuhan penilaian Uji Kompetensi Keahlian (UKK) Rekayasa Perangkat Lunak dan dilisensikan di bawah [MIT License](LICENSE).
