<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = ['id'];

    public function restaurants() {
        return $this->belongsTo('App\Restaurant');
    }

    public function restaurantTable()
    {
        return $this->belongsTo('App\RestaurantTable');
    }

    public function orderItems()
    {
        return $this->hasMany('App\OrderItem');
    }

    public function menus() {
        return $this->belongsToMany(Menu::class, 'order_items')
            ->using('App\OrderItem')
            ->withPivot('quantity', 'notes')
            ->as('order_items');
    }
}
