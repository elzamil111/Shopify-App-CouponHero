<?php

namespace App\Console\Commands;

use App\Models\ShopifyStore;
use Illuminate\Console\Command;

class BaseShopifyStoreCommand extends Command
{
    protected $store;

    protected function getStore()
    {
        $st = $this->ask('What is the store ID or url part');

        if (is_numeric($st)) {
            return $this->store = ShopifyStore::find($st);
        }

        $stores = ShopifyStore::where('shop_fqdn', 'LIKE', "%${st}%");

        if ($stores->count() == 1) {
            return $this->store = $stores->first();
        }

        if ($stores->count() == 0) {
            $this->error('No such store');
            exit(1);
        }

        $choice = $this->paginateContinuouslyWithInput(['ID', 'FQDN', 'Creation Date'], $stores, ['id', 'shop_fqdn', 'created_at']);

        return $this->store = ShopifyStore::find($choice);
    }

    protected function getStores()
    {
        $st = $this->ask('What is the store ID or url part');

        if (empty($st)) {
            exit(1);
        }

        if (is_numeric($st)) {
            return ShopifyStore::find($st);
        }

        return ShopifyStore::where('shop_fqdn', 'LIKE', $st)->get()->all();
    }

    protected function paginateContinuously($header, $collection, $force_visible = false)
    {
        $collection->chunk(
            20,
            function ($s) use ($header, $force_visible) {
                if ($force_visible) {
                    $s->each->setHidden([]);
                    $s->each->setVisible($force_visible);
                }
                $this->table($header, $s);
                $v = $this->ask('Enter for next page. Q to quit', false);
                if ($v == 'Q' || $v == 'q') {
                    return false;
                }
            }
        );
    }

    protected function paginateContinuouslyWithInput($header, $collection, $force_visible = false)
    {
        $choice = null;
        $collection->chunk(
            20,
            function ($s) use ($header, $force_visible, &$choice) {
                if ($force_visible) {
                    $s->each->setHidden([]);
                    $s->each->setVisible($force_visible);
                }
                $this->table($header, $s);
                $v = $this->ask('Enter the ID of the row or enter for next page', false);
                if (is_numeric($v)) {
                    $choice = $v;

                    return false;
                }
            }
        );

        if ($choice != null) {
            return $choice;
        }

        exit(1);
    }
}
