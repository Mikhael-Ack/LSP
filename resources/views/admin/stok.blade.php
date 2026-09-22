@extends('layouts.app')

@section('title', 'Kelola Menu & Stok — Dapur Ina Aina')

@section('content')
<div class="space-y-5">
    
    <!-- Page Header Card -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-900 p-5 rounded-xl border border-slate-800 shadow-md">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-300 bg-indigo-500/20 px-2 py-0.5 rounded border border-indigo-500/30">
                    Katalog & Persediaan
                </span>
                <span class="text-[10px] font-medium text-slate-400">
                    Role: <strong class="text-white capitalize">{{ data_get(session('user'), 'role') }}</strong>
                </span>
            </div>
            <h2 class="text-xl font-bold tracking-tight text-white font-heading mt-1.5 flex items-center gap-2">
                <x-icon name="cube" class="w-5 h-5 text-indigo-400" />
                <span>Manajemen Menu & Stok Fisik</span>
            </h2>
            <p class="text-xs text-slate-400 mt-0.5">Kelola data menu, tambah produk baru, perbarui harga satuan, dan kontrol status ketersediaan secara real-time.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <button onclick="openModalTambah()" 
                    class="h-9 px-4 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-md shadow-emerald-600/20 transition flex items-center gap-1.5 active:scale-95">
                <x-icon name="plus" class="w-4 h-4" />
                <span>Tambah Menu Baru</span>
            </button>
            <div class="hidden md:flex items-center gap-2 text-xs">
                <span class="px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 font-medium text-slate-300">
                    Total: <strong class="text-white">{{ $produkList->count() }}</strong>
                </span>
                <span class="px-3 py-2 rounded-lg bg-emerald-500/10 text-emerald-300 font-medium border border-emerald-500/20">
                    Tersedia: <strong class="text-white">{{ $produkList->where('status', 'Tersedia')->count() }}</strong>
                </span>
            </div>
        </div>
    </div>

    <!-- Stock & Menu DataTable Card -->
    <div class="bg-slate-900 rounded-xl shadow-md border border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-950 text-slate-400 font-semibold uppercase border-b border-slate-800 text-[11px]">
                    <tr>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">Nama Menu</th>
                        <th class="px-4 py-3 text-right">Harga Satuan</th>
                        <th class="px-4 py-3 text-center">Stok Cepat</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Aksi Manajemen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($produkList as $produk)
                        <tr class="hover:bg-slate-800/50 transition">
                            <td class="px-4 py-3.5">
                                <span class="text-[11px] font-medium px-2.5 py-1 rounded-md border flex items-center gap-1.5 w-fit {{ $produk->kategori_id == 1 ? 'bg-amber-500/10 text-amber-300 border-amber-500/20' : ($produk->kategori_id == 2 ? 'bg-orange-500/10 text-orange-300 border-orange-500/20' : 'bg-cyan-500/10 text-cyan-300 border-cyan-500/20') }}">
                                    @if($produk->kategori_id == 1) <x-icon name="fire" class="w-3.5 h-3.5 text-amber-400" />
                                    @elseif($produk->kategori_id == 2) <x-icon name="sparkles" class="w-3.5 h-3.5 text-orange-400" />
                                    @else <x-icon name="beaker" class="w-3.5 h-3.5 text-cyan-400" />
                                    @endif
                                    {{ $produk->kategori->nama_kategori }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="font-semibold text-white text-sm block">{{ $produk->nama_produk }}</span>
                                @if($produk->deskripsi)
                                    <span class="text-[11px] text-slate-400 block mt-0.5 line-clamp-1">{{ $produk->deskripsi }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-right font-semibold text-slate-200 font-mono">
                                Rp {{ number_format($produk->harga, 0, ',', '.') }}
                            </td>
                            
                            <!-- Hidden Forms for Valid HTML5 Table Structure -->
                            <form id="form-stok-{{ $produk->id }}" action="{{ route('admin.stok.update', $produk->id) }}" method="POST">
                                @csrf
                            </form>
                            <form id="form-del-{{ $produk->id }}" action="{{ route('admin.produk.destroy', $produk->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                            </form>

                            <td class="px-4 py-3.5 text-center">
                                <input form="form-stok-{{ $produk->id }}" type="number" name="stok" value="{{ $produk->stok }}" min="0" 
                                       class="w-16 text-center bg-slate-800 border border-slate-700 rounded-lg py-1 px-2 text-xs font-semibold text-white focus:bg-slate-900 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <select form="form-stok-{{ $produk->id }}" name="status" class="text-xs font-semibold rounded-lg border py-1 px-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none cursor-pointer transition {{ $produk->status === 'Tersedia' ? 'bg-emerald-500/10 text-emerald-300 border-emerald-500/30' : 'bg-rose-500/10 text-rose-300 border-rose-500/30' }}">
                                    <option value="Tersedia" {{ $produk->status === 'Tersedia' ? 'selected' : '' }}>● Tersedia</option>
                                    <option value="Habis" {{ $produk->status === 'Habis' ? 'selected' : '' }}>● Habis</option>
                                </select>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button form="form-stok-{{ $produk->id }}" type="submit" title="Simpan perubahan stok"
                                            class="h-7 px-2.5 inline-flex items-center justify-center rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-[11px] font-medium shadow-sm transition active:scale-95">
                                        Update
                                    </button>

                                    <!-- Edit Menu Button -->
                                    <button type="button" 
                                            onclick='openModalEdit(@json($produk))'
                                            class="h-7 w-7 inline-flex items-center justify-center rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 transition active:scale-95"
                                            title="Edit Detail Menu Lengkap">
                                        <x-icon name="pencil-square" class="w-3.5 h-3.5 text-amber-400" />
                                    </button>

                                    <!-- Delete Menu Button -->
                                    <button form="form-del-{{ $produk->id }}" type="submit" 
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus menu {{ addslashes($produk->nama_produk) }}?')"
                                            class="h-7 w-7 inline-flex items-center justify-center rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/20 transition active:scale-95"
                                            title="Hapus Menu">
                                        <x-icon name="trash" class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-slate-400">
                                Belum ada menu terdaftar. Silakan klik <strong>+ Tambah Menu Baru</strong>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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

<!-- MODAL EDIT MENU -->
<div id="modal-edit-menu" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <h3 class="font-bold text-base text-white flex items-center gap-2">
                <x-icon name="pencil-square" class="w-5 h-5 text-amber-400" />
                <span>Edit Detail Menu</span>
            </h3>
            <button type="button" onclick="closeModalEdit()" class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition">
                <x-icon name="x-mark" class="w-5 h-5" />
            </button>
        </div>

        <form id="form-edit-menu" method="POST" class="space-y-3.5">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Menu *</label>
                <input type="text" id="edit_nama_produk" name="nama_produk" required
                       class="w-full h-10 bg-slate-800 border border-slate-700 rounded-lg px-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Kategori *</label>
                    <select id="edit_kategori_id" name="kategori_id" required class="w-full h-10 bg-slate-800 border border-slate-700 rounded-lg px-3 text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach($kategoriList as $kat)
                            <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Harga Satuan (Rp) *</label>
                    <input type="number" id="edit_harga" name="harga" required min="0" step="500"
                           class="w-full h-10 bg-slate-800 border border-slate-700 rounded-lg px-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Stok Fisik *</label>
                    <input type="number" id="edit_stok" name="stok" required min="0"
                           class="w-full h-10 bg-slate-800 border border-slate-700 rounded-lg px-3 text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Status Ketersediaan *</label>
                    <select id="edit_status" name="status" required class="w-full h-10 bg-slate-800 border border-slate-700 rounded-lg px-3 text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="Tersedia">● Tersedia</option>
                        <option value="Habis">● Habis</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Deskripsi</label>
                <input type="text" id="edit_deskripsi" name="deskripsi" placeholder="Keterangan porsi atau bahan"
                       class="w-full h-10 bg-slate-800 border border-slate-700 rounded-lg px-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-800">
                <button type="button" onclick="closeModalEdit()" 
                        class="px-4 py-2 rounded-lg text-xs font-medium text-slate-400 hover:text-white hover:bg-slate-800 transition">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 rounded-lg text-xs font-semibold bg-indigo-600 hover:bg-indigo-500 text-white shadow-md transition active:scale-95 flex items-center gap-1.5">
                    <x-icon name="check" class="w-4 h-4" />
                    <span>Perbarui Menu</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
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

    function openModalEdit(produk) {
        const modal = document.getElementById('modal-edit-menu');
        const form = document.getElementById('form-edit-menu');
        
        form.action = `/admin/produk/${produk.id}`;
        document.getElementById('edit_nama_produk').value = produk.nama_produk;
        document.getElementById('edit_kategori_id').value = produk.kategori_id;
        document.getElementById('edit_harga').value = produk.harga;
        document.getElementById('edit_stok').value = produk.stok;
        document.getElementById('edit_status').value = produk.status;
        document.getElementById('edit_deskripsi').value = produk.deskripsi || '';

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModalEdit() {
        const modal = document.getElementById('modal-edit-menu');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>
@endpush
@endsection
