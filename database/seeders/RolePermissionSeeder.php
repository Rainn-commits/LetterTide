<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = DB::table('permissions')->pluck('id', 'kode_permission');

        $hakAkses = [
            'Super Admin' => ['template.manage', 'kopsurat.manage', 'role.manage', 'log.view'],
            'Admin'       => ['surat.create', 'arsip.manage', 'kopsurat.edit', 'pengguna.unit', 'retensi.evaluate', 'retensi.manage', 'log.view'],
            'User'        => ['surat.create', 'arsip.manage', 'retensi.evaluate'],
        ];

        foreach ($hakAkses as $namaRole => $daftarKode) {
            $roleId = DB::table('roles')->where('nama_role', $namaRole)->value('id');

            if (!$roleId) {
                continue;
            }

            foreach ($daftarKode as $kode) {
                if (!isset($permissions[$kode])) {
                    continue;
                }

                DB::table('role_permissions')->insertOrIgnore([
                    'role_id'       => $roleId,
                    'permission_id' => $permissions[$kode],
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }
    }
}
