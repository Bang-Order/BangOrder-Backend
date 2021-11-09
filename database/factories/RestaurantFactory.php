<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Restaurant;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\Hash;

$factory->define(Restaurant::class, function (Faker $faker) {
    return [
//        'name' => $faker->company(),
//        'email' => $faker->email(),
//        'password' => $faker->password(),
        'name' => 'Resto ABC',
        'email' => 'resto_abc@gmail.com',
        'password' => Hash::make('123456'),
        'address' => $faker->address(),
        //'image'=> $faker->imageUrl(500, 500, 'city'),
        'owner_name' => $faker->name,
        'telephone_number' => $faker->phoneNumber(),
    ];
});
