<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('integration_configs', function (Blueprint $table) {
            $table->decimal('commission_rate_percent', 5, 2)->default(10)->after('frontend_url');
            $table->unsignedTinyInteger('commission_due_day')->default(5)->after('commission_rate_percent');
        });

        Schema::table('drivers', function (Blueprint $table) {
            $table->boolean('is_platform_enabled')->default(true)->after('is_available');
            $table->text('platform_disabled_reason')->nullable()->after('is_platform_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn(['is_platform_enabled', 'platform_disabled_reason']);
        });

        Schema::table('integration_configs', function (Blueprint $table) {
            $table->dropColumn(['commission_rate_percent', 'commission_due_day']);
        });
    }
};
