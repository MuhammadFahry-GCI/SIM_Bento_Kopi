<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->increments('id_pembayaran');
            $table->unsignedInteger('id_pesanan')->unique();
            $table->enum('metode', ['qris','tunai']);
            $table->decimal('jumlah_bayar', 12, 2);
            $table->enum('status_bayar', ['menunggu','berhasil','gagal'])->default('menunggu');
            $table->string('referensi_transaksi', 100)->nullable();
            $table->dateTime('waktu_bayar')->nullable();
            $table->unsignedInteger('id_penerima')->nullable();
            $table->foreign('id_pesanan')->references('id_pesanan')->on('pesanan')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreign('id_penerima')->references('id_pengguna')->on('pengguna')->restrictOnDelete()->cascadeOnUpdate();
        });
        DB::statement('ALTER TABLE pembayaran ADD CONSTRAINT chk_pembayaran_jumlah CHECK (jumlah_bayar >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
