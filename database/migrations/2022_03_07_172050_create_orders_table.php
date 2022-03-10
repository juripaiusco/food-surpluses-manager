<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->char('reference', 5)->nullable();
            $table->integer('user_id')->nullable()->index();
            $table->integer('customer_id')->nullable()->index();
            $table->integer('retail_id')->nullable()->index();
            $table->float('points')->nullable();
            $table->longText('json_customer')->nullable();
            $table->longText('json_products')->nullable();
            $table->timestamp('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
