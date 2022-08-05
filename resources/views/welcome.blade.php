<!DOCTYPE html>
<html lang="en" style="position: relative; min-height: 100%;">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
</head>
<body style="margin-bottom: 60px;">
<div class="container-fluid" id="welcome">
    <div class="row welcome-top-logo">
        <div class="col-md-6 col-md-offset-3">
            <img src="{{ asset('/images/logo.png') }}" class="img-responsive center-block">
        </div>
    </div> 
    <div class="row blue-outset">
        <div class="col-md-12">
            <h1>Welcome to Coupon Hero</h1>
            <h2>Please Watch The Training Video Below</h2>
        </div>
    </div>
    <div class="row video-negative-margin">
        <div class="col-md-6 col-lg-6 col-sm-6 col-xs-8 col-md-offset-3 col-lg-offset-3 col-sm-offset-3 col-xs-offset-2">
            <div class="embed-responsive embed-responsive-16by9">
                <iframe class="embed-responsive-item" src="{{ config('shopify.welcome_screen_video') }}"
                        frameborder="0"
                        allowfullscreen></iframe>
            </div>
        </div>
    </div>
    <div class="row button-new-coupon">
        <div class="col-md-12">
            <a href="{{ route('embedded.new') }}" class="new-coupon"><i class="fa fa-plus"></i> Create My First
                Coupon</a>
        </div>
    </div>
</div>

<div class="footer">
    <div class="container" style="text-align: center;">
        <p>© 2017 Mpire Labs, Inc.</p>
        <small>To cancel your Coupon Hero subscription, contact support</small>
    </div>
</div>

@component('components.shopify_sdk')
    ShopifyApp.Bar.initialize({
    buttons: {
    secondary: [
    {label: "Training", href: "/embedded/welcome", target: "app"},
    {label: "Support", message: "support", callback: function() {
    window.open("mailto:{{ config('shopify.support_email') }}", '_blank'); }},
    {label: "More Apps", href: "https://mpireapps.io", target: "new"}
    ],
    primary: {label: "Home", href: "/embedded/my_coupons", target: "app"}
    }
    });
@endcomponent
<script>
    !function (f, b, e, v, n, t, s) {
        if (f.fbq) return;
        n = f.fbq = function () {
            n.callMethod ?
                n.callMethod.apply(n, arguments) : n.queue.push(arguments)
        };
        if (!f._fbq) f._fbq = n;
        n.push = n;
        n.loaded = !0;
        n.version = '2.0';
        n.queue = [];
        t = b.createElement(e);
        t.async = !0;
        t.src = v;
        s = b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t, s)
    }(window,
        document, 'script', '//connect.facebook.net/en_US/fbevents.js');
    fbq('init', '884057485044165');
    fbq('track', "PageView");
    fbq('trackCustom', "Customer", {
        currency: 'USD',
        value: 15.00,
        content_name: ['couponhero', 'shopifyapp'],
        content_category: 'shopify',
        content_ids: 'in-app',
        product_group: 'app',
        content_type: 'product_group'
    });</script>
</body>
</html>