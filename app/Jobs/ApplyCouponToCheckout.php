<?php

namespace App\Jobs;

use App\Models\ShopifyStore;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ApplyCouponToCheckout implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $coupon;
    protected $cart;
    protected $shop;

    /**
     * Create a new job instance.
     *
     * @param $coupon
     * @param $cart
     * @param $shop
     */
    public function __construct($coupon, $cart, $shop)
    {
        $this->coupon = $coupon;
        $this->cart = $cart;
        $this->shop = $shop;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        app('shopifyapp')->setStore(ShopifyStore::where('shop_fqdn', $this->shop)->first());

        try {
            $cookie = SetCookie::fromString('cart=' . $this->cart);
            $jar = CookieJar::fromArray([$cookie], $this->shop);
            $client = new Client(['cookies' => $jar, 'allow_redirects' => false, 'verify' => false]);
            $r = $client->post(
                'https://' . $this->shop . '/cart',
                ['form_params' => ['checkout' => '']]
            );

            if ($r->getStatusCode() == 302) {
                $checkout_token = array_reverse(explode('/', $r->getHeader('Location')[0]))[0];
                app('shopify')->put(
                    "admin/checkouts/{$checkout_token}.json",
                    [
                        'checkout' => [
                            'discount_code' => $this->coupon->discount_code,
                        ],
                    ]
                );
            }
        } catch (\Exception $e) {
        }
    }
}
