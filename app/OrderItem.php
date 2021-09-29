<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $guarded = ['id', 'order_id', 'menu_id'];

    public function order()
    {
        return $this->belongsTo('App\Order');
    }

    public function menu()
    {
        return $this->belongsTo('App\Menu');
    }
}
