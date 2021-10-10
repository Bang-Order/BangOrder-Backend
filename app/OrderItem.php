<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class OrderItem extends Pivot
{
    protected $table = 'order_items';

    public function order() {
        return $this->belongsTo('App\Order');
    }

    public function menu() {
        return $this->belongsTo('App\Menu');
    }
}
