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

            <!-- PANEL 2: NON-TUNAI (MIDTRANS SANDBOX SIMULATOR) -->
            <div id="field-nontunai" class="hidden bg-slate-800/60 p-4 sm:p-5 rounded-xl border border-blue-500/30 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-700/80 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-blue-500/20 flex items-center justify-center text-blue-400 border border-blue-500/30 font-bold">
                            <x-icon name="credit-card" class="w-4 h-4" />
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-white">Midtrans Sandbox Payment Gateway</h4>
                            <p class="text-[10px] text-slate-400">Virtual Account & Simulator Terintegrasi</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded border border-emerald-500/20 font-mono flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                        SANDBOX ACTIVE
                    </span>
                </div>

                <!-- Bank Selection Pills -->
                <div>
                    <span class="text-[10px] text-slate-400 font-semibold block mb-1.5 uppercase tracking-wider">Pilih Virtual Account / Channel Simulator:</span>
                    <div class="grid grid-cols-4 gap-1.5 text-center text-xs">
                        <button type="button" onclick="selectBankVa('bca')" id="tab-va-bca" class="py-1.5 px-2 rounded-lg font-bold border transition bg-blue-600 text-white border-blue-500 shadow-sm">
                            BCA VA
                        </button>
                        <button type="button" onclick="selectBankVa('bni')" id="tab-va-bni" class="py-1.5 px-2 rounded-lg font-bold border transition bg-slate-900 text-slate-300 border-slate-700 hover:bg-slate-800">
                            BNI VA
                        </button>
                        <button type="button" onclick="selectBankVa('bri')" id="tab-va-bri" class="py-1.5 px-2 rounded-lg font-bold border transition bg-slate-900 text-slate-300 border-slate-700 hover:bg-slate-800">
                            BRI VA
                        </button>
                        <button type="button" onclick="selectBankVa('qris')" id="tab-va-qris" class="py-1.5 px-2 rounded-lg font-bold border transition bg-slate-900 text-slate-300 border-slate-700 hover:bg-slate-800">
                            QRIS
                        </button>
                    </div>
                </div>

                <!-- PROMINENT VIRTUAL ACCOUNT CARD -->
                <div class="p-4 bg-slate-950 rounded-xl border-2 border-blue-500/50 shadow-lg space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-blue-400 uppercase tracking-wider flex items-center gap-1.5" id="label-bank-va">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            NOMOR BCA VIRTUAL ACCOUNT:
                        </span>
                        <span class="text-[10px] text-slate-400 font-mono" id="label-order-id">{{ $billing->no_tagihan }}</span>
                    </div>

                    <!-- Huge VA Number Display & Copy Button -->
                    <div class="flex items-center justify-between gap-2 p-2.5 bg-slate-900 rounded-lg border border-slate-700">
                        <span id="display-va-number" class="font-mono text-xl sm:text-2xl font-black text-amber-400 tracking-widest select-all">
                            {{ $vaData['va_number'] ?? ('91012' . str_pad($billing->id, 8, '0', STR_PAD_LEFT)) }}
                        </span>
                        <button type="button" onclick="copyVaNumber()" id="btn-copy-va"
                                class="px-3.5 py-2 bg-blue-600 hover:bg-blue-500 active:scale-95 text-white font-bold text-xs rounded-md shadow transition flex items-center gap-1.5 whitespace-nowrap">
                            <x-icon name="clipboard-document" class="w-4 h-4" />
                            <span id="copy-va-text">Salin VA</span>
                        </button>
                    </div>

                    <!-- Direct Simulator Link Button -->
                    <a id="btn-open-simulator" href="https://simulator.sandbox.midtrans.com/bca/va/index" target="_blank"
                       class="w-full py-2.5 px-3 bg-gradient-to-r from-blue-900/60 to-indigo-900/60 hover:from-blue-800/80 hover:to-indigo-800/80 border border-blue-500/40 text-blue-200 hover:text-white rounded-lg text-xs font-semibold flex items-center justify-center gap-2 transition group shadow-sm">
                        <span>Buka Simulator BCA Midtrans (simulator.sandbox.midtrans.com/bca/va/index)</span>
                        <x-icon name="arrow-top-right-on-square" class="w-4 h-4 text-blue-400 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition" />
                    </a>

                    <!-- Step-by-Step Instructions -->
                    <div class="p-2.5 bg-slate-900/90 rounded-lg border border-slate-800 text-[11px] text-slate-300 space-y-1">
                        <p class="font-bold text-amber-300 flex items-center gap-1">
                            <x-icon name="information-circle" class="w-3.5 h-3.5" /> Panduan Pembayaran Simulator Midtrans:
                        </p>
                        <ol class="list-decimal list-inside space-y-0.5 text-slate-400 pl-1 text-[10.5px]">
                            <li>Klik tombol <strong class="text-white">Salin VA</strong> di atas.</li>
                            <li>Buka link simulator BCA Midtrans di atas: <strong class="text-blue-300">simulator.sandbox.midtrans.com/bca/va/index</strong></li>
                            <li>Tempel Nomor VA di kolom <strong class="text-white">VA Number</strong>, klik <strong class="text-white">Inquire</strong>, lalu klik <strong class="text-white">Pay</strong>.</li>
                            <li>Setelah status di simulator selesai, kembali ke halaman ini & klik tombol hijau di bawah.</li>
                        </ol>
                    </div>

                    <!-- Total Amount Callout -->
                    <div class="flex justify-between items-center pt-1 text-xs">
                        <span class="text-slate-400">Total Tagihan:</span>
                        <span class="font-mono font-bold text-base text-emerald-400">Rp {{ number_format($billing->grand_total, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Primary Action: Konfirmasi Sudah Bayar di Simulator -->
                <button type="button" onclick="konfirmasiSimulatorLangsung()" id="btn-va-confirm"
                        class="w-full h-11 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-lg text-xs transition shadow-lg shadow-emerald-600/20 flex items-center justify-center gap-2 active:scale-[0.98]">
                    <x-icon name="check-circle" class="w-4 h-4" />
                    <span>Saya Sudah Bayar di Simulator BCA (Konfirmasi Lunas & Cetak)</span>
                </button>

                <!-- Secondary Action: Open Snap / Simulator Modal (Preserves Playwright E2E compatibility) -->
                <div class="pt-1 border-t border-slate-700/80 flex items-center justify-between gap-2">
                    <button type="button" onclick="bayarDenganMidtrans()" id="btn-midtrans-pay"
                            class="w-full py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-medium rounded-lg text-[11px] transition border border-slate-700 flex items-center justify-center gap-1.5">
                        <x-icon name="arrows-pointing-out" class="w-3.5 h-3.5 text-blue-400" />
                        <span>Opsi Lain: Buka Popup Snap Midtrans / Simulator Modal</span>
                    </button>
                </div>
            </div>

        </div>
    @endif

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
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
    const grandTotal = {{ $billing->grand_total }};
    let activeMidtransRef = '{{ $billing->no_tagihan }}';

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

    const bankSimulatorConfig = {
        bca: {
            label: 'NOMOR BCA VIRTUAL ACCOUNT:',
            va: '{{ $vaData["va_number"] ?? ("91012" . str_pad($billing->id, 8, "0", STR_PAD_LEFT)) }}',
            url: 'https://simulator.sandbox.midtrans.com/bca/va/index',
            btnText: 'Buka Simulator BCA Midtrans (simulator.sandbox.midtrans.com/bca/va/index)'
        },
        bni: {
            label: 'NOMOR BNI VIRTUAL ACCOUNT:',
            va: '988{{ str_pad($billing->id, 8, "0", STR_PAD_LEFT) }}',
            url: 'https://simulator.sandbox.midtrans.com/bni/va/index',
            btnText: 'Buka Simulator BNI Midtrans (simulator.sandbox.midtrans.com/bni/va/index)'
        },
        bri: {
            label: 'NOMOR BRI VIRTUAL ACCOUNT:',
            va: '10777{{ str_pad($billing->id, 8, "0", STR_PAD_LEFT) }}',
            url: 'https://simulator.sandbox.midtrans.com/bri/va/index',
            btnText: 'Buka Simulator BRI Midtrans (simulator.sandbox.midtrans.com/bri/va/index)'
        },
        qris: {
            label: 'SIMULATOR QRIS SANDBOX:',
            va: 'QRIS-INA-{{ $billing->no_tagihan }}',
            url: 'https://simulator.sandbox.midtrans.com/qris/index',
            btnText: 'Buka Simulator QRIS Midtrans (simulator.sandbox.midtrans.com/qris/index)'
        }
    };

    function selectBankVa(bank) {
        const cfg = bankSimulatorConfig[bank] || bankSimulatorConfig.bca;
        const labelEl = document.getElementById('label-bank-va');
        if (labelEl) {
            labelEl.innerHTML = '<span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> ' + cfg.label;
        }
        const vaEl = document.getElementById('display-va-number');
        if (vaEl) {
            vaEl.innerText = cfg.va;
        }
        const simBtn = document.getElementById('btn-open-simulator');
        if (simBtn) {
            simBtn.href = cfg.url;
            const spanText = simBtn.querySelector('span');
            if (spanText) spanText.innerText = cfg.btnText;
        }

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

        activeMidtransRef = 'MDT-' + bank.toUpperCase() + '-' + cfg.va;
    }

    async function konfirmasiSimulatorLangsung() {
        const btn = document.getElementById('btn-va-confirm');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<svg class="w-4 h-4 animate-spin mr-1.5 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Memverifikasi & Melunasi Tagihan...';
        }
        const currentVa = document.getElementById('display-va-number') ? document.getElementById('display-va-number').innerText.trim() : '{{ $billing->no_tagihan }}';
        await finalisasiPembayaranMidtrans('MDT-VA-' + currentVa);
    }

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
        }
    }

    function hitungKembalian() {
        const input = document.getElementById('uang_dibayar');
        if (!input) return;
        const uang = parseFloat(input.value) || 0;
        const kembalian = Math.max(0, uang - grandTotal);
        document.getElementById('kembalian-text').innerText = 'Rp ' + kembalian.toLocaleString('id-ID');
    }

    function setNominal(val) {
        const input = document.getElementById('uang_dibayar');
        if (!input) return;
        input.value = val;
        hitungKembalian();
    }

    function setNominalPas() {
        const input = document.getElementById('uang_dibayar');
        if (!input) return;
        input.value = grandTotal;
        hitungKembalian();
    }

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
                alert('Gagal mendapatkan token: ' + (data.message || 'Error'));
                resetBtnMidtrans();
                return;
            }

            activeMidtransRef = data.order_id || 'MDT-{{ $billing->no_tagihan }}';

            // Jika Midtrans Live Sandbox Snap tersedia dan valid
            if (data.mode === 'midtrans_sandbox_live' && window.snap && typeof window.snap.pay === 'function') {
                window.snap.pay(data.token, {
                    onSuccess: function(result) {
                        finalisasiPembayaranMidtrans(result.transaction_id || activeMidtransRef);
                    },
                    onPending: function(result) {
                        finalisasiPembayaranMidtrans(result.transaction_id || activeMidtransRef);
                    },
                    onError: function(result) {
                        alert('Pembayaran Midtrans Sandbox gagal atau dibatalkan.');
                        resetBtnMidtrans();
                    },
                    onClose: function() {
                        resetBtnMidtrans();
                    }
                });
            } else {
                // Fallback simulator modal interaktif
                bukaSimulatorMidtrans(data.order_id, data.token);
            }
        } catch (err) {
            console.error(err);
            bukaSimulatorMidtrans('{{ $billing->no_tagihan }}', 'SIM-MDT-' + Date.now());
        }
    }

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
                window.location.reload();
            } else {
                alert('Gagal menyelesaikan pembayaran: ' + (result.message || 'Error'));
                resetBtnMidtrans();
            }
        } catch (err) {
            alert('Terjadi kesalahan koneksi saat konfirmasi pembayaran.');
            resetBtnMidtrans();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        hitungKembalian();
    });
</script>
@endpush
@endsection
