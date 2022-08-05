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
            <h1>Activate free update</h1>
            <h2 style="line-height: 40px; font-size: 20px;">
                We Have Made Some Exciting Updates To CouponHero!<br/>
                Please Enter Your Purchase Email To Get Access Now...<br/>
                (You are not being billed anything; This is a completely free performance upgrade!)
            </h2>
        </div>
    </div>

    <div class="row" style="margin-top: 60px;">
        <div class="col-md-6 col-lg-6 col-sm-6 col-xs-8 col-xl-offset-4 col-md-offset-3 col-lg-offset-3 col-sm-offset-3 col-xs-offset-2">
            <div class="panel panel-default center-block" style="max-width: 600px">
                <div class="panel-heading">
                    <h3 class="panel-title">Log In</h3>
                </div>
                <div class="panel-body">
                    <form method="POST" action="/embedded/reauth">
                        {{ csrf_field() }}
                        <input name="store" type="hidden" value="{{ $store }}">
                        <div class="form-group">
                            <label for="email">Purchase Email Address (AccessAdrian.com)</label>
                            <input type="email" name="email" class="form-control" id="email" placeholder="Email">
                        </div>
                        <input type="submit" value="Log In" class="btn btn-primary pull-right">
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="footer">
    <div class="container" style="text-align: center;">
        <p>© 2017 Mpire Labs, Inc.</p>
        <small>To cancel your Coupon Hero subscription, contact support</small>
    </div>
</div>
</body>
</html>