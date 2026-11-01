<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\User\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@gmail.com',
                'email_verified_at' => now(),
                'password' => 'pass',
                'role' => 'super_admin',
            ],
            [
                'name' => 'Admin User',
                'email' => 'admin@gmail.com',
                'email_verified_at' => now(),
                'password' => 'pass',
                'role' => 'admin',
            ],
            [
                'name' => 'Sales User',
                'email' => 'sales@gmail.com',
                'email_verified_at' => now(),
                'password' => 'pass',
                'role' => 'sales',
            ],
            [
                'name' => 'Customer User',
                'email' => 'customer@gmail.com',
                'email_verified_at' => now(),
                'password' => 'pass',
                'role' => 'customer', // or add a 'customer' role in the other seeder
            ],
        ];

        foreach ($users as $data) {
            $roleName = $data['role'];
            unset($data['role']);

            /** @var User $user */
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                $data
            );

            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);

            $user->syncRoles([$role]);
        }
    }
}