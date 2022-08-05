<?php

namespace App\Support;

use App\Jobs\ApplyCouponToCheckout;
use App\Models\Cart;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use Illuminate\Support\Collection;

class Checkout
{
    static function applyCouponsToCheckout(Collection $carts, string $checkout)
    {
        if ($carts->count() == 0)
            return;

        try {
            $carts->each(
                function (Cart $cart) use ($checkout) {
                    app('shopify')->put(
                        "admin/checkouts/{$checkout}.json",
                        [
                            'checkout' => [
                                'discount_code' => $cart->coupon->discount_code,
                            ],
                        ]
                    );

                    $cart->update(['applied' => true]);
                }
            );
        } catch (\Exception $exception) {
        }
    }

    /**
     * @param      $coupon
     * @param      $cart
     * @param      $shop
     */
    static function applyCouponToCheckoutNow($coupon, $cart, $shop)
    {
        dispatch(new ApplyCouponToCheckout($coupon, $cart, $shop));
    }
}