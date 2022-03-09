<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(Model\Retail::class, function (Faker $faker) {

    $city = $faker->city;

    return [
        'name' => $city,
        'address' => $faker->streetAddress,
        'zip' => $faker->postcode,
        'city' => $city,
    ];
});
