<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(Model\Store::class, function (Faker $faker) {

    $product = new \App\Http\Controllers\Product();
    $type_array = $product->type_array;

    $random_id_type = random_int(0, 1);

    return [
        'cod' => \Illuminate\Support\Str::random(5),
        'kg' => $type_array[$random_id_type] == 'fead no' ? NULL : random_int(10, 100),
        'amount' => random_int(1, 20),
        'date' => $faker->dateTimeBetween('-30 days', 'now')
    ];
});
