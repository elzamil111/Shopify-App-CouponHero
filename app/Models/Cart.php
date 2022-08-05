<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'cart_token',
        'shopify_store_id',
        'coupon_id',
        'applied',
    ];

    protected $casts = ['applied' => 'boolean'];

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
}
