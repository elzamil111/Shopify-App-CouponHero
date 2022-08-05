<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActivationRequest;
use App\Models\License;
use App\Models\ShopifyStore;

class ExternalAccessController extends Controller
{
    public function home()
    {
        return redirect()->route('activate');
    }

    public function showActivate()
    {
        return view('activate', ['show_pixel' => ! $this->processFbPixel()]);
    }

    public function activate(ActivationRequest $request)
    {
        // Lowercase store
        $store = strtolower($request->input('shop_subdomain'));
        // First, let's make sure the shopify FQDN doesn't exist, because no rules allow us to do it
        if (ShopifyStore::where('shop_fqdn', $shop = ($store . '.myshopify.com'))
                        ->activated()->exists()
        ) {
            return redirect()->back()->withErrors(
                ['shop_subdomain' => 'This shop cannot be activated at this moment. Contact support']
            );
        }

        $license = License::where('license', $request->input('license'))->first();

        if (! $license) {
            return view('activate', ['show_pixel' => ! $this->processFbPixel()])->withErrors(
                ['license' => 'No such email (license) was found']
            );
        }

        if (! $license->canBeUsed()) {
            return view(
                'activate',
                ['show_pixel' => ! $this->processFbPixel()]
            )->withErrors(['license' => 'This license cannot be used to activate another store: max usage exceeded']);
        }

        // We're good, license can be used, and a shopify store wasn't activated yet
        try {
            $store = ShopifyStore::firstOrCreate(
                [
                    'shop_fqdn' => $shop,
                    'license_id' => $license->id,
                ]
            );
        } catch (\Exception $e) {
            \Log::info($e);

            // This means someone else (another license ID) has the shopify domain (not activated)
            return redirect()->back()->withErrors(
                ['shop_subdomain' => 'This shop cannot be activated at this moment. Contact support']
            );
        }

        // We now try to get the token
        app('shopify')->setShopUrl($shop);

        return redirect()->to(
            app('shopify')->getAuthorizeUrl(
                config('shopify.oauth_claims'),
                route('process_oauth')
            )
        );
    }

    public function processFbPixel()
    {
        // Check session
        $has_seen_pixel = session('has_seen_pixel', false);

        if (! $has_seen_pixel) session(['has_seen_pixel' => true]);

        return $has_seen_pixel;
    }
}
