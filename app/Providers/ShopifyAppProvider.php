<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Support\ShopifyApp;

class ShopifyAppProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            ShopifyApp::class,
            function () {
                return new ShopifyApp;
            }
        );

        $this->app->alias(ShopifyApp::class, 'shopifyapp');
    }
}
