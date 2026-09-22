<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk ke Sistem — Dapur Ina Aina</title>
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
            background-color: #0f172a;
        }
        h1, h2, h3, .font-heading { font-family: 'Instrument Sans', sans-serif; }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center p-4 text-slate-100 antialiased bg-slate-950">

    <div class="w-full max-w-[420px] mx-auto">
        
        <!-- Brand Header -->
        <div class="flex flex-col items-center text-center mb-6">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-amber-500 to-orange-600 flex items-center justify-center shadow-lg shadow-orange-500/20 text-white mb-3">
                <x-icon name="building-storefront" class="w-6 h-6" />
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white font-heading">Dapur Ina Aina</h1>
            <p class="text-xs text-slate-400 mt-1">Sistem Informasi Kasir & Operasional Restoran</p>
        </div>

        <!-- Main Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl p-6 sm:p-7">
            <div class="mb-5 pb-4 border-b border-slate-800/80">
                <h2 class="text-base font-semibold text-white">Login Pengguna</h2>
                <p class="text-xs text-slate-400 mt-0.5">Masukkan kredensial akun untuk mengakses sistem</p>
            </div>

            <!-- Flash Error -->
            @if(session('error'))
                <div class="mb-4 rounded-xl bg-rose-500/10 border border-rose-500/30 p-3 text-xs text-rose-300 flex items-start gap-2.5">
                    <x-icon name="exclamation-circle" class="w-4 h-4 text-rose-400 mt-0.5 shrink-0" />
                    <div>
                        <span class="font-semibold block text-rose-200">Gagal Masuk</span>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Flash Success -->
            @if(session('success'))
                <div class="mb-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 p-3 text-xs text-emerald-300 flex items-start gap-2.5">
                    <x-icon name="check-circle" class="w-4 h-4 text-emerald-400 mt-0.5 shrink-0" />
                    <div>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1.5">Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <x-icon name="user" class="w-4 h-4" />
                        </div>
                        <input type="text" name="username" placeholder="admin atau kasir" required autofocus
                               class="w-full h-10 pl-9 pr-3.5 rounded-lg border border-slate-700 bg-slate-800/80 text-xs text-white placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1.5">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <x-icon name="key" class="w-4 h-4" />
                        </div>
                        <input type="password" name="password" placeholder="••••••••" required
                               class="w-full h-10 pl-9 pr-3.5 rounded-lg border border-slate-700 bg-slate-800/80 text-xs text-white placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>
                </div>

                <button type="submit" 
                        class="w-full h-10 mt-2 inline-flex items-center justify-center gap-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 px-4 py-2 text-xs font-semibold text-white shadow-lg shadow-indigo-600/30 transition active:scale-[0.98]">
                    <x-icon name="login" class="w-4 h-4" /> Masuk Sekarang
                </button>
            </form>

            <!-- Quick Demo Credentials -->
            <div class="mt-6 pt-5 border-t border-slate-800">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Mode Pengujian Demo</span>
                    <span class="text-[10px] text-indigo-400 font-medium">1-Klik Langsung Masuk</span>
                </div>
                
                <div class="grid grid-cols-2 gap-2.5">
                    <!-- Kasir Demo -->
                    <a href="{{ route('switch.role', 'kasir') }}" 
                       class="flex flex-col p-3 rounded-xl border border-slate-800 bg-slate-800/50 hover:bg-slate-800 hover:border-slate-700 transition group">
                        <div class="flex items-center gap-1.5 text-xs font-semibold text-white">
                            <x-icon name="user" class="w-3.5 h-3.5 text-cyan-400" />
                            Role Kasir
                        </div>
                        <span class="text-[11px] text-slate-400 mt-1 font-mono">kasir / kasir123</span>
                    </a>

                    <!-- Admin Demo -->
                    <a href="{{ route('switch.role', 'admin') }}" 
                       class="flex flex-col p-3 rounded-xl border border-slate-800 bg-slate-800/50 hover:bg-slate-800 hover:border-slate-700 transition group">
                        <div class="flex items-center gap-1.5 text-xs font-semibold text-white">
                            <x-icon name="shield-check" class="w-3.5 h-3.5 text-amber-400" />
                            Administrator
                        </div>
                        <span class="text-[11px] text-slate-400 mt-1 font-mono">admin / admin123</span>
                    </a>
                </div>
            </div>

        </div>

        <!-- Back Link -->
        <div class="mt-6 text-center">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-400 hover:text-white transition">
                <x-icon name="arrow-left" class="w-3.5 h-3.5" /> Kembali ke Beranda
            </a>
        </div>

    </div>

</body>
</html>
