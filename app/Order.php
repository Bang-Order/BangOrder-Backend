<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = ['id'];

    public function restaurantTable()
    {
        return $this->belongsTo('App\RestaurantTable');
    }

    public function orderItems()
    {
        return $this->hasMany('App\OrderItem');
    }
}