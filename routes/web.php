<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DapurController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MejaController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PelayanController;
use App\Http\Controllers\PesananController;
use App\Http\Middleware\CekPeran;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

// Pelanggan (tanpa login), masuk lewat QR meja
Route::get('/meja/{nomor}', [PelangganController::class, 'form'])->name('pelanggan.form');
Route::post('/meja/{nomor}', [PelangganController::class, 'kirim'])->name('pelanggan.kirim');
Route::get('/pesanan/{nomor}/status', [PelangganController::class, 'status'])->name('pelanggan.status');

// Autentikasi dan pendaftaran member
Route::get('/login', [AuthController::class, 'form'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::get('/register', [AccountController::class, 'registerForm'])->name('register')->middleware('guest');
Route::post('/register', [AccountController::class, 'register'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/beranda', [AuthController::class, 'beranda'])->name('beranda');

    // Kasir
    Route::middleware(CekPeran::class . ':kasir')->prefix('kasir')->group(function () {
        Route::get('/', [KasirController::class, 'index'])->name('kasir.index');
        Route::get('/pesanan-baru', [KasirController::class, 'baru'])->name('kasir.baru');
        Route::post('/pesanan-baru', [KasirController::class, 'simpan'])->name('kasir.simpan');
        Route::post('/pesanan/{id}/konfirmasi', [KasirController::class, 'konfirmasi'])->name('kasir.konfirmasi');
    });

    // Pembatalan pesanan: kasir dan manajer (alasan wajib)
    Route::post('/pesanan/{id}/batal', [PesananController::class, 'batal'])
        ->middleware(CekPeran::class . ':kasir,manajer')->name('pesanan.batal');

    // Dapur dan barista
    Route::middleware(CekPeran::class . ':dapur')->prefix('dapur')->group(function () {
        Route::get('/', [DapurController::class, 'index'])->name('dapur.index');
        Route::post('/{id}/proses', [DapurController::class, 'proses'])->name('dapur.proses');
        Route::post('/{id}/siap', [DapurController::class, 'siap'])->name('dapur.siap');
        Route::post('/menu/{id}/ketersediaan', [DapurController::class, 'ketersediaan'])->name('dapur.ketersediaan');
    });

    // Member (pelanggan terdaftar)
    Route::get('/member', [MemberController::class, 'index'])
        ->middleware(CekPeran::class . ':member')->name('member.index');

    // Pelayan
    Route::middleware(CekPeran::class . ':pelayan')->prefix('pelayan')->group(function () {
        Route::get('/', [PelayanController::class, 'index'])->name('pelayan.index');
        Route::post('/pesanan/{id}/sajikan', [PelayanController::class, 'sajikan'])->name('pelayan.sajikan');
        Route::post('/meja/{id}/ubah', [PelayanController::class, 'ubahMeja'])->name('pelayan.meja');
    });

    // Manajer outlet: data master (CRUD)
    Route::middleware(CekPeran::class . ':manajer')->prefix('manajer')->group(function () {
        Route::get('/akun', [AccountController::class, 'index'])->name('akun.index');
        Route::post('/akun', [AccountController::class, 'store'])->name('akun.store');

        Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
        Route::post('/menu', [MenuController::class, 'store'])->name('menu.store');
        Route::put('/menu/{id}', [MenuController::class, 'update'])->name('menu.update');
        Route::delete('/menu/{id}', [MenuController::class, 'destroy'])->name('menu.destroy');

        Route::get('/meja', [MejaController::class, 'index'])->name('meja.index');
        Route::post('/meja', [MejaController::class, 'store'])->name('meja.store');
        Route::put('/meja/{id}', [MejaController::class, 'update'])->name('meja.update');
        Route::delete('/meja/{id}', [MejaController::class, 'destroy'])->name('meja.destroy');
    });

    // Manajer outlet dan manajemen grup: baca saja
    Route::middleware(CekPeran::class . ':manajer,group')->group(function () {
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
        Route::get('/log-aktivitas', [LaporanController::class, 'log'])->name('log');
    });
});
