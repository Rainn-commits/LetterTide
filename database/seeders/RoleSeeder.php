<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['Super Admin', 'Admin', 'User'];

        foreach ($roles as $namaRole) {
            if (!DB::table('roles')->where('nama_role', $namaRole)->exists()) {
                DB::table('roles')->insert([
                    'nama_role'  => $namaRole,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
