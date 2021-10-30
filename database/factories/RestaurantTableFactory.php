<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\RestaurantTable;
use Faker\Generator as Faker;

$factory->define(RestaurantTable::class, function (Faker $faker) {
    return [
        'restaurant_id' => 1,
        'table_number' => '1',
        //'link' => $faker->url(),
        'link' => 'http://localhost:8000/storage/id_1/qr_code/qr_id_1.jpg'
    ];
});
