<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create Permissions
        $permissions = [
            'akses dashboard',
            'kelola pengguna',
            'kelola master data',
            'akses tanaman pangan',
            'akses hortikultura',
            'akses perkebunan',
            'akses psp',
            'akses penyuluhan'
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // 2. Create Roles and Assign Permissions
        $superAdminRole = Role::findOrCreate('Super Admin', 'web');
        $superAdminRole->givePermissionTo(Permission::all());

        $kadinRole = Role::findOrCreate('Kepala Dinas', 'web');
        $kadinRole->givePermissionTo(['akses dashboard', 'akses tanaman pangan', 'akses hortikultura', 'akses perkebunan', 'akses psp', 'akses penyuluhan']);

        // Operator Tanaman Pangan
        $panganRole = Role::findOrCreate('Tanaman Pangan', 'web');
        $panganRole->givePermissionTo(['akses dashboard', 'akses tanaman pangan']);

        // Operator Hortikultura
        $hortiRole = Role::findOrCreate('Hortikultura', 'web');
        $hortiRole->givePermissionTo(['akses dashboard', 'akses hortikultura']);

        // Operator Perkebunan
        $bunRole = Role::findOrCreate('Perkebunan', 'web');
        $bunRole->givePermissionTo(['akses dashboard', 'akses perkebunan']);

        // Operator PSP
        $pspRole = Role::findOrCreate('PSP', 'web');
        $pspRole->givePermissionTo(['akses dashboard', 'akses psp']);

        // Operator Penyuluhan
        $penyuluhanRole = Role::findOrCreate('Penyuluhan', 'web');
        $penyuluhanRole->givePermissionTo(['akses dashboard', 'akses penyuluhan']);

        // 3. Create Default Users and Assign Roles
        $superadmin = User::firstOrCreate(
            ['email' => 'superadmin@simdistan.test'],
            [
                'name' => 'Super Admin SIM-Distan',
                'password' => bcrypt('password'),
            ]
        );
        $superadmin->assignRole($superAdminRole);

        // Kadin
        $kadin = User::firstOrCreate(
            ['email' => 'kadin@simdistan.test'],
            [
                'name' => 'Kepala Dinas Pertanian',
                'password' => bcrypt('password'),
            ]
        );
        $kadin->assignRole($kadinRole);

        // Operator Pangan
        $userPangan = User::firstOrCreate(
            ['email' => 'pangan@simdistan.test'],
            [
                'name' => 'Operator Tanaman Pangan',
                'password' => bcrypt('password'),
            ]
        );
        $userPangan->assignRole($panganRole);

        // Operator Hortikultura
        $userHorti = User::firstOrCreate(
            ['email' => 'horti@simdistan.test'],
            [
                'name' => 'Operator Hortikultura',
                'password' => bcrypt('password'),
            ]
        );
        $userHorti->assignRole($hortiRole);

        // Operator Perkebunan
        $userBun = User::firstOrCreate(
            ['email' => 'perkebunan@simdistan.test'],
            [
                'name' => 'Operator Perkebunan',
                'password' => bcrypt('password'),
            ]
        );
        $userBun->assignRole($bunRole);

        // Operator PSP
        $userPSP = User::firstOrCreate(
            ['email' => 'psp@simdistan.test'],
            [
                'name' => 'Operator PSP',
                'password' => bcrypt('password'),
            ]
        );
        $userPSP->assignRole($pspRole);

        // Operator Penyuluhan
        $userPenyuluhan = User::firstOrCreate(
            ['email' => 'penyuluhan@simdistan.test'],
            [
                'name' => 'Operator Penyuluhan',
                'password' => bcrypt('password'),
            ]
        );
        $userPenyuluhan->assignRole($penyuluhanRole);
    }
}
