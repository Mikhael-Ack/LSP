@extends('layouts.app')

@section('title', 'Laporan Penjualan — Administrator Dapur Ina Aina')

@section('content')
<style>
/* CSS Khusus Cetak / Print A4 Dokumen Akuntansi Resmi */
@media print {
    /* Sembunyikan elemen navigasi aplikasi */
    #main-sidebar, 
    #sidebar-backdrop, 
    header, 
    footer, 
    .no-print {
        display: none !important;
    }

    /* Reset seluruh margin & padding layout agar memenuhi kertas */
    html, body {
        background: #ffffff !important;
        color: #0f172a !important;
        font-family: 'Plus Jakarta Sans', Arial, sans-serif !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        margin: 0 !important;
        padding: 0 !important;
        font-size: 11px !important;
    }

    div.min-h-screen, 
    div.flex-1,
    div.lg\:ml-\[260px\] {
        margin-left: 0 !important;
        padding: 0 !important;
        min-height: auto !important;
        width: 100% !important;
        max-width: 100% !important;
    }

    main {
        padding: 8mm 10mm !important;
        margin: 0 !important;
    }

    .print-only {
        display: block !important;
    }

    /* Pengaturan Kertas Landscape A4 agar tabel leluasa dan tidak sempit */
    @page {
        size: A4 landscape;
        margin: 8mm 10mm;
    }

    /* Force high contrast dark text on paper */
    table, th, td, span, div, p, h1, h2, h3, h4 {
        color: #0f172a !important;
    }
    
    .bg-slate-900, 
    .bg-slate-950, 
    .bg-slate-800, 
    .bg-slate-800\/80, 
    .bg-slate-800\/70, 
    .bg-slate-800\/50 {
        background-color: transparent !important;
        background: transparent !important;
        border-color: #cbd5e1 !important;
        box-shadow: none !important;
    }

    /* Style Tabel Cetak */
    table {
        width: 100% !important;
        border-collapse: collapse !important;
        font-size: 10px !important;
        color: #0f172a !important;
    }

    table thead th {
        background-color: #f1f5f9 !important;
        color: #0f172a !important;
        border: 1px solid #94a3b8 !important;
        padding: 6px 8px !important;
        font-weight: 800 !important;
        text-transform: uppercase !important;
    }

    table tbody td {
        border: 1px solid #cbd5e1 !important;
        padding: 6px 8px !important;
        color: #0f172a !important;
    }

    table tbody tr:nth-child(even) td {
        background-color: #f8fafc !important;
    }

    table tfoot td {
        background-color: #f1f5f9 !important;
        color: #0f172a !important;
        border: 1.5px solid #94a3b8 !important;
        padding: 6px 8px !important;
        font-weight: 800 !important;
    }

    .border-slate-800, .border-slate-700 {
        border-color: #cbd5e1 !important;
    }

    /* Sederhanakan kartu ringkasan saat dicetak */
    .print-summary-card {
        border: 1px solid #cbd5e1 !important;
        background: #f8fafc !important;
        padding: 8px 12px !important;
        border-radius: 6px !important;
    }
}

.print-only {
    display: none;
}
</style>

