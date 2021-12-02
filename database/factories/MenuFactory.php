<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Menu;
use App\MenuCategory;
use Faker\Factory;
use Faker\Generator as Faker;
use FakerRestaurant\Provider\id_ID\Restaurant;

$factory->define(Menu::class, function (Faker $faker) {
    $menu_category = MenuCategory::find(rand(1, 5));
    $menu_category_id = $menu_category->id;
    $restaurant_id = $menu_category->restaurant_id;
    $foodFaker = Factory::create();
    $foodFaker->addProvider(new Restaurant($faker));

    return [
        'restaurant_id' => $restaurant_id,
        'menu_category_id' => $menu_category_id,
        'name' => $foodFaker->foodName(),
        'description' => $faker->sentence(),
        'price' => $faker->numberBetween(10000, 60000),
//        'image'=> $faker->imageUrl(500, 500, 'food'),
        'image' => 'https://picsum.photos/250?image=' . rand(25, 200),
        'is_available' => $faker->boolean(),
        'is_recommended' => $faker->boolean(10)
    ];
});
