<?php

namespace App\Providers;

use App\Facade\LazadaService;
use App\Facade\Scraper;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->singleton('getproduct', function ($app) {
            return new Scraper();
        });

        $this->app->singleton('lazada', function ($app) {
            return new LazadaService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
