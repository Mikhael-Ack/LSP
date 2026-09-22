@extends('layouts.app')

@section('title', 'Laporan Penjualan — Administrator Dapur Ina Aina')

@section('content')
<div class="space-y-5">
    
    <!-- Top Filter Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-900 p-5 rounded-xl border border-slate-800 shadow-md">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-300 bg-amber-500/20 px-2 py-0.5 rounded border border-amber-500/30">
                    Administrator
                </span>
                <span class="text-xs text-slate-400 font-medium">Periode: <strong class="text-white">{{ $labelPeriode }}</strong></span>
            </div>
            <h2 class="text-xl font-bold tracking-tight text-white font-heading mt-1.5 flex items-center gap-2">
                <x-icon name="chart-bar" class="w-5 h-5 text-indigo-400" /> Rekapitulasi Laporan Penjualan Berkala
            </h2>
            <p class="text-xs text-slate-400 mt-0.5">Pantau omzet transaksi kasir, rincian pembayaran tunai vs non-tunai, dan menu terlaris.</p>
        </div>

        <!-- Period Switcher Buttons -->
        <div class="bg-slate-950 p-1 rounded-lg border border-slate-800 flex items-center gap-1 text-xs">
            <a href="{{ route('admin.laporan', ['periode' => 'mingguan']) }}" 
               class="px-3 py-1.5 rounded-md transition font-medium flex items-center gap-1.5 {{ $periode === 'mingguan' ? 'bg-indigo-600 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-white' }}">
                <x-icon name="calendar" class="w-3.5 h-3.5" /> Mingguan
            </a>
            <a href="{{ route('admin.laporan', ['periode' => 'bulanan']) }}" 
               class="px-3 py-1.5 rounded-md transition font-medium flex items-center gap-1.5 {{ $periode === 'bulanan' ? 'bg-indigo-600 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-white' }}">
                <x-icon name="calendar" class="w-3.5 h-3.5" /> Bulanan
            </a>
            <a href="{{ route('admin.laporan', ['periode' => 'semua']) }}" 
               class="px-3 py-1.5 rounded-md transition font-medium flex items-center gap-1.5 {{ $periode === 'semua' ? 'bg-indigo-600 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-white' }}">
                <x-icon name="squares-2x2" class="w-3.5 h-3.5" /> Semua
            </a>
        </div>
    </div>

    <!-- 4 Stats Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Total Omzet -->
        <div class="bg-slate-900 p-5 rounded-xl border border-slate-800 shadow-md">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-400">Total Omzet Penjualan</span>
                <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-400 flex items-center justify-center border border-indigo-500/20">
                    <x-icon name="currency-dollar" class="w-4 h-4" />
                </div>
            </div>
            <div class="text-2xl font-bold text-white mt-2 font-mono tracking-tight">
                Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
            </div>
            <p class="text-[11px] text-slate-500 mt-1">Termasuk PPN PB1 10%</p>
        </div>

        <!-- Total Nota -->
        <div class="bg-slate-900 p-5 rounded-xl border border-slate-800 shadow-md">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-400">Transaksi Selesai</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center border border-emerald-500/20">
                    <x-icon name="check-double" class="w-4 h-4" />
                </div>
            </div>
            <div class="text-2xl font-bold text-white mt-2 font-mono tracking-tight">
                {{ $totalTransaksi }} <span class="text-xs font-normal text-slate-400">Nota</span>
            </div>
            <p class="text-[11px] text-emerald-400 font-medium mt-1">Status Lunas (Paid)</p>
        </div>

        <!-- Tunai -->
        <div class="bg-slate-900 p-5 rounded-xl border border-slate-800 shadow-md">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-400">Metode Tunai</span>
                <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center border border-amber-500/20">
                    <x-icon name="banknotes" class="w-4 h-4" />
                </div>
            </div>
            <div class="text-2xl font-bold text-amber-400 mt-2 font-mono tracking-tight">
                Rp {{ number_format($totalTunai, 0, ',', '.') }}
            </div>
            <p class="text-[11px] text-slate-500 mt-1">Kas Fisik Kasir</p>
        </div>

        <!-- Non Tunai -->
        <div class="bg-slate-900 p-5 rounded-xl border border-slate-800 shadow-md">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-400">Metode Non-Tunai</span>
                <div class="w-8 h-8 rounded-lg bg-cyan-500/10 text-cyan-400 flex items-center justify-center border border-cyan-500/20">
                    <x-icon name="credit-card" class="w-4 h-4" />
                </div>
            </div>
            <div class="text-2xl font-bold text-cyan-400 mt-2 font-mono tracking-tight">
                Rp {{ number_format($totalNonTunai, 0, ',', '.') }}
            </div>
            <p class="text-[11px] text-slate-500 mt-1">EDC Kartu Debit / Kredit</p>
        </div>

    </div>

    <!-- 5 Top Products -->
    @if($produkTerlaris->count() > 0)
        <div class="bg-slate-900 p-5 rounded-xl border border-slate-800 shadow-md">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold text-xs text-white flex items-center gap-1.5">
                    <x-icon name="trophy" class="w-4 h-4 text-amber-400" /> 5 Produk Menu Terlaris
                </h3>
                <span class="text-[10px] text-slate-400">Peringkat berdasarkan kuantitas pesanan selesai</span>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
                @foreach($produkTerlaris as $index => $item)
                    <div class="bg-slate-800/80 p-3 rounded-lg border border-slate-700/80 flex flex-col justify-between">
                        <div>
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-slate-900 border border-slate-700 text-slate-300">
                                #{{ $index + 1 }}
                            </span>
                            <h4 class="font-semibold text-xs text-white mt-1.5 line-clamp-1">
                                {{ $item->produk->nama_produk ?? 'Menu Dihapus' }}
                            </h4>
                        </div>
                        <div class="mt-2 pt-2 border-t border-slate-700/60 flex justify-between items-center text-[11px]">
                            <span class="text-slate-400">Terjual:</span>
                            <span class="font-bold text-amber-400 font-mono">{{ $item->total_terjual }} porsi</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Transaction Table Card -->
    <div class="bg-slate-900 rounded-xl shadow-md border border-slate-800 overflow-hidden">
        <div class="p-4 border-b border-slate-800 flex justify-between items-center">
            <div>
                <h3 class="text-sm font-semibold text-white">Rincian Transaksi Selesai</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Daftar transaksi pembayaran yang tercatat pada sistem</p>
            </div>
            <button onclick="window.print()" class="h-8 px-3 inline-flex items-center gap-1.5 rounded-lg border border-slate-700 bg-slate-800 text-xs font-medium text-slate-300 hover:text-white hover:bg-slate-700 transition">
                <x-icon name="printer" class="w-3.5 h-3.5" /> Cetak Laporan
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-950 text-slate-400 font-semibold uppercase border-b border-slate-800 text-[11px]">
                    <tr>
                        <th class="px-5 py-3">No. Nota</th>
                        <th class="px-5 py-3">Tanggal</th>
                        <th class="px-5 py-3 text-center">Meja</th>
                        <th class="px-5 py-3">Item Pesanan</th>
                        <th class="px-5 py-3 text-right">Pajak (PB1)</th>
                        <th class="px-5 py-3 text-right">Grand Total</th>
                        <th class="px-5 py-3 text-center">Metode</th>
                        <th class="px-5 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($transaksiList as $trx)
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="px-5 py-3.5 font-mono font-semibold text-white">
                                {{ $trx->no_tagihan ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-slate-400">
                                {{ \Carbon\Carbon::parse($trx->created_at)->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="px-2 py-0.5 rounded bg-slate-800 border border-slate-700 text-slate-300 font-semibold text-[11px]">
                                    {{ $trx->pesanan->no_meja ?? '-' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 max-w-xs">
                                @if($trx->pesanan && $trx->pesanan->detail)
                                    <span class="text-slate-300">
                                        {{ $trx->pesanan->detail->map(fn($d) => "{$d->jumlah}x " . ($d->produk->nama_produk ?? 'Menu'))->join(', ') }}
                                    </span>
                                @else
                                    <span class="text-slate-500">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right font-mono text-slate-400">
                                Rp {{ number_format($trx->pajak ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3.5 text-right font-mono font-bold text-white">
                                Rp {{ number_format($trx->grand_total, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold border {{ optional($trx->pembayaran)->metode === 'Tunai' ? 'bg-amber-500/10 text-amber-300 border-amber-500/30' : 'bg-cyan-500/10 text-cyan-300 border-cyan-500/30' }}">
                                    {{ optional($trx->pembayaran)->metode ?? 'Tunai' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-emerald-300 bg-emerald-500/10 border border-emerald-500/30 px-2 py-0.5 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Lunas
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-10 text-slate-500">
                                Belum ada transaksi penjualan yang tercatat pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
