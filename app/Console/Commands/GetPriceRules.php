<?php

namespace App\Console\Commands;

class GetPriceRules extends BaseShopifyStoreCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ch:pricerules';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Price Rules';

    public function handle()
    {
        $this->getStore();

        $this->store->setShopifyApi();
        $all = collect();
        $page = 1;

        while (true) {
            $rules = app('shopify')->get('/admin/price_rules.json', ['limit' => 250, 'page' => $page]);

            if ($rules->count() == 0)
                break;

            $rules->transform(
                function ($a) {
                    $a = collect($a);

                    return [
                        'id'         => $a['id'],
                        'title'      => $a['title'],
                        'value_type' => $a['value_type'],
                        'value'      => $a['value'],
                    ];
                }
            );

            $all = $all->merge($rules);
            if ($rules->count() < 250)
                break;

            $page++;
        }

        $all = $all->chunk(20);
        foreach ($all as $chunk) {
            $this->table(['ID', 'Title', 'Value Type', 'Value'], $chunk);
            $v = $this->ask('Enter for next page. Q to quit', false);
            if ($v == 'Q' || $v == 'q') {
                break;
            }
        }
    }
}
