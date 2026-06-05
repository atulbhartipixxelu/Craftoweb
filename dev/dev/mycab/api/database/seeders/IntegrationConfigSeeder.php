<?php

namespace Database\Seeders;

use App\Models\IntegrationConfig;
use Illuminate\Database\Seeder;

class IntegrationConfigSeeder extends Seeder
{
    public function run(): void
    {
        IntegrationConfig::current();
    }
}
