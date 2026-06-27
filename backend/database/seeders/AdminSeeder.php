<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Superadmin utama
        User::firstOrCreate(
            ['email' => 'superadmin@aethelread.id'],
            [
                'name'      => 'Super Admin',
                'password'  => 'cuma1sampai9',
                'role'      => 'superadmin',
                'is_active' => true,
            ]
        );

        // Admin kedua untuk testing
        User::firstOrCreate(
            ['email' => 'admin@aethelread.id'],
            [
                'name'      => 'Admin',
                'password'  => '1sampai9',
                'role'      => 'admin',
                'is_active' => true,
            ]
        );
    }
}