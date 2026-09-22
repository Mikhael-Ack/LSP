<p align="center">
  <h1 align="center">🍽️ Dapur Ina Aina</h1>
  <p align="center">
    <strong>Sistem Point of Sales (POS) & Manajemen Operasional Restoran Terintegrasi</strong>
    <br />
    <em>Uji Kompetensi Keahlian (UKK) Rekayasa Perangkat Lunak</em>
  </p>
  <p align="center">
    <img src="https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 11" />
    <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2" />
    <img src="https://img.shields.io/badge/Tailwind_CSS-v4.0-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS" />
    <img src="https://img.shields.io/badge/Midtrans-Sandbox-0052CC?style=for-the-badge&logo=cashapp&logoColor=white" alt="Midtrans Sandbox" />
    <img src="https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL" />
    <img src="https://img.shields.io/badge/Playwright-E2E_Passed-2EAD33?style=for-the-badge&logo=playwright&logoColor=white" alt="Playwright E2E" />
  </p>
</p>

---

## 📌 Tentang Proyek

**Dapur Ina Aina** adalah aplikasi web manajemen restoran dan kasir modern yang dirancang untuk mengoptimalkan operasional rumah makan mulai dari etalase menu digital, pemesanan meja, pelunasan kasir (POS), integrasi pembayaran digital Midtrans Sandbox, kontrol inventaris stok bahan/menu secara otomatis, hingga laporan rekap omzet penjualan harian bagi administrator.

Aplikasi ini dibangun menggunakan arsitektur **MVC (Model-View-Controller)** dengan framework **Laravel 11**, antarmuka modern bernuansa *dark theme premium* menggunakan **Tailwind CSS**, serta pengujian otomatis *End-to-End (E2E)* berbasis **Playwright**.

---

## ✨ Fitur-Fitur Utama

### 1. 🏠 Landing Page Interaktif & Responsif
* Tampilan etalase menu modern ramah pengguna dengan transisi animasi halus.
* Filter kategori menu secara interaktif (*Makanan Utama, Camilan, Minuman, Paket Hemat*).
* Desain responsif di layar dekstop, tablet, maupun perangkat mobile smartphone.

### 2. 🔐 Autentikasi & Otorisasi Berbasis Peran (*Role-Based Access Control*)
* **Kasir (`kasir`)**: Akses POS kasir, input pesanan meja, penerbitan tagihan (billing), pelunasan nota, dan penambahan/manajemen menu & stok.
* **Administrator (`admin`)**: Akses penuh ke seluruh sistem operasional restoran, monitoring kelola stok, dan laporan rekap omzet penjualan.

### 3. 🛒 Point of Sales (POS) Kasir
* Pemilihan menu cepat berbasis katalog dengan visual foto & harga.
* Input nomor meja dan perhitungan subtotal + Pajak PB1 (10%) secara otomatis.
* Pencatatan pesanan ke database dan penerbitan nomor tagihan unik (`BILL-YYYYMMDD-XXXX`).

### 4. 💳 Pelunasan Tagihan (2 Metode Pembayaran)
* **1. Tunai (Cash)**:
  * Input nominal uang tunai yang diterima.
  * Tombol denominasi cepat (*Uang Pas, 50K, 100K, 200K*).
  * Perhitungan uang kembalian otomatis & validasi nominal tidak boleh kurang.
