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
        Schema::create('pengaturan_kop_surat', function (Blueprint $table) {
             $table->id();
            $table->string('logo', 255)->nullable();
            $table->string('nama_pt', 100);
            $table->string('alamat_pt', 255);
            $table->string('telepon', 20);
            $table->string('email', 100);
            $table->string('website', 100)->nullable();
            $table->integer('versi');
            $table->boolean('is_aktif')->default(true);
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturan_kop_surat');
    }
};
