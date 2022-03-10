<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(Model\Customer::class, function (Faker $faker) {
    return [
        'cod' => strtoupper(\Illuminate\Support\Str::random(5)),
        'name' => $faker->firstName,
        'surname' => $faker->lastName,
        'address' => $faker->address,
        'points' => random_int(500, 1000),
    ];
});
