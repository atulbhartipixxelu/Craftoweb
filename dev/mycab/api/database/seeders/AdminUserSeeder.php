<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Default Filament admin (change password after first login in production).
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@himcab.local'],
            [
                'name' => 'HimCab Admin',
                'password' => 'password',
                'role' => 'admin',
                'phone' => null,
            ],
        );
    }
}
