<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Super Admin
        User::updateOrCreate(
            ['email' => 'superadmin@tfi.com'],
            [
                'name'      => 'Super Admin',
                'email'     => 'superadmin@tfi.com',
                'password'  => Hash::make('password'),
                'role'      => User::ROLE_SUPERADMIN,
                'is_active' => true,
                'phone'     => '+91 9000000001',
            ]
        );

        // Admin
        User::updateOrCreate(
            ['email' => 'admin@tfi.com'],
            [
                'name'      => 'Admin User',
                'email'     => 'admin@tfi.com',
                'password'  => Hash::make('password'),
                'role'      => User::ROLE_ADMIN,
                'is_active' => true,
                'phone'     => '+91 9000000002',
            ]
        );

        // Customer
        User::updateOrCreate(
            ['email' => 'customer@tfi.com'],
            [
                'name'      => 'John Customer',
                'email'     => 'customer@tfi.com',
                'password'  => Hash::make('password'),
                'role'      => User::ROLE_CUSTOMER,
                'is_active' => true,
                'phone'     => '+91 9000000003',
            ]
        );
    }
}
