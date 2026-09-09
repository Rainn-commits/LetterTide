<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pengaturan_kop_surat', function (Blueprint $table) {
            $table->softDeletes();
            $table->dropColumn('is_aktif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaturan_kop_surat', function (Blueprint $table) {
            $table->boolean('is_aktif')->default(true);
            $table->dropSoftDeletes();
        });
    }
};
