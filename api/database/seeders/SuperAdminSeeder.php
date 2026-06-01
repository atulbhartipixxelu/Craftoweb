<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'atulbhartipixxelu@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('atulbhartipixxelu#321'),
                'role' => 'super_admin',
                'email_verified_at' => now(),
            ]
        );
    }
}
