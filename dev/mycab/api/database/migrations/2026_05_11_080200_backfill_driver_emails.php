<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $byPhone = [
            '9801000001' => 'rajesh.driver@himcab.local',
            '9801000002' => 'suresh.driver@himcab.local',
            '9801000003' => 'vikram.driver@himcab.local',
            '9801000004' => 'amit.driver@himcab.local',
            '9801000005' => 'deepak.driver@himcab.local',
            '9801000006' => 'manoj.driver@himcab.local',
        ];

        foreach ($byPhone as $phone => $email) {
            DB::table('drivers')->where('phone', $phone)->update(['email' => $email]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
