<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\CouponImpression;
use App\Models\Order;
use App\Support\Checkout;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function getCoupon(Request $request, Coupon $coupon, $cart)
    {
        $coupon->addHidden(['id', 'shopify_store_id', 'created_at', 'updated_at', 'name']);

        Cart::updateOrCreate(
            [
                'cart_token' => $cart,
                'coupon_id' => $coupon->id,
                'shopify_store_id' => $coupon->shopify_store_id,
            ],
            [
                'applied' => true, // We will now assume it was applied, due to the JS
            ]
        );

        CouponImpression::forceCreate(
            [
                'shopify_store_id' => $coupon->shopify_store_id,
                'ip_address' => $request->ip(),
                'coupon_id' => $coupon->id,
            ]
        );

        app('shopify')->setAccessToken($coupon->shopifyStore->token);
        app('shopify')->setShopUrl($coupon->shopifyStore->shop_fqdn);

        if (! ($price_rule = cache('coupon:'.$coupon->id, false))) {
            $price_rule = $this->getPriceRule($coupon);
            if ($price_rule) {
                cache(['coupon:'.$coupon->id => $price_rule], 20);
            }
        }

        if ($price_rule === null) {
            return response('', 404);
        }

        $discount_amount = abs((int)$price_rule->get('value'));
        $discount_value = $price_rule->get('value_type') == 'fixed_amount' ? '$'.$discount_amount : $discount_amount.'%';

        if ($price_rule->get('target_type') == 'shipping_line') {
            $str = "Congratulations, you'll be receiving ${discount_value} off the shipping cost. Discount reflected at checkout!";
        }else{
            $str = "Congratulations, you'll be receiving ${discount_value} off your purchase. Discount reflected at checkout!";
        }

        // No longer needed
        // Checkout::applyCouponToCheckoutNow($coupon, $cart, $coupon->shopifyStore->shop_fqdn);

        return $coupon->toArray() + ['success_box_text' => $str];
    }

    public function webhookOnCheckoutUpdate(Request $request)
    {
        $cart_token = $request->input('cart_token');
        $checkout = $request->input('token');

        $carts = Cart::with('coupon')->where('cart_token', $cart_token)->where('applied', false)->get();

        if ($carts->count() > 0) {
            Checkout::applyCouponsToCheckout($carts, $checkout);
        }

        return response(null, 204);
    }

    public function webhookOnCheckoutCreate(Request $request)
    {
        $cart_token = $request->input('cart_token');
        $checkout = $request->input('token');

        $carts = Cart::with('coupon')->where('cart_token', $cart_token)->where('applied', false)->get();

        // Checkout::applyCouponsToCheckout($carts, $checkout);

        return response(null, 204);
    }

    public function webhookOnOrderPaid(Request $request)
    {
        $checkout_token = $request->input('checkout_token');
        $cart_token = $request->input('cart_token');
        $checkout_discounts = $request->input('discount_codes');

        // Convert the sale only if the discount matches the checkout discount
        if (count($checkout_discounts) > 0) {
            // Check if a cart token already exists
            $cart = Cart::with('coupon')->where('cart_token', $cart_token)->where('applied', true)->first();

            if ($cart) {
                if ($checkout_discounts[0]['code'] == $cart->coupon->discount_code) {
                    Order::create(
                        [
                            'shopify_store_id' => app('shopifyapp')->store()->id,
                            'coupon_id' => $cart->coupon_id,
                            'checkout_token' => $checkout_token,
                            'total_price' => $request->input('total_price'),
                        ]
                    );
                }
            }
        }

        return response(null, 204);
    }

    // Private to find price rule
    public function getPriceRule(Coupon $coupon)
    {
        $fqdn = $coupon->shopifyStore->shop_fqdn;
        $token = $coupon->shopifyStore->token;

        try {
            $c = new Client(['allow_redirects' => false, 'verify' => false]);
            $response = $c->request(
                'GET',
                "https://{$fqdn}/admin/discount_codes/lookup.json",
                [
                    'query'   => ['code' => $coupon->discount_code],
                    'headers' => ['X-Shopify-Access-Token' => $token],
                ]
            );

            $loc = $response->getHeader('Location')[0] ?? '';
            if (! $loc)
                return null;

            $path = parse_url($loc, PHP_URL_PATH);
            if (! $path)
                return null;

            // Get the price rule from the path
            $matches = null;
            $match_count = preg_match('/price_rules\/(\d+)/', $path, $matches);
            if ($match_count == 0)
                return null;

            $price_rule_id = $matches[1];

            // Get rule ID
            $response = app('shopify')->get('admin/price_rules/' . $price_rule_id . '.json');

            if ($response) {
                return $response;
            }

            return null;
        }catch (\Exception $e) {
            return null;
        }
    }
}
