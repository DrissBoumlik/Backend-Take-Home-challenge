<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (DB::getConfig("driver") === 'sqlite') {
            DB::connection()->getPdo()->sqliteCreateFunction('regexp_replace', function ($text, $pattern, $replacement) {
                return preg_replace('/' . $pattern . '/', $replacement, $text);
            });
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
