<?php

use App\MenuCategory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RestaurantSeeder::class,
            MenuCategorySeeder::class,
            MenuSeeder::class,
            RestaurantTableSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
