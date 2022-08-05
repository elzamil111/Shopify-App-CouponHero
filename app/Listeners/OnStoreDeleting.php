<?php

namespace App\Listeners;

use App\Events\StoreDeleting;
use App\Models\Coupon;

class OnStoreDeleting
{
    /**
     * Handle the event.
     *
     * @param  StoreDeleting $event
     *
     * @return void
     */
    public function handle(StoreDeleting $event)
    {
        $coupons = Coupon::whereShopifyStoreId($event->store->id)->whereNotNull('custom_icon')->get()->pluck(
            'custom_icon'
        );

        $coupons->each(function($item) {
            \Storage::disk('public')->delete($item);
        });
    }
}
