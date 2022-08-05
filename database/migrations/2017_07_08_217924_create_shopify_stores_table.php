<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopifyStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shopify_stores',
            function (Blueprint $table) {
                $table->increments('id');
                $table->string('shop_fqdn')->unique();
                $table->string('token')->nullable();
                $table->boolean('has_seen_welcome_screen')->default(false);
                $table->integer('license_id')->unsigned();
                $table->boolean('active')->default(true);
                $table->timestamps();

                $table->foreign('license_id')->references('id')->on('licenses')->onDelete('cascade');
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
        Schema::dropIfExists('shopify_stores');
    }
}
