<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->increments('id_pesanan');
            $table->string('nomor_pesanan', 20)->unique();
            $table->unsignedInteger('id_meja');
            $table->unsignedInteger('id_kasir')->nullable();
            $table->string('nama_pemesan', 100)->nullable();
            $table->text('catatan')->nullable();
            $table->enum('status_pesanan', ['menunggu_bayar','antre','diproses','siap','disajikan','dibatalkan'])->default('menunggu_bayar');
            $table->decimal('total_harga', 12, 2)->default(0);
            $table->dateTime('waktu_pesan')->useCurrent();
            $table->dateTime('waktu_siap')->nullable();
            $table->dateTime('waktu_disajikan')->nullable();
            $table->string('alasan_batal', 255)->nullable();
            $table->index(['status_pesanan', 'waktu_pesan'], 'idx_pesanan_antrean');
            $table->foreign('id_meja')->references('id_meja')->on('meja')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreign('id_kasir')->references('id_pengguna')->on('pengguna')->restrictOnDelete()->cascadeOnUpdate();
        });
        DB::statement('ALTER TABLE pesanan ADD CONSTRAINT chk_pesanan_total CHECK (total_harga >= 0)');
        DB::statement('ALTER TABLE pesanan ADD CONSTRAINT chk_pesanan_batal CHECK (status_pesanan <> \'dibatalkan\' OR alasan_batal IS NOT NULL)');
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
