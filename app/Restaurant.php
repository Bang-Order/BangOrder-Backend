<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Restaurant extends Authenticatable
{
    use Notifiable;

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $guarded = ['id'];

    public function menus()
    {
        return $this->hasMany('App\Menu');
    }

    public function restaurantTables()
    {
        return $this->hasMany('App\RestaurantTable');
    }

    public function menuCategories()
    {
        return $this->hasMany('App\MenuCategory');
    }
}
