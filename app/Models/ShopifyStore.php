<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class ShopifyStore extends Authenticatable
{
    protected $fillable = [
        'shop_fqdn',
        'license_id',
        'token',
        'has_seen_welcome_screen'
    ];

    protected $hidden = [
        'token',
        'license_id'
    ];

    protected $casts = [
        'has_seen_welcome_screen' => 'bool'
    ];

    public function license()
    {
        return $this->belongsTo(License::class);
    }

    public function scopeNotActivated($query)
    {
        return $query->whereNull('token');
    }

    public function scopeActivated($query)
    {
        return $query->whereNotNull('token');
    }

    public function coupons()
    {
        return $this->hasMany(Coupon::class);
    }

    public function setShopifyApi()
    {
        app('shopify')->setShopUrl($this->shop_fqdn);
        app('shopify')->setAccessToken($this->token);
    }
}
