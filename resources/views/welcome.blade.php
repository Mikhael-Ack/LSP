<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dapur Ina Aina — Sistem Manajemen Restoran & POS</title>
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
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        html { scroll-behavior: smooth; }
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0b0f19;
            color: #f8fafc;
            overflow-x: hidden;
        }
        h1, h2, h3, h4, .font-heading { font-family: 'Instrument Sans', sans-serif; }

        /* Scroll Reveal Animation */
        .reveal-on-scroll {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1), transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: opacity, transform;
        }
        .reveal-on-scroll.is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* 3D Container Scroll Tilt Effect (Claude Template Style) */
        .perspective-container {
            perspective: 1200px;
        }
        .device-tilt {
            transform: rotateX(10deg) scale(0.96);
            transition: transform 0.9s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.9s ease;
            transform-origin: center top;
        }
        .device-tilt.is-upright {
            transform: rotateX(0deg) scale(1);
        }
    </style>
</head>
<body class="bg-[#0b0f19] text-slate-100 antialiased min-h-screen flex flex-col selection:bg-indigo-600 selection:text-white">

    <!-- Top Scroll Progress Indicator Bar -->
    <div id="scroll-progress-bar" class="fixed top-0 left-0 h-[2.5px] bg-gradient-to-r from-indigo-500 via-cyan-400 to-emerald-400 z-[100] transition-all duration-75" style="width: 0%"></div>

    <!-- Top Navigation -->
    <header class="sticky top-0 z-50 bg-[#0f172a]/95 backdrop-blur-md border-b border-slate-800 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <!-- Brand -->
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 group">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-amber-500 to-orange-600 text-white flex items-center justify-center shadow-md shadow-orange-500/20 group-hover:scale-105 transition shrink-0">
                    <x-icon name="building-storefront" class="w-5 h-5 text-white" />
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-sm font-bold tracking-tight text-white font-heading">Dapur Ina Aina</span>
                        <span class="hidden sm:inline-flex text-[10px] font-bold px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-300 border border-amber-500/30 uppercase tracking-wider">POS Resto</span>
                    </div>
                    <p class="text-[11px] text-slate-400 hidden xs:block">Sistem Operasional Meja & Kasir</p>
                </div>
            </a>

            <!-- Desktop Navigation Links -->
            <nav class="hidden md:flex items-center space-x-6 text-xs font-medium text-slate-400">
                <a href="#simulasi-pos" class="hover:text-white transition">Simulasi Terminal POS</a>
                <a href="#alur" class="hover:text-white transition">Alur Operasional</a>
                <a href="#katalog" class="hover:text-white transition">Daftar Menu</a>
                <a href="#spesifikasi" class="hover:text-white transition">Modul Sistem</a>
            </nav>

            <!-- User Actions & Mobile Toggle -->
            <div class="flex items-center gap-2 sm:gap-3">
                @if(session('user'))
                    <div class="hidden sm:flex items-center gap-2 bg-slate-900 px-3 py-1.5 rounded-xl border border-slate-800 text-xs">
                        <span class="text-slate-400">Kasir:</span>
                        <strong class="text-white font-medium truncate max-w-[100px]">{{ data_get(session('user'), 'nama') }}</strong>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ data_get(session('user'), 'role') === 'admin' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30' }}">
                            {{ strtoupper(data_get(session('user'), 'role')) }}
                        </span>
                    </div>

                    @if(data_get(session('user'), 'role') === 'admin')
                        <a href="{{ route('admin.stok') }}" class="h-9 px-3.5 inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-medium transition shadow-sm">
                            <x-icon name="sliders" class="w-4 h-4" /> <span class="hidden sm:inline">Panel Admin</span>
                        </a>
                    @else
                        <a href="{{ route('pos.index') }}" class="h-9 px-3.5 inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-medium transition shadow-sm">
                            <x-icon name="receipt" class="w-4 h-4" /> <span class="hidden sm:inline">Buka Kasir</span>
                        </a>
                    @endif

                    <a href="{{ route('logout') }}" title="Logout" class="w-9 h-9 rounded-lg border border-slate-700 bg-slate-800 hover:bg-rose-600 hover:text-white text-slate-400 transition flex items-center justify-center text-xs">
                        <x-icon name="logout" class="w-4 h-4" />
                    </a>
                @else
                    <a href="{{ route('login') }}" class="h-9 px-3.5 sm:px-4 inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold transition shadow-md shadow-indigo-600/20">
                        <x-icon name="login" class="w-4 h-4" /> <span>Masuk</span>
                    </a>
                @endif

                <!-- Mobile Hamburger Toggle -->
                <button onclick="toggleMobileMenu()" class="md:hidden p-2 rounded-lg bg-slate-800 text-slate-300 hover:text-white border border-slate-700" aria-label="Buka Menu">
                    <x-icon name="list-bullet" class="w-5 h-5" />
                </button>
            </div>
        </div>

        <!-- Mobile Drawer Menu -->
        <div id="mobile-nav-drawer" class="md:hidden hidden border-t border-slate-800 bg-[#0f172a] px-4 py-3 space-y-2 text-xs">
            <a href="#simulasi-pos" onclick="toggleMobileMenu()" class="block py-2 text-slate-300 hover:text-white font-medium">Simulasi Terminal POS</a>
            <a href="#alur" onclick="toggleMobileMenu()" class="block py-2 text-slate-300 hover:text-white font-medium">Alur Operasional Restoran</a>
            <a href="#katalog" onclick="toggleMobileMenu()" class="block py-2 text-slate-300 hover:text-white font-medium">Katalog Menu 3 Kategori</a>
            <a href="#spesifikasi" onclick="toggleMobileMenu()" class="block py-2 text-slate-300 hover:text-white font-medium">Spesifikasi Modul Kasir</a>
        </div>
    </header>

    <!-- ============================================================= -->
    <!-- 1. FULLSCREEN HERO SECTION (Dedicated Viewport Height)        -->
    <!-- ============================================================= -->
    <section class="relative min-h-[calc(100vh-4rem)] flex flex-col justify-between items-center px-4 sm:px-6 lg:px-8 py-10 sm:py-16 bg-gradient-to-b from-[#0f172a] via-[#0b0f19] to-[#0b0f19] border-b border-slate-800 overflow-hidden">
        
        <!-- Subtle Tech Grid Background Pattern (Claude Template Style) -->
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#33415518_1px,transparent_1px),linear-gradient(to_bottom,#33415518_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_40%,#000_70%,transparent_100%)] pointer-events-none"></div>

        <!-- Ambient Radial Glow -->
        <div class="absolute top-1/3 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[550px] h-[350px] bg-indigo-600/10 rounded-full blur-3xl pointer-events-none"></div>

        <div></div> <!-- Spacer Top -->

        <!-- Hero Content Center -->
        <div class="text-center max-w-3xl mx-auto my-auto relative z-10 reveal-on-scroll">
            
            <!-- Badge Pill -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-slate-900/90 border border-slate-800 text-slate-300 text-xs font-medium mb-6 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Sistem POS & Billing Restoran Dapur Ina Aina</span>
            </div>

            <!-- Headline -->
            <h1 class="text-3xl sm:text-5xl md:text-6xl font-extrabold font-heading text-white tracking-tight leading-[1.15] mb-5 sm:mb-6">
                Solusi Kasir Terpadu, Billing Meja Cepat & Akurat
            </h1>

            <!-- Subtitle -->
            <p class="text-xs sm:text-base text-slate-400 leading-relaxed mb-8 sm:mb-10 max-w-2xl mx-auto">
                Aplikasi operasional restoran modern: pencatatan pesanan per nomor meja, kalkulasi otomatis pajak restoran PB1 10%, multi-metode pembayaran tunai & EDC, serta pengurangan stok fisik otomatis secara real-time.
            </p>

            <!-- Primary CTAs -->
            <div class="flex flex-wrap items-center justify-center gap-3.5">
                @if(session('user'))
                    @if(data_get(session('user'), 'role') === 'admin')
                        <a href="{{ route('admin.stok') }}" class="w-full sm:w-auto h-12 px-7 inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-xl shadow-indigo-600/25 transition active:scale-[0.98]">
                            <x-icon name="sliders" class="w-4 h-4" /> Masuk Panel Admin
                        </a>
                    @else
                        <a href="{{ route('pos.index') }}" class="w-full sm:w-auto h-12 px-7 inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-xl shadow-indigo-600/25 transition active:scale-[0.98]">
                            <x-icon name="receipt" class="w-4 h-4" /> Buka POS Kasir
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="w-full sm:w-auto h-12 px-7 inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-xl shadow-indigo-600/25 transition active:scale-[0.98]">
                        <x-icon name="login" class="w-4 h-4" /> Masuk ke Sistem Restoran
                    </a>
                @endif
                
                <a href="#simulasi-pos" class="w-full sm:w-auto h-12 px-6 inline-flex items-center justify-center gap-2 rounded-xl bg-slate-800/90 hover:bg-slate-700 border border-slate-700 text-white text-xs font-semibold shadow-sm transition active:scale-[0.98]">
                    <x-icon name="chart-bar" class="w-4 h-4 text-indigo-400" /> Coba Simulasi POS
                </a>
            </div>

        </div>

        <!-- Scroll Indicator at Bottom of Hero -->
        <div class="pt-8 pb-2 text-center relative z-10">
            <a href="#simulasi-pos" class="group inline-flex flex-col items-center gap-2 text-xs text-slate-500 hover:text-slate-300 transition">
                <span class="text-[11px] font-medium tracking-wider uppercase text-slate-500 group-hover:text-indigo-400 transition">Lihat Simulasi Terminal POS</span>
                <div class="w-5 h-9 rounded-full border border-slate-700 group-hover:border-indigo-500/60 flex justify-center pt-1.5 transition">
                    <div class="w-1.5 h-2 bg-indigo-500 rounded-full animate-bounce"></div>
                </div>
            </a>
        </div>

    </section>

    <!-- ============================================================= -->
    <!-- 2. DEDICATED SHOWCASE: TABLET ON DESKTOP / PHONE ON MOBILE    -->
    <!-- ============================================================= -->
    <section id="simulasi-pos" class="py-20 sm:py-28 bg-[#0b0f19] border-b border-slate-800 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Section Header -->
            <div class="text-center max-w-2xl mx-auto mb-12 sm:mb-16 reveal-on-scroll">
                <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-400">Live Interactive Workstation</span>
                <h2 class="text-2xl sm:text-3xl font-bold font-heading text-white mt-1.5">
                    Simulasi Antarmuka Kasir Terminal POS
                </h2>
                <p class="text-xs sm:text-sm text-slate-400 mt-2">
                    Uji langsung simulasi kasir di bawah: klik tombol <strong class="text-indigo-400">+</strong> untuk memesan menu, lihat kalkulasi otomatis pajak PB1 10%, dan proses transaksi.
                </p>
            </div>

            <!-- Responsive Device Chassis: Widescreen Tablet on Desktop / Handheld Phone on Mobile -->
            <div class="perspective-container max-w-[400px] sm:max-w-[500px] lg:max-w-5xl mx-auto relative reveal-on-scroll">
                
                <!-- Ambient Device Backlight Glow -->
                <div class="absolute -inset-2 bg-gradient-to-r from-indigo-500/15 via-cyan-500/10 to-amber-500/15 rounded-[42px] lg:rounded-[48px] blur-2xl opacity-80 pointer-events-none"></div>

                <!-- Titanium Tablet / Phone Chassis Frame (3D Container Scroll Tilt) -->
                <div class="device-tilt relative rounded-[38px] lg:rounded-[44px] p-3 sm:p-4 lg:p-5 bg-gradient-to-b from-slate-700 via-slate-800 to-slate-900 shadow-[0_30px_70px_-15px_rgba(0,0,0,0.9),0_0_40px_rgba(99,102,241,0.15)] ring-1 ring-white/10">
                    
                    <!-- Hardware Bezel Accents -->
                    <!-- Tablet/Phone Top Camera Dot -->
                    <div class="w-2.5 h-2.5 bg-black rounded-full mx-auto mb-2.5 shadow-inner hidden lg:block border border-slate-700/50"></div>

                    <!-- Inner Device Screen -->
                    <div class="rounded-[28px] lg:rounded-[32px] bg-[#0b0f19] border border-slate-800/90 overflow-hidden flex flex-col text-left">
                        
                        <!-- Top Device Status Bar -->
                        <div class="px-4 py-2.5 bg-slate-950/90 border-b border-slate-800 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500/80"></span>
                                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500/80"></span>
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500/80"></span>
                                </div>
                                <span class="text-[11px] font-mono text-slate-400 ml-1">Terminal POS Kasir 01 — Dapur Ina Aina</span>
                            </div>

                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Online
                                </span>
                                <span class="text-[11px] font-mono text-slate-500 hidden sm:inline">Pajak PB1 10% Terstandar</span>
                            </div>
                        </div>

                        <!-- Main POS Workstation Layout (Split 2-Col on Desktop / Stacked on Mobile) -->
                        <div class="p-3 sm:p-5 lg:p-6 grid grid-cols-1 lg:grid-cols-12 gap-5 bg-[#0b0f19]/80">
                            
                            <!-- LEFT COLUMN: Menu Selection & Categories (7 Cols on Desktop) -->
                            <div class="lg:col-span-7 space-y-4">
                                
                                <!-- Category Switcher Tabs -->
                                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs">
                                    <button type="button" onclick="simFilterCat('all')" id="sim-cat-all"
                                            class="sim-cat-btn px-3 py-1.5 rounded-lg bg-indigo-600 text-white font-semibold shrink-0 transition shadow-sm flex items-center gap-1.5">
                                        <x-icon name="squares-2x2" class="w-3.5 h-3.5" />
                                        <span>Semua Menu</span>
                                    </button>
                                    <button type="button" onclick="simFilterCat('makanan')" id="sim-cat-makanan"
                                            class="sim-cat-btn px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-400 hover:text-white shrink-0 transition flex items-center gap-1.5">
                                        <x-icon name="fire" class="w-3.5 h-3.5 text-amber-400" />
                                        <span>Makanan Utama</span>
                                    </button>
                                    <button type="button" onclick="simFilterCat('appetizer')" id="sim-cat-appetizer"
                                            class="sim-cat-btn px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-400 hover:text-white shrink-0 transition flex items-center gap-1.5">
                                        <x-icon name="sparkles" class="w-3.5 h-3.5 text-orange-400" />
                                        <span>Appetizer</span>
                                    </button>
                                    <button type="button" onclick="simFilterCat('minuman')" id="sim-cat-minuman"
                                            class="sim-cat-btn px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-400 hover:text-white shrink-0 transition flex items-center gap-1.5">
                                        <x-icon name="beaker" class="w-3.5 h-3.5 text-cyan-400" />
                                        <span>Minuman</span>
                                    </button>
                                </div>

                                <!-- Interactive Menu Items Grid (2 cols) -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" id="sim-menu-grid">
                                    
                                    <!-- Item 1: Nasi Goreng -->
                                    <div class="sim-item bg-slate-900/90 p-3.5 rounded-xl border border-slate-800 hover:border-slate-700 transition flex flex-col justify-between group"
                                         data-sim-cat="makanan">
                                        <div>
                                            <div class="flex justify-between items-center mb-1.5">
                                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-300">Makanan Utama</span>
                                                <span class="text-[10px] text-emerald-400">Stok: 20</span>
                                            </div>
                                            <h5 class="text-xs font-semibold text-white group-hover:text-indigo-300 transition">Nasi Goreng Spesial Dapur Ina</h5>
                                            <p class="text-[11px] text-slate-400 line-clamp-1 mt-0.5">Telur mata sapi, suwir ayam gurih, kerupuk.</p>
                                        </div>
                                        <div class="mt-3 pt-2 border-t border-slate-800/80 flex items-center justify-between">
                                            <span class="text-xs font-bold text-white font-mono">Rp 35.000</span>
                                            <button type="button" onclick="simAddToCart('Nasi Goreng Spesial Dapur Ina', 35000)" 
                                                    class="h-7 px-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 active:scale-90 text-white flex items-center gap-1 text-xs font-bold transition shadow-sm"
                                                    title="Tambah ke Tagihan">
                                                <span>+ Tambah</span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Item 2: Ayam Bakar -->
                                    <div class="sim-item bg-slate-900/90 p-3.5 rounded-xl border border-slate-800 hover:border-slate-700 transition flex flex-col justify-between group"
                                         data-sim-cat="makanan">
                                        <div>
                                            <div class="flex justify-between items-center mb-1.5">
                                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-300">Makanan Utama</span>
                                                <span class="text-[10px] text-emerald-400">Stok: 15</span>
                                            </div>
                                            <h5 class="text-xs font-semibold text-white group-hover:text-indigo-300 transition">Ayam Bakar Madu</h5>
                                            <p class="text-[11px] text-slate-400 line-clamp-1 mt-0.5">Bumbu madu karamel, sambal terasi matang.</p>
                                        </div>
                                        <div class="mt-3 pt-2 border-t border-slate-800/80 flex items-center justify-between">
                                            <span class="text-xs font-bold text-white font-mono">Rp 40.000</span>
                                            <button type="button" onclick="simAddToCart('Ayam Bakar Madu', 40000)" 
                                                    class="h-7 px-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 active:scale-90 text-white flex items-center gap-1 text-xs font-bold transition shadow-sm"
                                                    title="Tambah ke Tagihan">
                                                <span>+ Tambah</span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Item 3: Tahu Kipas -->
                                    <div class="sim-item bg-slate-900/90 p-3.5 rounded-xl border border-slate-800 hover:border-slate-700 transition flex flex-col justify-between group"
                                         data-sim-cat="appetizer">
                                        <div>
                                            <div class="flex justify-between items-center mb-1.5">
                                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-orange-500/20 text-orange-300">Appetizer</span>
                                                <span class="text-[10px] text-emerald-400">Stok: 25</span>
                                            </div>
                                            <h5 class="text-xs font-semibold text-white group-hover:text-indigo-300 transition">Tahu Kipas Udang</h5>
                                            <p class="text-[11px] text-slate-400 line-clamp-1 mt-0.5">Tahu goreng renyah isi sayur dan udang cincang.</p>
                                        </div>
                                        <div class="mt-3 pt-2 border-t border-slate-800/80 flex items-center justify-between">
                                            <span class="text-xs font-bold text-white font-mono">Rp 20.000</span>
                                            <button type="button" onclick="simAddToCart('Tahu Kipas Udang', 20000)" 
                                                    class="h-7 px-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 active:scale-90 text-white flex items-center gap-1 text-xs font-bold transition shadow-sm"
                                                    title="Tambah ke Tagihan">
                                                <span>+ Tambah</span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Item 4: Es Jeruk -->
                                    <div class="sim-item bg-slate-900/90 p-3.5 rounded-xl border border-slate-800 hover:border-slate-700 transition flex flex-col justify-between group"
                                         data-sim-cat="minuman">
                                        <div>
                                            <div class="flex justify-between items-center mb-1.5">
                                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-cyan-500/20 text-cyan-300">Minuman</span>
                                                <span class="text-[10px] text-emerald-400">Stok: 30</span>
                                            </div>
                                            <h5 class="text-xs font-semibold text-white group-hover:text-indigo-300 transition">Es Jeruk Kelapa Muda</h5>
                                            <p class="text-[11px] text-slate-400 line-clamp-1 mt-0.5">Perasan jeruk manis asli & kelapa muda segar.</p>
                                        </div>
                                        <div class="mt-3 pt-2 border-t border-slate-800/80 flex items-center justify-between">
                                            <span class="text-xs font-bold text-white font-mono">Rp 15.000</span>
                                            <button type="button" onclick="simAddToCart('Es Jeruk Kelapa Muda', 15000)" 
                                                    class="h-7 px-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 active:scale-90 text-white flex items-center gap-1 text-xs font-bold transition shadow-sm"
                                                    title="Tambah ke Tagihan">
                                                <span>+ Tambah</span>
                                            </button>
                                        </div>
                                    </div>

                                </div>

                            </div>

                            <!-- RIGHT COLUMN: Interactive Billing Sheet & Summary (5 Cols on Desktop) -->
                            <div class="lg:col-span-5 bg-slate-950 p-4 sm:p-5 rounded-2xl border border-slate-800 flex flex-col justify-between space-y-4">
                                
                                <div>
                                    <!-- Billing Sheet Header -->
                                    <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                                        <div class="flex items-center gap-2">
                                            <x-icon name="receipt" class="w-4 h-4 text-indigo-400" />
                                            <span class="text-xs font-bold text-white">Rincian Billing Meja</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                                                Meja #04
                                            </span>
                                            <span id="sim-item-count" class="text-[10px] text-slate-400">3 Item</span>
                                        </div>
                                    </div>

                                    <!-- Dynamic Items Cart List -->
                                    <div id="sim-cart-list" class="py-3 space-y-2 text-xs max-h-48 overflow-y-auto">
                                        <!-- Rendered dynamically via JS -->
                                    </div>

                                    <!-- Tax PB1 & Calculation Breakdown -->
                                    <div class="pt-3 border-t border-dashed border-slate-800 space-y-1.5 text-xs">
                                        <div class="flex items-center justify-between text-slate-400">
                                            <span>Subtotal Pesanan:</span>
                                            <span class="font-mono text-slate-300" id="sim-subtotal">Rp 112.000</span>
                                        </div>
                                        <div class="flex items-center justify-between text-slate-400">
                                            <span>Pajak Restoran PB1 (10%):</span>
                                            <span class="font-mono text-amber-400" id="sim-tax">+ Rp 11.200</span>
                                        </div>
                                        <div class="flex items-center justify-between text-sm font-bold text-white pt-2 border-t border-slate-800">
                                            <span>Grand Total:</span>
                                            <span class="font-mono text-emerald-400 text-base" id="sim-grand-total">Rp 123.200</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Selector & Action -->
                                <div class="space-y-3 pt-2 border-t border-slate-800">
                                    <div class="grid grid-cols-2 gap-2 text-xs">
                                        <button type="button" onclick="simSelectPayment('tunai')" id="sim-pay-tunai"
                                                class="py-2 px-3 rounded-xl border border-indigo-500 bg-indigo-950/60 text-white font-medium flex items-center justify-center gap-1.5 transition">
                                            <x-icon name="banknotes" class="w-4 h-4 text-amber-400" />
                                            <span>Tunai (Cash)</span>
                                        </button>
                                        <button type="button" onclick="simSelectPayment('edc')" id="sim-pay-edc"
                                                class="py-2 px-3 rounded-xl border border-slate-800 bg-slate-900 text-slate-400 font-medium flex items-center justify-center gap-1.5 transition hover:text-white">
                                            <x-icon name="credit-card" class="w-4 h-4 text-cyan-400" />
                                            <span>Non-Tunai Midtrans</span>
                                        </button>
                                    </div>

                                    <!-- Action Button (Clickable, triggers realistic feedback, no navigation) -->
                                    <button type="button" onclick="simProcessPayment()" 
                                            class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:scale-[0.98] text-center text-white text-xs font-bold shadow-lg shadow-emerald-600/25 transition">
                                        Proses Bayar & Cetak Nota
                                    </button>
                                </div>

                            </div>

                        </div>

                        <!-- Simulated Success Banner Modal inside Screen (Hidden by default) -->
                        <div id="sim-success-modal" class="hidden p-4 bg-slate-900 border-t border-emerald-500/40 text-left transition-all duration-300">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5 text-emerald-400 font-bold text-xs">
                                    <x-icon name="check-circle" class="w-5 h-5 text-emerald-400 shrink-0" />
                                    <div>
                                        <span>Simulasi Pembayaran Berhasil!</span>
                                        <p class="text-[11px] text-slate-300 font-normal mt-0.5">Nota Meja #04 telah lunas. Pengurangan stok otomatis tersinkronisasi.</p>
                                    </div>
                                </div>
                                <button type="button" onclick="document.getElementById('sim-success-modal').classList.add('hidden')" 
                                        class="px-3 py-1.5 text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg transition">
                                    Tutup
                                </button>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- Key Metrics Highlight -->
    <section class="py-12 bg-slate-950 border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 text-center reveal-on-scroll">
                <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-800">
                    <div class="text-xl sm:text-2xl font-extrabold text-white font-mono">3 Kategori</div>
                    <p class="text-[11px] sm:text-xs text-slate-400 mt-1">Makanan Utama, Appetizer, Minuman</p>
                </div>
                <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-800">
                    <div class="text-xl sm:text-2xl font-extrabold text-amber-400 font-mono">PB1 10%</div>
                    <p class="text-[11px] sm:text-xs text-slate-400 mt-1">Kalkulasi Pajak Restoran Otomatis</p>
                </div>
                <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-800">
                    <div class="text-xl sm:text-2xl font-extrabold text-cyan-400 font-mono">Multi-Bayar</div>
                    <p class="text-[11px] sm:text-xs text-slate-400 mt-1">Tunai Fisik & Non-Tunai EDC</p>
                </div>
                <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-800">
                    <div class="text-xl sm:text-2xl font-extrabold text-emerald-400 font-mono">Auto-Sync</div>
                    <p class="text-[11px] sm:text-xs text-slate-400 mt-1">Pengurangan Stok Real-Time</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Operational Workflow (4 Langkah Kerja) -->
    <section id="alur" class="py-16 sm:py-24 bg-[#0b0f19] border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl mx-auto text-center mb-12 sm:mb-16 reveal-on-scroll">
                <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-400">Standar Operasional Prosedur</span>
                <h2 class="text-2xl sm:text-3xl font-bold font-heading text-white mt-1.5">4 Tahap Alur Transaksi & Billing Meja</h2>
                <p class="text-xs sm:text-sm text-slate-400 mt-2">Mekanisme kerja kasir terstruktur dari awal tamu datang hingga pelunasan dan pelaporan.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 reveal-on-scroll">
                <!-- Step 1 -->
                <div class="bg-slate-900 p-5 sm:p-6 rounded-2xl border border-slate-800 shadow-sm hover:border-slate-700 transition">
                    <div class="w-9 h-9 rounded-xl bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 flex items-center justify-center text-xs font-bold font-mono mb-4">
                        01
                    </div>
                    <h3 class="text-sm font-semibold text-white mb-1.5 font-heading flex items-center gap-1.5">
                        <x-icon name="map-pin" class="w-4 h-4 text-indigo-400" /> Pilih Nomor Meja
                    </h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Kasir menentukan nomor meja fisik (01 s.d 20) tempat tamu bersantap agar seluruh pesanan terkunci secara tertib.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="bg-slate-900 p-5 sm:p-6 rounded-2xl border border-slate-800 shadow-sm hover:border-slate-700 transition">
                    <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-400 border border-amber-500/20 flex items-center justify-center text-xs font-bold font-mono mb-4">
                        02
                    </div>
                    <h3 class="text-sm font-semibold text-white mb-1.5 font-heading flex items-center gap-1.5">
                        <x-icon name="list-bullet" class="w-4 h-4 text-amber-400" /> Input Menu Pesanan
                    </h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Memilih item menu dari 3 kategori (Makanan Utama, Appetizer, Minuman) dengan filter cepat dan cek ketersediaan stok fisik.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="bg-slate-900 p-5 sm:p-6 rounded-2xl border border-slate-800 shadow-sm hover:border-slate-700 transition">
                    <div class="w-9 h-9 rounded-xl bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 flex items-center justify-center text-xs font-bold font-mono mb-4">
                        03
                    </div>
                    <h3 class="text-sm font-semibold text-white mb-1.5 font-heading flex items-center gap-1.5">
                        <x-icon name="receipt" class="w-4 h-4 text-cyan-400" /> Terbitkan Billing & PB1
                    </h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Sistem membuat nomor tagihan unik, menghitung otomatis pajak PB1 10%, dan kasir dapat mencetak lembar tagihan sementara.
                    </p>
                </div>

                <!-- Step 4 -->
                <div class="bg-slate-900 p-5 sm:p-6 rounded-2xl border border-slate-800 shadow-sm hover:border-slate-700 transition">
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex items-center justify-center text-xs font-bold font-mono mb-4">
                        04
                    </div>
                    <h3 class="text-sm font-semibold text-white mb-1.5 font-heading flex items-center gap-1.5">
                        <x-icon name="check-double" class="w-4 h-4 text-emerald-400" /> Pelunasan & Potong Stok
                    </h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Tamu membayar tunai atau EDC non-tunai. Status billing berubah Lunas, stok produk berkurang seketika, dan omzet terdata.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 3 Categories Menu Catalog (Real Data from DB) -->
    <section id="katalog" class="py-16 sm:py-24 bg-[#0f172a] border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-10 reveal-on-scroll">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-400">Katalog Resmi Restoran</span>
                    <h2 class="text-2xl sm:text-3xl font-bold font-heading text-white mt-1.5">Daftar Menu 3 Kategori</h2>
                    <p class="text-xs sm:text-sm text-slate-400 mt-1">Dikelola langsung secara terpusat oleh panel administrator.</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('login') }}" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 inline-flex items-center gap-1">
                        Buka Antarmuka Kasir &rarr;
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 sm:gap-6 reveal-on-scroll">
                @foreach($kategoriList as $kat)
                    @php
                        $iconName = match($kat->nama_kategori) {
                            'Makanan Utama' => 'fire',
                            'Appetizer' => 'sparkles',
                            'Minuman' => 'beaker',
                            default => 'squares-2x2',
                        };
                        $colorClass = match($kat->nama_kategori) {
                            'Makanan Utama' => 'text-amber-400 bg-amber-500/10 border-amber-500/20',
                            'Appetizer' => 'text-orange-400 bg-orange-500/10 border-orange-500/20',
                            'Minuman' => 'text-cyan-400 bg-cyan-500/10 border-cyan-500/20',
                            default => 'text-indigo-400 bg-indigo-500/10 border-indigo-500/20',
                        };
                    @endphp
                    <div class="rounded-2xl border border-slate-800 bg-slate-900 p-5 sm:p-6 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-800">
                                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-white">
                                    <x-icon name="{{ $iconName }}" class="w-4 h-4 {{ explode(' ', $colorClass)[0] }}" /> {{ $kat->nama_kategori }}
                                </span>
                                <span class="text-[10px] font-medium px-2 py-0.5 rounded border {{ $colorClass }}">
                                    {{ $kat->produk->count() }} Menu
                                </span>
                            </div>
                            <div class="space-y-2.5 text-xs">
                                @forelse($kat->produk as $p)
                                    <div class="flex justify-between items-center py-2 {{ !$loop->first ? 'border-t border-slate-800/80' : '' }}">
                                        <div>
                                            <p class="font-medium text-slate-200">{{ $p->nama_produk }}</p>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-[10px] {{ $p->stok > 0 ? 'text-slate-400' : 'text-rose-400 font-semibold' }}">
                                                    Stok: {{ $p->stok }} porsi
                                                </span>
                                                <span class="text-[9px] px-1.5 py-0.2 rounded font-medium {{ $p->status === 'Tersedia' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400' }}">
                                                    {{ $p->status }}
                                                </span>
                                            </div>
                                        </div>
                                        <span class="font-semibold text-white font-mono shrink-0 ml-2">Rp {{ number_format($p->harga, 0, ',', '.') }}</span>
                                    </div>
                                @empty
                                    <p class="text-slate-500 text-xs py-2">Belum ada menu di kategori ini.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Detailed Module Specifications -->
    <section id="spesifikasi" class="py-16 sm:py-24 bg-[#0b0f19] border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl mx-auto text-center mb-12 sm:mb-16 reveal-on-scroll">
                <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-400">Modul Rekayasa Sistem</span>
                <h2 class="text-2xl sm:text-3xl font-bold font-heading text-white mt-1.5">4 Fitur Utama Sesuai Spesifikasi</h2>
                <p class="text-xs sm:text-sm text-slate-400 mt-2">Seluruh modul dibangun memenuhi standar operasional kasir restoran.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 reveal-on-scroll">
                <div class="bg-slate-900 p-6 rounded-2xl border border-slate-800 shadow-sm">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center mb-4">
                        <x-icon name="list-bullet" class="w-5 h-5" />
                    </div>
                    <h3 class="text-sm font-semibold text-white mb-1.5 font-heading">3 Kategori Menu</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Pengelompokan tegas: <strong>Makanan Utama</strong>, <strong>Appetizer</strong>, dan <strong>Minuman</strong> untuk kemudahan filtrasi kasir.
                    </p>
                </div>

                <div class="bg-slate-900 p-6 rounded-2xl border border-slate-800 shadow-sm">
                    <div class="w-10 h-10 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center mb-4">
                        <x-icon name="receipt" class="w-5 h-5" />
                    </div>
                    <h3 class="text-sm font-semibold text-white mb-1.5 font-heading">Billing Meja & PB1 10%</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Penerbitan nomor billing otomatis, cetak nota sementara, serta kalkulasi pajak <strong>PB1 10%</strong> secara presisi.
                    </p>
                </div>

                <div class="bg-slate-900 p-6 rounded-2xl border border-slate-800 shadow-sm">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center mb-4">
                        <x-icon name="credit-card" class="w-5 h-5" />
                    </div>
                    <h3 class="text-sm font-semibold text-white mb-1.5 font-heading">Dual Payment Method</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Mendukung transaksi <strong>Tunai</strong> (hitung kembalian) dan <strong>EDC Non-Tunai</strong> (pencatatan kode approval).
                    </p>
                </div>

                <div class="bg-slate-900 p-6 rounded-2xl border border-slate-800 shadow-sm">
                    <div class="w-10 h-10 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center mb-4">
                        <x-icon name="cube" class="w-5 h-5" />
                    </div>
                    <h3 class="text-sm font-semibold text-white mb-1.5 font-heading">Stok Fisik & Laporan</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Stok berkurang otomatis saat pembayaran lunas, dan manajemen omzet berkala <strong>Mingguan & Bulanan</strong>.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="mt-auto bg-slate-950 border-t border-slate-800 py-6 sm:py-8 text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3 text-center sm:text-left">
            <div class="flex items-center gap-2">
                <x-icon name="building-storefront" class="w-4 h-4 text-amber-400 shrink-0" />
                <span class="font-bold text-white font-heading">Restoran Dapur Ina Aina</span>
                <span class="hidden xs:inline">&bull; Sistem Operasional Kasir & POS</span>
            </div>
            <div>
                <span>Mikhael Adicahya Kurniawan &bull; NPM: <strong>50423774</strong> &bull; Kelas: 4IA01</span>
            </div>
        </div>
    </footer>

    <!-- ========================================================= -->
    <!-- INTERACTIVE SCRIPT FOR IN-DEVICE POS & SMOOTH SCROLL     -->
    <!-- ========================================================= -->
    <script>
        // Mobile Drawer Toggle
        function toggleMobileMenu() {
            const drawer = document.getElementById('mobile-nav-drawer');
            drawer.classList.toggle('hidden');
        }

        // Scroll Progress Bar Update
        window.addEventListener('scroll', () => {
            const scrollTop = window.scrollY || document.documentElement.scrollTop;
            const docHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrollPercent = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
            const bar = document.getElementById('scroll-progress-bar');
            if (bar) bar.style.width = scrollPercent + '%';
        }, { passive: true });

        // Smooth Scroll Reveal Animation (Intersection Observer)
        document.addEventListener('DOMContentLoaded', () => {
            const revealElements = document.querySelectorAll('.reveal-on-scroll');
            
            const revealObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                    }
                });
            }, {
                root: null,
                threshold: 0.12,
                rootMargin: '0px 0px -40px 0px'
            });

            revealElements.forEach(el => revealObserver.observe(el));

            // 3D Device Tilt Straighten on Scroll (Claude Template Style)
            const deviceTilt = document.querySelector('.device-tilt');
            if (deviceTilt) {
                const tiltObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-upright');
                        }
                    });
                }, { threshold: 0.15 });
                tiltObserver.observe(deviceTilt);
            }

            // Initialize simulation cart
            renderSimCart();
        });

        // In-Device POS Simulation State
        const simCart = {
            'Nasi Goreng Spesial Dapur Ina': { price: 35000, qty: 2 },
            'Tahu Kipas Udang': { price: 20000, qty: 1 },
            'Es Jeruk Kelapa Muda': { price: 15000, qty: 2 }
        };

        function simFormatRupiah(num) {
            return 'Rp ' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function renderSimCart() {
            const container = document.getElementById('sim-cart-list');
            container.innerHTML = '';
            let subtotal = 0;
            let totalQty = 0;

            for (const [name, item] of Object.entries(simCart)) {
                if (item.qty > 0) {
                    const itemTotal = item.price * item.qty;
                    subtotal += itemTotal;
                    totalQty += item.qty;

                    const row = document.createElement('div');
                    row.className = 'flex items-center justify-between py-1 border-b border-slate-900/60';
                    row.innerHTML = `
                        <div class="flex items-center gap-1.5 truncate max-w-[170px] sm:max-w-[210px]">
                            <span class="text-slate-300 font-medium truncate">${name}</span>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <button type="button" onclick="simUpdateQty('${name}', -1)" class="w-4 h-4 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 flex items-center justify-center font-bold text-[10px] transition">-</button>
                            <span class="font-mono text-white text-[11px] w-4 text-center font-semibold">${item.qty}</span>
                            <button type="button" onclick="simUpdateQty('${name}', 1)" class="w-4 h-4 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 flex items-center justify-center font-bold text-[10px] transition">+</button>
                            <span class="font-mono text-white text-[11px] w-20 text-right font-medium">${simFormatRupiah(itemTotal)}</span>
                        </div>
                    `;
                    container.appendChild(row);
                }
            }

            const tax = Math.round(subtotal * 0.10);
            const grandTotal = subtotal + tax;

            document.getElementById('sim-item-count').textContent = totalQty + ' Item';
            document.getElementById('sim-subtotal').textContent = simFormatRupiah(subtotal);
            document.getElementById('sim-tax').textContent = '+ ' + simFormatRupiah(tax);
            document.getElementById('sim-grand-total').textContent = simFormatRupiah(grandTotal);
        }

        function simAddToCart(name, price) {
            if (simCart[name]) {
                simCart[name].qty += 1;
            } else {
                simCart[name] = { price: price, qty: 1 };
            }
            renderSimCart();
        }

        function simUpdateQty(name, delta) {
            if (simCart[name]) {
                simCart[name].qty += delta;
                if (simCart[name].qty <= 0) {
                    delete simCart[name];
                }
            }
            renderSimCart();
        }

        function simFilterCat(cat) {
            // Update active buttons
            document.querySelectorAll('.sim-cat-btn').forEach(btn => {
                btn.className = 'sim-cat-btn px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-slate-400 hover:text-white shrink-0 transition flex items-center gap-1.5';
            });
            const activeBtn = document.getElementById('sim-cat-' + cat);
            if (activeBtn) {
                activeBtn.className = 'sim-cat-btn px-3 py-1.5 rounded-lg bg-indigo-600 text-white font-semibold shrink-0 transition shadow-sm flex items-center gap-1.5';
            }

            // Filter menu cards
            document.querySelectorAll('.sim-item').forEach(item => {
                if (cat === 'all' || item.getAttribute('data-sim-cat') === cat) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        function simSelectPayment(type) {
            const btnTunai = document.getElementById('sim-pay-tunai');
            const btnEdc = document.getElementById('sim-pay-edc');
            if (type === 'tunai') {
                btnTunai.className = 'py-2 px-3 rounded-xl border border-indigo-500 bg-indigo-950/60 text-white font-medium flex items-center justify-center gap-1.5 transition';
                btnEdc.className = 'py-2 px-3 rounded-xl border border-slate-800 bg-slate-900 text-slate-400 font-medium flex items-center justify-center gap-1.5 transition hover:text-white';
            } else {
                btnEdc.className = 'py-2 px-3 rounded-xl border border-cyan-500 bg-cyan-950/60 text-white font-medium flex items-center justify-center gap-1.5 transition';
                btnTunai.className = 'py-2 px-3 rounded-xl border border-slate-800 bg-slate-900 text-slate-400 font-medium flex items-center justify-center gap-1.5 transition hover:text-white';
            }
        }

        function simProcessPayment() {
            const modal = document.getElementById('sim-success-modal');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 3500);
        }
    </script>
</body>
</html>
