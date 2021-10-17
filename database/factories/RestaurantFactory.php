<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Restaurant;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\Hash;

$factory->define(Restaurant::class, function (Faker $faker) {
    return [
        'name' => $faker->company(),
        'email' => $faker->email(),
        //'password' => $faker->password(),
        'password' => Hash::make('123456'),
        'telephone_number' => $faker->phoneNumber(),
        'address' => $faker->address(),
        'image'=> $faker->imageUrl($width = 640, $height = 480),
    ];
});
