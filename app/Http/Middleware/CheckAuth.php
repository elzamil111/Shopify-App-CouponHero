<?php

namespace App\Http\Middleware;

use App\Models\ShopifyStore;
use Closure;

class CheckAuth
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
        $store_id = session('store', false);
        
        if ($store_id != null && $store = ShopifyStore::find($store_id)) {
            app('shopifyapp')->setStore($store);
            return $next($request);
        }

        return response()->view('not_authenticated');
    }
}