<div class="space-y-6">
    
    <!-- ============================================================= -->
    <!-- KOP SURAT RESMI (Hanya Muncul Saat Dicetak / Print PDF)        -->
    <!-- ============================================================= -->
    <div class="print-only pb-4 mb-4 border-b-2 border-slate-900">
        <div class="flex justify-between items-start">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-black uppercase tracking-wider text-slate-900 font-heading">
                        DAPUR INA AINA
                    </h1>
                    <span class="text-xs font-bold px-2 py-0.5 border border-slate-800 rounded uppercase">Resto & Cafe</span>
                </div>
                <p class="text-xs text-slate-700 font-medium mt-0.5">Sistem Kasir & Rekapitulasi Operasional Penjualan</p>
                <p class="text-[11px] text-slate-500">Jl. Utama Resto No. 45, Jakarta &bull; Telp: (021) 7890-1234 &bull; Email: operasional@dapurinaaina.com</p>
            </div>
            <div class="text-right">
                <div class="inline-block px-3 py-1 bg-slate-900 text-white rounded text-xs font-bold uppercase tracking-wider mb-1">
                    Laporan Penjualan Kasir
                </div>
                <p class="text-xs text-slate-700 font-semibold">Periode: <span class="text-slate-900">{{ $labelPeriode }}</span></p>
                <p class="text-[10px] text-slate-500">Waktu Cetak: {{ date('d/m/Y H:i') }} WIB &bull; Oleh: {{ data_get(session('user'), 'nama', 'Admin') }}</p>
            </div>
        </div>
    </div>

    <!-- ============================================================= -->
    <!-- TOP HEADER & PERIODE FILTER (Tampilan Layar Desktop)          -->
    <!-- ============================================================= -->
    <div class="no-print flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-slate-900 p-5 rounded-2xl border border-slate-800 shadow-xl">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-300 bg-amber-500/20 px-2.5 py-0.5 rounded-md border border-amber-500/30">
                    Administrator
                </span>
                <span class="text-xs text-slate-400 font-medium">Periode Terpilih: <strong class="text-white">{{ $labelPeriode }}</strong></span>
            </div>
            <h2 class="text-xl font-bold tracking-tight text-white font-heading mt-2 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center border border-indigo-500/30">
                    <x-icon name="chart-bar" class="w-4 h-4" />
                </div>
                Rekapitulasi Laporan Penjualan Berkala
            </h2>
            <p class="text-xs text-slate-400 mt-1">Audit transaksi kasir, rincian pembayaran tunai vs non-tunai, dan pemotongan pajak PB1 10%.</p>
        </div>

        <!-- Tombol Aksi & Filter Periode -->
        <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
            <!-- Filter Switcher -->
            <div class="bg-slate-950 p-1 rounded-xl border border-slate-800 flex items-center gap-1 text-xs">
                <a href="{{ route('admin.laporan', ['periode' => 'mingguan']) }}" 
                   class="px-3 py-1.5 rounded-lg transition font-medium flex items-center gap-1.5 {{ $periode === 'mingguan' ? 'bg-indigo-600 text-white shadow-md font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                    <x-icon name="calendar" class="w-3.5 h-3.5" /> Mingguan
                </a>
                <a href="{{ route('admin.laporan', ['periode' => 'bulanan']) }}" 
                   class="px-3 py-1.5 rounded-lg transition font-medium flex items-center gap-1.5 {{ $periode === 'bulanan' ? 'bg-indigo-600 text-white shadow-md font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                    <x-icon name="calendar" class="w-3.5 h-3.5" /> Bulanan
                </a>
                <a href="{{ route('admin.laporan', ['periode' => 'semua']) }}" 
                   class="px-3 py-1.5 rounded-lg transition font-medium flex items-center gap-1.5 {{ $periode === 'semua' ? 'bg-indigo-600 text-white shadow-md font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                    <x-icon name="squares-2x2" class="w-3.5 h-3.5" /> Semua
                </a>
            </div>

            <!-- Tombol Cetak Dokumen -->
            <button onclick="window.print()" class="h-9 px-4 inline-flex items-center gap-2 rounded-xl border border-indigo-500/40 bg-indigo-600/20 hover:bg-indigo-600 text-indigo-300 hover:text-white text-xs font-semibold shadow-md transition">
                <x-icon name="printer" class="w-4 h-4" /> Cetak Laporan (PDF)
            </button>
        </div>
    </div>

    <!-- ============================================================= -->
    <!-- 4 STATISTIK KEUANGAN EKSEKUTIF                                -->
    <!-- ============================================================= -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Total Omzet -->
        <div class="bg-slate-900 p-5 rounded-2xl border border-slate-800 shadow-md relative overflow-hidden print-summary-card">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Omzet Penjualan</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center border border-emerald-500/20 no-print">
                    <x-icon name="currency-dollar" class="w-4 h-4" />
                </div>
            </div>
            <div class="text-2xl font-black text-white print:text-slate-900 mt-2.5 font-mono tracking-tight whitespace-nowrap">
                Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
            </div>
            <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-800/80 text-[11px]">
                <span class="text-slate-500">Pajak PB1 (10%):</span>
                <span class="font-mono font-medium text-slate-300 print:text-slate-700 whitespace-nowrap">Rp {{ number_format($totalPajak, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Total Nota Selesai -->
        <div class="bg-slate-900 p-5 rounded-2xl border border-slate-800 shadow-md print-summary-card">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Transaksi Selesai</span>
                <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-400 flex items-center justify-center border border-indigo-500/20 no-print">
                    <x-icon name="check-double" class="w-4 h-4" />
                </div>
            </div>
            <div class="text-2xl font-black text-white print:text-slate-900 mt-2.5 font-mono tracking-tight whitespace-nowrap">
                {{ $totalTransaksi }} <span class="text-xs font-bold text-slate-400 uppercase">Nota Tagihan</span>
            </div>
            <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-800/80 text-[11px]">
                <span class="text-slate-500">Status Validasi:</span>
                <span class="font-semibold text-emerald-400 print:text-emerald-700">100% Lunas (Paid)</span>
            </div>
        </div>

        <!-- Kas Fisik Tunai -->
        <div class="bg-slate-900 p-5 rounded-2xl border border-slate-800 shadow-md print-summary-card">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Penerimaan Tunai</span>
                <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center border border-amber-500/20 no-print">
                    <x-icon name="banknotes" class="w-4 h-4" />
                </div>
            </div>
            <div class="text-2xl font-black text-amber-400 print:text-slate-900 mt-2.5 font-mono tracking-tight whitespace-nowrap">
                Rp {{ number_format($totalTunai, 0, ',', '.') }}
            </div>
            <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-800/80 text-[11px]">
                <span class="text-slate-500">Metode:</span>
                <span class="font-medium text-amber-300 print:text-slate-700">Uang Tunai di Kasir</span>
            </div>
        </div>

        <!-- Non-Tunai / Digital Midtrans -->
        <div class="bg-slate-900 p-5 rounded-2xl border border-slate-800 shadow-md print-summary-card">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Metode Non-Tunai</span>
                <div class="w-8 h-8 rounded-lg bg-cyan-500/10 text-cyan-400 flex items-center justify-center border border-cyan-500/20 no-print">
                    <x-icon name="credit-card" class="w-4 h-4" />
                </div>
            </div>
            <div class="text-2xl font-black text-cyan-400 print:text-slate-900 mt-2.5 font-mono tracking-tight whitespace-nowrap">
                Rp {{ number_format($totalNonTunai, 0, ',', '.') }}
            </div>
            <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-800/80 text-[11px]">
                <span class="text-slate-500">Kanal:</span>
                <span class="font-medium text-cyan-300 print:text-slate-700">Midtrans VA / EDC Kartu</span>
            </div>
        </div>

    </div>

    <!-- ============================================================= -->
    <!-- 5 MENU TERLARIS (TOP 5) - NO PRINT JIKA INGIN FOKUS DATA      -->
    <!-- ============================================================= -->
    @if($produkTerlaris->count() > 0)
        <div class="no-print bg-slate-900 p-5 rounded-2xl border border-slate-800 shadow-md">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-bold text-xs text-white flex items-center gap-2">
                    <x-icon name="trophy" class="w-4 h-4 text-amber-400" /> 5 Produk Menu Terlaris
                </h3>
                <span class="text-[11px] text-slate-400">Peringkat berdasarkan volume pesanan selesai</span>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
                @foreach($produkTerlaris as $index => $item)
                    <div class="bg-slate-800/70 p-3 rounded-xl border border-slate-700/60 flex flex-col justify-between hover:border-slate-600 transition">
                        <div>
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-slate-900 border border-slate-700 text-amber-300 font-mono">
                                #{{ $index + 1 }}
                            </span>
                            <h4 class="font-semibold text-xs text-white mt-2 line-clamp-1" title="{{ $item->produk->nama_produk ?? 'Menu Dihapus' }}">
                                {{ $item->produk->nama_produk ?? 'Menu Dihapus' }}
                            </h4>
                        </div>
                        <div class="mt-2.5 pt-2 border-t border-slate-700/50 flex justify-between items-center text-[11px]">
                            <span class="text-slate-400">Terjual:</span>
                            <span class="font-bold text-amber-400 font-mono">{{ $item->total_terjual }} porsi</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- ============================================================= -->
    <!-- TABEL UTAMA RINCIAN TRANSAKSI PENJUALAN                      -->
    <!-- ============================================================= -->
    <div class="bg-slate-900 rounded-2xl shadow-xl border border-slate-800 overflow-hidden">
        
        <!-- Header Tabel (Hanya di Layar) -->
        <div class="no-print p-4 sm:p-5 border-b border-slate-800 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
                <h3 class="text-sm font-bold text-white tracking-tight flex items-center gap-2">
                    <x-icon name="receipt" class="w-4 h-4 text-indigo-400 no-print" /> Rincian Transaksi Tagihan Kasir
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Daftar rekaman pesanan, rincian menu, nomor meja, dan status pelunasan</p>
            </div>
            <span class="text-[11px] font-medium text-slate-400 bg-slate-800 px-3 py-1 rounded-lg border border-slate-700">
                Total: <strong class="text-white">{{ $transaksiList->count() }} Data</strong>
            </span>
        </div>

        <!-- Area Tabel Responsif & Rapi -->
        <div class="overflow-x-auto w-full">
            <table class="w-full text-xs text-left border-collapse">
                <thead class="bg-slate-950 text-slate-400 font-bold uppercase tracking-wider text-[10.5px] border-b border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5 text-center w-12 whitespace-nowrap">No.</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">No. Nota</th>
                        <th class="px-4 py-3.5 whitespace-nowrap">Waktu Transaksi</th>
                        <th class="px-3 py-3.5 text-center whitespace-nowrap">Meja</th>
                        <th class="px-4 py-3.5 min-w-[220px]">Rincian Menu Pesanan</th>
                        <th class="px-4 py-3.5 text-right whitespace-nowrap">Pajak PB1 (10%)</th>
                        <th class="px-4 py-3.5 text-right whitespace-nowrap">Grand Total</th>
                        <th class="px-3 py-3.5 text-center whitespace-nowrap">Metode</th>
                        <th class="px-3 py-3.5 text-center whitespace-nowrap">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 text-slate-300">
                    @forelse($transaksiList as $index => $trx)
                        <tr class="hover:bg-slate-800/50 transition">
                            
                            <!-- Nomor Urut -->
                            <td class="px-4 py-3 text-center text-slate-500 font-mono text-[11px] whitespace-nowrap">
                                {{ $index + 1 }}
                            </td>

                            <!-- No. Nota (Font Mono Rapi Tidak Pecah) -->
                            <td class="px-4 py-3 whitespace-nowrap font-mono font-bold text-white">
                                <span class="bg-slate-800/80 px-2 py-0.5 rounded border border-slate-700 text-slate-200">
                                    {{ $trx->no_tagihan ?? '-' }}
                                </span>
                            </td>

                            <!-- Tanggal & Jam Transaksi -->
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="font-medium text-slate-200">
                                    {{ \Carbon\Carbon::parse($trx->created_at)->format('d/m/Y') }}
                                </div>
                                <div class="text-[10px] text-slate-500 font-mono">
                                    {{ \Carbon\Carbon::parse($trx->created_at)->format('H:i') }} WIB
                                </div>
                            </td>

                            <!-- Meja (Pill Utuh Satu Baris) -->
                            <td class="px-3 py-3 text-center whitespace-nowrap">
                                @php
                                    $mejaStr = $trx->pesanan->no_meja ?? '-';
                                    $mejaDisplay = str_starts_with(strtolower($mejaStr), 'meja') ? $mejaStr : 'Meja ' . $mejaStr;
                                @endphp
                                <span class="inline-block px-2.5 py-1 rounded-md bg-slate-800 border border-slate-700 text-slate-200 font-bold text-[11px] whitespace-nowrap shadow-sm">
                                    {{ $mejaDisplay }}
                                </span>
                            </td>

                            <!-- Rincian Menu Pesanan (Format Tag Rapi Bersusun) -->
                            <td class="px-4 py-3">
                                @if($trx->pesanan && $trx->pesanan->detail && $trx->pesanan->detail->count() > 0)
                                    <div class="flex flex-col gap-1 max-w-sm">
                                        @foreach($trx->pesanan->detail as $det)
                                            <div class="flex items-center gap-1.5 text-[11.5px] leading-relaxed">
                                                <span class="inline-block px-1.5 py-0.2 rounded bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 font-mono font-bold text-[10px] shrink-0 whitespace-nowrap">
                                                    {{ $det->jumlah }}x
                                                </span>
                                                <span class="text-slate-200 font-medium">
                                                    {{ $det->produk->nama_produk ?? 'Menu Dihapus' }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-slate-500 italic text-[11px]">- Tidak ada detail -</span>
                                @endif
                            </td>

                            <!-- Pajak PB1 10% (Rata Kanan, Font Mono Utuh) -->
                            <td class="px-4 py-3 text-right font-mono text-slate-400 whitespace-nowrap text-xs">
                                Rp {{ number_format($trx->pajak ?? 0, 0, ',', '.') }}
                            </td>

                            <!-- Grand Total (Rata Kanan, Tebal, Utuh) -->
                            <td class="px-4 py-3 text-right font-mono font-bold text-white whitespace-nowrap text-[13px]">
                                Rp {{ number_format($trx->grand_total, 0, ',', '.') }}
                            </td>

                            <!-- Metode Pembayaran (Badge Utuh Tidak Kepotong) -->
                            <td class="px-3 py-3 text-center whitespace-nowrap">
                                @php
                                    $metode = optional($trx->pembayaran)->metode ?? 'Tunai';
                                @endphp
                                @if($metode === 'Tunai')
                                    <span class="inline-block px-2.5 py-1 rounded-md text-[10.5px] font-bold bg-amber-500/15 text-amber-300 border border-amber-500/30 whitespace-nowrap">
                                        Tunai
                                    </span>
                                @else
                                    <span class="inline-block px-2.5 py-1 rounded-md text-[10.5px] font-bold bg-cyan-500/15 text-cyan-300 border border-cyan-500/30 whitespace-nowrap">
                                        Non-Tunai
                                    </span>
                                @endif
                            </td>

                            <!-- Status Pelunasan -->
                            <td class="px-3 py-3 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10.5px] font-bold text-emerald-300 bg-emerald-500/15 border border-emerald-500/30 whitespace-nowrap">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Lunas
                                </span>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-12 text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <x-icon name="document-magnifying-glass" class="w-8 h-8 text-slate-600" />
                                    <p class="font-medium text-xs">Belum ada transaksi penjualan yang tercatat pada periode ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                <!-- Footer Rekap Total Tabel -->
                @if($transaksiList->count() > 0)
                    <tfoot class="bg-slate-950 font-bold border-t-2 border-slate-700 text-white text-xs">
                        <tr>
                            <td colspan="5" class="px-4 py-3.5 text-right text-slate-300 uppercase tracking-wider text-[11px]">
                                TOTAL REKAPITULASI ({{ $totalTransaksi }} TRANSAKSI):
                            </td>
                            <td class="px-4 py-3.5 text-right font-mono text-slate-300 whitespace-nowrap">
                                Rp {{ number_format($totalPajak, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3.5 text-right font-mono text-sm font-black text-emerald-400 whitespace-nowrap">
                                Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                            </td>
                            <td colspan="2" class="px-3 py-3.5 text-center text-[10px] text-slate-400 whitespace-nowrap">
                                Tunai: <strong class="text-amber-300">Rp {{ number_format($totalTunai, 0, ',', '.') }}</strong> | 
                                Non-Tunai: <strong class="text-cyan-300">Rp {{ number_format($totalNonTunai, 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                    </tfoot>
                @endif

            </table>
        </div>

    </div>

    <!-- ============================================================= -->
    <!-- LEMBAR PENGESAHAN TANDA TANGAN (Hanya Saat Dicetak / PDF)     -->
    <!-- ============================================================= -->
    <div class="print-only mt-10 pt-6 border-t border-slate-300">
        <div class="grid grid-cols-2 text-center text-xs">
            <div>
                <p class="text-slate-600 font-medium">Dibuat & Diverifikasi Oleh,</p>
                <div class="h-16"></div>
                <p class="font-bold text-slate-900 underline">{{ data_get(session('user'), 'nama', 'Petugas Kasir / Admin') }}</p>
                <p class="text-[10px] text-slate-500">Staf Administrasi & Kasir POS</p>
            </div>
            <div>
                <p class="text-slate-600 font-medium">Mengetahui & Menyetujui,</p>
                <div class="h-16"></div>
                <p class="font-bold text-slate-900 underline">( Supervisor / Manajemen Restoran )</p>
                <p class="text-[10px] text-slate-500">Owner Restoran Dapur Ina Aina</p>
            </div>
        </div>
    </div>

</div>
@endsection
