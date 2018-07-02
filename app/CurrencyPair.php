<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Validator;
use App\Coin;
use DB;
use App\Source;

class CurrencyPair extends Model
{

    public $table = 'currency_pair';
    public $timestamps = false;
    protected $fillable = ['name', 'priority'];

    public function prices()
    {
        return $this->hasMany('App\Price');
    }

    public function coins()
    {
        return $this->hasMany('App\Coin');
    }

    public static function addPairByAPI($request)
    {
        $validator = Validator::make($request->all(), [
                    'base_id' => 'required|numeric|max:5',
                    'quote_id' => 'required|numeric|max:5',
                    'source_id' => 'require|max:25',
        ]);

        if ($validator->fails()) {
            $error = 'Please enter correct coins data';
            throw new \Exception($error, 406);
        }
        $coins_in_pair = $request->all();
        $base = Coin::find($coins_in_pair['base_id']);
        $quote = Coin::find($coins_in_pair['quote_id']);
        $source = Source::find($coins_in_pair['source_id']);
        if (!$base  OR !$quote OR !$source) {
            $error = 'Please enter existed coins';
            throw new \Exception($error, 406);
        }
        //insert currency pair into table
        if ($coins_in_pair['quote_id'] == 1) {
            $priority = 1;
        } else {
            $priority = 2;
        }
        DB::table('currency_pair')->insert(
                [
                    'base_id' => $coins_in_pair['base_id'],
                    'quote_id' => $coins_in_pair['quote_id'],
                    'priority' => $priority,
                    'source_id' => $coins_in_pair['source_id'],
                ]
        );
    }
    
    public static function getPairName($currency_pair)
    {
        $base = Coin::where('id', $currency_pair->base_currency_id)->first();
        $quote = Coin::where('id', $currency_pair->quote_currency_id)->first();
        $nameOfCurrencyPair = $base->name . $quote->name;
        return $nameOfCurrencyPair;
    }

}
