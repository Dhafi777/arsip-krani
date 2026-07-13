<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Akun Admin
        User::create([
            'name' => 'Admin Sekper',
            'email' => 'admin@ptpn.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // Akun Krani
        User::create([
            'name' => 'Krani Sekper',
            'email' => 'krani@ptpn.com',
            'password' => Hash::make('password123'),
            'role' => 'krani',
        ]);
    }
}