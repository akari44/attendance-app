<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::firstOrCreate(
            ['email' => 'user3@example.com'], // 検索条件
            [
                'name' => 'user3',
                'password' => Hash::make('password'),
            ]
        );
    }
}
