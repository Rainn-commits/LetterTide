<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('pengaturan_kop_surat', 'is_aktif')) {
            Schema::table('pengaturan_kop_surat', function (Blueprint $table) {
                $table->boolean('is_aktif')->default(true)->after('versi');
            });
        }

        $terbaru = DB::table('pengaturan_kop_surat')
            ->whereNull('deleted_at')
            ->select('nama_kop', DB::raw('MAX(versi) as versi_terbaru'))
            ->groupBy('nama_kop')
            ->get();

        DB::table('pengaturan_kop_surat')->update(['is_aktif' => false]);

        foreach ($terbaru as $baris) {
            DB::table('pengaturan_kop_surat')
                ->whereNull('deleted_at')
                ->where('nama_kop', $baris->nama_kop)
                ->where('versi', $baris->versi_terbaru)
                ->update(['is_aktif' => true]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pengaturan_kop_surat', 'is_aktif')) {
            Schema::table('pengaturan_kop_surat', function (Blueprint $table) {
                $table->dropColumn('is_aktif');
            });
        }
    }
};
