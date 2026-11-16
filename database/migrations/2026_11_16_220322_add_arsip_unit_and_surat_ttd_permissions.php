<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const NAMA = [
        'arsip.unit'       => 'Lihat Seluruh Arsip Unit',
        'surat.ttd'        => 'ACC & Tanda Tangan Digital',
        'retensi.evaluate' => 'Retensi Arsip (Isi Penilaian)',
        'retensi.manage'   => 'Kelola Kriteria AHP & Validasi Retensi',
    ];

    private const UNTUK_ADMIN = ['arsip.unit', 'surat.ttd', 'retensi.evaluate', 'retensi.manage', 'arsip.manage'];

    private const CABUT_DARI_SUPER_ADMIN = [
        'surat.create', 'arsip.manage', 'arsip.unit', 'surat.ttd', 'retensi.evaluate', 'retensi.manage',
    ];

    public function up(): void
    {
        foreach (self::NAMA as $kode => $nama) {
            DB::table('permissions')->updateOrInsert(
                ['kode_permission' => $kode],
                ['nama_permission' => $nama, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        $idRole = fn (string $nama) => DB::table('roles')->where('nama_role', $nama)->value('id');
        $idPerm = fn (string $kode) => DB::table('permissions')->where('kode_permission', $kode)->value('id');

        if ($admin = $idRole('Admin')) {
            foreach (self::UNTUK_ADMIN as $kode) {
                $permId = $idPerm($kode);

                $sudahAda = DB::table('role_permissions')
                    ->where('role_id', $admin)
                    ->where('permission_id', $permId)
                    ->exists();

                if ($permId && !$sudahAda) {
                    DB::table('role_permissions')->insert([
                        'role_id'       => $admin,
                        'permission_id' => $permId,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                }
            }
        }

        if ($super = $idRole('Super Admin')) {
            DB::table('role_permissions')
                ->where('role_id', $super)
                ->whereIn('permission_id', DB::table('permissions')
                    ->whereIn('kode_permission', self::CABUT_DARI_SUPER_ADMIN)
                    ->pluck('id'))
                ->delete();
        }
    }

    public function down(): void
    {
        $id = DB::table('permissions')->whereIn('kode_permission', ['arsip.unit', 'surat.ttd'])->pluck('id');

        DB::table('role_permissions')->whereIn('permission_id', $id)->delete();
        DB::table('permissions')->whereIn('id', $id)->delete();
    }
};
