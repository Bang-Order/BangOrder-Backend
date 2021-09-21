<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MenuCategory extends Model
{
    public $timestamps = false;
    
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }
}
