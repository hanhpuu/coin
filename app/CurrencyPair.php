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
		$validator = Validator::make($request->all(), [
					'begin' => 'required|date',
					'end' => 'required|date|after:begin',
					'sort' => 'required|boolean',
		]);

		if ($validator->fails()) {
			$errors = $validator->errors();
			throw new \Exception($errors->first(), 406);
		}

		$curreny_pairs = self::all();
		$data = [];
		foreach ($curreny_pairs as $curreny_pair) {
			$id = $curreny_pair->id;
			$price_at_begin = DB::table('prices')
							->where('currency_pair_id', $id)
							->whereDate('openning_date', $request->begin)->first()->open;
			$max = DB::table('prices')
							->where('currency_pair_id', $id)
							->whereBetween('openning_date', [$request->begin, $request->end])->max('high');
			$date_reach_max = DB::table('prices')
							->where('currency_pair_id', $id)
							->where('high', $max)->first()->openning_date;
			$gain_in_percentage = ($max / $price_at_begin - 1) * 100;
			$gain_index = $gain_in_percentage * 100000;
			$data[$gain_index] = [
				'pair_id' => $id,
				'price at begin date' => $price_at_begin,
				'highest price' => $max,
				'date reach highest price' => $date_reach_max,
				'gain in percentage' => $gain_in_percentage
			];
		}
		if($request->sort == 1) {
			krsort($data);
		} else {
			ksort($data);
		}
		return array_values($data);
	}

}
