<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('permissions')->insert([
            ['nama_permission' => 'Kelola Role & Hak Akses', 'kode_permission' => 'role.manage', 'created_at' => now(), 'updated_at' => now()],
            ['nama_permission' => 'Kelola Pengguna', 'kode_permission' => 'user.manage', 'created_at' => now(), 'updated_at' => now()],
            ['nama_permission' => 'Kelola Pengaturan Kop Surat', 'kode_permission' => 'kopsurat.manage', 'created_at' => now(), 'updated_at' => now()],
            ['nama_permission' => 'Kelola Template Surat', 'kode_permission' => 'template.manage', 'created_at' => now(), 'updated_at' => now()],
            ['nama_permission' => 'Buat Surat Baru', 'kode_permission' => 'surat.create', 'created_at' => now(), 'updated_at' => now()],
            ['nama_permission' => 'Cetak Surat', 'kode_permission' => 'surat.print', 'created_at' => now(), 'updated_at' => now()],
            ['nama_permission' => 'Kelola Arsip Digital', 'kode_permission' => 'arsip.manage', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
