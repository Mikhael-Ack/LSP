<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Kategori;
use App\Models\Produk;

class RestoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Bawaan (Admin & Kasir)
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'nama' => 'Administrator Resto',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        $kasir = User::firstOrCreate(
            ['username' => 'kasir'],
            [
                'nama' => 'Kasir Utama',
                'password' => Hash::make('kasir123'),
                'role' => 'kasir',
            ]
        );

        // 2. Kategori Menu
        $kategoriMakanan = Kategori::firstOrCreate(['id' => 1], ['nama_kategori' => 'Makanan Utama']);
        $kategoriCamilan = Kategori::firstOrCreate(['id' => 2], ['nama_kategori' => 'Makanan Ringan & Camilan']);
        $kategoriMinuman = Kategori::firstOrCreate(['id' => 3], ['nama_kategori' => 'Minuman']);
        $kategoriPaket   = Kategori::firstOrCreate(['id' => 4], ['nama_kategori' => 'Paket Hemat']);

        // 3. Data Menu Produk
        $produkList = [
            // Makanan Utama
            ['kategori_id' => $kategoriMakanan->id, 'nama_produk' => 'Nasi Liwet Komplit', 'harga' => 35000, 'stok' => 40, 'status' => 'Tersedia', 'deskripsi' => 'Nasi liwet wangi khas Sunda disajikan dengan ayam goreng, tahu, tempe, lalapan dan sambal terasi.'],
            ['kategori_id' => $kategoriMakanan->id, 'nama_produk' => 'Ayam Bakar Madu', 'harga' => 28000, 'stok' => 35, 'status' => 'Tersedia', 'deskripsi' => 'Ayam bakar dengan lumuran madu manis gurih meresap sampai ke tulang.'],
            ['kategori_id' => $kategoriMakanan->id, 'nama_produk' => 'Bebek Goreng Kremes', 'harga' => 38000, 'stok' => 25, 'status' => 'Tersedia', 'deskripsi' => 'Bebek goreng empuk berbumbu rempah dengan taburan kremes renyah dan sambal korek.'],
            ['kategori_id' => $kategoriMakanan->id, 'nama_produk' => 'Gurame Asam Manis', 'harga' => 55000, 'stok' => 15, 'status' => 'Tersedia', 'deskripsi' => 'Ikan gurame segar fillet goreng tepung disiram saus asam manis nanas segar.'],
            ['kategori_id' => $kategoriMakanan->id, 'nama_produk' => 'Sate Maranggi Sapi (10 Tusuk)', 'harga' => 45000, 'stok' => 30, 'status' => 'Tersedia', 'deskripsi' => 'Sate daging sapi empuk khas Purwakarta dengan cocolan sambal tomat kecap pedas segar.'],
            
            // Camilan
            ['kategori_id' => $kategoriCamilan->id, 'nama_produk' => 'Tempe Mendoan Anget (5 Pcs)', 'harga' => 15000, 'stok' => 50, 'status' => 'Tersedia', 'deskripsi' => 'Tempe daun lembut digoreng tepung setengah matang disajikan dengan sambal kecap rawit.'],
            ['kategori_id' => $kategoriCamilan->id, 'nama_produk' => 'Tahu Gejrot Cirebon', 'harga' => 12000, 'stok' => 40, 'status' => 'Tersedia', 'deskripsi' => 'Tahu pong garing dengan kuah asam manis pedas bawang khas Cirebon.'],
            ['kategori_id' => $kategoriCamilan->id, 'nama_produk' => 'Bakwan Sayur Udang (3 Pcs)', 'harga' => 14000, 'stok' => 35, 'status' => 'Tersedia', 'deskripsi' => 'Bakwan sayur gurih dengan topping udang renyah.'],

            // Minuman
            ['kategori_id' => $kategoriMinuman->id, 'nama_produk' => 'Es Teh Manis Melati', 'harga' => 6000, 'stok' => 100, 'status' => 'Tersedia', 'deskripsi' => 'Teh melati seduh segar dengan gula tebu asli dan es batu melimpah.'],
            ['kategori_id' => $kategoriMinuman->id, 'nama_produk' => 'Es Jeruk Peras Segar', 'harga' => 10000, 'stok' => 60, 'status' => 'Tersedia', 'deskripsi' => 'Jeruk peras murni segar kaya vitamin C.'],
            ['kategori_id' => $kategoriMinuman->id, 'nama_produk' => 'Es Kelapa Muda Jeruk', 'harga' => 15000, 'stok' => 30, 'status' => 'Tersedia', 'deskripsi' => 'Daging kelapa muda murni dipadukan dengan kesegaran air perasan jeruk.'],
            ['kategori_id' => $kategoriMinuman->id, 'nama_produk' => 'Kopi Tubruk Nusantara', 'harga' => 12000, 'stok' => 50, 'status' => 'Tersedia', 'deskripsi' => 'Kopi robusta sangrai tradisional diseduh panas dengan aroma memikat.'],

            // Paket Hemat
            ['kategori_id' => $kategoriPaket->id, 'nama_produk' => 'Paket Puas Nasi Liwet + Es Teh', 'harga' => 38000, 'stok' => 30, 'status' => 'Tersedia', 'deskripsi' => 'Kombinasi lengkap Nasi Liwet Komplit + Es Teh Manis Melati hemat dan kenyang.'],
            ['kategori_id' => $kategoriPaket->id, 'nama_produk' => 'Paket Ayam Bakar + Es Jeruk', 'harga' => 35000, 'stok' => 30, 'status' => 'Tersedia', 'deskripsi' => 'Ayam Bakar Madu + Nasi Putih + Es Jeruk Segar.'],
        ];

        foreach ($produkList as $p) {
            Produk::updateOrCreate(
                ['nama_produk' => $p['nama_produk']],
                $p
            );
        }
    }
}
