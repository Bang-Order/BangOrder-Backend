<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RestaurantTable extends Model
{
    public $timestamps = false;
    
    public function restaurant()
    {
        return $this->belongsTo(Order::class);
    }
}
