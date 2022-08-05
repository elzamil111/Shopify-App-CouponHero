<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\ShopifyStore;
use App\Support\Traits\FindsPriceRuleFromDiscountCode;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class CouponHeroController extends Controller
{
    use FindsPriceRuleFromDiscountCode;

    public function myCoupons()
    {
        $coupons = Coupon::where('shopify_store_id', app('shopifyapp')->store()->id)->get();

        if ($coupons->count() == 0) {
            return redirect()->route('embedded.welcome');
        }

        return view(
            'my_coupons',
            [
                'coupons' => $coupons,
                'fonts'   => $coupons->pluck('title_font')
                                     ->merge($coupons->pluck('description_font'))
                                     ->merge($coupons->pluck('button_font'))
                                     ->unique()
                                     ->flatten()
                                     ->toJson(),
            ]
        );
    }

    public function ensureExists(Request $request)
    {
        $this->validate($request, ['code' => 'required']);
        $rule = $this->findPriceRule(app('shopifyapp')->store(), $request->input('code'));

        if ($rule) {
            return ['found' => true];
        }

        return ['found' => false];
    }

    public function delete(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()->route('embedded.my_coupons');
    }

    public function showNew()
    {
        $rules = $this->getRules();
        $rules = $rules->pluck('title');

        return view('edit_coupon', ['coupon' => Coupon::defaultCoupon(), 'discount_codes' => $rules]);
    }

    public function create(Request $request)
    {
        $coupon = new Coupon(
            $request->only(
                [
                    'coupon_token',
                    // Settings
                    'name',
                    'discount_code',
                    // Information
                    'title',
                    'description',
                    'button_text',
                    // Icon Settings
                    'icon_preset',
                    'icon_size',
                    'custom_icon',
                    // Sizing
                    'title_size',
                    'description_size',
                    'button_size',
                    // Fonts
                    'title_font',
                    'description_font',
                    'button_font',
                    'title_font_weight',
                    'description_font_weight',
                    'button_font_weight',
                    // Colors
                    'title_color',
                    'description_color',
                    'button_color',
                    'window_bg_color',
                    'button_bg_color',
                    'success_bar_bg_color',
                    // Radii
                    'window_border_radius',
                ]
            )
        );

//        if ($price_rule_id = $this->attemptFetchRuleIdFromCode(app('shopifyapp')->store(), $coupon->discount_code)) {
//            $coupon->price_rule_id = (string)$price_rule_id;
//        }

        // Temp workaround
        $coupon->price_rule_id = 0;
        $coupon->shopify_store_id = app('shopifyapp')->store()->id;

        // check if file uploaded

        if ($request->hasFile('custom_icon')) {
            $file_loc = $request->file('custom_icon')->storePublicly('/');
            $coupon->custom_icon = $file_loc;
        }

        // A random token
        $coupon->coupon_token = str_random(6);

        $coupon->save();

        return redirect()->route('embedded.my_coupons');
    }

    public function showEdit(Coupon $coupon)
    {
        $rules = $this->getRules();
        $rules = $rules->pluck('title');

        return view('edit_coupon', ['coupon' => $coupon, 'discount_codes' => $rules]);
    }

    public function edit(Request $request, Coupon $coupon)
    {
        $coupon->update(
            $request->only(
                [
                    // Settings
                    'name',
                    'discount_code',
                    // Information
                    'title',
                    'description',
                    'button_text',
                    // Icon Settings
                    'icon_preset',
                    'icon_size',
                    // Sizing
                    'title_size',
                    'description_size',
                    'button_size',
                    // Fonts
                    'title_font',
                    'description_font',
                    'button_font',
                    'title_font_weight',
                    'description_font_weight',
                    'button_font_weight',
                    // Colors
                    'title_color',
                    'description_color',
                    'button_color',
                    'window_bg_color',
                    'button_bg_color',
                    'success_bar_bg_color',
                    // Radii
                    'window_border_radius',
                ]
            )
        );

//        if ($price_rule_id = $this->attemptFetchRuleIdFromCode(app('shopifyapp')->store(), $coupon->discount_code)) {
//            $coupon->price_rule_id = (string)$price_rule_id;
//        }

        // Temp Workaround
        $coupon->price_rule_id = 0;

        $coupon->shopify_store_id = app('shopifyapp')->store()->id;

        if ($request->input('has_custom_icon') == false) {
            // This means the icon was removed
            if ($coupon->custom_icon != null) {
                \Storage::disk('public')->delete($coupon->custom_icon);
            }

            $coupon->custom_icon = null;
        } else {
            // One does exists
            if ($request->hasFile('custom_icon')) {
                if ($coupon->custom_icon != null) {
                    \Storage::disk('public')->delete($coupon->custom_icon);
                }
                // A new one was uploaded
                $file_loc = $request->file('custom_icon')->storePublicly('/');
                $coupon->custom_icon = $file_loc;
            }
        }

        $coupon->save();

        // Clear stale cache
        cache()->delete('coupon:' . $coupon->id);

        return redirect()->route('embedded.my_coupons');
    }

    protected function getRules($count = 250)
    {
        return app('shopify')->get('/admin/price_rules.json', ['limit' => 250]);

        return $all;
    }
}
