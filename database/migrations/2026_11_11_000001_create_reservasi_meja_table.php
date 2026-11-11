<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservasi_meja', function (Blueprint $table) {
            $table->increments('id_reservasi');
            $table->unsignedInteger('id_meja');
            $table->unsignedInteger('id_pesanan')->unique();
            $table->unsignedInteger('id_member')->nullable();
            $table->unsignedInteger('id_kasir')->nullable();
            $table->string('nama_pemesan', 100);
            $table->dateTime('waktu_mulai');
            $table->dateTime('waktu_selesai');
            $table->dateTime('waktu_diterima')->nullable();
            $table->enum('status_reservasi', ['dipesan', 'dibatalkan'])->default('dipesan');
            $table->dateTime('dibuat_pada');
            $table->index(['id_meja', 'status_reservasi', 'waktu_mulai', 'waktu_selesai'], 'idx_reservasi_waktu');
            $table->index(['id_member', 'status_reservasi'], 'idx_reservasi_member');
            $table->foreign('id_meja')->references('id_meja')->on('meja')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreign('id_pesanan')->references('id_pesanan')->on('pesanan')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreign('id_member')->references('id_pengguna')->on('pengguna')->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('id_kasir')->references('id_pengguna')->on('pengguna')->nullOnDelete()->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservasi_meja');
    }
};
