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
        Schema::create('retensi_kriteria', function (Blueprint $table) {
                $table->id();
                $table->string('kode', 5); // K1, K2, K3, K4
                $table->string('nama_kriteria', 100);
                $table->float('bobot');
                $table->enum('tipe', ['benefit', 'cost']);
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retensi_kriteria');
    }
};
