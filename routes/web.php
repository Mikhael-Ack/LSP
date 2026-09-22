<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PosController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes — Sistem Restoran Dapur Ina Aina
|--------------------------------------------------------------------------
| Mengadopsi pola JMS: Landing page pada root '/', autentikasi sebelum masuk,
| dan proteksi route berbasis role (Kasir & Administrator).
*/

// 1. Landing Page Publik
Route::get('/', function () {
    $kategoriList = \App\Models\Kategori::with('produk')->get();
    return view('welcome', compact('kategoriList'));
})->name('landing');

// 2. Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/switch-role/{role}', [AuthController::class, 'quickLogin'])->name('switch.role');

// 3. Fitur Kasir & Pemesanan (Diproteksi: Harus Login)
Route::prefix('kasir')->name('pos.')->middleware('resto.auth')->group(function () {
    Route::get('/', [PosController::class, 'index'])->name('index');
    Route::post('/pesan', [PosController::class, 'simpanPesanan'])->name('pesan');
    Route::get('/billing/{id}', [PosController::class, 'billing'])->name('billing');
    Route::get('/billing/{id}/va', [PosController::class, 'getVa'])->name('va');
    Route::get('/billing/{id}/cek-status', [PosController::class, 'cekStatusMidtrans'])->name('cekStatus');
    Route::post('/billing/{id}/snap-token', [PosController::class, 'getSnapToken'])->name('snapToken');
    Route::post('/billing/{id}/bayar', [PosController::class, 'bayar'])->name('bayar');
});

// 4. Fitur Menu & Manajemen Stok (Bisa Diakses Kasir & Administrator)
Route::middleware('resto.auth')->group(function () {
    Route::get('/admin/stok', [AdminController::class, 'stok'])->name('admin.stok');
    Route::post('/admin/stok/{id}', [AdminController::class, 'updateStok'])->name('admin.stok.update');
    Route::post('/admin/produk', [AdminController::class, 'storeProduk'])->name('admin.produk.store');
    Route::put('/admin/produk/{id}', [AdminController::class, 'updateProduk'])->name('admin.produk.update');
    Route::delete('/admin/produk/{id}', [AdminController::class, 'destroyProduk'])->name('admin.produk.destroy');
});

// 5. Fitur Administrator (Diproteksi: Khusus Role Admin)
Route::prefix('admin')->name('admin.')->middleware('resto.admin')->group(function () {
    Route::get('/laporan', [AdminController::class, 'laporan'])->name('laporan');
});
