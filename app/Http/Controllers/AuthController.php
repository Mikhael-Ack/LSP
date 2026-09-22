<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        // Verifikasi password sederhana untuk akun demo/db
        if ($user && ($user->password === $request->password || password_verify($request->password, $user->password))) {
            session(['user' => $user]);
            
            if ($user->role === 'admin') {
                return redirect()->route('admin.stok')->with('success', "Selamat datang, {$user->nama} (Administrator)!");
            } else {
                return redirect()->route('pos.index')->with('success', "Selamat datang, {$user->nama} (Kasir)!");
            }
        }

        return redirect()->back()->with('error', 'Username atau password salah!');
    }

    public function quickLogin($role)
    {
        $user = User::where('role', $role)->first();
        if ($user) {
            session(['user' => $user]);
            if ($role === 'admin') {
                return redirect()->route('admin.stok')->with('success', "Beralih ke akun {$user->nama} (Administrator)");
            } else {
                return redirect()->route('pos.index')->with('success', "Beralih ke akun {$user->nama} (Kasir)");
            }
        }
        return redirect()->route('pos.index');
    }

    public function logout()
    {
        session()->forget('user');
        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }
}
