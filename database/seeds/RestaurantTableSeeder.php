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
            'link' => 'http://localhost:8000/storage/id_1/qr_code/qr_id_1.jpg'
        ]);
    }
}
