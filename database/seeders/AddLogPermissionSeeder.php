<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class AddLogPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $id = DB::table('permissions')->insertGetId([
            'nama_permission' => 'Lihat Log Aktivitas Sistem',
            'kode_permission' => 'log.view',
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        $superAdminId = DB::table('roles')->where('nama_role', 'Super Admin')->value('id');

        DB::table('role_permissions')->insert([
            'role_id'       => $superAdminId,
            'permission_id' => $id,
            'created_at'    => now(),
        ]);
    }
}
