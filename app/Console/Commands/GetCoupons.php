<?php

namespace App\Console\Commands;

use App\Models\Coupon;

class GetCoupons extends BaseShopifyStoreCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ch:coupons';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Coupons';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $choice = $this->choice('By slug or store?', ['slug', 'store']);

        if ($choice == 'slug') {
            $slug = $this->ask('Which slug?');
            $this->paginateContinuously(
                ['ID', 'Store ID', 'Slug', 'Name', 'Discount Code'],
                Coupon::where('coupon_token', 'LIKE', "%${slug}%"),
                ['id', 'shopify_store_id', 'coupon_token', 'name', 'discount_code']
            );
        } else {
            $this->getStore();

            $this->info('Using store ' . $this->store->shop_fqdn);

            $this->paginateContinuously(
                ['ID', 'Store ID', 'Slug', 'Name', 'Discount Code'],
                $this->store->coupons(),
                ['id', 'shopify_store_id', 'coupon_token', 'name', 'discount_code']
            );
        }
    }
}
