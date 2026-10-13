<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rides', function (Blueprint $table): void {
            $table->decimal('passenger_live_lat', 10, 7)->nullable()->after('dropoff_lng');
            $table->decimal('passenger_live_lng', 10, 7)->nullable()->after('passenger_live_lat');
        });
    }

    public function down(): void
    {
        Schema::table('rides', function (Blueprint $table): void {
            $table->dropColumn(['passenger_live_lat', 'passenger_live_lng']);
        });
    }
};
