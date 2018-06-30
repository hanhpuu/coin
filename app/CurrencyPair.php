<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Validator;
use App\Coin;
use DB;

class CurrencyPair extends Model {

    public $table = 'currency_pair';
    public $timestamps = false;
    protected $fillable = ['name'];

    public function prices() {
        return $this->hasMany('App\Price');
    }

    public function coins() {
        return $this->hasMany('App\Coin');
    }

    public static function addPairNameByAPI($request) {
        $validator = Validator::make($request->all(), [
                    'base_currency_name' => 'required|max:5',
                    'quote_currency_name' => 'required|max:5',
        ]);

        if ($validator->fails()) {
            $error = 'Please enter all coins name';
            throw new \Exception($error, 406);
        }

        $coins_in_pair = self::checkCoinExistsOrCreate($request);
        
        //insert currency pair into table
        $pair_name = $coins_in_pair['base_currency_name'] . $coins_in_pair['quote_currency_name'];
        $base_currency_id = Coin::where('name',$coins_in_pair['base_currency_name'])->firstOrFail()->id;
        $quote_currency_id = Coin::where('name',$coins_in_pair['quote_currency_name'])->firstOrFail()->id;
        
        DB::table('currency_pair')->insert(
                [
                    'base_currency_id' => $base_currency_id,
                    'quote_currency_id' => $quote_currency_id,
                    'name' => $pair_name,
                ]
        );
        return $pair_name;
    }

    public static function checkCoinExistsOrCreate($request) {
        $coins_in_pair = $request->all();
        Coin::firstOrCreate(['name' => $coins_in_pair['base_currency_name']]);
        Coin::firstOrCreate(['name' => $coins_in_pair['quote_currency_name']]);
        return $coins_in_pair;
    }

}
