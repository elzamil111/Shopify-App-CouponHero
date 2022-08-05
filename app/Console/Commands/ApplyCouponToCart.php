<?php

namespace App\Console\Commands;

use App\Models\Coupon;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use Illuminate\Console\Command;
use Illuminate\Support\Debug\Dumper;

class ApplyCouponToCart extends BaseShopifyStoreCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ch:apply';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Apples a Coupon to a Checkout Instance';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $cart = $this->ask('Give us your cart token');
        $coupon = $this->ask('Give the coupon slug');

        $coupon = Coupon::with('shopifyStore')->where('coupon_token', $coupon)->firstOrFail();


        // We now emulate
        app('shopify')->setShopUrl($coupon->shopifyStore->shop_fqdn);
        app('shopify')->setAccessToken($coupon->shopifyStore->token);

        try {
            $cookie = SetCookie::fromString('cart=' . $cart);
            $jar = CookieJar::fromArray([$cookie], $coupon->shopifyStore->shop_fqdn);
            $client = new Client(['cookies' => $jar, 'allow_redirects' => false, 'verify' => false]);
            $r = $client->post(
                'https://' . $coupon->shopifyStore->shop_fqdn . '/cart',
                ['form_params' => ['checkout' => '']]
            );

            if ($r->getStatusCode() == 302) {
                $checkout_token = array_reverse(explode('/', $r->getHeader('Location')[0]))[0];
                $this->info("Checkout token is <options=bold,underscore>${checkout_token}</>");
                $this->info("Checkout URL is " . $r->getHeader('Location')[0]);
                $discounted = app('shopify')->put(
                    "admin/checkouts/{$checkout_token}.json",
                    [
                        'checkout' => [
                            'discount_code' => $coupon->discount_code,
                        ],
                    ]
                );

                (new Dumper)->dump($discounted);
            }else{
                dd($r);
            }
        } catch (\Exception $e) {
            dd($e);
        }
    }
}
