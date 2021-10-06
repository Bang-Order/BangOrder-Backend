<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\RestaurantTable;
use Faker\Generator as Faker;

$factory->define(RestaurantTable::class, function (Faker $faker) {
    return [
        'restaurant_id' => rand(1, 3),
        'table_number' => rand(1, 100),
        'link' => $faker->url(),
    ];
});
