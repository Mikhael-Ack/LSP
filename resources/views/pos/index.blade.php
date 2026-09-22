@extends('layouts.app')

@section('title', 'POS Kasir — Dapur Ina Aina')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6" id="pos-app">
    
    <!-- LEFT 8 COLS: MENU CATALOG -->
    <div class="lg:col-span-8 space-y-5">
        
        <!-- Category Segmented Controls & Add Item Button -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 bg-slate-900 p-2 rounded-xl border border-slate-800 shadow-sm">
            <div class="flex items-center gap-1.5 overflow-x-auto p-0.5">
                <button onclick="filterKategori('all')" id="btn-tab-all"
                        class="tab-btn px-4 py-2 rounded-lg text-xs font-semibold transition flex items-center gap-1.5 bg-indigo-600 text-white shadow-sm whitespace-nowrap">
                    <x-icon name="squares-2x2" class="w-4 h-4" />
                    <span>Semua Menu</span>
                </button>
                @foreach($kategoriList as $kat)
                    <button onclick="filterKategori('{{ $kat->id }}')" id="btn-tab-{{ $kat->id }}"
                            class="tab-btn px-4 py-2 rounded-lg text-xs font-medium text-slate-400 hover:text-white hover:bg-slate-800 transition flex items-center gap-1.5 whitespace-nowrap">
                        @if($kat->id == 1) <x-icon name="fire" class="w-4 h-4 text-amber-400" />
                        @elseif($kat->id == 2) <x-icon name="sparkles" class="w-4 h-4 text-orange-400" />
                        @else <x-icon name="beaker" class="w-4 h-4 text-cyan-400" />
                        @endif
                        <span>{{ $kat->nama_kategori }}</span>
                    </button>
                @endforeach
            </div>

            <!-- Quick Add Menu Item Button for Staff / Kasir / Admin -->
            <button onclick="openModalTambah()" 
                    class="px-3.5 py-2 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-sm transition flex items-center justify-center gap-1.5 whitespace-nowrap active:scale-95">
                <x-icon name="plus" class="w-4 h-4" />
                <span>+ Tambah Menu</span>
            </button>
        </div>

        <!-- Menu Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4" id="menu-container">
            @foreach($produkList as $produk)
                @php $isAvailable = ($produk->status === 'Tersedia' && $produk->stok > 0); @endphp
                <div class="menu-item group bg-slate-900 rounded-xl p-4 border border-slate-800 shadow-sm transition flex flex-col justify-between select-none {{ $isAvailable ? 'cursor-pointer hover:border-indigo-500/70 hover:shadow-lg hover:shadow-indigo-500/10 active:scale-[0.98]' : 'opacity-60 cursor-not-allowed' }}"
                     data-kategori="{{ $produk->kategori_id }}"
                     @if($isAvailable) onclick="addToCart({{ $produk->id }}, '{{ addslashes($produk->nama_produk) }}', {{ $produk->harga }}, {{ $produk->stok }})" @endif>
                    
                    <div>
                        <!-- Header with Icon & Stock Badge -->
                        <div class="flex justify-between items-start mb-3">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center text-lg bg-slate-800 border border-slate-700">
                                @if($produk->kategori_id == 1) <x-icon name="fire" class="w-5 h-5 text-amber-400" />
                                @elseif($produk->kategori_id == 2) <x-icon name="sparkles" class="w-5 h-5 text-orange-400" />
                                @else <x-icon name="beaker" class="w-5 h-5 text-cyan-400" />
                                @endif
                            </div>
                            
                            @if($isAvailable)
                                <span class="text-[11px] font-medium text-emerald-400 bg-emerald-500/10 px-2.5 py-0.5 rounded-full border border-emerald-500/20 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Stok: {{ $produk->stok }}
                                </span>
                            @else
                                <span class="text-[11px] font-medium text-rose-400 bg-rose-500/10 px-2.5 py-0.5 rounded-full border border-rose-500/20 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Habis
                                </span>
                            @endif
                        </div>

                        <!-- Product Info -->
                        <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                            {{ $produk->kategori->nama_kategori }}
                        </span>
                        <h3 class="font-semibold text-white text-sm mt-0.5 leading-snug group-hover:text-indigo-300 transition">
                            {{ $produk->nama_produk }}
                        </h3>
                    </div>

                    <!-- Price & Action -->
                    <div class="mt-4 pt-3 border-t border-slate-800 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-slate-400 block font-medium">Harga</span>
                            <span class="text-sm font-bold text-white font-mono">
                                Rp {{ number_format($produk->harga, 0, ',', '.') }}
                            </span>
                        </div>

                        @if($isAvailable)
                            <button type="button"
                                    onclick="event.stopPropagation(); addToCart({{ $produk->id }}, '{{ addslashes($produk->nama_produk) }}', {{ $produk->harga }}, {{ $produk->stok }})"
                                    class="h-8 w-8 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white flex items-center justify-center font-bold text-sm shadow-sm transition active:scale-90"
                                    title="Klik untuk tambah ke keranjang">
                                <x-icon name="plus" class="w-4 h-4" />
                            </button>
                        @else
                            <button disabled class="h-8 w-8 rounded-lg bg-slate-800 text-slate-600 flex items-center justify-center text-xs cursor-not-allowed">
                                <x-icon name="minus" class="w-3 h-3" />
                            </button>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>

    </div>

    <!-- RIGHT 4 COLS: ORDER SUMMARY / CART -->
    <div class="lg:col-span-4">
        <div class="bg-slate-900 rounded-xl p-5 shadow-xl border border-slate-800 sticky top-20 flex flex-col justify-between">
            
            <div>
                <!-- Cart Header -->
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <div>
                        <h2 class="font-semibold text-sm text-white flex items-center gap-1.5">
                            <x-icon name="receipt" class="w-4 h-4 text-indigo-400" />
                            <span>Rincian Pesanan Meja</span>
                        </h2>
                        <p class="text-[11px] text-slate-400">Masukkan nomor meja & kuantitas menu</p>
                    </div>
                    <button onclick="clearCart()" class="text-xs font-medium text-slate-500 hover:text-rose-400 transition">Reset</button>
                </div>

                <!-- Table Input -->
                <div class="my-4">
                    <label class="block text-[11px] font-medium text-slate-300 mb-1.5">Nomor Meja Pelanggan *</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500">
                            <x-icon name="map-pin" class="w-4 h-4" />
                        </span>
                        <input type="text" id="no_meja" placeholder="Contoh: Meja 05" 
                               class="w-full h-10 bg-slate-800 border border-slate-700 rounded-lg pl-9 pr-3 text-xs font-semibold text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>
                </div>

                <!-- Cart Items Container -->
                <div class="space-y-2 max-h-72 overflow-y-auto pr-1 my-2" id="cart-items">
                    <div class="text-center py-10">
                        <x-icon name="cart" class="w-10 h-10 text-slate-700 mx-auto mb-2" />
                        <p class="text-xs font-medium text-slate-400">Keranjang masih kosong</p>
                        <p class="text-[10px] text-slate-500 mt-0.5">Pilih menu dari daftar katalog</p>
                    </div>
                </div>
            </div>

            <!-- Billing & Checkout Calculation -->
            <div class="mt-4 pt-4 border-t border-slate-800 space-y-2.5">
                <div class="flex justify-between text-xs text-slate-400">
                    <span>Subtotal Menu</span>
                    <span id="subtotal-val" class="font-semibold text-white font-mono">Rp 0</span>
                </div>
                <div class="flex justify-between text-xs text-slate-400">
                    <span class="flex items-center gap-1">
                        Pajak PB1 (10%)
                        <span class="text-[10px] bg-slate-800 text-slate-400 px-1.5 py-0.2 rounded font-medium border border-slate-700">Resto</span>
                    </span>
                    <span id="pajak-val" class="font-semibold text-white font-mono">Rp 0</span>
                </div>

                <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800 flex justify-between items-center mt-3">
                    <div>
                        <span class="text-[10px] uppercase font-semibold text-slate-400 block tracking-wider">Total Tagihan</span>
                        <span class="text-base font-bold text-amber-400 font-mono" id="grandtotal-val">Rp 0</span>
                    </div>
                    <span class="text-[10px] bg-slate-800 text-slate-300 px-2 py-0.5 rounded-md font-mono border border-slate-700">PB1 Included</span>
                </div>

                <!-- Action Button -->
                <button onclick="prosesPesanan()" id="btn-submit-order"
                        class="w-full h-11 mt-2 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-lg text-xs transition shadow-lg shadow-emerald-600/20 flex items-center justify-center gap-1.5 active:scale-[0.98]">
                    <x-icon name="printer" class="w-4 h-4" />
                    <span>Terbitkan Billing / Tagihan</span>
                </button>
            </div>

        </div>
    </div>

