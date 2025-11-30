<?php

namespace App\Http\Controllers\Auth;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Menampilkan form login.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Menangani proses login.
     */
    public function login(Request $request)
    {
        // 1. Validasi input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Coba login otomatis menggunakan fitur Auth Laravel
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Ambil user yang sedang login
            $user = Auth::user();

            // 3. Cek Role untuk Redirect (Opsional: Membedakan halaman awal)
            // Jika Barista, mungkin langsung ke POS. Jika Admin, ke Dashboard.
            // Namun, karena Dashboard kita sudah dinamis, kita arahkan semua ke dashboard dulu.
            return redirect()->intended('dashboard');
        }

        // 4. Jika login gagal, kembalikan ke halaman login dengan pesan error
        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Menangani proses logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}