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
        Schema::table('rides', function (Blueprint $table) {
            $table->string('payment_method', 32)->nullable()->after('fare_estimate');
            $table->string('payment_status', 32)->default('unpaid')->after('payment_method');
            $table->decimal('fare_paid', 10, 2)->nullable()->after('payment_status');
            $table->timestamp('paid_at')->nullable()->after('fare_paid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rides', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_status', 'fare_paid', 'paid_at']);
        });
    }
};