</div>

<!-- MODAL TAMBAH MENU BARU -->
<div id="modal-tambah-menu" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <h3 class="font-bold text-base text-white flex items-center gap-2">
                <x-icon name="plus" class="w-5 h-5 text-emerald-400" />
                <span>Tambah Menu Baru</span>
            </h3>
            <button type="button" onclick="closeModalTambah()" class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition">
                <x-icon name="x-mark" class="w-5 h-5" />
            </button>
        </div>

        <form action="{{ route('admin.produk.store') }}" method="POST" class="space-y-3.5">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Menu *</label>
                <input type="text" name="nama_produk" required placeholder="Contoh: Es Teh Manis Jumbo"
                       class="w-full h-10 bg-slate-800 border border-slate-700 rounded-lg px-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Kategori *</label>
                    <select name="kategori_id" required class="w-full h-10 bg-slate-800 border border-slate-700 rounded-lg px-3 text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach($kategoriList as $kat)
                            <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Harga Satuan (Rp) *</label>
                    <input type="number" name="harga" required min="0" step="500" placeholder="7000"
                           class="w-full h-10 bg-slate-800 border border-slate-700 rounded-lg px-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Stok Awal *</label>
                    <input type="number" name="stok" required min="0" value="25"
                           class="w-full h-10 bg-slate-800 border border-slate-700 rounded-lg px-3 text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Status Ketersediaan *</label>
                    <select name="status" required class="w-full h-10 bg-slate-800 border border-slate-700 rounded-lg px-3 text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="Tersedia">● Tersedia</option>
                        <option value="Habis">● Habis</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Deskripsi (Opsional)</label>
                <input type="text" name="deskripsi" placeholder="Keterangan porsi atau bahan"
                       class="w-full h-10 bg-slate-800 border border-slate-700 rounded-lg px-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-800">
                <button type="button" onclick="closeModalTambah()" 
                        class="px-4 py-2 rounded-lg text-xs font-medium text-slate-400 hover:text-white hover:bg-slate-800 transition">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-md transition active:scale-95 flex items-center gap-1.5">
                    <x-icon name="check" class="w-4 h-4" />
                    <span>Simpan Menu</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Floating Toast Notification -->
