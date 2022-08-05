<?php

namespace App\Support\Traits;

use App\Models\ShopifyStore;
use GuzzleHttp\Client;

trait FindsPriceRuleFromDiscountCode
{
    protected function findPriceRule(ShopifyStore $store, string $code)
    {
        try {
            $c = new Client(['allow_redirects' => false, 'verify' => false]);
            $response = $c->request(
                'GET',
                "https://{$store->shop_fqdn}/admin/discount_codes/lookup.json",
                [
                    'query'   => ['code' => $code],
                    'headers' => ['X-Shopify-Access-Token' => $store->token],
                ]
            );

            $loc = $response->getHeader('Location')[0] ?? '';
            if (! $loc)
                return '';

            $path = parse_url($loc, PHP_URL_PATH);
            if (! $path)
                return '';

            // Get rule ID
            $path .= '.json';
            usleep(500000); // For the 500ms delay
            $response = app('shopify')->get($path);

            if ($response) {
                return $response->get('price_rule_id');
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}