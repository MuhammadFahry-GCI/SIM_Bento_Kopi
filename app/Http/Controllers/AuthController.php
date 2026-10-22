<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function form()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $cred = $request->validate(['username' => 'required|string', 'password' => 'required|string']);

        if (Auth::attempt($cred + ['aktif' => 1])) {
            $request->session()->regenerate();
            LogAktivitas::catat('login', 'pengguna', Auth::id());
            return redirect()->route('beranda');
        }

        return back()->withErrors(['username' => 'Username atau kata sandi salah, atau akun nonaktif.'])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        LogAktivitas::catat('logout', 'pengguna', Auth::id());
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function beranda()
    {
        return match (Auth::user()->peran) {
            'kasir' => redirect()->route('kasir.index'),
            'dapur' => redirect()->route('dapur.index'),
            'pelayan' => redirect()->route('pelayan.index'),
            'member' => redirect()->route('member.index'),
            default => redirect()->route('laporan'),
        };
    }
}