<div id="toast-notif" class="fixed bottom-6 right-6 z-50 transform translate-y-20 opacity-0 pointer-events-none transition-all duration-300 bg-slate-900 border border-emerald-500/40 text-white px-4 py-2.5 rounded-xl shadow-2xl flex items-center gap-2.5 text-xs font-semibold">
    <x-icon name="check" class="w-5 h-5 text-emerald-400" id="toast-icon-check" />
    <span id="toast-msg">Menu ditambahkan ke pesanan</span>
</div>

<!-- ========================================================================================= -->
<!-- [PERUBAHAN]: MODAL POP-UP VALIDASI INTERAKTIF (PENGGANTI ALERT() BAWAAN BROWSER)            -->
<!-- Muncul jika kasir belum memilih menu atau belum mengisi nomor meja saat klik tagihan      -->
<!-- ========================================================================================= -->
<div id="modal-validasi" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm transition-opacity duration-200">
    <div class="bg-slate-900 border border-slate-700/80 rounded-2xl max-w-sm w-full p-6 shadow-2xl text-center relative transform transition-all scale-100 duration-150">
        <!-- Icon Bulat Peringatan Dinamis (Kuning/Merah) -->
        <div id="modal-validasi-icon-bg" class="w-14 h-14 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-400 mx-auto mb-4 flex items-center justify-center shadow-lg">
            <svg id="modal-validasi-icon-warn" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <svg id="modal-validasi-icon-error" class="w-7 h-7 hidden text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        <h3 id="modal-validasi-title" class="text-base font-bold text-white mb-1.5">Harap Lengkapi Data</h3>
        <p id="modal-validasi-msg" class="text-xs text-slate-300 leading-relaxed mb-5">Silakan pilih menu pesanan terlebih dahulu.</p>

        <button type="button" onclick="closeModalValidasi()" id="btn-tutup-validasi"
                class="w-full py-2.5 px-4 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-600/30 transition active:scale-95 flex items-center justify-center gap-1.5">
            <span>Mengerti & Lengkapi</span>
        </button>
    </div>
</div>

