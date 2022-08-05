<?php

namespace App\Http\Middleware;

use Closure;

class WelcomeScreen
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (config('shopify.welcome_screen') && app('shopifyapp')->store()->coupons()->count() == 0
        ) {
            return redirect()->route('embedded.welcome');
        }

        return $next($request);
    }
}
