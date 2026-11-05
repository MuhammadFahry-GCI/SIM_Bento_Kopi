<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artikel_pengetahuan', function (Blueprint $table) {
            $table->increments('id_artikel');
            $table->string('judul', 150);
            $table->string('kategori', 40);
            $table->string('akses', 20)->default('publik');
            $table->longText('isi');
            $table->unsignedInteger('dibuat_oleh')->nullable();
            $table->unsignedInteger('diperbarui_oleh')->nullable();
            $table->timestamps();
            $table->index('kategori');
            $table->index('akses');
            $table->foreign('dibuat_oleh')->references('id_pengguna')->on('pengguna')->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('diperbarui_oleh')->references('id_pengguna')->on('pengguna')->nullOnDelete()->cascadeOnUpdate();
        });

        Schema::table('keluhan', function (Blueprint $table) {
            $table->string('kode_tiket', 12)->nullable()->unique();
        });
    }

    public function down(): void
    {
        Schema::table('keluhan', function (Blueprint $table) {
            $table->dropUnique('keluhan_kode_tiket_unique');
            $table->dropColumn('kode_tiket');
        });

        Schema::dropIfExists('artikel_pengetahuan');
    }
};
