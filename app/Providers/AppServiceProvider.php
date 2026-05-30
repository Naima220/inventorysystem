<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Fix for shared hosting where base_path might resolve to public directory
        $dbPath = base_path('database');
        if (strpos($dbPath, 'public' . DIRECTORY_SEPARATOR . 'database') !== false) {
            $realBasePath = str_replace(DIRECTORY_SEPARATOR . 'public', '', base_path());
            $this->app->useDatabasePath($realBasePath . DIRECTORY_SEPARATOR . 'database');
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultstringLength(191);
    }
}
