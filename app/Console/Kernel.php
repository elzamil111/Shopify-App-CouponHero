<?php

namespace App\Console;

use App\Console\Commands\ApplyCouponToCart;
use App\Console\Commands\CreatePortalAuthenticationToken;
use App\Console\Commands\GetCoupons;
use App\Console\Commands\GetLicenses;
use App\Console\Commands\GetPriceRules;
use App\Console\Commands\GetStores;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        CreatePortalAuthenticationToken::class,
        GetCoupons::class,
        GetLicenses::class,
        GetStores::class,
        ApplyCouponToCart::class,
        GetPriceRules::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
