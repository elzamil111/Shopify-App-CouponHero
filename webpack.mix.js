let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/assets/js/app.js', 'public/js')
    .sass('resources/assets/sass/app.scss', 'public/css')
    .copy('resources/assets/images', 'public/images')
    .js('resources/assets/js/my_coupons.js', 'public/js');

// For the coupon hero frontend
mix.js('resources/assets/js/coupon_hero.js', 'public');

if (mix.inProduction()) {
    mix.version();
}

mix.disableNotifications();