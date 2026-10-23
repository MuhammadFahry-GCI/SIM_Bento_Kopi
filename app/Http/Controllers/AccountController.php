<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    public function registerForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'username' => 'required|string|max:50|alpha_dash|unique:pengguna,username',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $pengguna = Pengguna::create([
            'nama_lengkap' => $data['nama_lengkap'],
            'username' => $data['username'],
            'password_hash' => Hash::make($data['password']),
            'peran' => 'member',
            'aktif' => true,
        ]);

        Auth::login($pengguna);
        $request->session()->regenerate();
        LogAktivitas::catat('registrasi_member', 'pengguna', $pengguna->id_pengguna);

        return redirect()->route('beranda')->with('ok', 'Akun member berhasil dibuat. Selamat datang!');
    }

    public function index()
    {
        $pengguna = Pengguna::whereIn('peran', ['member', 'kasir', 'dapur'])
            ->orderBy('peran')
            ->orderBy('nama_lengkap')
            ->get();

        return view('manajer.akun', compact('pengguna'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'username' => 'required|string|max:50|alpha_dash|unique:pengguna,username',
            'peran' => ['required', Rule::in(['member', 'kasir', 'dapur'])],
            'password' => 'required|string|min:8|confirmed',
        ]);

        $akun = Pengguna::create([
            'nama_lengkap' => $data['nama_lengkap'],
            'username' => $data['username'],
            'password_hash' => Hash::make($data['password']),
            'peran' => $data['peran'],
            'aktif' => true,
        ]);

        LogAktivitas::catat(
            'buat_akun',
            'pengguna',
            $akun->id_pengguna,
            $akun->username . ' (' . $akun->peran . ')'
        );

        return redirect()->route('akun.index')->with('ok', 'Akun ' . $akun->peran . ' berhasil dibuat.');
    }
}
