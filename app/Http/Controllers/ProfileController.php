<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Tampilkan Halaman Profil Petugas (Sesuai Mockup Layar 2)
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            $user = User::where('role', 'user')->orWhere('username', 'petugas')->first() ?? User::first();
        }

        return view('profile.index', compact('user'));
    }

    /**
     * Ubah Password Akun
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            $user = User::where('role', 'user')->orWhere('username', 'petugas')->first() ?? User::first();
        }

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Verifikasi password lama jika user punya password
        if ($user->password && !Hash::check($validated['current_password'], $user->password)) {
            return back()->with('error', 'Password lama tidak sesuai!');
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        return back()->with('success', 'Password akun berhasil diperbarui!');
    }

    /**
     * Logout Session
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari sesi.');
    }
}
