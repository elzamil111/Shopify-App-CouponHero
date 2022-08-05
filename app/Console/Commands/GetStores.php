<?php

namespace App\Console\Commands;

use App\Models\License;
use App\Models\ShopifyStore;

class GetStores extends BaseShopifyStoreCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ch:stores';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Stores';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $choice = $this->choice('Find by [License] or by [Store]?', ['license', 'store']);

        if ($choice == 'store') {
            $st = $this->ask('What is the store ID or url part. Empty for all', false);

            $q = new ShopifyStore;

            if ($st !== false) {
                if (is_numeric($st)) {
                    $q = $q->where('id', $st);
                } else {
                    $q = $q->where('shop_fqdn', 'LIKE', "%${st}%");
                }
            }

            $this->paginateContinuously(['ID', 'FQDN', 'Token', 'Creation Date'], $q, ['id', 'shop_fqdn', 'token', 'created_at']);
        } else {
            $st = $this->ask('Enter license email or ID');

            $q = new License;

            if (is_numeric($st)) {
                $q = $q->where('id', $st);
            } else {
                $q = $q->where('license', 'LIKE', "%${st}%");
            }

            $license = $this->paginateContinuouslyWithInput(['ID', 'Email', 'Creation Date'], $q, ['id', 'license', 'created_at']);

            $license = License::find($license);

            if ($license->shopifyStores->count() == 0) {
                $this->error('This license has no stores');
                exit(0);
            }

            $this->paginateContinuously(['ID', 'FQDN', 'Token', 'Creation Date'], $license->shopifyStores(), ['id', 'shop_fqdn', 'token', 'created_at']);
        }
    }
}
