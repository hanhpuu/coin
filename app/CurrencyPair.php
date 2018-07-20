<?php

namespace App;

use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Validator;
use App\Coin;
use DB;

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
		$validator = self::checkCurrencyPairValidation($request);
		if ($validator->fails()) {
			$error = $validator->errors()->first();
			throw new \Exception($error, 406);
		}
		self::insertNewCurrencyPair($request);
	}
	
	public static function insertNewCurrencyPair($request)
	{
		$coins_in_pair = $request->all();
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

	public static function checkCurrencyPairValidation($request)
	{
		$validator = Validator::make($request->all(), [
					'base_id' => 'required|numeric|exists:coins,id',
					'quote_id' => 'required|numeric|exists:coins,id',
					'source_id' => [
						'required',
						'numeric',
						'exists:sources,id',
						Rule::unique('currency_pair')->where(function ($query) use($request) {
								$query->where('base_id', $request->base_id)->where('quote_id', $request->quote_id);
							}),
					],
		]);

		return $validator;
	}

	public static function getPairName($currency_pair)
	{
		$base = Coin::where('id', $currency_pair->base_id)->first();
		$quote = Coin::where('id', $currency_pair->quote_id)->first();
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
		$validator = self::checkDataValidate($request);
		if ($validator->fails()) {
			$errors = $validator->errors();
			throw new \Exception($errors->first(), 406);
		}
		$ids = self::getPairByQuote($request->quote);
		$pair_name_array = self::fetchPairName();
		$max_price_array = self::fetchMaxPrice($request->begin, $request->end);
		$open_price_array = self::fetchOpenPrice($request->begin);
		$result = self::combinePriceArray($ids, $max_price_array, $open_price_array, $pair_name_array);
		return array_values($result);
	}

	public static function checkDataValidate($request)
	{
		$validator = Validator::make($request->all(), [
					'begin' => 'required|date|before:tomorrow',
					'end' => 'required|date|after:begin',
					'sort' => 'boolean',
					'quote' => [
						Rule::in(['USDT', 'BTC', 'Both']),
					]
		]);
		return $validator;
	}

	public static function fetchMaxPrice($begin, $end)
	{
		$max_array = [];
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
			$max_array[$max_price_array->currency_pair_id] = (array) $max_price_array;
		}
		return $max_array;
	}

	public static function fetchOpenPrice($begin)
	{
		$open_array = [];
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
			$open_array[$open_price_array->currency_pair_id] = (array) $open_price_array;
		}
		return $open_array;
	}

	public static function getPairByQuote($quote)
	{
		if ($quote == 'USDT') {
			$ids = self::where('quote_id', SELF::ID_OF_USDT)->pluck('id')->toArray();
		} else if ($quote == 'BTC') {
			$ids = self::where('quote_id', SELF::ID_OF_BTC)->pluck('id')->toArray();
		} else {
			$ids = self::all()->pluck('id')->toArray();
		}
		return $ids;
	}

	public static function fetchPairName()
	{
		$pair_name_arrays = DB::table('currency_pair')
				->join('coins as c1', 'currency_pair.base_id', '=', 'c1.id')
				->join('coins as c2', 'currency_pair.quote_id', '=', 'c2.id')
				->select('currency_pair.id', 'c1.name as base_name', 'c2.name as quote_name')
				->get();
		$name_array = [];
		foreach ($pair_name_arrays as $pair_name_array) {
			$name_array[$pair_name_array->id] = (array) $pair_name_array;
		}
		return $name_array;
	}

	public static function combinePriceArray($ids, $max_array, $open_array, $name_array)
	{
		$result = array();
		foreach ($ids as $currency_pair_id) {
			// if data of this $currency_pair_id exists in both 2 arrays		
			if ($max_array[$currency_pair_id] && $open_array[$currency_pair_id] && $name_array[$currency_pair_id]) {
				$gain_in_percentage = ($max_array[$currency_pair_id]['high'] / $open_array[$currency_pair_id]['open'] - 1) * 100;
				$result[$currency_pair_id] = array_merge($name_array[$currency_pair_id], $max_array[$currency_pair_id], $open_array[$currency_pair_id]);
				$result[$currency_pair_id]['gain_in_percentage'] = $gain_in_percentage;
			} else {
				$result[$currency_pair_id] = [];
			}
		}
		usort($result, function($a, $b) {
			return $a['gain_in_percentage'] <=> $b['gain_in_percentage'];
		});
		return $result;
	}

	
	
}
