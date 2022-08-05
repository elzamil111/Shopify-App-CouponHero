<?php

namespace App\Http\Middleware;

use Closure;
use Oseintow\Shopify\Shopify;

class WebhookShopifyHMACVerifier
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
            app('shopify')->verifyWebHook(
                $request->getContent(),
                $request->server('HTTP_X_SHOPIFY_HMAC_SHA256')
            ), 401
        );

        return $next($request);
    }
}
