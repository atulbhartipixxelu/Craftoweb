<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('integration_configs', function (Blueprint $table) {
            $table->boolean('razorpay_enabled')->default(false)->after('commission_due_day');
            $table->string('razorpay_key_id')->nullable()->after('razorpay_enabled');
            $table->text('razorpay_key_secret')->nullable()->after('razorpay_key_id');
            $table->boolean('phonepe_enabled')->default(false)->after('razorpay_key_secret');
            $table->string('phonepe_merchant_id')->nullable()->after('phonepe_enabled');
            $table->text('phonepe_salt_key')->nullable()->after('phonepe_merchant_id');
            $table->unsignedTinyInteger('phonepe_salt_index')->default(1)->after('phonepe_salt_key');
            $table->string('phonepe_env', 16)->default('sandbox')->after('phonepe_salt_index');
        });
    }

    public function down(): void
    {
        Schema::table('integration_configs', function (Blueprint $table) {
            $table->dropColumn([
                'razorpay_enabled',
                'razorpay_key_id',
                'razorpay_key_secret',
                'phonepe_enabled',
                'phonepe_merchant_id',
                'phonepe_salt_key',
                'phonepe_salt_index',
                'phonepe_env',
            ]);
        });
    }
};
