<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    public $timestamps = false;
    
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
