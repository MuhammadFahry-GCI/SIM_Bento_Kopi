<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keluhan', function (Blueprint $table) {
            $table->increments('id_keluhan');
            $table->unsignedInteger('id_pesanan')->nullable();
            $table->enum('kategori', ['pesanan_salah','urutan_penyajian','rasa_menu','layanan_staf','fasilitas','lainnya']);
            $table->text('deskripsi');
            $table->string('nama_pelapor', 100)->nullable();
            $table->string('kontak_pelapor', 50)->nullable();
            $table->enum('status_keluhan', ['baru','diproses','selesai'])->default('baru');
            $table->tinyInteger('kompensasi_persen')->nullable();
            $table->text('tindak_lanjut')->nullable();
            $table->unsignedInteger('id_pencatat')->nullable();
            $table->unsignedInteger('id_penangan')->nullable();
            $table->dateTime('waktu_lapor')->useCurrent();
            $table->dateTime('waktu_selesai')->nullable();
            $table->foreign('id_pesanan')->references('id_pesanan')->on('pesanan')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreign('id_pencatat')->references('id_pengguna')->on('pengguna')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreign('id_penangan')->references('id_pengguna')->on('pengguna')->restrictOnDelete()->cascadeOnUpdate();
        });
        DB::statement('ALTER TABLE keluhan ADD CONSTRAINT chk_keluhan_kompensasi CHECK (kompensasi_persen IS NULL OR kompensasi_persen BETWEEN 0 AND 100)');
    }

    public function down(): void
    {
        Schema::dropIfExists('keluhan');
    }
};
