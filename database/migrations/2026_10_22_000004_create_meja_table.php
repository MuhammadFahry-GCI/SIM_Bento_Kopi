<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meja', function (Blueprint $table) {
            $table->increments('id_meja');
            $table->string('nomor_meja', 10)->unique();
            $table->integer('kapasitas');
            $table->enum('area', ['indoor','merokok','sofa','outdoor']);
            $table->enum('status_meja', ['kosong','terisi'])->default('kosong');
        });
        DB::statement('ALTER TABLE meja ADD CONSTRAINT chk_meja_kapasitas CHECK (kapasitas > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('meja');
    }
};
