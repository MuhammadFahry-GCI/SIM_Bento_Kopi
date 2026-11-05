<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->unsignedInteger('id_member')->nullable();
            $table->index('id_member');
            $table->foreign('id_member')->references('id_pengguna')->on('pengguna')->nullOnDelete()->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropForeign(['id_member']);
            $table->dropIndex(['id_member']);
            $table->dropColumn('id_member');
        });
    }
};
