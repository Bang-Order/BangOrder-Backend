<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Order;
use Faker\Generator as Faker;

$factory->define(Order::class, function (Faker $faker) {
    return [
        'restaurant_id' => 1,
        'restaurant_table_id' => 1,
        'total_price' => $faker->numberBetween(10000, 60000),
        'transaction_id' => $faker->uuid(),
    ];
});
