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

        User::updateOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('o123456789n'),
                'role_id'  => $superAdminRole?->id,
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'     => 'Admin',
                'password' => Hash::make('t123456789o'),
                'role_id'  => $adminRole?->id,
            ]
        );

        User::updateOrCreate(
            ['email' => 'user1@gmail.com'],
            [
                'name'     => 'User1',
                'password' => Hash::make('on12345678901'),
                'role_id'  => $userRole?->id,
            ]
        );

        User::updateOrCreate(
            ['email' => 'user2@gmail.com'],
            [
                'name'     => 'User2',
                'password' => Hash::make('tw12345678902'),
                'role_id'  => $userRole?->id,
            ]
        );

        User::updateOrCreate(
            ['email' => 'user3@gmail.com'],
            [
                'name'     => 'User3',
                'password' => Hash::make('tr12345678903'),
                'role_id'  => $userRole?->id,
            ]
        );
    }
}
