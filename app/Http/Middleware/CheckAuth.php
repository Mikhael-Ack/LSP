<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('user')) {
            // Redirect tenang ke login tanpa pesan error agresif
            return redirect()->route('login');
        }
        return $next($request);
    }
}
