<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dapur Ina Aina — Sistem Kasir & Operasional')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
                        heading: ['Instrument Sans', 'Plus Jakarta Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0b0f19;
            color: #f8fafc;
        }
        h1, h2, h3, h4, .font-heading { font-family: 'Instrument Sans', sans-serif; }
        @media print {
            #main-sidebar, #sidebar-backdrop, header, footer, .no-print {
                display: none !important;
            }
            .min-h-screen, div.ml-0, div.lg\:ml-\[260px\] {
                margin-left: 0 !important;
                padding: 0 !important;
                min-height: auto !important;
            }
        }
    </style>
</head>
<body class="bg-[#0b0f19] min-h-screen antialiased text-slate-100 flex">

    <!-- Backdrop Overlay on Mobile -->
    <div id="sidebar-backdrop" onclick="toggleMobileSidebar()" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-40 hidden lg:hidden transition-opacity"></div>

    <!-- ============================================== -->
    <!-- LEFT SIDEBAR (Heroicons + Reusable Components) -->
    <!-- ============================================== -->
    <aside id="main-sidebar" class="w-[260px] bg-[#0f172a] border-r border-slate-800/80 fixed inset-y-0 left-0 z-50 flex flex-col justify-between -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out shadow-2xl lg:shadow-none">
        
        <!-- Sidebar Top: Brand & Menu Links -->
        <div class="p-4 space-y-6">
            
            <!-- Brand Logo & Mobile Close -->
            <div class="flex items-center justify-between">
                <a href="{{ url('/') }}" class="flex items-center gap-3 px-1 py-1 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-orange-600 text-white flex items-center justify-center shadow-lg shadow-orange-500/20 group-hover:scale-105 transition transform">
                        <x-icon name="building-storefront" class="w-5 h-5" />
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-sm font-bold tracking-tight text-white font-heading">Dapur Ina Aina</span>
                            <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-amber-500/20 text-amber-300 border border-amber-500/30 uppercase">Resto</span>
                        </div>
                        <p class="text-[11px] text-slate-400">Sistem Operasional & POS</p>
                    </div>
                </a>

                <!-- Mobile Close Button -->
                <button onclick="toggleMobileSidebar()" class="lg:hidden p-1.5 rounded-lg bg-slate-800 text-slate-400 hover:text-white border border-slate-700" aria-label="Tutup Menu">
                    <x-icon name="arrow-left" class="w-4 h-4" />
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="space-y-5 text-xs">
                
                <!-- Section: Operasional -->
                <div>
                    <span class="px-2.5 text-[10px] font-bold uppercase tracking-wider text-slate-500 block mb-1.5">
                        Operasional Kasir
                    </span>
                    <div class="space-y-1">
                        <a href="{{ route('pos.index') }}" 
                           class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl font-medium transition {{ request()->routeIs('pos.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                            <x-icon name="receipt" class="w-4 h-4" />
                            <span>POS Kasir & Meja</span>
                        </a>
                    </div>
                </div>

                <!-- Section: Manajemen Restoran -->
                <div>
                    <span class="px-2.5 text-[10px] font-bold uppercase tracking-wider text-slate-500 block mb-1.5">
                        Katalog & Stok Menu
                    </span>
                    <div class="space-y-1">
                        <a href="{{ route('admin.stok') }}" 
                           class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl font-medium transition {{ request()->routeIs('admin.stok*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                            <x-icon name="cube" class="w-4 h-4" />
                            <span>Kelola Menu & Stok</span>
                        </a>

                        @if(data_get(session('user'), 'role') === 'admin')
                            <a href="{{ route('admin.laporan') }}" 
                               class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl font-medium transition {{ request()->routeIs('admin.laporan*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                                <x-icon name="chart-bar" class="w-4 h-4" />
                                <span>Laporan Penjualan</span>
                            </a>
                        @else
                            <div class="flex items-center justify-between px-3 py-2 rounded-xl text-slate-600 opacity-60 cursor-not-allowed" title="Fitur khusus Administrator">
                                <span class="flex items-center gap-2.5">
                                    <x-icon name="chart-bar" class="w-4 h-4" />
                                    <span>Laporan Penjualan</span>
                                </span>
                                <x-icon name="lock" class="w-3.5 h-3.5" />
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Section: Navigasi Portal -->
                <div>
                    <span class="px-2.5 text-[10px] font-bold uppercase tracking-wider text-slate-500 block mb-1.5">
                        Portal Restoran
                    </span>
                    <div class="space-y-1">
                        <a href="{{ url('/') }}" 
                           class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/70 transition">
                            <x-icon name="globe" class="w-4 h-4" />
                            <span>Halaman Beranda</span>
                        </a>
                    </div>
                </div>

            </nav>

        </div>

        <!-- Sidebar Bottom: User Card & Role Switcher -->
        <div class="p-3 border-t border-slate-800/80 bg-slate-950/60">
            @if(session('user'))
                <div class="bg-slate-900 rounded-xl p-3 border border-slate-800 space-y-2.5">
                    <!-- User Info -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-slate-800 text-white flex items-center justify-center font-bold text-xs border border-slate-700">
                                {{ strtoupper(substr(data_get(session('user'), 'nama', 'U'), 0, 1)) }}
                            </div>
                            <div>
                                <span class="block text-xs font-semibold text-white leading-tight truncate max-w-[110px]">
                                    {{ data_get(session('user'), 'nama') }}
                                </span>
                                <span class="inline-flex items-center gap-1 text-[10px] font-medium {{ data_get(session('user'), 'role') === 'admin' ? 'text-amber-400' : 'text-cyan-400' }}">
                                    <x-icon :name="data_get(session('user'), 'role') === 'admin' ? 'shield-check' : 'user'" class="w-3 h-3" />
                                    {{ data_get(session('user'), 'role') === 'admin' ? 'Admin' : 'Kasir' }}
                                </span>
                            </div>
                        </div>

                        <!-- Logout Button -->
                        <a href="{{ route('logout') }}" title="Logout" 
                           class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-rose-600 hover:text-white text-slate-400 border border-slate-700 flex items-center justify-center text-xs transition">
                            <x-icon name="logout" class="w-3.5 h-3.5" />
                        </a>
                    </div>

                    <!-- Role Switch Action -->
                    <div class="pt-2 border-t border-slate-800">
                        @if(data_get(session('user'), 'role') === 'kasir')
                            <a href="{{ route('switch.role', 'admin') }}" 
                               class="w-full flex items-center justify-center gap-1.5 py-1.5 px-2 rounded-lg bg-amber-500/10 hover:bg-amber-500/20 text-amber-300 border border-amber-500/20 text-[11px] font-medium transition">
                                <x-icon name="arrow-path" class="w-3.5 h-3.5" /> Ganti ke Admin
                            </a>
                        @else
                            <a href="{{ route('switch.role', 'kasir') }}" 
                               class="w-full flex items-center justify-center gap-1.5 py-1.5 px-2 rounded-lg bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-300 border border-cyan-500/20 text-[11px] font-medium transition">
                                <x-icon name="arrow-path" class="w-3.5 h-3.5" /> Ganti ke Kasir
                            </a>
                        @endif
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="w-full flex items-center justify-center gap-2 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-sm transition">
                    <x-icon name="login" class="w-4 h-4" /> Masuk Akun
                </a>
            @endif
        </div>

    </aside>

    <!-- ============================================== -->
    <!-- MAIN CONTENT AREA                              -->
    <!-- ============================================== -->
    <div class="flex-1 ml-0 lg:ml-[260px] min-h-screen flex flex-col min-w-0 transition-all duration-300">
        
        <!-- Top App Bar -->
        <header class="h-14 bg-[#0f172a]/95 backdrop-blur-md border-b border-slate-800/80 px-4 sm:px-6 flex items-center justify-between sticky top-0 z-40">
            <!-- Left: Mobile Hamburger & Breadcrumb -->
            <div class="flex items-center gap-3">
                <button onclick="toggleMobileSidebar()" class="lg:hidden p-1.5 rounded-lg bg-slate-800 text-slate-300 hover:text-white border border-slate-700 shrink-0" aria-label="Buka Menu Navigasi">
                    <x-icon name="list-bullet" class="w-5 h-5" />
                </button>
                <div class="flex items-center gap-2 text-xs">
                    <span class="text-slate-400 font-medium hidden sm:inline">Sistem Restoran</span>
                    <span class="text-slate-600 hidden sm:inline">/</span>
                    <span class="text-white font-semibold truncate max-w-[150px] sm:max-w-none">
                        @if(request()->routeIs('pos.*')) POS Kasir
                        @elseif(request()->routeIs('admin.stok*')) Kelola Stok
                        @elseif(request()->routeIs('admin.laporan*')) Laporan Penjualan
                        @else Dashboard
                        @endif
                    </span>
                </div>
            </div>

            <!-- Server Status & Live Indicator -->
            <div class="flex items-center gap-2 sm:gap-3 text-xs">
                <div class="inline-flex items-center gap-1.5 px-2 sm:px-2.5 py-1 rounded-full bg-slate-900 border border-slate-800 text-[10px] sm:text-[11px] text-slate-300">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="hidden sm:inline">Sistem Terhubung</span>
                    <span class="sm:hidden">Online</span>
                </div>
                <span class="text-slate-500 text-[10px] sm:text-[11px] font-mono hidden xs:inline">{{ date('d M Y') }}</span>
            </div>
        </header>

        <!-- Main Body Content -->
        <main class="flex-grow p-3 sm:p-5 lg:p-6">
            
            <!-- Flash Alert Success -->
            @if(session('success'))
                <div class="mb-5 rounded-xl bg-emerald-500/10 border border-emerald-500/30 p-3.5 text-xs text-emerald-300 flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px] font-bold">
                            <x-icon name="check" class="w-3.5 h-3.5" />
                        </span>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Flash Alert Error -->
            @if(session('error'))
                <div class="mb-5 rounded-xl bg-rose-500/10 border border-rose-500/30 p-3.5 text-xs text-rose-300 flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-rose-600 text-white flex items-center justify-center text-[10px] font-bold">!</span>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-[#0f172a] border-t border-slate-800/80 py-3.5 px-4 sm:px-6 text-center text-xs text-slate-500">
            &copy; {{ date('Y') }} <b>Dapur Ina Aina</b> &bull; Uji Kompetensi Keahlian (UKK) Rekayasa Perangkat Lunak.
        </footer>

    </div>

    <script>
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('main-sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }
        }
    </script>
    @stack('scripts')
</body>
</html>
