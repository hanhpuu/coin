<?php

namespace App;

use Illuminate\Validation\Rule;
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
		if (!$base OR ! $quote OR ! $source) {
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

	CONST ID_OF_USDT = 1;
	CONST ID_OF_BTC = 2;

	/**
	 * To see how much currency pairs gain during a time period
	 *
	 * @param  boolean $sort desc if 1, asc if 0
	 * @param date $begin
	 * @param date $end
	 * @return an array
	 */
	public static function checkGainOfAllCurrencyCoin($request)
	{
		self::checkDataValidate($request);
		$ids = self::getPairByQuote($request->quote);
		$max_price_array = self::fetchMaxPrice($request->begin, $request->end);
		$open_price_array = self::fetchOpenPrice($request->begin);
		$result = self::combinePriceArray($ids, $max_price_array, $open_price_array);
		//	desc if 1, asc if 0	
		if ($request->sort == 1) {
			krsort($result);
		} else {
			ksort($result);
		}
		return array_values($result);
	}

	public static function checkDataValidate($request)
	{
		$validator = Validator::make($request->all(), [
					'begin' => 'required|date',
					'end' => 'required|date|after:begin',
					'sort' => 'required|boolean',
					'quote' => [
						'required',
						Rule::in(['USDT', 'BTC']),
					]
		]);
		if ($validator->fails()) {
			$errors = $validator->errors();
			throw new \Exception($errors->first(), 406);
		}
	}

	public static function fetchMaxPrice($begin, $end)
	{
		$data1 = [];
		$max_price_arrays = DB::SELECT("select prices.currency_pair_id, prices.high, prices.openning_date
								FROM prices 
								INNER JOIN (
									SELECT currency_pair_id, MAX(high) high
									FROM prices
									WHERE prices.openning_date BETWEEN '$begin' AND '$end'
									GROUP BY currency_pair_id
								) new_table
                                ON prices.currency_pair_id = new_table.currency_pair_id AND prices.high = new_table.high
								");

		foreach ($max_price_arrays as $max_price_array) {
			$data1[$max_price_array->currency_pair_id] = (array) $max_price_array;
		}
		return $data1;
	}

	public static function fetchOpenPrice($begin)
	{
		$data2 = [];
		$open_price_arrays = DB::SELECT("select prices.currency_pair_id, prices.openning_date, prices.open
								FROM prices
								INNER JOIN (
									SELECT currency_pair_id, MIN(openning_date) openning_date
									FROM prices
									WHERE prices.openning_date  >= '$begin' 
									GROUP BY currency_pair_id
								) new_table
								ON prices.currency_pair_id = new_table.currency_pair_id AND prices.openning_date = new_table.openning_date
								");
		foreach ($open_price_arrays as $open_price_array) {
			$data2[$open_price_array->currency_pair_id] = (array) $open_price_array;
		}
		return $data2;
	}

	public static function getPairByQuote($quote)
	{
		if ($quote == 'USDT') {
			$ids = self::where('quote_currency_id', SELF::ID_OF_USDT)->pluck('id')->toArray();
		} else {
			$ids = self::where('quote_currency_id', SELF::ID_OF_BTC)->pluck('id')->toArray();
		}
		return $ids;
	}

	public static function combinePriceArray($id, $data1, $data2)
	{
		$result = array();
		foreach ($id as $currency_pair_id) {
		// if data of this $currency_pair_id exists in both 2 arrays		
			if ($data1[$currency_pair_id] && $data2[$currency_pair_id]) {
				$gain_in_percentage = ($data1[$currency_pair_id]['high'] / $data2[$currency_pair_id]['open'] - 1) * 100;
				$gain_index = $gain_in_percentage * 1000000;
				$result[$gain_index] = array_merge($data1[$currency_pair_id], $data2[$currency_pair_id]);
				$result[$gain_index]['gain_in_percentage'] = $gain_in_percentage;
			} else {
				$result[$currency_pair_id] = [];
			}
		}
		return $result;
	}

}
