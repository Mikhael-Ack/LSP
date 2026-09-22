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
    public function index()
    {
        $kategoriList = Kategori::all();
        $produkList = Produk::with('kategori')->get();
        return view('pos.index', compact('kategoriList', 'produkList'));
    }

    public function simpanPesanan(Request $request)
    {
        $request->validate([
            'no_meja' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:produk,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($request) {
            // Ambil kasir default
            $kasir = User::where('role', 'kasir')->first() ?? User::first();

            // Cek stok terlebih dahulu
            $totalBayar = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
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

            // Generate No Pesanan Unik
            $noPesanan = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            // Simpan Pesanan
            $pesanan = Pesanan::create([
                'user_id' => $kasir ? $kasir->id : 1,
                'no_pesanan' => $noPesanan,
                'no_meja' => $request->no_meja,
                'tanggal' => now(),
                'status_pesanan' => 'Billed',
            ]);

            // Simpan Detail Pesanan
            foreach ($itemsData as $data) {
                DetailPesanan::create([
                    'pesanan_id' => $pesanan->id,
                    'produk_id' => $data['produk']->id,
                    'jumlah' => $data['qty'],
                    'harga_satuan' => $data['harga'],
                    'subtotal' => $data['subtotal'],
                ]);
            }

            // Hitung Pajak PB1 10%
            $pajak = (int) round($totalBayar * 0.10);
            $grandTotal = $totalBayar + $pajak;

            // Generate Billing
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

    public function billing($id)
    {
        $billing = Billing::with(['pesanan.detail.produk', 'pembayaran'])->findOrFail($id);
        $vaData = \App\Services\MidtransService::createVaCharge($billing, 'bca');
        return view('pos.billing', compact('billing', 'vaData'));
    }

    public function getVa(Request $request, $id)
    {
        $billing = Billing::with(['pesanan.detail.produk'])->findOrFail($id);
        $bank = $request->input('bank', 'bca');
        $vaData = \App\Services\MidtransService::createVaCharge($billing, $bank);
        return response()->json($vaData);
    }

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

        if ($request->metode === 'Tunai' && $request->uang_dibayar < $billing->grand_total) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Uang tunai yang dibayarkan kurang dari total tagihan!'], 422);
            }
            return redirect()->back()->with('error', 'Uang yang dibayarkan kurang dari total tagihan!');
        }

        return DB::transaction(function () use ($request, $billing) {
            $kembalian = $request->metode === 'Tunai' 
                ? max(0, $request->uang_dibayar - $billing->grand_total) 
                : 0;

            // Simpan Pembayaran
            Pembayaran::create([
                'billing_id' => $billing->id,
                'metode' => $request->metode,
                'uang_dibayar' => $request->metode === 'Tunai' ? $request->uang_dibayar : $billing->grand_total,
                'kembalian' => $kembalian,
                'no_referensi' => $request->no_referensi ?? ($request->metode === 'Non-Tunai' ? 'MDT-' . strtoupper(substr(uniqid(), -6)) : null),
                'tanggal_bayar' => now(),
            ]);

            // Update status pesanan jadi Paid
            $billing->pesanan->update(['status_pesanan' => 'Paid']);

            // Potong stok otomatis & ubah status habis jika sisa 0
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
