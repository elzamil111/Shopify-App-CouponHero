<?php

namespace App\Console\Commands;

use App\Models\License;

class GetLicenses extends BaseShopifyStoreCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ch:licenses';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Licenses';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $lic = $this->ask('What is the license ID or email. Empty for all', false);

        $q = new License;

        if ($lic !== false) {
            if (is_numeric($lic)) {
                $q = $q->where('id', $lic);
            } else {
                $q = $q->where('license', 'LIKE', "%${lic}%");
            }
        }

        $this->paginateContinuously(['ID', 'Email', 'Creation Date'], $q, ['id', 'license', 'created_at']);
    }
}
