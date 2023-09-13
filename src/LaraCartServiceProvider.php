<?php

namespace abdulrhmanak\laracart;

use abdulrhmanak\laracart\Http\Models\Cart;
use Illuminate\Support\ServiceProvider;

class LaraCartServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(Cart::class, function ($app) {
            return new Cart();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
//            $this->publishes(...); // assets and config

            $this->loadMigrationsFrom(__DIR__ . '/../src/database/migrations');
        }
    }
}
