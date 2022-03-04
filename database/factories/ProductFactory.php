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
        'description' => $faker->sentence(40),
        'type' => $type_array[$random_id_type],
        'points' => random_int(1, 20),
        'kg' => $type_array[$random_id_type] == 'fead no' ? NULL : random_int(1, 5),
        'amount' => 1,
    ];
});
