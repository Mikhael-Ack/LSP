<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('user')) {
            return redirect()->route('login');
        }

        if (data_get(session('user'), 'role') !== 'admin') {
            return redirect()->route('pos.index')->with('error', 'Akses Ditolak: Anda login sebagai Kasir. Fitur ini khusus Administrator!');
        }

        return $next($request);
    }
}
