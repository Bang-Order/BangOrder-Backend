<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Order;
use App\OrderItem;
use Faker\Generator as Faker;

$factory->define(OrderItem::class, function (Faker $faker) {
    return [
        'order_id' => rand(1, 10),
        'menu_id' => rand(1, 50),
        'quantity' => $faker->numberBetween($min = 1, $max = 10),
        'notes' => (rand(1, 2) == 1) ? '' : $faker->sentence(6)
    ];
});
