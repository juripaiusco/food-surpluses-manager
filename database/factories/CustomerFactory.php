<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(Model\Customer::class, function (Faker $faker) {
    return [
        'cod' => strtoupper(\Illuminate\Support\Str::random(5)),
        'number' => \Illuminate\Support\Str::random(5),
        'name' => $faker->firstName,
        'surname' => $faker->lastName,
        'address' => $faker->address,
        'family_number' => random_int(1, 5),
        'points' => random_int(500, 1000),
        'points_renew' => random_int(1000, 2000),
    ];
});
