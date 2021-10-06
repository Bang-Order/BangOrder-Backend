<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Order;
use Faker\Generator as Faker;

$factory->define(Order::class, function (Faker $faker) {
    return [
        'restaurant_id' => rand(1, 3),
        'restaurant_table_id' => rand(1, 10),
        'total_price' => $faker->numberBetween($min = 10000, $max = 60000),
        'transaction_id' => $faker->uuid(),
    ];
});
