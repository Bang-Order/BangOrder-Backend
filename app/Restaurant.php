<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];

    public function menus()
    {
        return $this->hasMany('App\Menu');
    }

    public function restaurantTables()
    {
        return $this->hasMany('App\RestaurantTable');
    }

    public function menuCategories()
    {
        return $this->hasMany('App\MenuCategory');
    }
}
