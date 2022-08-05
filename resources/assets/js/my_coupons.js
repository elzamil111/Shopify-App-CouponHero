var WebFont = require('webfontloader');
require('./bootstrap')
require('fittextjs');

new Clipboard('.click-to-copy');

$(document).ready(() => {
    $('[data-toggle="tooltip"]').tooltip();

    $('.click-to-copy').click(function () {
        let id = $(this).attr('aria-describedby');

        $('#' + id).find('.tooltip-inner').text('Copied!');
    });

    $('.coupon-name-and-discount-code').fitText(1.8, {maxFontSize: '26px'})

    WebFont.load({
        google: {
            families: window.coupon_fonts
        }
    });
});