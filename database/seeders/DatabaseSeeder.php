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
            UnitKerjaSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,
        ]);

        $idRole = fn (string $nama) => DB::table('roles')->where('nama_role', $nama)->value('id');
        $idUnit = fn (string $kode) => DB::table('unit_kerja')->where('kode', $kode)->value('id');

        $superAdmin = $idRole('Super Admin');
        $admin      = $idRole('Admin');
        $user       = $idRole('User');

        $akun = [
            ['superadmin@gmail.com',    'Super Admin',            'o123456789n',   $superAdmin, null],

            ['admin@gmail.com',         'Admin Kantor Pusat',     't123456789o',   $admin, 'PUSAT'],
            ['user1@gmail.com',         'User Pusat 1',           'on12345678901', $user,  'PUSAT'],
            ['user2@gmail.com',         'User Pusat 2',           'tw12345678902', $user,  'PUSAT'],

            ['admin.web@gmail.com',     'Admin Inovindo Web',     'a123456789w',   $admin, 'WEB'],
            ['user3@gmail.com',         'User Web 1',             'tr12345678903', $user,  'WEB'],
            ['user4@gmail.com',         'User Web 2',             'fo12345678904', $user,  'WEB'],

            ['admin.academy@gmail.com', 'Admin Inovindo Academy', 'a123456789a',   $admin, 'ACADEMY'],
            ['user5@gmail.com',         'User Academy 1',         'fv12345678905', $user,  'ACADEMY'],
            ['user6@gmail.com',         'User Academy 2',         'si12345678906', $user,  'ACADEMY'],
        ];

        foreach ($akun as [$email, $nama, $password, $roleId, $kodeUnit]) {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name'          => $nama,
                    'password'      => Hash::make($password),
                    'role_id'       => $roleId,
                    'unit_kerja_id' => $kodeUnit ? $idUnit($kodeUnit) : null,
                    'is_aktif'      => true,
                ]
            );
        }
    }
}
