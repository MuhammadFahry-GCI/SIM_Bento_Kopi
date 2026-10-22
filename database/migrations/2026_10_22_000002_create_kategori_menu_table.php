<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_menu', function (Blueprint $table) {
            $table->increments('id_kategori');
            $table->string('nama_kategori', 50)->unique();
            $table->enum('jenis', ['minuman','makanan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_menu');
    }
};
