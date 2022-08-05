// We need jQuery & company
var cookie = require('js-cookie');
var http = require('axios');
var $ = require('jquery/dist/jquery.slim.min');
var WebFont = require('webfontloader');
var tinycolor = require('tinycolor2');

// It seems a script on some stores re-call this script every single time there's
// an ajax page change. This will prevent CH from popping up again
window.couponHeroHasLoaded = window.couponHeroHasLoaded || false;

var couponClosed = false;
var intervalPid = null;

// Lines 9 to 28 are credits to Benjamin De Cock [https://github.com/bendc]
const style = (() => {
    const parseStyles = (() => {
        const isSelector = (() => {
            var re;
            return str => {
                re = re || new RegExp("^\\s{" + str.search(/\S/) + "}\\S");
                return re.test(str);
            };
        })();
        return str =>
            str.split(/\n/).filter(el => el).map((el, i, arr) => {
                if (isSelector(el))
                    return i > 0 ? "}" + el + "{" : el + "{";
                return el.replace(/\b\s/, ":") + (arr[i + 1] ? ";" : "}");
            }).join("");
    })();
    const insertStyles = str =>
        document.head.insertAdjacentHTML("beforeend", "<style>" + str + "</style>");
    return str => insertStyles(parseStyles(str));
})();

let getParam = (name, url) => {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
};

let loadFonts = (fonts) => {
    WebFont.load({
        google: {
            families: fonts.filter((v, i, a) => a.indexOf(v) === i)
        }
    });
};

let printSuccess = (coupon) => {
    let success_bar_template = `
        <div id="success-bar">
            <span>${coupon.success_box_text}</span>
            <div id="success-bar-close">✖</div>
        </div>
    `;
    style(`
        #success-bar
            background-color ${coupon.success_bar_bg_color}
            min-height 50px
            line-height 1.2
            font-size 18px
            position fixed
            bottom 0
            left 0
            margin 0
            vertical-align middle
            padding 15px
            color white
            width 100%
            text-align center
            z-index 999999999999
            
        #success-bar #success-bar-close
            float right
            cursor pointer
    `);
    $('body').append(success_bar_template);
    $('#success-bar-close').one('click', function() {
        $(this).parent().remove();
    });
};

let applyCoupon = coupon => {
    loadFonts([coupon.title_font, coupon.description_font, coupon.button_font]);
    let encoded = encodeURIComponent(coupon.discount_code);
    // Use axios to load the coupon
    http.get(`/discount/${encoded}`); // No error catching
    let tc_bgcolor = tinycolor(coupon.button_bg_color);
    let hover_color = coupon.button_bg_color;

    if (tc_bgcolor.isLight()) {
        hover_color = tc_bgcolor.darken(20).toString();
    }else { // Is Dark
        hover_color = tc_bgcolor.lighten(20).toString();
    }

    let isMobile = window.innerWidth < 450;

    style(`
        #coupon-hero-image
            width ${coupon.icon_size}px
            margin-bottom 20px
            margin-right -14px
            padding-left 15px
            padding-right 15px
        
        #coupon-hero-title
            margin-bottom 15px
            color ${coupon.title_color}
            font-size ${coupon.title_size}px
            font-family "${coupon.title_font}"
            font-weight ${coupon.title_font_weight}
            padding-left 15px
            padding-right 15px
        
        #coupon-hero-description
            color ${coupon.description_color}
            font-family "${coupon.description_font}"
            font-weight ${coupon.description_font_weight}
            font-size ${coupon.description_size}px
            width 100%
            padding-left 15px
            padding-right 15px
            margin 0
        
        #coupon-hero-button
            width 100%
            text-align center
            text-decoration none
            white-space nowrap
            font-family "${coupon.button_font}"
            font-size ${coupon.button_size}px
            font-weight ${coupon.button_font_weight}
            padding-top 10px
            padding-bottom 10px
            line-height 1
            vertical-align middle
            display inline-block
            margin-top 25px
            background ${coupon.button_bg_color}
            color ${coupon.button_color}
            border-radius ${coupon.window_border_radius}px
            border-top-left-radius 0
            border-top-right-radius 0
            -webkit-font-smoothing antialiased
            transition background 300ms
            -webkit-transition background 300ms
        
        #coupon-hero-button:active
            outline 0
            box-shadow inset 0 3px 5px rgba(0,0,0,0.125)
        
        #coupon-hero-button:hover
            background: ${hover_color}
        
        #coupon_hero_backdrop
            z-index 99999999
            position fixed
            width 100%
            height 100%
            background rgba(0,0,0,0.4)
        
        #coupon-hero-x
            font-size 40px
            position absolute
            right 10px
            top -10px
    `);

    let widths;
    if (isMobile) {
        // Mobile
        widths = "margin-left: calc(-50% + 10px); width: calc(100% - 20px);"
    } else {
        // Desktop
        widths = "margin-left: -225px; width: 100%;"
    }

    let coupon_template = `
        <div style="border-radius: ${coupon.window_border_radius}px; background-color: ${coupon.window_bg_color}; text-align: center; z-index: 99999999999999; position: fixed; padding: 30px 0 0 0; max-width: 450px; left: 50%; ${widths}; top: 15%;">
            <a id="coupon-hero-x" href="#">×</a>
            <img id="coupon-hero-image" src="${coupon.icon}" alt="Coupon"/>
            <h1 id="coupon-hero-title">${coupon.title}</h1>
            <p id="coupon-hero-description">${coupon.description}</p>
            <a id="coupon-hero-button" href="#">${coupon.button_text}</a>
        </div>
    `;

    let backdrop_template = `
        <div id="coupon_hero_backdrop"></div>
    `;

    // We're gonna download the image so it doesn't "weird out" the lightbox
    let img = new Image();
    img.onload = function () {
        $('body').prepend(coupon_template);
        $('body').prepend(backdrop_template);
        intervalPid = setInterval(() => {
            // Check if we were "clicked"
            if (couponClosed) {
                clearInterval(intervalPid);
                return;
            }

            $('#coupon-hero-button, #coupon-hero-x').off('click').on('click', function () {
                couponClosed = true;
                $(this).parent().remove();
                $('#coupon_hero_backdrop').remove();
                printSuccess(coupon);

                return false;
            });
        }, 200);
    }

    img.src = coupon.icon;
};

let init = () => {
    if (window.couponHeroHasLoaded) return;
    window.couponHeroHasLoaded = true;

    let param = getParam('ch');

    if (param == null || param == '') {
        if (typeof window.__st !== 'undefined') {
            param = getParam('ch', window.__st.pageurl)
        }
    }

    if (param) {
        // We will get the cart token without relying on the token
        http.get('/cart/update.js')
            .then(response => {
                let cart = response.data.token;
                http.get(`https://couponhero.mpireapps.io/api/get_coupon/${param}/${cart}`)
                    .then(response => applyCoupon(response.data));
            });
        // We won't catch errors, as this means coupon doesn't exist
    }
};


init();
