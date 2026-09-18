<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['nama_permission' => 'Buat Surat', 'kode_permission' => 'surat.create'],
            ['nama_permission' => 'Template Surat', 'kode_permission' => 'template.manage'],
            ['nama_permission' => 'Arsip Digital', 'kode_permission' => 'arsip.manage'],
            ['nama_permission' => 'Pengaturan Kop Surat', 'kode_permission' => 'kopsurat.manage'],
            ['nama_permission' => 'Kelola Pengguna & Role', 'kode_permission' => 'role.manage'],
            ['nama_permission' => 'Evaluasi Retensi Arsip', 'kode_permission' => 'retensi.evaluate'],
            ['nama_permission' => 'Kelola Kriteria Retensi', 'kode_permission' => 'retensi.manage'],
            ['nama_permission' => 'Log Aktivitas Sistem', 'kode_permission' => 'log.view'],
        ];

        foreach ($permissions as $perm) {
            DB::table('permissions')->updateOrInsert(
                ['kode_permission' => $perm['kode_permission']],
                [
                    'nama_permission' => $perm['nama_permission'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
