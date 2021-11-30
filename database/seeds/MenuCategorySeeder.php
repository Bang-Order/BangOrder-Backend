<?php

use App\MenuCategory;
use App\Restaurant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //factory(MenuCategory::class, 10)->create();
        $restaurant = Restaurant::first();
        $restaurant->menuCategories()->create(['name' => 'Kategori A']);
        $restaurant->menuCategories()->create(['name' => 'Kategori B']);
        $restaurant->menuCategories()->create(['name' => 'Kategori C']);
        $restaurant->menuCategories()->create(['name' => 'Kategori D']);
        $restaurant->menuCategories()->create(['name' => 'Kategori E']);
    }
}