@push('scripts')
<script>
    let cart = [];
    let onModalValidasiClose = null;

    function openModalTambah() {
        const modal = document.getElementById('modal-tambah-menu');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModalTambah() {
        const modal = document.getElementById('modal-tambah-menu');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // =========================================================================
    // [PERUBAHAN]: HELPER MODAL POP-UP VALIDASI (MENGGANTIKAN SEMUA ALERT() NATIVE)
    // =========================================================================
    function showPosAlert(title, message, type = 'warning', onCloseCallback = null) {
        const modal = document.getElementById('modal-validasi');
        const titleEl = document.getElementById('modal-validasi-title');
        const msgEl = document.getElementById('modal-validasi-msg');
        const iconBg = document.getElementById('modal-validasi-icon-bg');
        const iconWarn = document.getElementById('modal-validasi-icon-warn');
        const iconError = document.getElementById('modal-validasi-icon-error');
        
        if (titleEl) titleEl.innerText = title;
        if (msgEl) msgEl.innerText = message;
        
        if (type === 'error') {
            iconBg.className = 'w-14 h-14 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-400 mx-auto mb-4 flex items-center justify-center shadow-lg';
            iconWarn.classList.add('hidden');
            iconError.classList.remove('hidden');
        } else {
            iconBg.className = 'w-14 h-14 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-400 mx-auto mb-4 flex items-center justify-center shadow-lg';
            iconWarn.classList.remove('hidden');
            iconError.classList.add('hidden');
        }
        
        onModalValidasiClose = onCloseCallback;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Auto-focus tombol tutup agar kasir bisa langsung tekan Enter/Spasi
        setTimeout(() => {
            const btn = document.getElementById('btn-tutup-validasi');
            if (btn) btn.focus();
        }, 50);
    }

    function closeModalValidasi() {
        const modal = document.getElementById('modal-validasi');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        if (typeof onModalValidasiClose === 'function') {
            const cb = onModalValidasiClose;
            onModalValidasiClose = null;
            cb();
        }
    }

    let toastTimer;
    function showToast(msg, type = 'success') {
        const toast = document.getElementById('toast-notif');
        const toastMsg = document.getElementById('toast-msg');
        toastMsg.innerText = msg;

        if (type === 'warning') {
            toast.className = 'fixed bottom-6 right-6 z-50 transform translate-y-0 opacity-100 transition-all duration-300 bg-slate-900 border border-amber-500/60 text-amber-300 px-4 py-2.5 rounded-xl shadow-2xl flex items-center gap-2.5 text-xs font-semibold';
        } else {
            toast.className = 'fixed bottom-6 right-6 z-50 transform translate-y-0 opacity-100 transition-all duration-300 bg-slate-900 border border-emerald-500/60 text-white px-4 py-2.5 rounded-xl shadow-2xl flex items-center gap-2.5 text-xs font-semibold';
        }

        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => {
            toast.classList.add('translate-y-20', 'opacity-0', 'pointer-events-none');
        }, 2200);
    }

    function filterKategori(katId) {
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('bg-indigo-600', 'text-white', 'shadow-sm');
            btn.classList.add('text-slate-400');
        });

        const activeBtn = document.getElementById('btn-tab-' + katId);
        if (activeBtn) {
            activeBtn.classList.add('bg-indigo-600', 'text-white', 'shadow-sm');
            activeBtn.classList.remove('text-slate-400');
        }

        const items = document.querySelectorAll('.menu-item');
        items.forEach(item => {
            if (katId === 'all' || item.dataset.kategori === katId) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    function addToCart(id, nama, harga, maxStok) {
        const existing = cart.find(i => i.id === id);
        if (existing) {
            if (existing.qty < maxStok) {
                existing.qty++;
            } else {
                showToast('⚠️ Maksimal stok ' + nama + ' hanya ' + maxStok, 'warning');
                return;
            }
        } else {
            cart.push({ id, nama, harga, qty: 1, maxStok });
        }
        showToast('+' + nama + ' ditambahkan ke pesanan');
        renderCart();
    }

    function changeQty(id, delta) {
        const item = cart.find(i => i.id === id);
        if (!item) return;

        item.qty += delta;
        if (item.qty <= 0) {
            cart = cart.filter(i => i.id !== id);
        } else if (item.qty > item.maxStok) {
            item.qty = item.maxStok;
            showToast('⚠️ Maksimal stok tercapai (' + item.maxStok + ')', 'warning');
        }
        renderCart();
    }

    function clearCart() {
        cart = [];
        renderCart();
    }

    function renderCart() {
        const container = document.getElementById('cart-items');
        if (cart.length === 0) {
            container.innerHTML = `
                <div class="text-center py-10">
                    <svg class="w-10 h-10 text-slate-700 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0z" /></svg>
                    <p class="text-xs font-medium text-slate-400">Keranjang masih kosong</p>
                    <p class="text-[10px] text-slate-500 mt-0.5">Pilih menu dari daftar katalog</p>
                </div>
            `;
            document.getElementById('subtotal-val').innerText = 'Rp 0';
            document.getElementById('pajak-val').innerText = 'Rp 0';
            document.getElementById('grandtotal-val').innerText = 'Rp 0';
            return;
        }

        let html = '';
        let subtotal = 0;

        cart.forEach(item => {
            const itemSubtotal = item.harga * item.qty;
            subtotal += itemSubtotal;
            html += `
                <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-800 border border-slate-700/80 hover:bg-slate-800/80 transition">
                    <div class="flex-1 pr-2">
                        <h4 class="font-medium text-xs text-white">${item.nama}</h4>
                        <span class="text-[11px] font-semibold text-indigo-400 font-mono">Rp ${item.harga.toLocaleString('id-ID')}</span>
                    </div>
                    <div class="flex items-center gap-1 bg-slate-900 border border-slate-700 rounded-md p-0.5">
                        <button onclick="changeQty(${item.id}, -1)" class="w-5 h-5 rounded hover:bg-slate-800 font-bold text-xs text-slate-400 hover:text-white flex items-center justify-center">-</button>
                        <span class="font-bold text-xs w-5 text-center text-white">${item.qty}</span>
                        <button onclick="changeQty(${item.id}, 1)" class="w-5 h-5 rounded hover:bg-slate-800 font-bold text-xs text-slate-400 hover:text-white flex items-center justify-center">+</button>
                    </div>
                </div>
            `;
        });

        const pajak = Math.round(subtotal * 0.10);
        const grandtotal = subtotal + pajak;

        container.innerHTML = html;
        document.getElementById('subtotal-val').innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
        document.getElementById('pajak-val').innerText = 'Rp ' + pajak.toLocaleString('id-ID');
        document.getElementById('grandtotal-val').innerText = 'Rp ' + grandtotal.toLocaleString('id-ID');
    }

    // =========================================================================
    // [PERUBAHAN]: LOGIKA PROSES PESANAN & VALIDASI SEBELUM TERBITKAN BILLING
    // Memastikan keranjang tidak kosong dan nomor meja sudah diisi kasir
    // =========================================================================
    async function prosesPesanan() {
        // [VALIDASI KONDISI 1]: Keranjang Menu harus memiliki minimal 1 item
        if (cart.length === 0) {
            showPosAlert(
                'Menu Belum Dipilih',
                'Harap memilih Menu terlebih dahulu sebelum menerbitkan billing / tagihan!',
                'warning'
            );
            return;
        }

        // [VALIDASI KONDISI 2]: Nomor Meja harus diisi (tidak boleh kosong/spasi)
        const noMejaInput = document.getElementById('no_meja');
        const noMeja = noMejaInput ? noMejaInput.value.trim() : '';
        if (!noMeja) {
            // Beri efek highlight merah di border input nomor meja
            if (noMejaInput) {
                noMejaInput.classList.add('border-rose-500', 'ring-2', 'ring-rose-500/50');
            }
            showPosAlert(
                'Nomor Meja Kosong',
                'Harap mengisi Nomor Meja pelanggan terlebih dahulu!',
                'warning',
                () => {
                    // Otomatis kursor fokus ke input no meja setelah modal ditutup
                    if (noMejaInput) noMejaInput.focus();
                }
            );
            return;
        }

        const btn = document.getElementById('btn-submit-order');
        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin mr-1.5 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Memproses Pesanan...';

        try {
            const response = await fetch("{{ route('pos.pesan') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    no_meja: noMeja,
                    items: cart.map(i => ({ id: i.id, qty: i.qty }))
                })
            });

            const data = await response.json();
            if (response.ok && data.success) {
                window.location.href = data.redirect;
            } else {
                showPosAlert('Gagal Menerbitkan Billing', data.message || 'Terjadi kesalahan sistem saat memproses pesanan.', 'error');
                btn.disabled = false;
                btn.innerHTML = '<svg class="w-4 h-4 mr-1.5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg> Terbitkan Billing / Tagihan';
            }
        } catch (err) {
            showPosAlert('Kesalahan Koneksi', 'Gagal menghubungi server. Pastikan koneksi internet aktif.', 'error');
            btn.disabled = false;
            btn.innerHTML = '<svg class="w-4 h-4 mr-1.5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg> Terbitkan Billing / Tagihan';
        }
    }

    // =========================================================================
    // [PERUBAHAN]: HILANGKAN HIGHLIGHT MERAH OTOMATIS SAAT KASIR MENGETIK NO MEJA
    // =========================================================================
    document.addEventListener('DOMContentLoaded', function() {
        const noMejaInput = document.getElementById('no_meja');
        if (noMejaInput) {
            noMejaInput.addEventListener('input', function() {
                this.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500/50');
            });
        }
    });
</script>
@endpush
@endsection
