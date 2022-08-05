<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name') }}</title>
    <meta property="og:title" content="Coupon Hero">
    <meta property="og:description" content="Your Account Has Been Created Successfully!">
    <meta property="og:url" content="{{ route('activate') }}">
    <meta property="og:type" content="website">

    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
    <script>
        !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
        n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
        document,'script','//connect.facebook.net/en_US/fbevents.js');
        fbq('init', '884057485044165');
        fbq('track', "PageView");
        
        @if($show_pixel)
            fbq('track', 'Purchase', {
                content_name: 'couponhero',
                content_category: 'shopify',
                content_ids: 'activation',
                product_group: 'app',
                content_type: 'product_group',
                value: 15.00,
                currency: 'USD'
            });
        @endif
    </script>
    <noscript><img height="1" width="1" style="display: none !important;" src="https://www.facebook.com/tr?id=884057485044165&amp;ev=PageView&amp;noscript=1" hidden=""></noscript>
    @unless(Session::has('purchased_fired'))
    <noscript><img height="1" width="1" style="display: none !important;" src="https://www.facebook.com/tr?id=884057485044165&amp;ev=Purchase&amp;noscript=1" hidden=""></noscript>
    @endunless
</head>
<body id="activate">
<div class="container-fluid">
    <div class="row top-logo-wrapper">
        <div class="col-md-6 col-md-offset-3">
            <img src="{{ asset('/images/logo.png') }}" class="img-responsive center-block">
        </div>
    </div>
    <div class="container">
        <div class="row content-announcement">
            <div class="col-md-12 text-center">
                <h1>THANK YOU FOR CREATING YOUR {{ strtoupper(config('app.name')) }} ACCOUNT!</h1>
                <h2>PLEASE <b>WATCH THE VIDEO BELOW</b> <u>BEFORE YOU CONTINUE & INSTALL!</u></h2>
                <h3><b>To activate and install the app to your shopify store,</b> <br>
                    please enter your license key and store url</h3>
            </div>
        </div>
        <div class="row">
            @if($errors->any())
                <div class="alert alert-danger" role="alert">
                    <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                    <span class="sr-only">Error:</span>

                    {{ $errors->first() }}

                </div>
            @endif
        </div>
        <div class="row tutorial-form">
            <div class="col-md-6">
                <div class="embed-responsive embed-responsive-4by3">
                    <iframe class="embed-responsive-item" src="{{ config('shopify.activate_screen_video') }}"
                            frameborder="0"
                            allowfullscreen></iframe>
                </div>
            </div>
            <div class="col-md-6 activation-form">
                <form action="{{ route('activate') }}" method="POST"> 
                    {{ csrf_field() }}
                    @captcha
                    <div class="row">
                        <div class="form-group">
                            <label for="license">Purchase Email Address:</label>
                            <input type="email" class="form-control input-lg" name="license"
                                   placeholder="Email Address">
                        </div>
                        <div class="form-group">
                            <label for="shop_subdomain">Shopify Store URL:</label>
                            <div class="input-group">
                                <input type="text" class="form-control input-lg" name="shop_subdomain"
                                       placeholder="My Store Subdomain">
                                <span class="input-group-addon" id="basic-addon3">.myshopify.com</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <button class="btn btn-activate btn-block"><i class="fa fa-bolt"></i> Activate Now</button>
                    </div>
                </form>
            </div>
        </div>
        <hr>

        <div class="row">
            <div class="col-md-12 text-center">
                <h3>PLEASE <b>CHECK YOUR EMAIL IMMEDIATELY</b> FOR 2 EMAILS FROM {{ config('shopify.support_email') }}
                </h3>
                <h4><b>EMAIL 1:</b> Will have your full order details and documentation for your records.</h4>
                <h4><b>EMAIL 2:</b> Will be titled "Welcome To {{ config('app.name') }} (Account Details Inside)"<br>
                    and will contain your login information.</h4>
            </div>
        </div>

        <div class="row">
            <div class="panel panel-activate">
                <div class="panel-heading"><b>ATTENTION:</b> IF YOU DO NOT GET YOUR LOGIN DETAILS EMAIL</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p>Please note that occasionally emails get lost in cyber space or unintentionally hit the
                                dreaded "spam folder".</p>
                            <p>For this reason if you do not get your login details from us here are a couple of options
                                to get you going quickly:</p>
                            <p><b>Option #1</b> - Email us at {{ config('shopify.support_email') }} and we will response as quickly as
                                possible.</p>
                            <p><b>Option #2</b> (Faster) - Simply go to http://accessadrian.com and click on "forgot
                                password" and enter your email address in the box, It will immediately send you a new
                                password via email.</p>
                            <p>We hate when little things happen that cause you any delays in getting started ! That's
                                why we wanted to put this information right here for you!</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 adrian-avatar">
                            <img src="{{ asset('/images/adrian.png') }}" alt="Adrian Morrison" width="100"
                                 class="pull-left"/>
                            <p><i>To Your Success</i></p>
                            <p>- Adrian Morrison</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row black-border"></div>
    <div class="row text-center footer">
        <p>This site is not a part of the Facebook website or Facebook Inc.</p>
        <p>Additionally, This site is NOT endorsed by Facebook in any way.</p>
        <p>FACEBOOK is a trademark of FACEBOOK, Inc.</p><br/>
        <p>Launch Wise, LLC. - Copyright 2017 © - All Rights Reserved.</p><br/>
        <p><a href="http://launchwise.io/dmca/">DMCA Notice</a> | <a
                    href="http://launchwise.io/anti-spam/">Anti-Spam</a> | <a href="http://launchwise.io/terms-of-use/">Terms
                of Use</a> | <a href="http://launchwise.io/privacy-policy/">Privacy Policy</a> | <a
                    href="http://launchwise.io/refund-policy/">Refund Policy</a> | <a href="http://adrianmorrison.com/">Adrian
                Morrison Official Site</a></p><br/>

    </div>
</div>
</body>
</html>