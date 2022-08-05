<?php

namespace App\Http\Controllers;

use App\Events\StoreCreated;
use App\Events\StoreDeleting;
use App\Http\Requests\PortalCallbacks\Create;
use App\Http\Requests\PortalCallbacks\Delete;
use App\Http\Requests\PortalCallbacks\Update;
use App\Models\License;
use App\Models\ShopifyStore;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    /**
     * Processes the OAuth result from Shopify (Consumes the authorization code)
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processOauthResult(Request $request)
    {
        app('shopify')->setShopUrl($request->query('shop'));
        $token = app('shopify')->getAccessToken($request->get('code'));

        // Save it to the store
        $store = ShopifyStore::where('shop_fqdn', $shop = $request->query('shop'))->first();

        $store->update(['token' => $token]);

        app('shopify')->setAccessToken($token);

        // Now create all required webhooks
        foreach (array_merge(config('shopify.webhooks'), [['app/uninstalled', 'webhooks.uninstalled']]) as $webhook) {
            $topic = $webhook[0];
            $route = route($webhook[1]);

            try {
                // Sometimes, the webhook isn't created because of the insuficient scopes
                app('shopify')->post(
                    'admin/webhooks.json',
                    [
                        'webhook' => [
                            'topic'   => $topic,
                            'address' => $route,
                            'format'  => 'json',
                        ],
                    ]
                );
            } catch (\Exception $e) {
                // We will log the exception
                \Log::error($e);
            }
        }

        // Trigger event
        event(new StoreCreated($store));

        // Redirect to shopify
        return redirect()->to("https://{$shop}/admin/apps/" . config('shopify.key'));
    }

    /**
     * Destroys the Shopify Store
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function onAppUninstalled(Request $request)
    {
        event(new StoreDeleting(app('shopifyapp')->store()));

        \Log::info('SOMEONE DELETED THE APP: ' . $request->getContent());

        // Delete the user
        app('shopifyapp')->store()->delete();

        return response(null, 204);
    }

    /**
     * Processes the welcome page POST (do not show again)
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function welcome(Request $request)
    {
        if ($request->input('do_not_show', false)) {
            // Never show it again
            app('shopifyapp')->store()->update(['has_seen_welcome_screen' => true]);
        }

        return redirect()->route(
            config('shopify.embedded_app_route')
        );
    }

    /**
     * Shows welcome view
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showWelcome()
    {
        session(['has_shown_welcome_video' => true]);

        return view('welcome');
    }

    /**
     * This is mostly a route to execute middleware
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function embeddedIndex()
    {
        return redirect()->route(
            config('shopify.embedded_app_route')
        );
    }

    public function requiredReAuth(Request $request)
    {
        $v = \Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'store' => 'required',
            ]
        );

        $shop = $request->input('store');

        if ($v->fails()) {
            return redirect()->back()->withErrors($v->errors());
        }

        $license = License::where('license', $request->input('email'))->first();

        if ($license == null) {
            return redirect()->back()->withErrors(['email' => 'Invalid email license']);
        }

        if (! $license->canBeUsed()) {
            return redirect()->back()->withErrors(['email' => 'Exceeded usage limit']);
        }

        try {
            ShopifyStore::firstOrCreate(
                [
                    'shop_fqdn'  => $shop,
                    'license_id' => $license->id,
                ]
            );
        } catch (\Exception $e) {
            // This means someone else (another license ID) has the shopify domain (not activated)
            return redirect()->back()->withErrors(
                ['shop_subdomain' => 'This shop cannot be activated at this moment. Contact support']
            );
        }

        $s = app('shopify')->setShopUrl($shop);

        $uri = $s->getAuthorizeUrl(
            config('shopify.oauth_claims'),
            route('process_oauth')
        );

        return response()->view('force_redirect', ['url' => $uri]);
    }

    // Portal API
    public function createLicense(Create $request)
    {
        License::updateOrCreate(
            ['license' => $request->input('license')],
            ['max_uses' => $request->input('active_licenses')]
        );

        return response()->json(['success' => true]);
    }

    public function updateLicense(Update $request)
    {
        $license = License::updateOrCreate(
            ['license' => $request->input('license')],
            ['max_uses' => $request->input('active_licenses')]
        );

        $shop_query = ShopifyStore::whereLicenseId($license->id);

        if ($shop_count = $shop_query->count() > $license->max_uses) {
            if ($license->max_uses == 0) {
                // Suspend all
                $shop_query->update(['active' => false]);
            } else {
                $to_suspend = $shop_count - $license->max_uses;
                $shops = $shop_query->where('active', true)->orderBy('created_at', 'desc')->limit($to_suspend)->get();
                $shops->each->update(['active' => false]);
            }
        } else if ($shop_count = $shop_query->count() < $license->max_uses) {
            // Re-enable shops
            $to_reactivate = $license->max_uses - $shop_count;
            $shops = $shop_query->where('active', false)->orderBy('created_at', 'desc')->limit($to_reactivate)->get();
            $shops->each->update(['active' => true]);
        }

        return response()->json(['success' => true]);
    }

    public function destroyLicense(Delete $request)
    {
        License::whereLicense($request->input('license'))->delete();

        return response()->json(['success' => true]);
    }
}
