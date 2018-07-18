<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Validator;
use Illuminate\Validation\Rule;
use DB;

class DistinctPair extends Model
{

	public $timestamps = false;

	public static function addPairByAPI($request)
	{
		$validator = self::checkDistinctPairValidation($request);
		if ($validator->fails()) {
			$error = $validator->errors()->first();
			throw new \Exception($error, 406);
		}
		self::insertNewDistinctPair($request);
	}

	public static function checkGainOfPotentialGroup($request)
	{
		self::checkGroupIDValidation($request);
		$id_array = self::where('potential_group_id', $request->potential_group_id)->pluck('id')->toArray();
		$price_array = self::fetchPrice($id_array);
		$name_array = self::fetchPairName($id_array);
		$data = self::combinePriceArray($id_array,$price_array, $name_array);
		return $data;
	}

	public static function combinePriceArray($id_array,$price_array,$name_array)
	{
		$result = [];
		foreach ($id_array as $id) {
			$index = $id;
			if ($price_array[$index] && $name_array[$index]) {
		        $gain_in_percentage = ($price_array[$index]['latest_price']/$price_array[$index]['initial_price']-1)*100 ;
				$result[$index] = array_merge($price_array[$index],$name_array[$index]);
				$result[$index]['gain_in_percentage'] = $gain_in_percentage;
			}
		}
		usort($result, function($a, $b) {
			return $a['gain_in_percentage'] <=> $b['gain_in_percentage'];
		});
		return $result;
	}

	public static function fetchPrice($id_array)
	{
		$price_array = array();
		$ids = self::whereIn('potential_group_id', $id_array)->select('id', 'initial_price','latest_price')->get();
		foreach ($ids as $id) {
			$value = $id->toArray();
			$price_array[$id['id']] = $value;
		}
		return $price_array;
	}

	public static function fetchPairName($id_array)
	{
		$pair_name_arrays = DB::table('distinct_pairs')
				->join('coins as c1', 'distinct_pairs.base_id', '=', 'c1.id')
				->join('coins as c2', 'distinct_pairs.quote_id', '=', 'c2.id')
				->whereIn('distinct_pairs.id', $id_array)		
				->select('distinct_pairs.id', 'c1.name as base_name', 'c2.name as quote_name')
				->get();
		$name_array = [];
		foreach ($pair_name_arrays as $pair_name_array) {
			$name_array[$pair_name_array->id] = (array) $pair_name_array;
		}
		return $name_array;
	}

	public static function checkGroupIDValidation($request)
	{
		$validator = Validator::make($request->all(), [
					'potential_group_id' => 'required|exists:distinct_pairs',
		]);

		if ($validator->fails()) {
			$error = $validator->errors()->first();
			throw new \Exception($error, 406);
		}
	}

	public static function checkDistinctPairValidation($request)
	{
		$validator = Validator::make($request->all(), [
					'base_id' => 'required|numeric|exists:coins,id',
					'quote_id' => 'required|numeric|exists:coins,id',
					'initial_price' => 'required|numeric',
					'potential_group_id' => 'required|numeric',
					'source_id' => [
						'required',
						'numeric',
						'exists:sources,id',
						Rule::unique('distinct_pairs')->where(function ($query) use($request) {
								$query->where('base_id', $request->base_id)->where('quote_id', $request->quote_id)->where('initial_price', $request->initial_price);
							}),	
					],
		]);
		return $validator;
	}
	
	public static function insertNewDistinctPair($request)
	{
		$distinct_pair = $request->all();
		//insert currency pair into table
		if ($distinct_pair['quote_id'] == 1) {
			$priority = 1;
		} else {
			$priority = 2;
		}
		DB::table('distinct_pairs')->insert(
				[
					'base_id' => $distinct_pair['base_id'],
					'quote_id' => $distinct_pair['quote_id'],
					'priority' => $priority,
					'initial_price' => $distinct_pair['initial_price'],
					'latest_price' => $distinct_pair['initial_price'],
					'source_id' => $distinct_pair['source_id'],
					'potential_group_id' => $distinct_pair['potential_group_id'],
				]
		);
	}
	
	public static function SaveLatestPrice()
	{
		date_default_timezone_set('Asia/Bangkok');
		$today = date("Y-m-d h:i:s");
		$distinct_pairs = DistinctPair::whereDate('date_completed', '!=', $today)->orderBy('priority', 'asc')->get();
		foreach ($distinct_pairs as $distinct_pair) {
			$name_of_pair = CurrencyPair::getPairName($distinct_pair);
			$current_url = Price::getURL($name_of_pair);
			$raw_data = Price::getRawDataFromCurrentURL($current_url);
			$number_of_raw_data = count($raw_data);
			$latest_price_in_USDT = ($raw_data[$number_of_raw_data - 1][2] + $raw_data[$number_of_raw_data - 1][3]) / 2;
			if ($distinct_pair->quote_id != 1) {
				$currency_pair_in_USDT = DistinctPair::where('base_id', $distinct_pair->quote_id)
								->where('quote_id', 1)->first();
				$latest_price_in_USDT = $latest_price_in_USDT*$currency_pair_in_USDT['latest_price'];
			}
			$distinct_pair->latest_price = $latest_price_in_USDT;
			$distinct_pair->date_completed = $today;
			$distinct_pair->save();
		}
	}
	
}
