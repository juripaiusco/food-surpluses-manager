<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('cod')->unique();
            $table->string('number')->unique();
            $table->string('name');
            $table->string('surname');
            $table->string('address')->nullable();
            $table->integer('family_number');
            $table->integer('points');
            $table->integer('points_renew');
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
        Schema::dropIfExists('customers');
        /*Schema::table('customers', function (Blueprint $table) {
            //
        });*/
    }
}
