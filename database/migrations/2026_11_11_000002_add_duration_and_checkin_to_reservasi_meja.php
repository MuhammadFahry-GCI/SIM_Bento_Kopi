<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservasi_meja', function (Blueprint $table) {
            $table->unsignedTinyInteger('durasi_jam')->default(2);
            $table->dateTime('waktu_checkin')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('reservasi_meja', function (Blueprint $table) {
            $table->dropColumn(['durasi_jam', 'waktu_checkin']);
        });
    }
};
