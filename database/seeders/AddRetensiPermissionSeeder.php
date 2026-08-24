<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddRetensiPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $id = DB::table('permissions')->insertGetId([
            'nama_permission' => 'Evaluasi Retensi Arsip',
            'kode_permission' => 'retensi.manage',
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        $roleIds = DB::table('roles')
            ->whereIn('nama_role', ['Super Admin', 'Admin'])
            ->pluck('id');

        foreach ($roleIds as $roleId) {
            DB::table('role_permissions')->insert([
                'role_id'       => $roleId,
                'permission_id' => $id,
                'created_at'    => now(),
            ]);
        }
    }
}
