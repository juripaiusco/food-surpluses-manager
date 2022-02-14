<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(Model\Product::class, function (Faker $faker) {

    $product = new \App\Http\Controllers\Product();
    $type_array = $product->type_array;

    $random_id_type = random_int(0, 1);

    return [
        'cod' => \Illuminate\Support\Str::random(5),
        'name' => $faker->colorName,
        'type' => $type_array[$random_id_type],
        'kg' => $type_array[$random_id_type] == 'fear no' ? NULL : random_int(10, 100),
        'amount' => random_int(1, 20),
    ];
});
