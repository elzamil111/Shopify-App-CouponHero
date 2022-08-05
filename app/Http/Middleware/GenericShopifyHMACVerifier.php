<?php

namespace App\Http\Middleware;

use Closure;
use Oseintow\Shopify\Shopify;

class GenericShopifyHMACVerifier
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
        abort_unless(
            app('shopify')->verifyRequest(
                $request->getQueryString()
            ),
            403
        );

        return $next($request);
    }
}
