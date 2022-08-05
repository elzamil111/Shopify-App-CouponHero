<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
</head>
<body id="edit-coupon">
<nav class="navbar navbar-main navbar-static-top navbar-default">
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
            <a class="navbar-brand" href="#">Customize</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <form class="navbar-form navbar-right">
                <a href="{{ route('embedded.my_coupons') }}" class="btn btn-cancel"><i class="fa fa-times"></i>
                    Cancel</a>
            </form>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>
<form id="vue_bind" autocomplete="off" enctype="multipart/form-data"
      @if($coupon->exists) action="{{ route('embedded.edit', $coupon->coupon_token) }}"
      @else action="{{ route('embedded.new') }}" @endif  method="POST" @submit="checkValidation($event)">
    {{ csrf_field() }}
    <input type="hidden" name="has_custom_icon" :value="has_custom_icon">
    <nav class="sub-navbar navbar-static-top navbar">
        <div class="container-fluid">
            <div class="connecting-line">
                <div class="progress-line"></div>
            </div>
            <ul class="nav nav-tabs" role="tablist">

                <li role="presentation" class="active">
                    <a href="#" data-toggle="tab" aria-controls="step1" role="tab" title="Step 1" @click="step = 1">
                            <span class="round-tab">
                                <i class="fa fa-align-center"></i>
                            </span>
                        <span class="tab-title" style="margin-left: -15px;">Content</span>
                    </a>
                </li>

                <li role="presentation" :class="{active: step >= 2}">
                    <a href="#" data-toggle="tab" aria-controls="step2" role="tab"
                       title="Step 2" @click="step = 2">
                            <span class="round-tab">
                                <i class="fa fa-money"></i>
                            </span>
                        <span class="tab-title">Icon</span>
                    </a>
                </li>
                <li role="presentation" :class="{active: step >= 3}">
                    <a href="#" data-toggle="tab" aria-controls="step3" role="tab" title="Step 3" @click="step = 3">
                            <span class="round-tab">
                                <i class="fa fa-font"></i>
                            </span>
                        <span class="tab-title" style="margin-left: -25px;">Typography</span>
                    </a>
                </li>

                <li role="presentation" :class="{active: step == 4}">
                    <a href="#" data-toggle="tab" aria-controls="complete" role="tab" title="Complete"
                       @click="step = 4">
                            <span class="round-tab">
                                <i class="fa fa-paint-brush"></i>
                            </span>
                        <span class="tab-title" style="white-space: nowrap; margin-left: -40px;">Colors & Accents</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container-fluid more-padding additional-bottom-margin">

        <div class="row" v-if="error != ''">
            <div class="alert alert-danger" role="alert" v-text="error">
            </div>
        </div>

        <div class="row">
            <div class="col-md-7 form-panel" v-show="step == 1">
                <div class="row inner-panel">
                    <div class="row">
                        <div class="col-md-4">
                            <h4>Settings</h4>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <input name="name" v-model="name" type="text"
                                       class="form-control input-lg"
                                       placeholder="Coupon Name" @change="clearValidation">
                            </div>
                            <div class="form-group">
                                <label for="discount_code">Discount Code</label>
                                <input name="discount_code" autocomplete="discount_code" class="form-control input-lg"
                                       id="discount_code" value="{{ $coupon->discount_code }}" required
                                       autocomplete="off"/>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            <h4>Information</h4>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <input name="title" v-model.trim="title" type="text" class="form-control input-lg"
                                       placeholder="Main Title" @change="clearValidation">
                            </div>
                            <div class="form-group">
                                <textarea name="description" v-model.trim="description" class="form-control input-lg"
                                          placeholder="Description" @change="clearValidation"></textarea>
                            </div>
                            <div class="form-group">
                                <input name="button_text" v-model.trim="button_text" type="text"
                                       class="form-control input-lg" @change="clearValidation"
                                       placeholder="Button Text">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-7 form-panel" v-show="step == 2">
                <div class="row inner-panel">
                    <div class="row">
                        <div class="col-md-4">
                            <h4>Settings</h4>
                        </div> 
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="icon_preset">Icon Preset</label>
                                <select class="form-control input-lg" name="icon_preset" v-model="icon_preset">
                                    @foreach($coupon->preset_names as $preset => $name)
                                        <option value="{{ $preset }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="icon_size">Icon Size</label>
                                <div class="slider" id="icon_size"></div>
                                <input type="hidden" name="icon_size" :value="icon_size">
                                <h5>@{{ icon_size }}px</h5>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            <h4>Custom Icon</h4>
                        </div>
                        <div class="col-md-8">
                            <input type="file" name="custom_icon" @change="onFileChange" class="custom_icon"
                                   v-show="!has_custom_icon">
                            <button class="btn btn-danger" @click.prevent.self="removeImage" v-show="has_custom_icon">
                                Remove Custom Image
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-7 form-panel" v-show="step == 3">
                <div class="row inner-panel">
                    <div class="row">
                        <div class="col-md-4">
                            <h4>Sizing</h4>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="title_size">Main Title Size</label>
                                <div class="slider" id="title_size"></div>
                                <input type="hidden" name="title_size" :value="title_size">
                                <h5>@{{ title_size }}px</h5>
                            </div>
                            <div class="form-group">
                                <label for="description_size">Description Size</label>
                                <div class="slider" id="description_size"></div>
                                <input type="hidden" name="description_size" :value="description_size">
                                <h5>@{{ description_size }}px</h5>
                            </div>
                            <div class="form-group">
                                <label for="button_size">Button Type Size</label>
                                <div class="slider" id="button_size"></div>
                                <input type="hidden" name="button_size" :value="button_size">
                                <h5>@{{ button_size }}px</h5>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            <h4>Fonts</h4>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="title_font">Main Title Font</label>
                                <select name="title_font" class="form-control input-lg" id="title_font"
                                        v-model="title_font">
                                    <option value="Roboto">Roboto</option>
                                    <option value="Montserrat">Montserrat</option>
                                    <option value="Open Sans">Open Sans</option>
                                    <option value="Macondo">Macondo</option>
                                    <option value="Source Sans Pro">Source Sans Pro</option>
                                    <option value="Lobster">Lobster</option>
                                    <option value="Pacifico">Pacifico</option>
                                    <option value="Bree Serif">Bree Serif</option>
                                    <option value="Baloo Chettan">Baloo Chettan</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="description_font">Description Font</label>
                                <select name="description_font" class="form-control input-lg" id="description_font"
                                        v-model="description_font">
                                    <option value="Roboto">Roboto</option>
                                    <option value="Montserrat">Montserrat</option>
                                    <option value="Open Sans">Open Sans</option>
                                    <option value="Macondo">Macondo</option>
                                    <option value="Source Sans Pro">Source Sans Pro</option>
                                    <option value="Lobster">Lobster</option>
                                    <option value="Pacifico">Pacifico</option>
                                    <option value="Bree Serif">Bree Serif</option>
                                    <option value="Baloo Chettan">Baloo Chettan</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="button_font">Button Font</label>
                                <select name="button_font" class="form-control input-lg" id="button_font"
                                        v-model="button_font">
                                    <option value="Roboto">Roboto</option>
                                    <option value="Montserrat">Montserrat</option>
                                    <option value="Open Sans">Open Sans</option>
                                    <option value="Macondo">Macondo</option>
                                    <option value="Source Sans Pro">Source Sans Pro</option>
                                    <option value="Lobster">Lobster</option>
                                    <option value="Pacifico">Pacifico</option>
                                    <option value="Bree Serif">Bree Serif</option>
                                    <option value="Baloo Chettan">Baloo Chettan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <hr/>
                    <div class="row">
                        <div class="col-md-4">
                            <h4>Fonts Weights</h4>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="title_font_weight">Main Title Font Weight</label>
                                <select name="title_font_weight" class="form-control input-lg" id="title_font_weight"
                                        v-model="title_font_weight">
                                    <option value="400">Regular</option>
                                    <option value="600">Bold</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="description_font_weight">Description Font Weight</label>
                                <select name="description_font_weight" class="form-control input-lg"
                                        id="description_font_weight" v-model="description_font_weight">
                                    <option value="400">Regular</option>
                                    <option value="600">Bold</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="button_font_weight">Button Font Weight</label>
                                <select name="button_font_weight" class="form-control input-lg" id="button_font_weight"
                                        v-model="button_font_weight">
                                    <option value="400">Regular</option>
                                    <option value="600">Bold</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-7 form-panel" v-show="step == 4">
                <div class="row inner-panel">
                    <div class="row">
                        <div class="col-md-4">
                            <h4>Text Colors</h4>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="title_color">Title Color</label>
                                <input type="color" name="title_color" class="form-control" v-model="title_color">
                            </div>
                            <div class="form-group">
                                <label for="description_color">Description Color</label>
                                <input type="color" name="description_color" class="form-control"
                                       v-model="description_color">
                            </div>
                            <div class="form-group">
                                <label for="button_color">Button Text Color</label>
                                <input type="color" name="button_color" class="form-control" v-model="button_color">
                            </div>
                        </div>
                    </div>
                    <hr/>
                    <div class="row">
                        <div class="col-md-4">
                            <h4>Background</h4>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="window_bg_color">Window Background</label>
                                <input type="color" name="window_bg_color" class="form-control"
                                       v-model="window_bg_color">
                            </div>
                            <div class="form-group">
                                <label for="button_bg_color">Button Background</label>
                                <input type="color" name="button_bg_color" class="form-control"
                                       v-model="button_bg_color">
                            </div>
                        </div>
                    </div>
                    <hr/>
                    <div class="row">
                        <div class="col-md-4">
                            <h4>Border Radii</h4>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="window_border_radius">Window Border Radius</label>
                                <div class="slider" id="window_border_radius"></div>
                                <input type="hidden" name="window_border_radius" :value="window_border_radius">
                                <h5>@{{ window_border_radius }}px</h5>
                            </div>
                        </div>
                    </div>
                    <hr/>
                    <div class="row">
                        <div class="col-md-4">
                            <h4>Success Bar</h4>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="success_bar_bg_color">Success Bar Background</label>
                                <input type="color" name="success_bar_bg_color" class="form-control"
                                       v-model="success_bar_bg_color" data-toggle="tooltip" data-placement="left"
                                       title="This is the background color of the success box when the coupon is successfully applied.">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-5 coupon-wrapper">
                <div class="row inner-panel">
                    <div class="coupon"
                         :style="{'border-radius': window_border_radius + 'px', background: window_bg_color}">
                        <img :src="icon_source" alt="Coupon" :width="icon_size" class="coupon-image center-block">
                        <h1 class="coupon-title" v-text="title"
                            :style="{'font-size': title_size + 'px', color: title_color, 'font-family': title_font, 'font-weight': title_font_weight}"></h1>
                        <p class="coupon-desc" v-text="description"
                           :style="{'font-size': description_size + 'px', color: description_color, 'font-family': description_font, 'font-weight': description_font_weight}"></p>
                        <a v-show="button_text != ''" href="#" class="coupon-btn btn" v-text="button_text"
                           :style="{'font-size': button_size + 'px', color: button_color, 'font-family': button_font, 'font-weight': button_font_weight, background: button_bg_color, 'border-radius': window_border_radius + 'px'}"></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <nav class="navbar footer-navbar navbar-fixed-bottom">
        <div class="container-fluid more-padding">
            <div class="navbar-form navbar-left" v-show="step >= 2">
                <button class="btn btn-neutral" @click.prevent.self="step--">
                    Previous Step
                </button>
            </div>
            <div class="navbar-form navbar-right">
                <button class="btn btn-next" @click.prevent.self="step++" v-show="step <= 3">
                    Next Step <i class="fa fa-angle-right"></i>
                </button>

                <button class="btn btn-next" v-show="step == 4">
                    Save <i class="fa fa-check"></i>
                </button>
            </div>
        </div>

    </nav>
</form>

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
    window.discount_codes = {!! json_encode($discount_codes) !!};
    window.coupon = {!! json_encode($coupon) !!};
</script>
<script src="https://cdn.jsdelivr.net/npm/clipboard@1/dist/clipboard.min.js"></script>
<script src="{{ mix('js/app.js') }}"></script>
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
    });
</script>
</body>
</html>
