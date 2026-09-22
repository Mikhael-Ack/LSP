<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Billing;
use App\Models\DetailPesanan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function stok()
    {
        $produkList = Produk::with('kategori')->get();
        $kategoriList = Kategori::all();
        return view('admin.stok', compact('produkList', 'kategoriList'));
    }

    public function updateStok(Request $request, $id)
    {
        $request->validate([
            'stok' => 'required|integer|min:0',
            'status' => 'required|in:Tersedia,Habis',
        ]);

        $produk = Produk::findOrFail($id);
        $produk->stok = $request->stok;
        $produk->status = ($request->stok == 0) ? 'Habis' : $request->status;
        $produk->save();

        return redirect()->back()->with('success', "Stok {$produk->nama_produk} berhasil diperbarui!");
    }

    public function storeProduk(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:100',
            'kategori_id' => 'required|exists:kategori,id',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'status' => 'required|in:Tersedia,Habis',
            'deskripsi' => 'nullable|string|max:255',
        ]);

        $status = ($request->stok == 0) ? 'Habis' : $request->status;

        $produk = Produk::create([
            'nama_produk' => $request->nama_produk,
            'kategori_id' => $request->kategori_id,
            'harga' => $request->harga,
            'stok' => $request->stok,
            'status' => $status,
            'deskripsi' => $request->deskripsi ?? '',
        ]);

        return redirect()->back()->with('success', "Menu '{$produk->nama_produk}' berhasil ditambahkan ke katalog!");
    }

    public function updateProduk(Request $request, $id)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:100',
            'kategori_id' => 'required|exists:kategori,id',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'status' => 'required|in:Tersedia,Habis',
            'deskripsi' => 'nullable|string|max:255',
        ]);

        $produk = Produk::findOrFail($id);
        $status = ($request->stok == 0) ? 'Habis' : $request->status;

        $produk->update([
            'nama_produk' => $request->nama_produk,
            'kategori_id' => $request->kategori_id,
            'harga' => $request->harga,
            'stok' => $request->stok,
            'status' => $status,
            'deskripsi' => $request->deskripsi ?? $produk->deskripsi,
        ]);

        return redirect()->back()->with('success', "Data menu '{$produk->nama_produk}' berhasil diperbarui!");
    }

    public function destroyProduk($id)
    {
        $produk = Produk::findOrFail($id);

        $hasOrders = DetailPesanan::where('produk_id', $id)->exists();
        if ($hasOrders) {
            $produk->status = 'Habis';
            $produk->stok = 0;
            $produk->save();
            return redirect()->back()->with('warning', "Menu '{$produk->nama_produk}' memiliki riwayat pesanan, status otomatis dinonaktifkan (Habis).");
        }

        $nama = $produk->nama_produk;
        $produk->delete();

        return redirect()->back()->with('success', "Menu '{$nama}' berhasil dihapus dari sistem!");
    }

    public function laporan(Request $request)
    {
        $periode = $request->get('periode', 'mingguan');
        $query = Billing::with(['pesanan.detail.produk', 'pembayaran'])
            ->whereHas('pesanan', function ($q) {
                $q->where('status_pesanan', 'Paid');
            });

        $now = Carbon::now();

        if ($periode === 'mingguan') {
            $query->whereBetween('created_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]);
            $labelPeriode = 'Minggu Ini (' . $now->copy()->startOfWeek()->format('d M Y') . ' - ' . $now->copy()->endOfWeek()->format('d M Y') . ')';
        } elseif ($periode === 'bulanan') {
            $query->whereMonth('created_at', $now->month)
                  ->whereYear('created_at', $now->year);
            $labelPeriode = 'Bulan ' . $now->translatedFormat('F Y');
        } else {
            $labelPeriode = 'Semua Periode';
        }

        $transaksiList = $query->latest()->get();

        $totalPendapatan = $transaksiList->sum('grand_total');
        $totalTransaksi = $transaksiList->count();
        $totalPajak = $transaksiList->sum('pajak');

        // Rekap Metode Bayar
        $totalTunai = $transaksiList->filter(fn($t) => optional($t->pembayaran)->metode === 'Tunai')->sum('grand_total');
        $totalNonTunai = $transaksiList->filter(fn($t) => in_array(optional($t->pembayaran)->metode, ['Non-Tunai', 'Debit', 'Kredit']))->sum('grand_total');

        // Produk Terlaris
        $produkTerlaris = DetailPesanan::select('produk_id', DB::raw('SUM(jumlah) as total_terjual'), DB::raw('SUM(subtotal) as total_omzet'))
            ->whereHas('pesanan', function ($q) use ($periode, $now) {
                $q->where('status_pesanan', 'Paid');
                if ($periode === 'mingguan') {
                    $q->whereBetween('tanggal', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]);
                } elseif ($periode === 'bulanan') {
                    $q->whereMonth('tanggal', $now->month)->whereYear('tanggal', $now->year);
                }
            })
            ->groupBy('produk_id')
            ->orderByDesc('total_terjual')
            ->with('produk.kategori')
            ->take(5)
            ->get();

        return view('admin.laporan', compact(
            'transaksiList',
            'periode',
            'labelPeriode',
            'totalPendapatan',
            'totalTransaksi',
            'totalPajak',
            'totalTunai',
            'totalNonTunai',
            'produkTerlaris'
        ));
    }
}
