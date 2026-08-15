<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,
        ]);

        $superAdminRole = DB::table('roles')->where('nama_role', 'Super Admin')->first();
        $adminRole      = DB::table('roles')->where('nama_role', 'Admin')->first();
        $userRole       = DB::table('roles')->where('nama_role', 'User')->first();

        User::factory()->create([
            'name'     => 'Super Admin',
            'email'    => 'superadmin@gmail.com',
            'password' => Hash::make('12345607'),
            'role_id'  => $superAdminRole ? $superAdminRole->id : null,
        ]);

        User::factory()->create([
            'name'     => 'Admin',
            'email'    => 'admin@gmail.com',
            'password' => Hash::make('12345608'),
            'role_id'  => $adminRole ? $adminRole->id : null,
        ]);

        User::factory()->create([
            'name'     => 'User',
            'email'    => 'user@gmail.com',
            'password' => Hash::make('12345609'),
            'role_id'  => $userRole ? $userRole->id : null,
        ]);
    }
}