<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Billing;
use App\Models\Pembayaran;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    /**
     * Menampilkan antarmuka utama Point of Sale (POS) untuk Kasir.
     * Mengambil seluruh kategori dan daftar menu produk yang tersedia.
     */
    public function index()
    {
        $kategoriList = Kategori::all();
        $produkList = Produk::with('kategori')->get();
        return view('pos.index', compact('kategoriList', 'produkList'));
    }

    /**
     * Memproses penyimpanan pesanan baru per meja dan menerbitkan invoice billing.
     * Menggunakan DB::transaction untuk menjamin keutuhan data (ACID).
     */
    public function simpanPesanan(Request $request)
    {
        // 1. Validasi input: meja wajib diisi dan minimal ada 1 menu yang dipesan
        $request->validate([
            'no_meja' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:produk,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($request) {
            // Ambil akun kasir yang bertugas
            $kasir = User::where('role', 'kasir')->first() ?? User::first();

            $totalBayar = 0;
            $itemsData = [];

            // 2. Validasi ketersediaan stok fisik sebelum pesanan dicatat
            foreach ($request->items as $item) {
                // lockForUpdate mencegah race condition pembelian ganda secara bersamaan
                $produk = Produk::lockForUpdate()->find($item['id']);
                if ($produk->stok < $item['qty']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stok {$produk->nama_produk} tidak mencukupi! Tersisa: {$produk->stok}",
                    ], 422);
                }

                $subtotal = $produk->harga * $item['qty'];
                $totalBayar += $subtotal;

                $itemsData[] = [
                    'produk' => $produk,
                    'qty' => $item['qty'],
                    'harga' => $produk->harga,
                    'subtotal' => $subtotal,
                ];
            }

            // 3. Buat nomor transaksi pesanan unik (ORD-YYYYMMDD-XXXX)
            $noPesanan = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            // 4. Simpan header pesanan (Status awal: Billed)
            $pesanan = Pesanan::create([
                'user_id' => $kasir ? $kasir->id : 1,
                'no_pesanan' => $noPesanan,
                'no_meja' => $request->no_meja,
                'tanggal' => now(),
                'status_pesanan' => 'Billed',
            ]);

            // 5. Simpan rincian item ke tabel detail_pesanan
            foreach ($itemsData as $data) {
                DetailPesanan::create([
                    'pesanan_id' => $pesanan->id,
                    'produk_id' => $data['produk']->id,
                    'jumlah' => $data['qty'],
                    'harga_satuan' => $data['harga'],
                    'subtotal' => $data['subtotal'],
                ]);
            }

            // 6. Hitung Pajak Restoran PB1 (10% sesuai regulasi F&B)
            $pajak = (int) round($totalBayar * 0.10);
            $grandTotal = $totalBayar + $pajak;

            // 7. Terbitkan lembar tagihan resmi (Billing) sebelum pembayaran
            $noTagihan = 'INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
            $billing = Billing::create([
                'pesanan_id' => $pesanan->id,
                'no_tagihan' => $noTagihan,
                'total_bayar' => $totalBayar,
                'pajak' => $pajak,
                'grand_total' => $grandTotal,
                'created_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil disimpan & Billing diterbitkan!',
                'billing_id' => $billing->id,
                'pesanan_id' => $pesanan->id,
                'no_tagihan' => $noTagihan,
                'grand_total' => $grandTotal,
                'redirect' => route('pos.billing', $billing->id),
            ]);
        });
    }

    /**
     * Menampilkan halaman tagihan (Billing) meja dan memuat nomor VA awal (default BCA).
     */
    public function billing($id)
    {
        $billing = Billing::with(['pesanan.detail.produk', 'pembayaran'])->findOrFail($id);
        $vaData = \App\Services\MidtransService::createVaCharge($billing, 'bca');
        return view('pos.billing', compact('billing', 'vaData'));
    }

    /**
     * Endpoint AJAX untuk mengganti bank Virtual Account (BCA, BNI, BRI, QRIS, Mandiri).
     * Memanggil Core API /v2/charge secara asinkron tanpa reload halaman.
     */
    public function getVa(Request $request, $id)
    {
        $billing = Billing::with(['pesanan.detail.produk'])->findOrFail($id);
        $bank = $request->input('bank', 'bca');
        $vaData = \App\Services\MidtransService::createVaCharge($billing, $bank);
        return response()->json($vaData);
    }

    /**
     * Membuat Token Transaksi Snap resmi dari Midtrans Sandbox.
     * Token ini digunakan oleh frontend JavaScript (snap.js) untuk membuka modal pop-up pembayaran.
     */
    public function getSnapToken(Request $request, $id)
    {
        $billing = Billing::with(['pesanan.detail.produk'])->findOrFail($id);

        if ($billing->pembayaran) {
            return response()->json([
                'success' => false,
                'message' => 'Tagihan ini sudah lunas!',
            ], 400);
        }

        $snapData = \App\Services\MidtransService::createSnapToken($billing);

        return response()->json($snapData);
    }

    /**
     * Memeriksa status transaksi langsung ke server Midtrans (GET /v2/{order_id}/status).
     * Jika di simulator/bank sudah 'settlement' atau 'capture', sistem otomatis melunasi tagihan
     * dan memotong stok barang secara realtime.
     */
    public function cekStatusMidtrans(Request $request, $id)
    {
        $billing = Billing::with(['pesanan.detail.produk', 'pembayaran'])->findOrFail($id);

        // Jika sudah tercatat lunas di database internal
        if ($billing->pembayaran) {
            return response()->json([
                'status' => 'settlement',
                'is_paid' => true,
                'message' => 'Tagihan sudah lunas!',
            ]);
        }

        $orderId = $request->query('order_id');
        if (!$orderId) {
            return response()->json([
                'status' => 'pending',
                'is_paid' => false,
                'message' => 'Order ID tidak ditemukan',
            ]);
        }

        // Panggil status transaksi dari API Midtrans
        $statusData = \App\Services\MidtransService::getTransactionStatus($orderId);

        if ($statusData && isset($statusData['transaction_status'])) {
            $trxStatus = $statusData['transaction_status'];

            // Status settlement / capture menandakan dana sudah sukses diterima
            if (in_array($trxStatus, ['settlement', 'capture'])) {
                return DB::transaction(function () use ($billing, $statusData) {
                    // 1. Simpan bukti pelunasan Non-Tunai
                    Pembayaran::create([
                        'billing_id' => $billing->id,
                        'metode' => 'Non-Tunai',
                        'uang_dibayar' => $billing->grand_total,
                        'kembalian' => 0,
                        'no_referensi' => $statusData['transaction_id'] ?? $statusData['order_id'],
                        'tanggal_bayar' => now(),
                    ]);

                    // 2. Ubah status pesanan menjadi Paid (Lunas)
                    $billing->pesanan->update(['status_pesanan' => 'Paid']);

                    // 3. Potong stok fisik produk & update status jadi 'Habis' jika stok = 0
                    foreach ($billing->pesanan->detail as $detail) {
                        $produk = Produk::lockForUpdate()->find($detail->produk_id);
                        if ($produk) {
                            $sisaStok = max(0, $produk->stok - $detail->jumlah);
                            $produk->stok = $sisaStok;
                            if ($sisaStok == 0) {
                                $produk->status = 'Habis';
                            }
                            $produk->save();
                        }
                    }

                    return response()->json([
                        'status' => 'settlement',
                        'is_paid' => true,
                        'message' => 'Pembayaran terverifikasi di Midtrans Sandbox!',
                    ]);
                });
            }

            return response()->json([
                'status' => $trxStatus,
                'is_paid' => false,
                'message' => "Status di Midtrans: {$trxStatus}",
            ]);
        }

        return response()->json([
            'status' => 'pending',
            'is_paid' => false,
            'message' => 'Menunggu pembayaran di Simulator Midtrans...',
        ]);
    }

    /**
     * Memproses pelunasan transaksi (Metode Tunai / Non-Tunai).
     * Menghitung uang kembalian, mengubah status pesanan ke Paid, serta memotong stok fisik.
     */
    public function bayar(Request $request, $id)
    {
        $billing = Billing::with('pesanan.detail.produk')->findOrFail($id);

        if ($billing->pembayaran) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Tagihan ini sudah lunas!'], 400);
            }
            return redirect()->back()->with('error', 'Tagihan ini sudah lunas!');
        }

        $request->validate([
            'metode' => 'required|in:Tunai,Non-Tunai,Debit,Kredit',
            'uang_dibayar' => 'nullable|numeric|min:0',
            'no_referensi' => 'nullable|string',
        ]);

        // Validasi: Uang tunai yang diserahkan pelanggan tidak boleh kurang dari total tagihan
        if ($request->metode === 'Tunai' && $request->uang_dibayar < $billing->grand_total) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Uang tunai yang dibayarkan kurang dari total tagihan!'], 422);
            }
            return redirect()->back()->with('error', 'Uang yang dibayarkan kurang dari total tagihan!');
        }

        return DB::transaction(function () use ($request, $billing) {
            // Hitung uang kembalian (hanya untuk pembayaran tunai)
            $kembalian = $request->metode === 'Tunai' 
                ? max(0, $request->uang_dibayar - $billing->grand_total) 
                : 0;

            // 1. Simpan riwayat pembayaran ke database
            Pembayaran::create([
                'billing_id' => $billing->id,
                'metode' => $request->metode,
                'uang_dibayar' => $request->metode === 'Tunai' ? $request->uang_dibayar : $billing->grand_total,
                'kembalian' => $kembalian,
                'no_referensi' => $request->no_referensi ?? ($request->metode === 'Non-Tunai' ? 'MDT-' . strtoupper(substr(uniqid(), -6)) : null),
                'tanggal_bayar' => now(),
            ]);

            // 2. Perbarui status pesanan menjadi Paid (Lunas)
            $billing->pesanan->update(['status_pesanan' => 'Paid']);

            // 3. Potong stok otomatis & ubah status menu jadi Habis jika sisa stok = 0
            foreach ($billing->pesanan->detail as $detail) {
                $produk = Produk::lockForUpdate()->find($detail->produk_id);
                if ($produk) {
                    $sisaStok = max(0, $produk->stok - $detail->jumlah);
                    $produk->stok = $sisaStok;
                    if ($sisaStok == 0) {
                        $produk->status = 'Habis';
                    }
                    $produk->save();
                }
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pembayaran ' . $request->metode . ' berhasil diproses!',
                    'redirect' => route('pos.billing', $billing->id),
                ]);
            }

            return redirect()->route('pos.billing', $billing->id)->with('success', 'Pembayaran berhasil diproses dan stok telah diperbarui!');
        });
    }
}
