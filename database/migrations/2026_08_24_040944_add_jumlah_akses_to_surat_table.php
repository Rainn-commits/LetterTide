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
        Schema::table('surat', function (Blueprint $table) {
            $table->unsignedInteger('jumlah_akses')->default(0)->after('file_arsip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat', function (Blueprint $table) {
            //
        });
    }
};
