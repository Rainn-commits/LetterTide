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
        Schema::table('evaluasi_retensi', function (Blueprint $table) {
            $table->string('status_persetujuan', 20)->nullable()->after('status_rekomendasi');
            $table->foreignId('divalidasi_oleh')->nullable()->after('dievaluasi_oleh')->constrained('users')->nullOnDelete();
            $table->timestamp('divalidasi_pada')->nullable()->after('dievaluasi_pada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluasi_retensi', function (Blueprint $table) {
            $table->dropConstrainedForeignId('divalidasi_oleh');
            $table->dropColumn(['status_persetujuan', 'divalidasi_pada']);
        });
    }
};
