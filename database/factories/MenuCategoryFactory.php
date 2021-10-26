<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\MenuCategory;
use Faker\Generator as Faker;

$factory->define(MenuCategory::class, function (Faker $faker) {
    // $faker1 = \Faker\Factory::create();
    // $faker1->addProvider(new \FakerRestaurant\Provider\en_US\Restaurant($faker1));
    return [
        'restaurant_id' => 1,
        'name' => $faker->name(),
    ];
});
