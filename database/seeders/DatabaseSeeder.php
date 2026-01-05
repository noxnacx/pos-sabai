<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // สร้าง Admin
        User::create([
            'name' => 'Admin Owner',
            'username' => 'admin', // <--- ใช้ username แทน email
            // 'email' => 'admin@pos.com', // ลบออกหรือ comment ไว้
            'password' => Hash::make('password'),
            'role' => 'owner'
        ]);

        $this->call(ShopSeeder::class);
    }
}
