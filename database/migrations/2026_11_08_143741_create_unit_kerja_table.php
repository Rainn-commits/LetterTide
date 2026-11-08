<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('unit_kerja')) {
            Schema::create('unit_kerja', function (Blueprint $table) {
                $table->id();
                $table->string('kode', 20)->unique();
                $table->string('nama', 100);
                $table->timestamps();
            });
        }

        $units = [
            ['kode' => 'PUSAT',   'nama' => 'Kantor Pusat'],
            ['kode' => 'WEB',     'nama' => 'Inovindo Web'],
            ['kode' => 'ACADEMY', 'nama' => 'Inovindo Academy'],
        ];

        foreach ($units as $unit) {
            DB::table('unit_kerja')->updateOrInsert(
                ['kode' => $unit['kode']],
                ['nama' => $unit['nama'], 'created_at' => now(), 'updated_at' => now()]
            );
        }

        if (!Schema::hasColumn('users', 'unit_kerja_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('unit_kerja_id')
                    ->nullable()
                    ->after('role_id')
                    ->constrained('unit_kerja')
                    ->nullOnDelete();
            });
        }

        $idPusat = DB::table('unit_kerja')->where('kode', 'PUSAT')->value('id');
        $idSuperAdmin = DB::table('roles')->where('nama_role', 'Super Admin')->value('id');

        DB::table('users')
            ->whereNull('unit_kerja_id')
            ->when($idSuperAdmin, fn ($q) => $q->where('role_id', '!=', $idSuperAdmin))
            ->update(['unit_kerja_id' => $idPusat]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'unit_kerja_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropConstrainedForeignId('unit_kerja_id');
            });
        }

        Schema::dropIfExists('unit_kerja');
    }
};
