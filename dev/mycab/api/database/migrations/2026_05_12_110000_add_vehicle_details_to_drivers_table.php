<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->string('cab_model')->nullable()->after('vehicle_type');
            $table->unsignedTinyInteger('seating_capacity')->nullable()->after('cab_model');
            $table->decimal('rate_per_km', 8, 2)->nullable()->after('seating_capacity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn(['cab_model', 'seating_capacity', 'rate_per_km']);
        });
    }
};
