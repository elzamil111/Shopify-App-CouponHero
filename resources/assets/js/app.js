/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
var WebFont = require('webfontloader');


// Pre-load
WebFont.load({
    google: {
        families: [window.coupon.title_font, window.coupon.description_font, window.coupon.button_font].filter((v, i, a) => a.indexOf(v) === i)
    }
});

$('.connecting-line').css(
    'width',
    $('.round-tab:last').offset().left - $('.round-tab:first').offset().left
);

setTimeout(() => {
    $('.coupon-wrapper').affix();

    $('.coupon-wrapper').on('affix.bs.affix', () => {
        if( !$( document ).scrollTop() ) return false;
        $('.coupon-wrapper').css('left', $('.coupon-wrapper').position().left - 27);
    });

    $('.coupon-wrapper').on('affixed-top.bs.affix', () => {
        $('.coupon-wrapper').css('left', 0);
    });
}, 2000);

var Vue = require('vue');
require('bootstrap-3-typeahead')
require('jquery-ui/ui/core');
require('jquery-ui/ui/widget');
require('jquery-ui/ui/widgets/slider');

var app = new Vue({
    el: '#vue_bind',
    data: $.extend(window.coupon, {
        step: 1,
        custom_icon_string: null,
        error: ''
    }),
    watch: {
        step() {
            $('.progress-line').css(
                'width',
                (((this.step - 1) / 3) * 100) + '%'
            )
        },
        title_font() {
            this.loadSingleFont(this.title_font);
        },
        description_font() {
            this.loadSingleFont(this.description_font);
        },
        button_font() {
            this.loadSingleFont(this.button_font);
        }
    },
    methods: {
        onFileChange(e) {
            var files = e.target.files || e.dataTransfer.files;
            if (!files.length)
                return;
            this.createImage(files[0]);
        },
        createImage(file) {
            var image = new Image();
            var reader = new FileReader();
            var vm = this;

            reader.onload = (e) => {
                vm.custom_icon_string = e.target.result;
                vm.has_custom_icon = true;
            };
            reader.readAsDataURL(file);
        },
        removeImage: function (e) {
            this.custom_icon_string = null;
            this.has_custom_icon = false;

            $('input[name=custom_icon]').val('');
        },
        loadSingleFont(f) {
            WebFont.load({
                google: {
                    families: [f]
                }
            });
        },
        checkValidation(e) {
            this.error = '';

            if (this.name.trim() == '') {
                this.error = 'Enter a coupon name';
                event.preventDefault();
            }

            if (this.title.trim() == '') {
                this.error = 'Enter a coupon title';
                event.preventDefault();
            }

            if (this.description.trim() == '') {
                this.error = 'Enter a description';
                event.preventDefault();
            }

            if (this.button_text.trim() == '') {
                this.error = 'Enter a button text';
                event.preventDefault();
            }

            if ($('#discount_code').val().trim() == '') {
                this.error = 'Enter a discount code';
                event.preventDefault();
            }

            return true;
        },
        clearValidation() {
            this.error = '';
        }
    },
    computed: {
        icon_source() {
            if (this.has_custom_icon) {
                if (this.custom_icon_string != null) return this.custom_icon_string;
                return this.icon;
            }

            return `https://couponhero.mpireapps.io/images/${this.icon_preset}.png`
        }
    }
});


$(document).ready(() => {
    new Clipboard('.click-to-copy');

    $('#discount_code').typeahead({source: window.discount_codes, showHintOnFocus: true, items: 'all', fitToElement: true}, 'json');

    $('#icon_size').slider({
        range: "min",
        value: window.coupon.icon_size,
        min: 100,
        max: 420,
        slide: function (event, ui) {
            app.icon_size = ui.value;
        }
    });
    $('#title_size').slider({
        range: "min",
        value: window.coupon.title_size,
        min: 6,
        max: 48,
        slide: function (event, ui) {
            app.title_size = ui.value;
        }
    });
    $('#description_size').slider({
        range: "min",
        value: window.coupon.description_size,
        min: 6,
        max: 36,
        slide: function (event, ui) {
            app.description_size = ui.value;
        }
    });
    $('#button_size').slider({
        range: "min",
        value: window.coupon.button_size,
        min: 6,
        max: 24,
        slide: function (event, ui) {
            app.button_size = ui.value;
        }
    });
    $('#window_border_radius').slider({
        range: "min",
        value: window.coupon.window_border_radius,
        min: 0,
        max: 25,
        slide: function (event, ui) {
            app.window_border_radius = ui.value;
        }
    });
    $('#button_border_radius').slider({
        range: "min",
        value: window.coupon.button_border_radius,
        min: 0,
        max: 25,
        slide: function (event, ui) {
            app.button_border_radius = ui.value;
        }
    });

    $('[data-toggle="tooltip"]').tooltip();
})