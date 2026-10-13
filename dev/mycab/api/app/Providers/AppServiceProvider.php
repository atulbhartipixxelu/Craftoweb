<?php

namespace App\Providers;

use App\Support\IntegrationSettings;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->ensureFileBasedCacheWhenDatabaseMissing();

        IntegrationSettings::applyToConfig();
    }

    private function ensureFileBasedCacheWhenDatabaseMissing(): void
    {
        if (config('cache.default') !== 'database' && config('session.driver') !== 'database') {
            return;
        }

        try {
            if (! Schema::hasTable('cache') || ! Schema::hasTable('sessions')) {
                config([
                    'cache.default' => 'file',
                    'session.driver' => 'file',
                ]);
            }
        } catch (\Throwable) {
            config([
                'cache.default' => 'file',
                'session.driver' => 'file',
            ]);
        }
    }
}