* **2. Non-Tunai (Midtrans Sandbox Simulator)**:
  * Menampilkan **Nomor Virtual Account (BCA VA)** secara jelas & mencolok dengan tombol **1-Klik Salin Nomor VA**.
  * Tautan langsung ke [Midtrans BCA VA Simulator](https://simulator.sandbox.midtrans.com/bca/va/index).
  * Opsi saluran pembayaran lain (*QRIS GoPay/ShopeePay, BNI VA, BRI VA*).
  * Tombol konfirmasi lunas instan dengan pencatatan kode referensi transaksi.

### 5. 🧾 Cetak Struk / Nota Pembayaran
* Format struk kasir kasual yang siap cetak langsung (*Thermal Receipt Printer*).
* Rincian menu, kuantitas, harga, pajak PB1 10%, uang dibayar, dan uang kembalian.

### 6. 📦 Manajemen Menu & Kontrol Stok Realtime
* Tambah menu baru dengan form modal terpadu (kategori, nama, harga, stok awal, deskripsi).
* Update stok menu cepat & otomatis memotong stok saat pesanan lunas.
* Pengubahan status menu otomatis menjadi `Habis` jika stok mencapai 0.

### 7. 📊 Laporan Penjualan & Omzet Administrator
* Ringkasan total omzet kotor, omzet bersih, dan rekap setoran Pajak PB1 (10%).
* Pemisahan statistik omzet antara transaksi **Tunai** vs **Non-Tunai**.
* Tabel riwayat transaksi lengkap dengan status lunas dan tanggal transaksi.

---

## 📂 Struktur Direktori Proyek

```plaintext
Project/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AdminController.php      # Controller laporan & kelola menu/stok
│   │   │   ├── AuthController.php       # Controller autentikasi & login
│   │   │   ├── Controller.php           # Base controller
│   │   │   └── PosController.php        # Controller kasir POS, billing, & bayar
│   │   └── Middleware/
│   │       ├── RestoAdminMiddleware.php # Proteksi route khusus Administrator
│   │       └── RestoAuthMiddleware.php  # Proteksi route login kasir & admin
│   ├── Models/
│   │   ├── Billing.php                  # Model tagihan billing
│   │   ├── DetailPesanan.php            # Model item detail pesanan
│   │   ├── Kategori.php                 # Model kategori menu
│   │   ├── Pembayaran.php               # Model pelunasan transaksi
│   │   ├── Pesanan.php                  # Model pesanan & meja
│   │   ├── Produk.php                   # Model produk menu & stok
│   │   └── User.php                     # Model pengguna petugas
│   └── Services/
│       └── MidtransService.php          # Integrasi Midtrans Snap & Sandbox Core API
├── config/
│   ├── midtrans.php                     # Konfigurasi Midtrans Server & Client Key
│   └── ...
├── database/
│   ├── migrations/                      # 7 File skema migrasi database lengkap
│   ├── seeders/
│   │   ├── DatabaseSeeder.php           # Seeder utama
│   │   └── RestoSeeder.php              # Seeder akun bawaan & data menu
│   └── database_dapur_ina_aina.sql      # Dump file SQL siap import
├── resources/
│   └── views/
│       ├── admin/
│       │   ├── laporan.blade.php        # Halaman laporan omzet admin
│       │   └── stok.blade.php           # Halaman kelola menu & stok
│       ├── auth/
│       │   └── login.blade.php          # Halaman masuk petugas
│       ├── components/                  # Blade icons & widget reusable
│       ├── layouts/
│       │   └── app.blade.php            # Layout induk tema gelap responsif
│       ├── pos/
│       │   ├── billing.blade.php        # Halaman pelunasan billing & Midtrans VA
│       │   └── index.blade.php          # Halaman kasir POS transaksi
│       └── welcome.blade.php            # Landing page publik interaktif
├── routes/
│   └── web.php                          # Rute URL aplikasi
├── tests/
│   └── e2e/
│       └── tests_e2e_playwright.cjs     # Pengujian E2E otomatis Playwright
├── .env.example                         # Contoh konfigurasi environment
├── package.json                         # Dependensi Vite, Tailwind & script test
└── README.md                            # Dokumentasi lengkap proyek
```

---

## ⚙️ Persyaratan Sistem (*Prerequisites*)

* **PHP** >= 8.2 (ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `curl`)
* **Composer** >= 2.0
* **MySQL / MariaDB** (Port default: `3306`)
* **Node.js** >= 18.x & **NPM**

---

## 🚀 Panduan Instalasi & Menjalankan Aplikasi

### 1. Clone Repository
```bash
git clone https://github.com/USERNAME/REPO_NAME.git
cd REPO_NAME
```

### 2. Pasang Dependensi PHP & Node.js
```bash
composer install
npm install
```

### 3. Konfigurasi Environment (`.env`)
Salin file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
Buka file `.env` dan sesuaikan pengaturan database serta Midtrans:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_dapur_ina_aina
DB_USERNAME=root
DB_PASSWORD=

# Kredensial Midtrans Sandbox (Opsional jika ingin menggunakan simulator)
MIDTRANS_SERVER_KEY=SB-Mid-server-DemoRestoInaAina2026
MIDTRANS_CLIENT_KEY=SB-Mid-client-DemoRestoInaAina2026
MIDTRANS_IS_PRODUCTION=false
```

Generate App Key:
```bash
php artisan key:generate
```

### 4. Setup Database & Seeding Data
Pastikan MySQL di XAMPP / MariaDB sudah berjalan dan buat database bernama `db_dapur_ina_aina`:
```bash
# Jalankan migrasi dan seeding akun & menu bawaan otomatis
php artisan migrate --seed
```
> *Alternatif:* Anda juga dapat mengimpor file `database/database_dapur_ina_aina.sql` langsung melalui phpMyAdmin.

### 5. Kompilasi Aset Frontend & Jalankan Server
Buka 2 tab terminal:

**Terminal 1 (Build / Dev Vite):**
```bash
npm run dev
# atau untuk build produksi: npm run build
```

**Terminal 2 (Server Laravel):**
```bash
php artisan serve
```

Akses aplikasi di browser: **[http://127.0.0.1:8000](http://127.0.0.1:8000)**

---

## 👥 Akun Pengguna Bawaan (*Default Credentials*)

| Peran (Role) | Username | Password | Hak Akses |
|---|---|---|---|
| **Administrator** | `admin` | `admin123` | Akses POS, Billing, Manajemen Stok, Laporan Omzet |
| **Kasir** | `kasir` | `kasir123` | Akses POS, Billing, Pelunasan Nota, Tambah Menu & Stok |

---

## 🧪 Pengujian Otomatis (*Automated E2E Testing*)

Aplikasi ini dilengkapi pengujian otomatis *End-to-End* berbasis Playwright yang memverifikasi 25 skenario kritis:
* Autentikasi Kasir & Admin
* Transaksi Kasir, Perhitungan PB1 10%, & Pelunasan Tunai
* Pelunasan Non-Tunai Midtrans Sandbox (Virtual Account BCA & Simulator)
* Hak Akses Kasir untuk Tambah Menu & Sinkronisasi Stok
* Administrator CRUD Menu, Hapus Menu, & Rekapitulasi Laporan Omzet

Untuk menjalankan pengujian:
```bash
npm run test:e2e
```

---

## 📝 Lisensi
Proyek ini dikembangkan untuk keperluan **Uji Kompetensi Keahlian (UKK) Rekayasa Perangkat Lunak** dan dilisensikan di bawah lisensi [MIT](LICENSE).
