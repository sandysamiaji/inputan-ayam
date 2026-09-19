<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Tampilkan Halaman Login (Tema Merah Marun Putih)
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Proses Autentikasi Pengguna
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required' => 'Username atau Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $loginInput = trim($credentials['login']);
        $password = $credentials['password'];
        $remember = $request->boolean('remember');

        // Deteksi apakah input merupakan email atau username
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // 1. Coba autentikasi standar Laravel
        $attempt = Auth::attempt([$fieldType => $loginInput, 'password' => $password], $remember);

        // 2. Jika gagal, coba fallback pencarian user berdasarkan username atau email
        if (!$attempt) {
            $user = User::where('username', $loginInput)
                ->orWhere('email', $loginInput)
                ->first();

            if ($user && Hash::check($password, $user->password)) {
                Auth::login($user, $remember);
                $attempt = true;
            }
        }

        if ($attempt) {
            $user = Auth::user();

            // Verifikasi status akun aktif
            if (!$user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withInput($request->only('login', 'remember'))
                    ->with('error', 'Akun Anda dinonaktifkan oleh Administrator. Silakan hubungi admin farm.');
            }

            $request->session()->regenerate();

            \App\Services\AuditService::log(
                'LOGIN',
                'auth',
                "Pengguna {$user->name} (" . ($user->username ? '@' . ltrim($user->username, '@') : 'user') . ") berhasil masuk ke dalam sistem",
                null,
                null,
                null,
                $user
            );

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Selamat datang kembali, ' . ($user->name ?: $user->username) . '!');
        }

        return back()->withInput($request->only('login', 'remember'))
            ->with('error', 'Username atau Password yang dimasukkan tidak sesuai.');
    }

    /**
     * Logout Pengguna
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            \App\Services\AuditService::log(
                'LOGOUT',
                'auth',
                "Pengguna {$user->name} (" . ($user->username ? '@' . ltrim($user->username, '@') : 'user') . ") keluar dari sistem",
                null,
                null,
                null,
                $user
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari sistem.');
    }
}
