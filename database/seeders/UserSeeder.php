<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => '西　伶奈', 'email' => 'reina.n@coachtech.com'],
            ['name' => '山田　太郎', 'email' => 'taro.y@coachtech.com'],
            ['name' => '益田　一世', 'email' => 'issei.m@coachtech.com'],
            ['name' => '山本　敬吉', 'email' => 'keiichi.y@coachtech.com'],
            ['name' => '秋田　朋美', 'email' => 'tomomi.a@coachtech.com'],
            ['name' => '中西　教夫', 'email' => 'norio.n@coachtech.com'],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make('password'),
                ]
            );
        }
    }
}
