<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::create([
            'name' => 'admin',
            'email' => 'admin@coachtech.com',
            'password' => Hash::make('password'),
        ]);
    }
}
