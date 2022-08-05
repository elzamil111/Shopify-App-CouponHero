<?php

namespace App\Http\Middleware;

use Closure;

class ForceShopifyAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // This middleware will start a session and force authentication
        if ($store = app('shopifyapp')->store()) {
            session(['store' => $store->id]);
        }

        return $next($request);
    }
}
