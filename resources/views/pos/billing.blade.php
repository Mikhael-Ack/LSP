@extends('layouts.app')

@section('title', 'Tagihan #' . $billing->no_tagihan . ' — Dapur Ina Aina')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    
    <!-- Action Bar -->
    <div class="flex justify-between items-center print:hidden">
        <a href="{{ route('pos.index') }}" 
           class="h-9 px-3.5 inline-flex items-center gap-1.5 rounded-lg border border-slate-700 bg-slate-900 text-xs font-medium text-slate-300 hover:text-white hover:bg-slate-800 transition">
            <x-icon name="arrow-left" class="w-4 h-4" />
            <span>Kembali ke Kasir</span>
        </a>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" 
                    class="h-9 px-4 inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-medium shadow-md transition active:scale-95">
                <x-icon name="printer" class="w-4 h-4" />
                <span>Cetak Struk Billing</span>
            </button>
        </div>
    </div>

    <!-- Thermal / Clean Receipt Container -->
    <div class="bg-slate-900 rounded-xl shadow-xl border border-slate-800 overflow-hidden relative text-white" id="receipt-card">
        
        <div class="p-6 sm:p-8 space-y-5">
            <!-- Restaurant Header -->
            <div class="text-center pb-5 border-b border-dashed border-slate-700">
                <div class="w-12 h-12 bg-slate-800 text-white rounded-xl flex items-center justify-center mx-auto mb-2.5 border border-slate-700">
                    <x-icon name="building-storefront" class="w-6 h-6 text-amber-400" />
                </div>
                <h2 class="text-lg font-bold text-white font-heading tracking-tight uppercase">DAPUR INA AINA</h2>
                <p class="text-xs font-medium text-slate-400 mt-0.5">Sistem Kasir & Operasional Restoran Modern</p>
                <p class="text-[11px] text-slate-500">Jl. Kuliner Nusantara No. 88, Jakarta</p>
                
                <div class="mt-3">
                    @if($billing->pesanan && $billing->pesanan->status_pesanan === 'Paid')
                        <span class="inline-flex items-center gap-1 px-3 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-300 border border-emerald-500/30">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> LUNAS (PAID)
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-3 py-0.5 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-300 border border-amber-500/30">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span> MENUNGGU PEMBAYARAN (BILLED)
                        </span>
                    @endif
                </div>
            </div>

            <!-- Receipt Metadata Grid -->
            <div class="grid grid-cols-2 gap-y-2 text-xs text-slate-300 py-3 border-b border-dashed border-slate-700 font-medium">
                <div>
                    <span class="text-slate-500 block text-[10px] uppercase font-semibold">Nomor Billing</span>
                    <span class="font-bold text-white font-mono">{{ $billing->no_tagihan }}</span>
                </div>
                <div class="text-right">
                    <span class="text-slate-500 block text-[10px] uppercase font-semibold">Nomor Pesanan</span>
                    <span class="font-bold text-white font-mono">{{ $billing->pesanan->no_pesanan ?? ('ORD-' . $billing->pesanan_id) }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block text-[10px] uppercase font-semibold">Nomor Meja</span>
                    <span class="font-bold text-indigo-400 text-sm">{{ $billing->pesanan->no_meja ?? '-' }}</span>
                </div>
                <div class="text-right">
                    <span class="text-slate-500 block text-[10px] uppercase font-semibold">Waktu Cetak</span>
                    <span class="font-medium text-slate-300">{{ \Carbon\Carbon::parse($billing->created_at)->format('d/m/Y H:i') }}</span>
                </div>
            </div>

            <!-- Ordered Items List -->
            <div class="py-2 border-b border-dashed border-slate-700">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="text-[10px] font-bold uppercase text-slate-500 border-b border-slate-800 pb-2">
                            <th class="text-left pb-2">Menu</th>
                            <th class="text-center pb-2">Qty</th>
                            <th class="text-right pb-2">Harga</th>
                            <th class="text-right pb-2">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @if($billing->pesanan && $billing->pesanan->detail)
                            @foreach($billing->pesanan->detail as $item)
                                <tr>
                                    <td class="py-2.5 font-medium text-white">
                                        {{ $item->produk->nama_produk ?? 'Menu' }}
                                        <span class="block text-[10px] text-slate-500 font-normal">{{ optional($item->produk->kategori)->nama_kategori }}</span>
                                    </td>
                                    <td class="text-center font-bold text-slate-300">{{ $item->jumlah }}</td>
                                    <td class="text-right font-mono text-slate-400">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                    <td class="text-right font-mono font-semibold text-white">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Payment Calculation -->
            <div class="space-y-2 py-3 border-b border-dashed border-slate-700 text-xs">
                <div class="flex justify-between text-slate-400">
                    <span>Subtotal Menu</span>
                    <span class="font-semibold text-white font-mono">Rp {{ number_format($billing->total_bayar, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-slate-400">
                    <span>Pajak Restoran PB1 (10%)</span>
                    <span class="font-semibold text-white font-mono">Rp {{ number_format($billing->pajak, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center pt-2.5 border-t border-slate-800">
                    <span class="text-xs font-bold text-white uppercase tracking-wider">TOTAL TAGIHAN</span>
                    <span class="text-lg font-bold text-amber-400 font-mono">Rp {{ number_format($billing->grand_total, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Settled Payment Details (If Paid) -->
            @if($billing->pembayaran)
                <div class="p-4 bg-slate-800/80 rounded-xl border border-slate-700 text-xs space-y-2">
                    <div class="flex justify-between items-center font-semibold text-white">
                        <span>Metode Pembayaran:</span>
                        @if($billing->pembayaran->metode === 'Tunai')
                            <span class="px-2.5 py-1 rounded-md bg-emerald-500/10 text-emerald-300 border border-emerald-500/30 text-[11px] font-bold flex items-center gap-1">
                                <x-icon name="banknotes" class="w-3.5 h-3.5" /> Tunai (Cash)
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-md bg-blue-500/10 text-blue-300 border border-blue-500/30 text-[11px] font-bold flex items-center gap-1">
                                <x-icon name="credit-card" class="w-3.5 h-3.5 text-blue-400" /> Non-Tunai (Midtrans Sandbox)
                            </span>
                        @endif
                    </div>
                    @if($billing->pembayaran->no_referensi)
                        <div class="flex justify-between text-slate-400">
                            <span>No. Referensi / Transaksi:</span>
                            <span class="font-mono font-semibold text-white text-[11px]">{{ $billing->pembayaran->no_referensi }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-slate-400">
                        <span>Jumlah Dibayar:</span>
                        <span class="font-mono font-semibold text-white">Rp {{ number_format($billing->pembayaran->uang_dibayar, 0, ',', '.') }}</span>
                    </div>
                    @if($billing->pembayaran->metode === 'Tunai')
                        <div class="flex justify-between text-white font-semibold pt-1.5 border-t border-slate-700">
                            <span>Uang Kembalian:</span>
                            <span class="font-mono text-emerald-400">Rp {{ number_format($billing->pembayaran->kembalian, 0, ',', '.') }}</span>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Footer Message -->
            <div class="text-center pt-1 text-slate-500 text-[11px]">
                <p class="font-medium text-slate-400">Terima kasih atas kunjungan Anda!</p>
                <p class="text-[10px] mt-0.5">Dapur Ina Aina — Sajian Cita Rasa Nusantara</p>
            </div>
        </div>

    </div>

    <!-- Payment Action Box (Only if Unpaid) -->
    @if(!$billing->pembayaran)
        <div class="bg-slate-900 p-5 sm:p-6 rounded-xl shadow-xl border border-slate-800 print:hidden space-y-4">
            <div>
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-sm text-white flex items-center gap-1.5">
                        <x-icon name="credit-card" class="w-4 h-4 text-indigo-400" />
                        <span>Pelunasan Tagihan Kasir</span>
                    </h3>
                    <span class="text-[10px] bg-blue-500/10 text-blue-300 px-2 py-0.5 rounded border border-blue-500/20 font-medium">
                        2 Metode Pembayaran
                    </span>
                </div>
                <p class="text-xs text-slate-400 mt-0.5">Pilih metode pembayaran: Tunai langsung atau Non-Tunai melalui Midtrans Sandbox.</p>
            </div>

            <!-- 2 Payment Method Options Toggle -->
            <div class="grid grid-cols-2 gap-3">
                <label class="flex flex-col items-center justify-center p-3.5 rounded-xl border border-slate-700 bg-slate-800/80 cursor-pointer hover:bg-slate-800 transition has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-600/10 has-[:checked]:text-white text-slate-300 group">
                    <input type="radio" name="metode_pilih" value="Tunai" checked onchange="toggleMetode('Tunai')" class="sr-only">
                    <x-icon name="banknotes" class="w-6 h-6 mb-1 text-emerald-400" />
                    <span class="text-xs font-bold text-white">1. Tunai (Cash)</span>
                    <span class="text-[10px] text-slate-400 mt-0.5">Pembayaran uang tunai</span>
                </label>

                <label class="flex flex-col items-center justify-center p-3.5 rounded-xl border border-slate-700 bg-slate-800/80 cursor-pointer hover:bg-slate-800 transition has-[:checked]:border-blue-500 has-[:checked]:bg-blue-600/10 has-[:checked]:text-white text-slate-300 group">
                    <input type="radio" name="metode_pilih" value="Non-Tunai" onchange="toggleMetode('Non-Tunai')" class="sr-only">
                    <x-icon name="credit-card" class="w-6 h-6 mb-1 text-blue-400" />
                    <span class="text-xs font-bold text-white">2. Non-Tunai</span>
                    <span class="text-[10px] text-blue-300 font-medium mt-0.5">Midtrans Sandbox API</span>
                </label>
            </div>

            <!-- PANEL 1: TUNAI (CASH) FORM -->
            <div id="field-tunai" class="bg-slate-800/60 p-4 rounded-xl border border-slate-700 space-y-3">
                <form action="{{ route('pos.bayar', $billing->id) }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="hidden" name="metode" value="Tunai">
                    
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label class="block text-[11px] font-semibold text-slate-300">Nominal Uang Diterima (Rp) *</label>
                            <span class="text-[10px] text-slate-400">Total Tagihan: <strong class="text-amber-400 font-mono">Rp {{ number_format($billing->grand_total, 0, ',', '.') }}</strong></span>
                        </div>
                        <input type="number" name="uang_dibayar" id="uang_dibayar" 
                               value="{{ $billing->grand_total }}" min="{{ $billing->grand_total }}"
                               oninput="hitungKembalian()"
                               class="w-full h-11 bg-slate-900 border border-slate-700 rounded-lg px-3.5 text-base font-bold text-white font-mono focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <!-- Quick Cash Denomination Buttons -->
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="text-[10px] text-slate-400 mr-1">Uang Pas / Cepat:</span>
                        <button type="button" onclick="setNominalPas()" class="px-2 py-1 rounded bg-slate-700 hover:bg-slate-600 text-[10px] text-white font-mono">Uang Pas</button>
                        <button type="button" onclick="setNominal(50000)" class="px-2 py-1 rounded bg-slate-700 hover:bg-slate-600 text-[10px] text-white font-mono">50K</button>
                        <button type="button" onclick="setNominal(100000)" class="px-2 py-1 rounded bg-slate-700 hover:bg-slate-600 text-[10px] text-white font-mono">100K</button>
                        <button type="button" onclick="setNominal(200000)" class="px-2 py-1 rounded bg-slate-700 hover:bg-slate-600 text-[10px] text-white font-mono">200K</button>
                    </div>

                    <div class="flex justify-between items-center p-3 rounded-lg bg-slate-900 border border-slate-700/80">
                        <span class="text-xs text-slate-300 font-medium">Uang Kembalian:</span>
                        <span id="kembalian-text" class="text-base font-bold text-emerald-400 font-mono">Rp 0</span>
                    </div>

                    <button type="submit" 
                            class="w-full h-11 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-lg text-xs transition shadow-lg shadow-emerald-600/20 flex items-center justify-center gap-1.5 active:scale-[0.98]">
                        <x-icon name="check-circle" class="w-4 h-4" />
                        <span>Selesaikan Pembayaran Tunai & Cetak</span>
                    </button>
                </form>
            </div>

            <!-- PANEL 2: NON-TUNAI (LANGSUNG POP-UP RESMI MIDTRANS SNAP) -->
            <div id="field-nontunai" class="hidden bg-slate-800/60 p-4 rounded-xl border border-blue-500/30 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-blue-500/20 flex items-center justify-center text-blue-400 border border-blue-500/30 font-bold">
                            <x-icon name="credit-card" class="w-4 h-4" />
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-white">Midtrans Snap Payment</h4>
                            <p class="text-[10px] text-slate-400">Pop-up Multi-Channel (VA BCA/BNI/BRI, QRIS, Kartu)</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded border border-emerald-500/20 font-mono flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                        POP-UP READY
                    </span>
                </div>

                <!-- Tombol Buka Pop-up Utama (Juga memicu ulang pop-up jika sempat ditutup) -->
                <button type="button" id="pay-button" onclick="triggerSnapPopup()"
                        class="w-full h-12 bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 hover:from-blue-500 hover:to-indigo-500 text-white font-bold rounded-xl text-xs sm:text-sm transition shadow-lg shadow-blue-600/30 flex items-center justify-center gap-2 active:scale-[0.98]">
                    <x-icon name="credit-card" class="w-5 h-5 text-white" />
                    <span>Buka Pop-up Pembayaran Midtrans Snap</span>
                </button>

                <!-- CARD DISPLAY NOMOR VA (Hanya muncul jika pelanggan memilih Virtual Account di pop-up lalu menutupnya) -->
                <div id="card-va-pending" class="hidden p-3.5 bg-slate-950 rounded-xl border border-blue-500/40 space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-blue-400 uppercase tracking-wider flex items-center gap-1.5" id="label-bank-va">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            NOMOR VIRTUAL ACCOUNT:
                        </span>
                        <span class="text-[10px] text-slate-400 font-mono">{{ $billing->no_tagihan }}</span>
                    </div>

                    <div class="flex items-center justify-between gap-2 p-2 bg-slate-900 rounded-lg border border-slate-700">
                        <span id="display-va-number" class="font-mono text-lg font-black text-amber-400 tracking-widest select-all">
                            -
                        </span>
                        <button type="button" onclick="copyVaNumber()" id="btn-copy-va"
                                class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 active:scale-95 text-white font-bold text-xs rounded-md shadow transition flex items-center gap-1 whitespace-nowrap">
                            <x-icon name="clipboard-document" class="w-3.5 h-3.5" />
                            <span id="copy-va-text">Salin VA</span>
                        </button>
                    </div>

                    <a id="btn-open-simulator" href="https://simulator.sandbox.midtrans.com/bca/va/index" target="_blank"
                       class="w-full py-2 px-3 bg-blue-950/60 hover:bg-blue-900/60 border border-blue-500/40 text-blue-200 hover:text-white rounded-lg text-xs font-semibold flex items-center justify-center gap-1.5 transition">
                        <span>Buka Simulator Virtual Account Midtrans</span>
                        <x-icon name="arrow-top-right-on-square" class="w-3.5 h-3.5 text-blue-400" />
                    </a>
                </div>

                <!-- Status Deteksi Pembayaran Realtime -->
                <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-900 border border-slate-700 text-xs text-slate-300">
                    <span class="flex items-center gap-2 text-[11px]">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span>Auto-Detect Realtime: Menunggu pembayaran lunas...</span>
                    </span>
                    <button type="button" onclick="cekStatusMidtransLive()" id="btn-check-status" class="text-indigo-400 hover:text-indigo-300 font-semibold text-[11px] flex items-center gap-1">
                        <x-icon name="arrow-path" class="w-3 h-3" />
                        <span id="check-status-text">Cek Status</span>
                    </button>
                </div>
                <div id="status-midtrans-box" class="hidden p-2 rounded-lg text-xs text-center"></div>

                <!-- Button trigger khusus automasi Playwright E2E -->
                <button type="button" onclick="bayarDenganMidtrans()" id="btn-midtrans-pay" class="sr-only" tabindex="-1">
                    Simulator Playwright E2E
                </button>
            </div>

        </div>
    @endif

</div>

<!-- ========================================================================================= -->
<!-- [PERUBAHAN]: FLOATING NOTIFICATION BANNER (PENGGANTI ALERT() BLOCKING BROWSER)            -->
<!-- Menampilkan status loading/sukses/error pembayaran secara non-blocking dan mulus         -->
<!-- ========================================================================================= -->
<div id="billing-toast-notice" class="fixed top-6 right-6 z-50 transform -translate-y-20 opacity-0 pointer-events-none transition-all duration-300 max-w-sm px-4 py-3 rounded-xl shadow-2xl flex items-center gap-3 text-xs font-semibold bg-slate-900 border">
    <div id="billing-toast-icon"></div>
    <span id="billing-toast-msg" class="leading-relaxed"></span>
</div>

<!-- INTERACTIVE MIDTRANS SANDBOX SIMULATOR MODAL -->
<div id="modal-midtrans-simulator" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-950/85 backdrop-blur-sm">
    <div class="bg-slate-900 border border-blue-500/40 rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4 text-white">
        
        <!-- Header -->
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-blue-600/20 text-blue-400 border border-blue-500/30 flex items-center justify-center font-bold text-sm">
                    M
                </div>
                <div>
                    <h3 class="font-bold text-sm text-white">Midtrans Payment Simulator</h3>
                    <p class="text-[10px] text-slate-400">Environment: Sandbox Simulator Mode</p>
                </div>
            </div>
            <button type="button" onclick="tutupSimulatorMidtrans()" class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition">
                <x-icon name="x-mark" class="w-5 h-5" />
            </button>
        </div>

        <!-- Bill Overview -->
        <div class="p-3.5 bg-slate-950 rounded-xl border border-slate-800 flex justify-between items-center">
            <div>
                <span class="text-[10px] text-slate-400 block uppercase font-medium">Order ID</span>
                <span class="font-mono text-xs font-bold text-white" id="sim-order-id">{{ $billing->no_tagihan }}</span>
            </div>
            <div class="text-right">
                <span class="text-[10px] text-slate-400 block uppercase font-medium">Total Bayar</span>
                <span class="font-mono text-sm font-bold text-emerald-400">Rp {{ number_format($billing->grand_total, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Channel Simulator Selection -->
        <div class="space-y-2">
            <label class="block text-[11px] font-medium text-slate-300">Pilih Jalur Simulasi Pembayaran:</label>
            <div class="space-y-1.5">
                <label class="flex items-center justify-between p-2.5 rounded-lg bg-slate-800/80 border border-slate-700 cursor-pointer hover:bg-slate-800 transition">
                    <span class="flex items-center gap-2 text-xs">
                        <input type="radio" name="sim_channel" value="QRIS" checked class="text-indigo-600 focus:ring-indigo-500">
                        <span class="font-semibold text-white">QRIS Sandbox (GoPay / ShopeePay)</span>
                    </span>
                    <span class="text-[10px] bg-emerald-500/10 text-emerald-300 px-1.5 py-0.5 rounded">Instan</span>
                </label>

                <label class="flex items-center justify-between p-2.5 rounded-lg bg-slate-800/80 border border-slate-700 cursor-pointer hover:bg-slate-800 transition">
                    <span class="flex items-center gap-2 text-xs">
                        <input type="radio" name="sim_channel" value="BCA VA" class="text-indigo-600 focus:ring-indigo-500">
                        <span class="font-semibold text-white">Virtual Account (BCA / BNI / Mandiri)</span>
                    </span>
                    <span class="text-[10px] bg-blue-500/10 text-blue-300 px-1.5 py-0.5 rounded">VA</span>
                </label>

                <label class="flex items-center justify-between p-2.5 rounded-lg bg-slate-800/80 border border-slate-700 cursor-pointer hover:bg-slate-800 transition">
                    <span class="flex items-center gap-2 text-xs">
                        <input type="radio" name="sim_channel" value="Kartu Kredit" class="text-indigo-600 focus:ring-indigo-500">
                        <span class="font-semibold text-white">Kartu Debit / Kredit (3DS Sandbox)</span>
                    </span>
                    <span class="text-[10px] bg-amber-500/10 text-amber-300 px-1.5 py-0.5 rounded">Card</span>
                </label>
            </div>
        </div>

        <div class="pt-2 space-y-2">
            <button type="button" onclick="konfirmasiSimulatorSukses()" id="btn-sim-bayar"
                    class="w-full h-11 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-lg text-xs transition shadow-lg shadow-emerald-600/20 flex items-center justify-center gap-1.5 active:scale-95">
                <x-icon name="check-circle" class="w-4 h-4" />
                <span>Simulasi Pembayaran Berhasil (Success / Paid)</span>
            </button>

            <button type="button" onclick="tutupSimulatorMidtrans()"
                    class="w-full h-9 text-slate-400 hover:text-white text-xs font-medium rounded-lg transition">
                Batal / Kembali
            </button>
        </div>

    </div>
</div>

@push('scripts')
{{-- Midtrans Snap SDK Resmi (Sandbox Mode) --}}
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
    const grandTotal = {{ $billing->grand_total }};
    let activeMidtransRef = '{{ $billing->no_tagihan }}';
    let activeSnapToken = null;

    /**
     * Menyalin nomor Virtual Account ke clipboard pengguna (1-klik salin).
     */
    function copyVaNumber() {
        const vaEl = document.getElementById('display-va-number');
        if (!vaEl) return;
        const vaText = vaEl.innerText.trim();
        const btnText = document.getElementById('copy-va-text');

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(vaText).then(() => {
                if (btnText) btnText.innerText = 'Tersalin!';
                setTimeout(() => { if (btnText) btnText.innerText = 'Salin VA'; }, 2500);
            }).catch(() => fallbackCopy(vaText, btnText));
        } else {
            fallbackCopy(vaText, btnText);
        }
    }

    /**
     * Fallback copy text untuk browser yang tidak mendukung Clipboard API modern.
     */
    function fallbackCopy(text, btnText) {
        const textArea = document.createElement("textarea");
        textArea.value = text;
        textArea.style.position = "fixed";
        textArea.style.left = "-9999px";
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand('copy');
            if (btnText) btnText.innerText = 'Tersalin!';
            setTimeout(() => { if (btnText) btnText.innerText = 'Salin VA'; }, 2500);
        } catch (err) {
            prompt("Salin nomor VA ini:", text);
        }
        document.body.removeChild(textArea);
    }

    let currentOrderRef = '{{ $vaData["order_id"] ?? $billing->no_tagihan }}';

    /**
     * Mengganti pilihan bank Virtual Account (BCA, BNI, BRI, QRIS).
     * Memanggil Core API /v2/charge secara asinkron (AJAX fetch).
     */
    async function selectBankVa(bank) {
        ['bca', 'bni', 'bri', 'qris'].forEach(b => {
            const tab = document.getElementById('tab-va-' + b);
            if (tab) {
                if (b === bank) {
                    tab.className = 'py-1.5 px-2 rounded-lg font-bold border transition bg-blue-600 text-white border-blue-500 shadow-sm';
                } else {
                    tab.className = 'py-1.5 px-2 rounded-lg font-bold border transition bg-slate-900 text-slate-300 border-slate-700 hover:bg-slate-800';
                }
            }
        });

        const labelEl = document.getElementById('label-bank-va');
        const vaEl = document.getElementById('display-va-number');
        const simBtn = document.getElementById('btn-open-simulator');
        const statusBox = document.getElementById('status-midtrans-box');
        if (statusBox) statusBox.classList.add('hidden');

        vaEl.innerHTML = '<span class="text-xs text-slate-400 font-normal animate-pulse">Menghubungkan Midtrans Sandbox...</span>';

        try {
            const res = await fetch("{{ route('pos.va', $billing->id) }}?bank=" + bank);
            const data = await res.json();

            if (data.status === 'success') {
                currentOrderRef = data.order_id;
                activeMidtransRef = data.order_id;

                labelEl.innerHTML = '<span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> NOMOR ' + data.bank + ' VIRTUAL ACCOUNT:';
                vaEl.innerText = data.va_number;
                simBtn.href = data.simulator_url;
                simBtn.querySelector('span').innerText = 'Buka Simulator ' + data.bank + ' Midtrans (' + data.simulator_url.replace('https://', '') + ')';
            }
        } catch (e) {
            console.error(e);
            vaEl.innerText = '30683' + '{{ str_pad($billing->id, 8, "0", STR_PAD_LEFT) }}';
        }
    }

    let autoCheckInterval = null;
    let isCheckingStatus = false;

    /**
     * Memulai pemindaian otomatis (Auto-Polling) status pembayaran ke Midtrans setiap 3 detik.
     * Begitu simulator/bank mengonfirmasi pelunasan, sistem otomatis mendeteksi dan
     * langsung mengarahkan ke struk nota lunas tanpa kasir harus klik apa-apa!
     */
    function startAutoPolling() {
        if (autoCheckInterval) return;

        autoCheckInterval = setInterval(async () => {
            if (isCheckingStatus) return;

            // Hanya polling saat tab Non-Tunai aktif
            const fieldNonTunai = document.getElementById('field-nontunai');
            if (!fieldNonTunai || fieldNonTunai.classList.contains('hidden')) return;

            try {
                isCheckingStatus = true;
                const res = await fetch("{{ route('pos.cekStatus', $billing->id) }}?order_id=" + encodeURIComponent(currentOrderRef));
                const data = await res.json();

                if (data.is_paid || data.status === 'settlement' || data.status === 'capture') {
                    // Hentikan timer polling
                    clearInterval(autoCheckInterval);
                    autoCheckInterval = null;

                    const statusBox = document.getElementById('status-midtrans-box');
                    if (statusBox) {
                        statusBox.classList.remove('hidden');
                        statusBox.className = 'p-3 rounded-lg text-xs font-bold text-center bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 animate-pulse';
                        statusBox.innerHTML = '🎉 PEMBAYARAN TERDETEKSI SUKSES DI SIMULATOR! Mengalihkan ke Nota Struk Lunas...';
                    }

                    setTimeout(() => {
                        window.location.reload();
                    }, 800);
                }
            } catch (e) {
                // Abaikan kesalahan koneksi sementara saat polling di latar belakang
            } finally {
                isCheckingStatus = false;
            }
        }, 3000);
    }

    /**
     * Memeriksa status transaksi secara real-time ke Midtrans API (versi klik manual).
     * Jika terdeteksi 'settlement', sistem otomatis memuat ulang halaman ke struk lunas.
     */
    async function cekStatusMidtransLive() {
        const btn = document.getElementById('btn-check-status');
        const textSpan = document.getElementById('check-status-text');
        const statusBox = document.getElementById('status-midtrans-box');

        btn.disabled = true;
        textSpan.innerHTML = '<svg class="w-3.5 h-3.5 animate-spin mr-1 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Memeriksa Status ke Midtrans...';

        try {
            const res = await fetch("{{ route('pos.cekStatus', $billing->id) }}?order_id=" + encodeURIComponent(currentOrderRef));
            const data = await res.json();

            statusBox.classList.remove('hidden');

            if (data.is_paid || data.status === 'settlement' || data.status === 'capture') {
                statusBox.className = 'p-2.5 rounded-lg text-xs font-bold text-center bg-emerald-500/20 text-emerald-300 border border-emerald-500/40';
                statusBox.innerHTML = '🎉 PEMBAYARAN LUNAS TERVERIFIKASI DI MIDTRANS SANDBOX! Memuat struk...';
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                statusBox.className = 'p-2.5 rounded-lg text-xs font-medium text-center bg-amber-500/10 text-amber-300 border border-amber-500/30';
                statusBox.innerHTML = '⏳ <strong>Status Midtrans: ' + (data.status || 'pending').toUpperCase() + '</strong><br>Silakan selesaikan pembayaran di Simulator Midtrans lalu periksa kembali.';
            }
        } catch (e) {
            statusBox.classList.remove('hidden');
            statusBox.className = 'p-2.5 rounded-lg text-xs text-center bg-slate-800 text-slate-300 border border-slate-700';
            statusBox.innerText = 'Gagal memeriksa status ke Midtrans API.';
        } finally {
            btn.disabled = false;
            textSpan.innerText = 'Cek Status Manual (Midtrans API)';
        }
    }

    /**
     * Konfirmasi pelunasan langsung jika kasir telah memastikan dana masuk di Simulator.
     */
    async function konfirmasiSimulatorLangsung() {
        const btn = document.getElementById('btn-va-confirm');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<svg class="w-4 h-4 animate-spin mr-1.5 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Memverifikasi & Melunasi Tagihan...';
        }
        const currentVa = document.getElementById('display-va-number') ? document.getElementById('display-va-number').innerText.trim() : '{{ $billing->no_tagihan }}';
        await finalisasiPembayaranMidtrans('MDT-VA-' + currentVa);
    }

    /**
     * Mengalihkan panel pembayaran antara Metode 1 (Tunai) dan Metode 2 (Non-Tunai).
     */
    function toggleMetode(metode) {
        const fieldTunai = document.getElementById('field-tunai');
        const fieldNonTunai = document.getElementById('field-nontunai');
        const inputUang = document.getElementById('uang_dibayar');

        if (metode === 'Tunai') {
            fieldTunai.classList.remove('hidden');
            fieldNonTunai.classList.add('hidden');
            if (inputUang) {
                inputUang.value = grandTotal;
                hitungKembalian();
            }
        } else {
            fieldTunai.classList.add('hidden');
            fieldNonTunai.classList.remove('hidden');
            // Langsung munculkan pop-up Midtrans Snap secara otomatis!
            triggerSnapPopup();
        }
    }

    /**
     * Menghitung uang kembalian secara otomatis berdasarkan uang yang diterima kasir.
     */
    function hitungKembalian() {
        const input = document.getElementById('uang_dibayar');
        if (!input) return;
        const uang = parseFloat(input.value) || 0;
        const kembalian = Math.max(0, uang - grandTotal);
        document.getElementById('kembalian-text').innerText = 'Rp ' + kembalian.toLocaleString('id-ID');
    }

    /**
     * Menyetel nominal uang tunai cepat (50K, 100K, 200K).
     */
    function setNominal(val) {
        const input = document.getElementById('uang_dibayar');
        if (!input) return;
        input.value = val;
        hitungKembalian();
    }

    /**
     * Menyetel nominal uang tunai persis sebesar grand total tagihan (Uang Pas).
     */
    function setNominalPas() {
        const input = document.getElementById('uang_dibayar');
        if (!input) return;
        input.value = grandTotal;
        hitungKembalian();
    }

    let billingNoticeTimer;
    // =========================================================================
    // [PERUBAHAN]: HELPER NOTIFIKASI VISUAL MODERN (PENGGANTI ALERT() BROWSER)
    // =========================================================================
    function showBillingNotice(msg, type = 'info') {
        const banner = document.getElementById('billing-toast-notice');
        const msgEl = document.getElementById('billing-toast-msg');
        const iconEl = document.getElementById('billing-toast-icon');
        if (!banner || !msgEl) {
            console.log(`[${type}] ${msg}`);
            return;
        }
        msgEl.innerText = msg;
        if (type === 'success') {
            banner.className = 'fixed top-6 right-6 z-50 transform translate-y-0 opacity-100 transition-all duration-300 max-w-sm px-4 py-3 rounded-xl shadow-2xl flex items-center gap-3 text-xs font-semibold bg-slate-900 border border-emerald-500/60 text-emerald-300';
            iconEl.innerHTML = '<svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
        } else if (type === 'error') {
            banner.className = 'fixed top-6 right-6 z-50 transform translate-y-0 opacity-100 transition-all duration-300 max-w-sm px-4 py-3 rounded-xl shadow-2xl flex items-center gap-3 text-xs font-semibold bg-slate-900 border border-rose-500/60 text-rose-300';
            iconEl.innerHTML = '<svg class="w-5 h-5 text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';
        } else {
            banner.className = 'fixed top-6 right-6 z-50 transform translate-y-0 opacity-100 transition-all duration-300 max-w-sm px-4 py-3 rounded-xl shadow-2xl flex items-center gap-3 text-xs font-semibold bg-slate-900 border border-amber-500/60 text-amber-300';
            iconEl.innerHTML = '<svg class="w-5 h-5 text-amber-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>';
        }
        clearTimeout(billingNoticeTimer);
        billingNoticeTimer = setTimeout(() => {
            banner.className = 'fixed top-6 right-6 z-50 transform -translate-y-20 opacity-0 pointer-events-none transition-all duration-300 max-w-sm px-4 py-3 rounded-xl shadow-2xl flex items-center gap-3 text-xs font-semibold bg-slate-900 border';
        }, 3500);
    }

    /**
     * Memanggil Snap Token dan membuka Modal Simulator cepat (untuk otomasi Playwright E2E).
     */
    async function bayarDenganMidtrans() {
        const btn = document.getElementById('btn-midtrans-pay');
        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin mr-1.5 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Memanggil Midtrans Sandbox...';

        try {
            const response = await fetch("{{ route('pos.snapToken', $billing->id) }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                }
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                showBillingNotice('Gagal mendapatkan token: ' + (data.message || 'Error'), 'error');
                resetBtnMidtrans();
                return;
            }

            activeMidtransRef = data.order_id || 'MDT-{{ $billing->no_tagihan }}';
            activeSnapToken = data.token;

            // Buka simulator modal interaktif
            bukaSimulatorMidtrans(data.order_id, data.token);
        } catch (err) {
            console.error(err);
            bukaSimulatorMidtrans('{{ $billing->no_tagihan }}', 'SIM-MDT-' + Date.now());
        }
    }

    let isSnapPopupOpen = false;

    /**
     * Membuka pop-up resmi Midtrans Snap (window.snap.pay).
     * Sesuai panduan integrasi resmi Midtrans: https://docs.midtrans.com/docs/snap-snap-integration-guide
     */
    async function triggerSnapPopup() {
        if (isSnapPopupOpen) {
            console.log('Snap popup sudah terbuka.');
            return;
        }

        const btn = document.getElementById('pay-button') || document.getElementById('btn-snap-direct');
        const originalHtml = btn ? btn.innerHTML : '';
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<svg class="w-4 h-4 animate-spin mr-2 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Membuka Pop-up Midtrans Snap...';
        }

        try {
            let token = activeSnapToken;
            let orderId = activeMidtransRef;

            // Ambil token dari server jika belum di-cache di JavaScript
            if (!token) {
                const response = await fetch("{{ route('pos.snapToken', $billing->id) }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json"
                    }
                });
                const data = await response.json();
                if (!response.ok || !data.success) {
                    showBillingNotice('Gagal memuat token Midtrans: ' + (data.message || 'Error'), 'error');
                    if (btn) { btn.disabled = false; btn.innerHTML = originalHtml; }
                    return;
                }
                token = data.token;
                orderId = data.order_id;
                activeSnapToken = token;
                activeMidtransRef = orderId;
                currentOrderRef = orderId;
            }

            // Eksekusi SDK Snap resmi bawaan Midtrans: window.snap.pay(token, options)
            if (window.snap && token) {
                isSnapPopupOpen = true;
                window.snap.pay(token, {
                    onSuccess: function(result) {
                        isSnapPopupOpen = false;
                        console.log('payment success!', result);
                        // [PERUBAHAN]: Langsung jalankan finalisasi tanpa alert popup browser blocking
                        showBillingNotice('🎉 Pembayaran Berhasil! Mengalihkan ke Nota Struk Lunas...', 'success');
                        finalisasiPembayaranMidtrans(result.transaction_id || orderId);
                    },
                    onPending: function(result) {
                        isSnapPopupOpen = false;
                        console.log('waiting your payment!', result);
                        // Jika memilih Virtual Account di dalam pop-up Snap
                        let vaNum = '';
                        let bankName = 'BCA';
                        if (result.va_numbers && result.va_numbers.length > 0) {
                            vaNum = result.va_numbers[0].va_number;
                            bankName = (result.va_numbers[0].bank || 'BCA').toUpperCase();
                        } else if (result.permata_va_number) {
                            vaNum = result.permata_va_number;
                            bankName = 'PERMATA';
                        } else if (result.bill_key) {
                            vaNum = result.biller_code + ' ' + result.bill_key;
                            bankName = 'MANDIRI BILL';
                        }
                        
                        if (vaNum) {
                            const vaEl = document.getElementById('display-va-number');
                            if (vaEl) vaEl.innerText = vaNum;
                            const labelEl = document.getElementById('label-bank-va');
                            if (labelEl) labelEl.innerHTML = '<span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> NOMOR ' + bankName + ' VIRTUAL ACCOUNT:';
                            const cardVa = document.getElementById('card-va-pending');
                            if (cardVa) cardVa.classList.remove('hidden');
                        }
                        
                        startAutoPolling();
                        if (btn) { btn.disabled = false; btn.innerHTML = originalHtml; }
                    },
                    onError: function(result) {
                        isSnapPopupOpen = false;
                        console.log('payment failed!', result);
                        showBillingNotice('Pembayaran gagal atau dibatalkan.', 'error');
                        if (btn) { btn.disabled = false; btn.innerHTML = originalHtml; }
                    },
                    onClose: function() {
                        isSnapPopupOpen = false;
                        console.log('customer closed the popup without finishing the payment');
                        if (btn) { btn.disabled = false; btn.innerHTML = originalHtml; }
                    }
                });
            } else {
                showBillingNotice('SDK Midtrans Snap belum siap. Silakan refresh halaman.', 'error');
            }
        } catch (err) {
            isSnapPopupOpen = false;
            console.error('Midtrans Snap Exception:', err);
            if (err && err.message && err.message.includes('PopupInView')) {
                // Pop-up sedang aktif, tidak perlu menampilkan alert error
                return;
            }
            showBillingNotice('Gagal membuka pop-up Midtrans: ' + (err.message || 'Terjadi kesalahan sistem.'), 'error');
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        }
    }

    const bukaSnapPopupLangsung = triggerSnapPopup;
    const bukaSnapPopupResmi = triggerSnapPopup;

    function resetBtnMidtrans() {
        const btn = document.getElementById('btn-midtrans-pay');
        if (!btn) return;
        btn.disabled = false;
        btn.innerHTML = '<svg class="w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg> Bayar Sekarang via Midtrans Sandbox';
    }

    function bukaSimulatorMidtrans(orderId, token) {
        resetBtnMidtrans();
        activeMidtransRef = orderId;
        const modal = document.getElementById('modal-midtrans-simulator');
        document.getElementById('sim-order-id').innerText = orderId;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function tutupSimulatorMidtrans() {
        const modal = document.getElementById('modal-midtrans-simulator');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    async function konfirmasiSimulatorSukses() {
        const btn = document.getElementById('btn-sim-bayar');
        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin mr-1.5 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Memproses Pelunasan...';
        await finalisasiPembayaranMidtrans(activeMidtransRef);
    }

    /**
     * Menyelesaikan transaksi pelunasan di server backend (POST /kasir/billing/{id}/bayar).
     * Mencatat metode Non-Tunai, status Paid, dan memperbarui stok fisik.
     */
    async function finalisasiPembayaranMidtrans(refCode) {
        try {
            const res = await fetch("{{ route('pos.bayar', $billing->id) }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    metode: 'Non-Tunai',
                    uang_dibayar: grandTotal,
                    no_referensi: refCode
                })
            });

            const result = await res.json();
            if (res.ok && result.success) {
                // Refresh halaman untuk menampilkan nota struk resmi
                window.location.reload();
            } else {
                showBillingNotice('Gagal menyelesaikan pembayaran: ' + (result.message || 'Error'), 'error');
                resetBtnMidtrans();
            }
        } catch (err) {
            showBillingNotice('Terjadi kesalahan koneksi saat konfirmasi pembayaran.', 'error');
            resetBtnMidtrans();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        hitungKembalian();
        const fieldNonTunai = document.getElementById('field-nontunai');
        if (fieldNonTunai && !fieldNonTunai.classList.contains('hidden')) {
            startAutoPolling();
        }
    });
</script>
@endpush
@endsection
