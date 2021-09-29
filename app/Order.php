<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = ['id', 'restaurant_id', 'table_id'];

    public function restaurantTable()
    {
        return $this->belongsTo('App\RestaurantTable');
    }

    public function orderItems()
    {
        return $this->hasMany('App\OrderItem');
    }
}
