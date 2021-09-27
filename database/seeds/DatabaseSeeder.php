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
            MenuSeeder::class,
            MenuCategorySeeder::class
        ]);
    }
}
