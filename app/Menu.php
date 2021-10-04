<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $guarded = ['id'];

    public function restaurant()
    {
        return $this->belongsTo('App\Restaurant');
    }

    public function menuCategory()
    {
        return $this->belongsTo('App\MenuCategory');
    }

    public function orderItems()
    {
        return $this->hasMany('App\OrderItem');
    }
}
