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
        Schema::create('kriteria_ahp', function (Blueprint $table) {
            $table->id();
            $table->json('matriks');          
            $table->double('lambda_max');
            $table->double('ci');
            $table->double('ri');
            $table->double('cr');
            $table->foreignId('diubah_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::table('evaluasi_retensi', function (Blueprint $table) {
            $table->double('d_plus')->nullable()->after('skor_total');
            $table->double('d_minus')->nullable()->after('d_plus');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluasi_retensi', function (Blueprint $table) {
            $table->dropColumn(['d_plus', 'd_minus']);
        });

        Schema::dropIfExists('kriteria_ahp');
    }
};
