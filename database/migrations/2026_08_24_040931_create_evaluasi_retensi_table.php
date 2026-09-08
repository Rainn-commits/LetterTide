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
        Schema::create('evaluasi_retensi', function (Blueprint $table) {
                $table->id();
                $table->foreignId('surat_id')->constrained('surat')->onDelete('cascade');
                $table->string('nilai_guna', 30)->nullable();
                $table->string('kerahasiaan', 30)->nullable();    
                $table->float('skor_total')->nullable();
                $table->string('status_rekomendasi', 20)->nullable(); 
                $table->foreignId('dievaluasi_oleh')->nullable()->constrained('users');
                $table->timestamp('dievaluasi_pada')->nullable();
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluasi_retensi');
    }
};
