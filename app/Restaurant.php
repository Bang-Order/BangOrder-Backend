<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    public $timestamps = false;
    
    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    public function restaurantTables()
    {
        return $this->hasMany(RestaurantTable::class);
    }

    public function menuCategories()
    {
        return $this->hasMany(MenuCategory::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
