<?php

use App\MenuCategory;
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
        DB::table('menu_categories')->insert([
            'restaurant_id' => 1,
            'name' => 'Kategori A',
        ]);
        DB::table('menu_categories')->insert([
            'restaurant_id' => 1,
            'name' => 'Kategori B',
        ]);
        DB::table('menu_categories')->insert([
            'restaurant_id' => 1,
            'name' => 'Kategori C',
        ]);
        DB::table('menu_categories')->insert([
            'restaurant_id' => 1,
            'name' => 'Kategori D',
        ]);
        DB::table('menu_categories')->insert([
            'restaurant_id' => 1,
            'name' => 'Kategori E',
        ]);
    }
}
