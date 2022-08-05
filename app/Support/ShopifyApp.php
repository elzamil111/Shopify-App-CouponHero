<?php

namespace App\Support;

use App\Models\ShopifyStore;

class ShopifyApp
{
    protected $_store;

    public function setStore(ShopifyStore $shopifyStore, $setApi = true)
    {
        $this->_store = $shopifyStore;

        if ($setApi) {
            $this->newApi();
        }

        return $this;
    }

    public function setShop(ShopifyStore $shopifyStore, $setApi = true)
    {
        return $this->setStore($shopifyStore, $setApi);
    }

    public function store()
    {
        return $this->_store;
    }

    public function shop()
    {
        return $this->store();
    }

    public function newApi(ShopifyStore $store = null)
    {
        $store = $store ?? $this->_store;

        if ($store == null) {
            return;
        }

        return app('shopify')->setShopUrl($store->shop_fqdn)->setAccessToken($store->token);
    }
}