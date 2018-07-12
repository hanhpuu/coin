<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Validator;
use DB;

class DistinctPair extends Model
{
	public $timestamps = false;

	public static function addPairByAPI($request)
	{
		$validator = Validator::make($request->all(), [
					'base_id' => 'required|numeric|max:5',
					'quote_id' => 'required|numeric|max:5',
					'distinct_price' => 'required|numeric',
					'source_id' => 'numeric',
		]);

		if ($validator->fails()) {
			$error = $validator->errors()->first();
			throw new \Exception($error, 406);
		}
		$distinct_pair = $request->all();
		$base = Coin::find($distinct_pair['base_id']);
		$quote = Coin::find($distinct_pair['quote_id']);
		$source = Source::find($distinct_pair['source_id']);
		if (!$base OR ! $quote OR ! $source) {
			$error = 'Please enter existed data';
			throw new \Exception($error, 406);
		}
		$existed_data = self::where('base_id',$distinct_pair['base_id'])->where('quote_id',$distinct_pair['quote_id'])->where('source_id',$distinct_pair['source_id'])->first();
		if ($existed_data) {
			$error = 'This pair already existed';
			throw new \Exception($error, 406);
		}
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
					'distinct_price' => $distinct_pair['distinct_price'],
					'source_id' => $distinct_pair['source_id'],
				]
		);
	}


}
