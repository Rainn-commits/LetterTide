<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = DB::table('roles')->where('nama_role', 'Super Admin')->first()->id;
        $admin = DB::table('roles')->where('nama_role', 'Admin')->first()->id;
        $user = DB::table('roles')->where('nama_role', 'User')->first()->id;

        $permissions = DB::table('permissions')->pluck('id', 'kode_permission');

        $data = [
            ['role_id' => $superAdmin, 'permission_id' => $permissions['surat.create']],
            ['role_id' => $superAdmin, 'permission_id' => $permissions['template.manage']],
            ['role_id' => $superAdmin, 'permission_id' => $permissions['arsip.manage']],
            ['role_id' => $superAdmin, 'permission_id' => $permissions['kopsurat.manage']],
            ['role_id' => $superAdmin, 'permission_id' => $permissions['role.manage']],
            ['role_id' => $superAdmin, 'permission_id' => $permissions['retensi.evaluate']],
            ['role_id' => $superAdmin, 'permission_id' => $permissions['retensi.manage']],
            ['role_id' => $superAdmin, 'permission_id' => $permissions['log.view']],

            ['role_id' => $admin, 'permission_id' => $permissions['surat.create']],
            ['role_id' => $admin, 'permission_id' => $permissions['template.manage']],
            ['role_id' => $admin, 'permission_id' => $permissions['arsip.manage']],
            ['role_id' => $admin, 'permission_id' => $permissions['kopsurat.manage']],
            ['role_id' => $admin, 'permission_id' => $permissions['role.manage']],
            ['role_id' => $admin, 'permission_id' => $permissions['retensi.evaluate']],
            ['role_id' => $admin, 'permission_id' => $permissions['log.view']],

            ['role_id' => $user, 'permission_id' => $permissions['surat.create']],
            ['role_id' => $user, 'permission_id' => $permissions['template.manage']],
            ['role_id' => $user, 'permission_id' => $permissions['arsip.manage']],
            ['role_id' => $user, 'permission_id' => $permissions['retensi.evaluate']],
        ];

        foreach ($data as $row) {
            $row['created_at'] = now();
            $row['updated_at'] = now();
            
            DB::table('role_permissions')->insertOrIgnore($row);
        }
    }
}
