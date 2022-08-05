<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'ExternalAccessController@home');
Route::get('/activate', 'ExternalAccessController@showActivate')->name('activate');
Route::post('/activate', 'ExternalAccessController@activate');

Route::get('/process_shopify_oauth_result', 'SystemController@processOauthResult')
     ->middleware(['set_shop:false', 'shopify'])
     ->name('process_oauth');

Route::prefix('/portal')->name('portal.')->middleware('portal_auth')->group(
    function() {
        Route::post('/create', 'SystemController@createLicense')->name('create');
        Route::post('/delete', 'SystemController@destroyLicense')->name('delete');
        Route::post('/update', 'SystemController@updateLicense')->name('update');
    }
);

Route::prefix('/webhooks')->name('webhooks.')->middleware(['set_shop', 'webhook_shopify'])->group(
    function () {
        // System Webhook
        Route::post('/uninstalled', 'SystemController@onAppUninstalled')->name('uninstalled');

        // Your webhooks
        Route::post('/onCheckoutCreate', 'ApiController@webhookOnCheckoutCreate')->name('checkout_created');
        Route::post('/onCheckoutUpdate', 'ApiController@webhookOnCheckoutUpdate')->name('checkout_updated');
        Route::post('/onOrderPaid', 'ApiController@webhookOnOrderPaid')->name('order_paid');
    }
);

Route::prefix('/embedded')->name('embedded.')->group(
    function() {
        // System Routes
        Route::get('/', 'SystemController@embeddedIndex')->middleware(['set_shop', 'force_auth', 'welcome']);
        Route::post('/reauth', 'SystemController@requiredReAuth');

        Route::middleware('check_auth')->group(function() {
            Route::get('/welcome', 'SystemController@showWelcome')->middleware('check_auth')->name('welcome');
            Route::post('/welcome', 'SystemController@welcome');

            // Your own routes
            Route::get('/my_coupons', 'CouponHeroController@myCoupons')->name('my_coupons');
            Route::get('/my_coupons/new', 'CouponHeroController@showNew')->name('new');
            Route::post('/my_coupons/new', 'CouponHeroController@create');

            Route::get('/my_coupons/{coupon}/delete', 'CouponHeroController@delete')->name('delete');
            Route::get('/my_coupons/{coupon}', 'CouponHeroController@showEdit')->name('edit');
            Route::post('/my_coupons/{coupon}', 'CouponHeroController@edit');

            Route::post('/my_coupons/verify_code', 'CouponHeroController@ensureExists');
        });
    }
);