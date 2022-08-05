@if(app()->environment('production'))
<script src="https://cdn.shopify.com/s/assets/external/app.js"></script>
<script>
    ShopifyApp.init({
        apiKey: '{{ config('shopify.key') }}',
        shopOrigin: 'https://{{ app('shopifyapp')->store()->shop_fqdn }}'
    });

    ShopifyApp.ready(function() {
        ShopifyApp.Bar.loadingOff();

        {{ $slot }}
    });
</script>
@endif