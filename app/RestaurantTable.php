<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RestaurantTable extends Model
{
    public $timestamps = false;

    protected $guarded = ['id', 'restaurant_id'];

    public function restaurant()
    {
        return $this->belongsTo('App\Restaurant');
    }

    public function orders()
    {
        return $this->hasMany('App\Order');
    }
}
