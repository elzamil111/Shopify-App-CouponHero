<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    public $fillable = [
        'license', 'max_uses'
    ];

    public function shopifyStores()
    {
        return $this->hasMany(ShopifyStore::class);
    }

    public function canBeUsed()
    {
        return $this->shopifyStores()->activated()->count() < $this->max_uses;
    }
}
