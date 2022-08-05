<?php

namespace App\Events;

use App\Models\ShopifyStore;
use Illuminate\Queue\SerializesModels;
use Oseintow\Shopify\Shopify;

class StoreCreated
{
    use SerializesModels;

    public $store;

    /**
     * Create a new event instance.
     *
     * @param ShopifyStore $store
     *
     */
    public function __construct(ShopifyStore $store)
    {
        $this->store = $store;
    }
}
