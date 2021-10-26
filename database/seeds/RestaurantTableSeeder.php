<?php

use App\RestaurantTable;
use Illuminate\Database\Seeder;

class RestaurantTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(RestaurantTable::class, 1)->create();
    }
}
