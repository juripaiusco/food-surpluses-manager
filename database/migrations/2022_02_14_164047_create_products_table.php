<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('cod');
            $table->string('type');
            $table->string('name');
            $table->longText('description')->nullable();
            $table->integer('points');
            $table->float('kg')->nullable();
            $table->float('amount')->nullable();
            $table->float('kg_total')->nullable();
            $table->float('amount_total')->nullable();
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
        Schema::dropIfExists('products');
    }
}
