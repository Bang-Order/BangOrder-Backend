<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Restaurant;
use Faker\Generator as Faker;

$factory->define(Restaurant::class, function (Faker $faker) {
    return [
        'name' => $faker->company(),
        'email' => $faker->email(),
        'password' => $faker->password(),
        'telephone_number' => $faker->phoneNumber(),
        'address' => $faker->address(),
        'image'=> $faker->imageUrl($width = 640, $height = 480),
    ];
});
