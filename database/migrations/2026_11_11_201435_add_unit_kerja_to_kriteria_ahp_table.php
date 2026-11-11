<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('kriteria_ahp')->delete();

        Schema::table('kriteria_ahp', function (Blueprint $table) {
            $table->foreignId('unit_kerja_id')
                ->after('id')
                ->constrained('unit_kerja')
                ->cascadeOnDelete();
            $table->unique('unit_kerja_id');
        });
    }

    public function down(): void
    {
        Schema::table('kriteria_ahp', function (Blueprint $table) {
            $table->dropUnique(['unit_kerja_id']);
            $table->dropConstrainedForeignId('unit_kerja_id');
        });
    }
};
