<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Shopify Api
    |--------------------------------------------------------------------------
    |
    | This file is for setting the credentials for shopify api key and secret.
    |
    */

    'key'                   => env("SHOPIFY_APIKEY", null),
    'secret'                => env("SHOPIFY_SECRET", null),

    // Whether or not you want a welcome screen
    'welcome_screen'        => true,

    // The video to show on the welcome screen (tutorial)
    'welcome_screen_video'  => 'https://player.vimeo.com/video/231498729',

    // The video to show on the application activation screen
    'activate_screen_video' => 'https://player.vimeo.com/video/231498531',

    // Activation screen support email
    'support_email'         => 'support@couponhero.io',

    // The claims to automatically ask Shopify
    'oauth_claims'          => [
        'write_checkouts',
        'read_checkouts',
        'read_price_rules',
        'write_script_tags',
        'read_orders',
    ],

    // The webhooks to automatically create. Each one is a pair ['topic', 'route_name']. ex: ['orders/create', 'webhooks.orders_create']
    'webhooks'              => [
        ['checkouts/create', 'webhooks.checkout_created'],
        ['checkouts/update', 'webhooks.checkout_updated'],
        ['orders/paid', 'webhooks.order_paid'],
    ],

    // Route where your main "embedded app" route is located at.
    'embedded_app_route'    => 'embedded.my_coupons',
];