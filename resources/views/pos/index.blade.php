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
    <x-icon name="check" class="w-5 h-5 text-emerald-400" />
    <span id="toast-msg">Menu ditambahkan ke pesanan</span>
</div>

@push('scripts')
<script>
    let cart = [];

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

    let toastTimer;
    function showToast(msg) {
        const toast = document.getElementById('toast-notif');
        const toastMsg = document.getElementById('toast-msg');
        toastMsg.innerText = msg;
        toast.classList.remove('translate-y-20', 'opacity-0', 'pointer-events-none');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => {
            toast.classList.add('translate-y-20', 'opacity-0', 'pointer-events-none');
        }, 1800);
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
                alert('Maksimal stok tersedia hanya ' + maxStok);
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
            alert('Maksimal stok tercapai!');
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

    async function prosesPesanan() {
        const noMeja = document.getElementById('no_meja').value.trim();
        if (!noMeja) {
            alert('Silakan isi Nomor Meja terlebih dahulu!');
            document.getElementById('no_meja').focus();
            return;
        }

        if (cart.length === 0) {
            alert('Pilih minimal satu menu untuk membuat pesanan!');
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
                alert('Gagal: ' + (data.message || 'Terjadi kesalahan sistem'));
                btn.disabled = false;
                btn.innerHTML = '<svg class="w-4 h-4 mr-1.5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg> Terbitkan Billing / Tagihan';
            }
        } catch (err) {
            alert('Gagal menghubungi server!');
            btn.disabled = false;
            btn.innerHTML = '<svg class="w-4 h-4 mr-1.5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg> Terbitkan Billing / Tagihan';
        }
    }
</script>
@endpush
@endsection
