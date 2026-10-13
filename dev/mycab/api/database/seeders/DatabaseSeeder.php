<?php

namespace Database\Seeders;

use App\Models\Driver;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AdminUserSeeder::class);
        $this->call(VehicleTypeSeeder::class);
        $this->call(IntegrationConfigSeeder::class);

        $drivers = [
            ['name' => 'Rajesh Kumar', 'phone' => '9801000001', 'email' => 'rajesh.driver@himcab.local', 'vehicle_type' => 'mini', 'plate_number' => 'HP01A1001', 'latitude' => 31.1048, 'longitude' => 77.1734],
            ['name' => 'Suresh Thakur', 'phone' => '9801000002', 'email' => 'suresh.driver@himcab.local', 'vehicle_type' => 'mini', 'plate_number' => 'HP02B2002', 'latitude' => 31.0987, 'longitude' => 77.1651],
            ['name' => 'Vikram Singh', 'phone' => '9801000003', 'email' => 'vikram.driver@himcab.local', 'vehicle_type' => 'sedan', 'plate_number' => 'HP03C3003', 'latitude' => 31.1102, 'longitude' => 77.1823],
            ['name' => 'Amit Sharma', 'phone' => '9801000004', 'email' => 'amit.driver@himcab.local', 'vehicle_type' => 'sedan', 'plate_number' => 'HP04D4004', 'latitude' => 31.0955, 'longitude' => 77.1599],
            ['name' => 'Deepak Negi', 'phone' => '9801000005', 'email' => 'deepak.driver@himcab.local', 'vehicle_type' => 'suv', 'plate_number' => 'HP05E5005', 'latitude' => 31.1089, 'longitude' => 77.1888],
            ['name' => 'Manoj Rawat', 'phone' => '9801000006', 'email' => 'manoj.driver@himcab.local', 'vehicle_type' => 'suv', 'plate_number' => 'HP06F6006', 'latitude' => 31.1012, 'longitude' => 77.1705],
            ['name' => 'Ravi Bike', 'phone' => '9801000007', 'email' => 'ravi.bike@himcab.local', 'vehicle_type' => 'bike', 'plate_number' => 'HP07G7007', 'latitude' => 31.1065, 'longitude' => 77.1760],
            ['name' => 'Sunil TukTuk', 'phone' => '9801000008', 'email' => 'sunil.tuktuk@himcab.local', 'vehicle_type' => 'tuktuk', 'plate_number' => 'HP08H8008', 'latitude' => 31.0998, 'longitude' => 77.1688],
        ];

        foreach ($drivers as $row) {
            Driver::query()->create(array_merge($row, ['is_available' => true]));
        }
    }
}
