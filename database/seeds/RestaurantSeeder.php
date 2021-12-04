<?php

use App\Restaurant;
use Illuminate\Database\Seeder;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Restaurant::class, 1)->create();
        Restaurant::find(1)->markEmailAsVerified();
    }
}
