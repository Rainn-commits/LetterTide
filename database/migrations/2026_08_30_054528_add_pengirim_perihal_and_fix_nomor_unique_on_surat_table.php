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
            $table->string('pengirim', 150)->nullable()->after('jenis');
            $table->string('perihal', 255)->nullable()->after('pengirim');
        });

        Schema::table('surat', function (Blueprint $table) {
            $table->dropUnique(['nomor_surat']);
            $table->index('nomor_surat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat', function (Blueprint $table) {
            $table->dropIndex(['nomor_surat']);
            $table->unique('nomor_surat');
        });

        Schema::table('surat', function (Blueprint $table) {
            $table->dropColumn(['pengirim', 'perihal']);
        });
    }
};
