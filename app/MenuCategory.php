<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MenuCategory extends Model
{
    protected $guarded = ['id'];

    public function restaurant()
    {
        // return $this->belongsTo(Restaurant::class);
        return $this->belongsTo('App\MenuCategory');
    }

    public function menus()
    {
        return $this->hasMany('App\Menu');
    }
}
