<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Menu;
use App\MenuCategory;
use Faker\Generator as Faker;

$factory->define(Menu::class, function (Faker $faker) {
    $menu_category = MenuCategory::all()->find(rand(1, 10));
    $menu_category_id = $menu_category->id;
    $restaurant_id = $menu_category->restaurant_id;

    return [
        'restaurant_id' => $restaurant_id,
        'menu_category_id' => $menu_category_id,
        'name' => $faker->name(),
        'description' => $faker->sentence(6),
        'price' => $faker->numberBetween($min = 10000, $max = 60000),
        'image' => 'https://picsum.photos/250?image=' . rand(25, 200),
        'is_recommendation' => $faker->boolean(25)
    ];
});
