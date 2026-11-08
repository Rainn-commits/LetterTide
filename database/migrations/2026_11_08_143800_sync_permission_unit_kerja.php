<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $permissionBaru = [
            ['nama_permission' => 'Ubah Kop Surat Unit',   'kode_permission' => 'kopsurat.edit'],
            ['nama_permission' => 'Kelola Pengguna Unit',  'kode_permission' => 'pengguna.unit'],
        ];

        foreach ($permissionBaru as $perm) {
            DB::table('permissions')->updateOrInsert(
                ['kode_permission' => $perm['kode_permission']],
                ['nama_permission' => $perm['nama_permission'], 'created_at' => now(), 'updated_at' => now()]
            );
        }

        $role = fn (string $nama) => DB::table('roles')->where('nama_role', $nama)->value('id');
        $perm = fn (string $kode) => DB::table('permissions')->where('kode_permission', $kode)->value('id');

        $idSuper = $role('Super Admin');
        $idAdmin = $role('Admin');
        $idUser  = $role('User');

        $aturan = [
            [$idSuper, ['template.manage', 'kopsurat.manage', 'role.manage', 'log.view'],
                       ['surat.create', 'arsip.manage', 'retensi.evaluate', 'retensi.manage']],
            [$idAdmin, ['surat.create', 'arsip.manage', 'kopsurat.edit', 'pengguna.unit', 'retensi.evaluate', 'log.view'],
                       ['role.manage', 'kopsurat.manage', 'template.manage']],
            [$idUser,  ['surat.create', 'arsip.manage', 'retensi.evaluate'],
                       ['template.manage']],
        ];

        foreach ($aturan as [$roleId, $tambah, $cabut]) {
            if (!$roleId) {
                continue;
            }

            foreach ($tambah as $kode) {
                $permId = $perm($kode);
                if ($permId) {
                    DB::table('role_permissions')->insertOrIgnore([
                        'role_id'       => $roleId,
                        'permission_id' => $permId,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                }
            }

            $idCabut = DB::table('permissions')->whereIn('kode_permission', $cabut)->pluck('id');
            DB::table('role_permissions')
                ->where('role_id', $roleId)
                ->whereIn('permission_id', $idCabut)
                ->delete();
        }
    }

    public function down(): void
    {
        
    }
};
