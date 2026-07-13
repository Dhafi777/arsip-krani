<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Membuat Akun Krani
        User::updateOrCreate(
            ['email' => 'krani@ptpn4.com'],
            [
                'name' => 'Daxxa (Krani)',
                'password' => bcrypt('password123'),
                'role' => 'krani'
            ]
        );

        // Membuat Akun Admin
        User::updateOrCreate(
            ['email' => 'admin@ptpn4.com'],
            [
                'name' => 'Bapak Admin',
                'password' => bcrypt('password123'),
                'role' => 'admin'
            ]
        );
    }
}