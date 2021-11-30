<?php

use App\Restaurant;
use Illuminate\Database\Seeder;

class BankAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $restaurant = Restaurant::first();
        $restaurant->bankAccount()->create([
            'bank_name' => 'BCA',
            'account_holder_name' => $restaurant->owner_name,
            'account_number' => '1234567890',
        ]);
    }
}
