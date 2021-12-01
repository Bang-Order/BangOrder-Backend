<?php

use App\Restaurant;
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
        $restaurant = Restaurant::first();
        $restaurant->restaurantTables()->create([
            'restaurant_id' => 1,
            'table_number' => '1',
            'link' => 'https://firebasestorage.googleapis.com/v0/b/bangorder-db7d2.appspot.com/o/id_1%2Fqr_code%2Fqr_id_1.jpg?alt=media'
        ]);
    }
}
