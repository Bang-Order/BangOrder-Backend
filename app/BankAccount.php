<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $primaryKey = 'restaurant_id';
    public $incrementing = false;

    protected $guarded = ['restaurant_id'];

    const ADMIN_CHARGE = 5500;

    public function restaurant() {
        return $this->belongsTo('App\Restaurant');
    }
}
