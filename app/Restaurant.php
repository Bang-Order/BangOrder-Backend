<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Restaurant extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $guarded = ['id'];

    public function getPassword()
    {
        return $this->password;
    }

    public function menus()
    {
        return $this->hasMany('App\Menu')->orderBy('name');
    }

    public function restaurantTables()
    {
        return $this->hasMany('App\RestaurantTable');
    }

    public function menuCategories()
    {
        return $this->hasMany('App\MenuCategory')->orderBy('name');
    }

    public function orders() {
        return $this->hasMany('App\Order')->latest();
    }

    public function bankAccount() {
        return $this->hasOne('App\BankAccount');
    }

    public function balanceTransactions() {
        return $this->hasMany('App\BalanceTransaction')->latest();
    }
}
