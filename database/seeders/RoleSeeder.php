<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['nama_role' => 'Super Admin', 'created_at' => now(), 'updated_at' => now()],
            ['nama_role' => 'Admin', 'created_at' => now(), 'updated_at' => now()],
            ['nama_role' => 'User', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
