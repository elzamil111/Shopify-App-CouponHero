<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Horizon\Horizon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
            \Request::setTrustedProxies(
                ['127.0.0.1']
            );
            $this->app->register(\Laravel\Tinker\TinkerServiceProvider::class);
        } else {
            $this->app->register(\Sentry\SentryLaravel\SentryLaravelServiceProvider::class);
            $this->app->alias(\Sentry\SentryLaravel\SentryFacade::class, 'Sentry');
        }

        Horizon::auth(
            function ($re) {
                return true;
            }
        );
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
