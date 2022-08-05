<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'coupons', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('shopify_store_id')->unsigned()->index();
            $table->string('coupon_token')->unique();
            // Settings
            $table->string('name');
            $table->string('discount_code');
            // Information
            $table->string('title');
            $table->text('description');
            $table->string('button_text');
            // Icon Settings
            $table->integer('icon_preset');
            $table->integer('icon_size');
            $table->string('custom_icon')->nullable();
            // Sizing
            $table->integer('title_size');
            $table->integer('description_size');
            $table->integer('button_size');
            // Fonts
            $table->string('title_font');
            $table->string('description_font');
            $table->string('button_font');
            $table->integer('title_font_weight');
            $table->integer('description_font_weight');
            $table->integer('button_font_weight');
            // Colors
            $table->string('title_color');
            $table->string('description_color');
            $table->string('button_color');
            $table->string('window_bg_color');
            $table->string('button_bg_color');
            // Radii
            $table->integer('window_border_radius');
            $table->integer('button_border_radius');

            $table->foreign('shopify_store_id')->references('id')->on('shopify_stores')->onDelete('cascade');

            $table->timestamps();
        }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupons');
    }
}
