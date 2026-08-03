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
        Schema::create('surat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('template_surat');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('kop_surat_id')->constrained('pengaturan_kop_surat');
            $table->string('nomor_surat', 50)->unique();
            $table->string('jenis', 20); // 'Masuk' / 'Keluar'
            $table->date('tanggal_surat');
            $table->string('file_arsip', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat');
    }
};
