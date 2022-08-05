<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
</head>
<body id="my-coupons">
<nav class="navbar navbar-main navbar-default">
    <div class="container-fluid more-padding">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">My Coupons</a>
        </div>
 
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <form class="navbar-form navbar-right">
                <a href="{{ route('embedded.new') }}" class="btn btn-new"><i class="fa fa-plus"></i> New Coupon</a>
            </form>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>

<div class="container-fluid more-padding">
    <div class="row">
        @foreach($coupons as $coupon)
            <div class="col-md-6 col-sm-6 col-lg-4">
                <div class="panel coupon-panel">
                    <div class="panel-coupon-header">
                        <div class="coupon"
                             style="border-radius: {{ $coupon->window_border_radius }}px; background-color: {{ $coupon->window_bg_color }}">
                            <div class="coupon-illustration">
                                <img src="{{ $coupon->icon }}" alt="Coupon Illustration"
                                     style="max-width: 250px; max-height: 130px;">
                            </div>
                            <h1 class="coupon-name"
                                style="color: {{ $coupon->title_color }}; font-family: '{{ $coupon->title_font }}'; font-weight: {{ $coupon->title_font_weight }}">{{ $coupon->title }}</h1>
                            <p class="coupon-text"
                               style="color: {{ $coupon->description_color }}; font-family: '{{ $coupon->description_font }}'; font-weight: {{ $coupon->description_font_weight }}">{{ $coupon->description }}</p>
                            <div class="form-group"><a href="javascript:void(0)"
                                                       style="font-weight: {{ $coupon->button_font_weight }}; font-family: '{{ $coupon->button_font }}'; background: {{ $coupon->button_bg_color }}; color: {{ $coupon->button_color }}; border-radius: {{ $coupon->window_border_radius }}px"
                                                       class="coupon-link btn">{{ $coupon->button_text }}</a></div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <h2 class="coupon-name-git and-discount-code">{{ $coupon->name }}
                                    - {{ $coupon->discount_code }}</h2>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8 col-sm-6">
                                <div class="row"><span class="detail">URL Token</span></div>
                                <div class="row"><span class="value click-to-copy"
                                                       data-clipboard-text="?ch={{ $coupon->coupon_token }}"
                                                       data-toggle="tooltip" data-placement="right"
                                                       title="Click to Copy">?ch={{ $coupon->coupon_token }}</span>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="row"><span class="detail">Revenue</span></div>
                                <div class="row"><span
                                            class="value">${{ $coupon->orders()->sum('total_price') }}</span></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8 col-sm-6">
                                <div class="row"><span class="detail">Impressions</span></div>
                                <div class="row"><span
                                            class="value">{{ $coupon->impressions()->count() }}</span>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="row"><span class="detail">Uses</span></div>
                                <div class="row"><span class="value">{{ $coupon->orders()->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <div class="row coupon-actions">
                            <div class="col-xl-5 col-lg-6 col-md-6 col-sm-5 col-xs-6 action-delete"><i
                                        class="fa fa-times"></i> <a
                                        href="{{ route('embedded.delete', $coupon->coupon_token) }}">Delete</a>
                            </div>
                            <div class="col-xl-7 col-lg-6 col-md-6 col-sm-7 col-xs-6 action-customize"><i
                                        class="fa fa-paint-brush"></i> <a
                                        href="{{ route('embedded.edit', $coupon->coupon_token) }}">Customize</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="container-fluid footer" style="margin-bottom: 20px;">
    <hr style="border-top: 1px solid #B4BCC0;"/>
    <div class="row" style="text-align: center; color: #9BA3A7;">
        <p>&copy; 2017 Mpire Labs, Inc.</p>
        <small>To cancel your {{ config('app.name') }} subscription, contact support</small>
    </div>
</div>

<script>
    // We will load all the fonts
    window.coupon_fonts = {!! $fonts !!};
</script>

<script src="https://cdn.jsdelivr.net/npm/clipboard@1/dist/clipboard.min.js"></script>
<script src="{{ mix('/js/my_coupons.js') }}"></script>

@component('components.shopify_sdk')
    ShopifyApp.Bar.initialize({
        buttons: {
            secondary: [
                {label: "Training", href: "/embedded/welcome", target: "app"},
                {label: "Support", message: "support", callback: function() { window.open("mailto:{{ config('shopify.support_email') }}", '_blank'); }},
                {label: "More Apps", href: "https://mpireapps.io", target: "new"}
            ],
            primary: {label: "Home", href: "/embedded/my_coupons", target: "app"}
        }
    });
@endcomponent
<script>
    !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
        n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
        document,'script','//connect.facebook.net/en_US/fbevents.js');
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
    });
</script>
</body>
</html>