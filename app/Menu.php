<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    public $timestamps = false;
    
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function menuCategory()
    {
        return $this->belongsTo(MenuCategory::class);
    }
}
