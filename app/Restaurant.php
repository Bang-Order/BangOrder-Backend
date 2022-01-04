<?php

namespace App;

use App\Notifications\CustomResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Restaurant extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable;

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $guarded = ['id'];

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }

    public function menus()
    {
        return $this->hasMany('App\Menu')->orderBy('name');
    }

    public function restaurantTables()
    {
        return $this->hasMany('App\RestaurantTable')->orderBy('table_number');
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
