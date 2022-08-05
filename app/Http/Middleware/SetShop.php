<?php

namespace App\Http\Middleware;

use App\Models\ShopifyStore;
use Closure;

class SetShop
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @param bool                      $setModel
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $setModel = true)
    {
        if ($shop = $request->header('X-Shopify-Shop-Domain', false)) {
            app('shopify')->setShopUrl($shop);
        } else if ($shop = $request->query('shop')) {
            app('shopify')->setShopUrl($shop);
        }

        if ($setModel) {
            // Check if store exists
            $model = ShopifyStore::where('shop_fqdn', $shop)->first();
            if ($model == null) {
                if ($request->header('X-Shopify-Topic', false)) {
                    abort(204);
                    return;
                }

                return response()->view('reauth', ['store' => $shop]);
            }

            app('shopifyapp')->setShop($model, false);

            app('shopify')->setAccessToken($model->token);
        }

        return $next($request);
    }
}
