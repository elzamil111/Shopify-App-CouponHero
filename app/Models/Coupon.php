<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    public $preset_names = [
        8 => 'Coin 1',
        17 => 'Coin 2',
        11 => 'Coin 3',
        20 => 'Coin 4',
        9 => 'Dollar 1',
        18 => 'Dollar 2',
        10 => 'Dollar 3',
        19 => 'Dollar 4',
        4 => 'Dollar Sign',
        7 => 'Dollar Sign 1',
        16 => 'Dollar Sign 2',
        12 => 'Dollar Sign 3',
        21 => 'Dollar Sign 4',
        5 => 'Green Dollar Bill',
        2 => 'GreenCoin',
        3 => 'Lighter Money Bag',
        1 => 'Money Bag',
        13 => 'Moneybag 1',
        22 => 'Moneybag 2',
        14 => 'Moneybag 3',
        15 => 'Moneybag 4',
        6 => 'Orange Coin',
    ];

    protected $guarded = ['id'];

    protected $hidden = ['custom_icon', 'price_rule_id'];

    protected $appends = ['icon', 'has_custom_icon'];

    protected $casts = [
        'icon_preset' => 'integer',
    ];

    private $presets = [
        1 => '1.png',
        2 => '2.png',
        3 => '3.png',
        4 => '4.png',
        5 => '5.png',
        6 => '6.png',
        7 => '7.png',
        8 => '8.png',
        9 => '9.png',
        10 => '10.png',
        11 => '11.png',
        12 => '12.png',
        13 => '13.png',
        14 => '14.png',
        15 => '15.png',
        16 => '16.png',
        17 => '17.png',
        18 => '18.png',
        19 => '19.png',
        20 => '20.png',
        21 => '21.png',
        22 => '22.png',
    ];

    public static function defaultCoupon()
    {
        return new self(
            [
                'coupon_token' => '',
                // Settings
                'name' => '',
                'discount_code' => '',
                // Information
                'title' => '',
                'description' => '',
                'button_text' => '',
                // Icon Settings
                'icon_preset' => '11',
                'icon_size' => '190',
                'custom_icon' => null,
                // Sizing
                'title_size' => '30',
                'description_size' => '18',
                'button_size' => '24',
                // Fonts
                'title_font' => 'Montserrat',
                'description_font' => 'Open Sans',
                'button_font' => 'Montserrat',
                'title_font_weight' => '400',
                'description_font_weight' => '400',
                'button_font_weight' => '400',
                // Colors
                'title_color' => '#1365d2',
                'description_color' => '#6f7c8c',
                'button_color' => '#ffffff',
                'window_bg_color' => '#ffffff',
                'button_bg_color' => '#30d3ab',
                // Success Bar
                'success_bar_bg_color' => '#32b259',

                // Radii
                'window_border_radius' => '10',
            ]
        );
    }

    public function getIconAttribute()
    {
        if ($this->custom_icon != null) {
            return asset('/storage/'.$this->custom_icon);
        } else {
            return asset('/images/'.$this->presets[$this->icon_preset]);
        }
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function shopifyStore()
    {
        return $this->belongsTo(ShopifyStore::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function impressions()
    {
        return $this->hasMany(CouponImpression::class);
    }

    public function getHasCustomIconAttribute()
    {
        return $this->custom_icon != null;
    }

    public function getRouteKeyName()
    {
        return 'coupon_token';
    }
}
