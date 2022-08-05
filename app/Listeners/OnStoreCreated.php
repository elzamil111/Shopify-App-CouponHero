<?php

namespace App\Listeners;

use App\Events\StoreCreated;

class OnStoreCreated
{
    /**
     * Handle the event.
     *
     * @param  StoreCreated $event
     *
     * @return void
     */
    public function handle(StoreCreated $event)
    {
        // We will make sure the script tag doesn't exist
        $s = app('shopify')->get('admin/script_tags.json');
        if ($s->where('src', asset('/coupon_hero.js'))->count() > 0)
            return;

        // We'll add the script tag
        app('shopify')->post(
            'admin/script_tags.json',
            [
                'script_tag' => [
                    'event' => 'onload',
                    'src'   => asset('/coupon_hero.js'),
                ],
            ]
        );
    }
}
