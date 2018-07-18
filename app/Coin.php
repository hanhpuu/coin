<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Validator;

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
    
		public static function checkCoinValidation($request)
	{
		$validator = Validator::make($request->all(), [
					'name' => 'required|unique:coins|max:5',
		]);
		return $validator;
	}
}
