<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Coin extends Model
{
    protected $table = 'coins';
    public $timestamps = false;
    protected $fillable = ['name'];
    
    public function prices()
    {
        return $this->hasMany('App\Price');
    }
    
    public function currency_pairs()
    {
        return $this->hasMany('App\CurrencyPair');
    }
}
