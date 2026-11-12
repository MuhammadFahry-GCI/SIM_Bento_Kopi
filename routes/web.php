<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DapurController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\KeluhanController;
use App\Http\Controllers\PengetahuanController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MejaController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PelayanController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\ReservasiController;
use App\Http\Middleware\CekPeran;
use Illuminate\Support\Facades\Route;

Route::get('/', [ReservasiController::class, 'index'])->name('reservasi.index');

// Pelanggan (tanpa login), masuk lewat QR meja
Route::get('/meja/{nomor}', [PelangganController::class, 'form'])->name('pelanggan.form');
Route::post('/meja/{nomor}', [PelangganController::class, 'kirim'])->name('pelanggan.kirim');
Route::get('/pesanan/{nomor}/pembayaran', [PelangganController::class, 'pembayaran'])->name('pelanggan.pembayaran');
Route::get('/pesanan/{nomor}/status', [PelangganController::class, 'status'])->name('pelanggan.status');
Route::get('/pesanan/{nomor}/keluhan', [KeluhanController::class, 'form'])->name('keluhan.form');
Route::post('/pesanan/{nomor}/keluhan', [KeluhanController::class, 'store'])->middleware('throttle:5,1')->name('keluhan.store');
Route::get('/keluhan/{kode}', [KeluhanController::class, 'track'])->where('kode', '[A-Z0-9]{12}')->name('keluhan.track');
Route::get('/panduan', [PengetahuanController::class, 'publicIndex'])->name('pengetahuan.index');

// Autentikasi dan pendaftaran member
Route::get('/login', [AuthController::class, 'form'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware(['guest', 'throttle:5,1']);
Route::get('/register', [AccountController::class, 'registerForm'])->name('register')->middleware('guest');
Route::post('/register', [AccountController::class, 'register'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/beranda', [AuthController::class, 'beranda'])->name('beranda');

    Route::middleware(CekPeran::class . ':kasir,member')->group(function () {
        Route::get('/reservasi/meja/{id}', [ReservasiController::class, 'form'])->name('reservasi.form');
        Route::post('/reservasi/meja/{id}', [ReservasiController::class, 'store'])->name('reservasi.store');
    });

    // Kasir
    Route::middleware(CekPeran::class . ':kasir')->prefix('kasir')->group(function () {
        Route::get('/', [KasirController::class, 'index'])->name('kasir.index');
        Route::get('/pesanan-baru', [KasirController::class, 'baru'])->name('kasir.baru');
        Route::post('/pesanan-baru', [KasirController::class, 'simpan'])->name('kasir.simpan');
        Route::post('/pesanan/{id}/konfirmasi', [KasirController::class, 'konfirmasi'])->name('kasir.konfirmasi');
        Route::post('/reservasi/{id}/mulai', [KasirController::class, 'mulaiReservasi'])->name('kasir.reservasi.mulai');
        Route::post('/reservasi/pesanan/{id}/diterima', [ReservasiController::class, 'pesananDiterima'])
            ->name('kasir.reservasi.pesanan.diterima');
        Route::post('/meja/{id}/kosongkan', [KasirController::class, 'kosongkanMeja'])->name('kasir.meja.kosongkan');
    });

    // Pembatalan pesanan: kasir dan manajer (alasan wajib)
    Route::post('/pesanan/{id}/batal', [PesananController::class, 'batal'])
        ->middleware(CekPeran::class . ':kasir,manajer')->name('pesanan.batal');

    // Dapur dan barista
    Route::middleware(CekPeran::class . ':dapur')->prefix('dapur')->group(function () {
        Route::get('/', [DapurController::class, 'index'])->name('dapur.index');
        Route::post('/{id}/siap', [DapurController::class, 'siap'])->name('dapur.siap');
        Route::post('/menu/{id}/ketersediaan', [DapurController::class, 'ketersediaan'])->name('dapur.ketersediaan');
    });

    // Member (pelanggan terdaftar)
    Route::get('/member', [MemberController::class, 'index'])
        ->middleware(CekPeran::class . ':member')->name('member.index');
    Route::post('/member/reservasi/{id}/diterima', [ReservasiController::class, 'diterima'])
        ->middleware(CekPeran::class . ':member')->name('member.reservasi.diterima');
    Route::post('/member/reservasi/{id}/batal', [ReservasiController::class, 'batalkan'])
        ->middleware(CekPeran::class . ':member')->name('member.reservasi.batal');
    Route::post('/member/pesanan/{id}/diterima', [ReservasiController::class, 'pesananDiterima'])
        ->middleware(CekPeran::class . ':member')->name('member.pesanan.diterima');

    // Pelayan
    Route::middleware(CekPeran::class . ':pelayan')->prefix('pelayan')->group(function () {
        Route::get('/', [PelayanController::class, 'index'])->name('pelayan.index');
        Route::post('/pesanan/{id}/sajikan', [PelayanController::class, 'sajikan'])->name('pelayan.sajikan');
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

        Route::get('/pengetahuan', [PengetahuanController::class, 'index'])->name('pengetahuan.manage');
        Route::post('/pengetahuan', [PengetahuanController::class, 'store'])->name('pengetahuan.store');
        Route::get('/pengetahuan/{id}/edit', [PengetahuanController::class, 'edit'])->name('pengetahuan.edit');
        Route::put('/pengetahuan/{id}', [PengetahuanController::class, 'update'])->name('pengetahuan.update');
        Route::delete('/pengetahuan/{id}', [PengetahuanController::class, 'destroy'])->name('pengetahuan.destroy');

        Route::get('/keluhan', [KeluhanController::class, 'index'])->name('keluhan.manage');
        Route::put('/keluhan/{id}', [KeluhanController::class, 'update'])->name('keluhan.update');
    });

    // Manajer outlet dan manajemen grup: baca laporan saja
    Route::middleware(CekPeran::class . ':manajer,group')->group(function () {
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
    });

    // Log aktivitas hanya dapat dibaca manajer outlet
    Route::middleware(CekPeran::class . ':manajer')->group(function () {
        Route::get('/log-aktivitas', [LaporanController::class, 'log'])->name('log');
    });
});
