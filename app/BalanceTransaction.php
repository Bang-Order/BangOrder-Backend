<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BalanceTransaction extends Model
{
    protected $guarded = ['id'];

    public function restaurant() {
        return $this->belongsTo('App\Restaurant');
    }
}
