<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;
use App\DistinctPair;
use App\CurrencyPair;

class DistinctPrice extends Model
{

	public $timestamps = false;
	protected $fillable = ['distinct_pair_id', 'openning_date_in_unix', 'openning_date', 'open', 'high', 'low', 'close', 'quote_open', 'quote_high', 'quote_low', 'quote_close', 'closing_date', 'average'];

	public function distinct_pair()
	{
		return $this->belongsTo('App\DistinctPair');
	}

	const LIMIT_API_CALL_PER_CHUNK = 9;
	const CORE_API_LINK = "https://www.binance.com/api/v1/klines?symbol=";
	const INTERVAL = "&interval=15m";
	const ID_OF_USDT = 1;

	public static function fetchAndSaveDataInPresent()
	{
		$today = date("Y-m-d h:i:s");
		$distinct_pairs = DistinctPair::whereDate('date_completed', '!=', $today)->orderBy('priority', 'asc')->get();
		$remaining_call = self::LIMIT_API_CALL_PER_CHUNK;
		foreach ($distinct_pairs as $distinct_pair) {
			$number_api_called = self::fetchAndSaveDistinctPairDataInPresent($distinct_pair, $remaining_call, $today);
			$remaining_call = $remaining_call - $number_api_called;
			if ($remaining_call <= 0) {
				return;
			}
		}
	}

	/**
	 * To get price of a currency pair, loop til reach limit and get all data of currently loop one .
	 *
	 * @param  string  $nameOfDistinctPair
	 * @return integer $i number of api 
	 */
	public static function fetchAndSaveDistinctPairDataInPresent($distinct_pair, $limit, $today)
	{
		$nameOfDistinctPair = CurrencyPair::getPairName($distinct_pair);
		$url = self::CORE_API_LINK . $nameOfDistinctPair . self::INTERVAL;
		$max = self::where('distinct_pair_id', $distinct_pair->id)->max('openning_date_in_unix');
		$max = $max ? $max : '1530441900000' ;
//		unix time of '2018-07-01 10:45:00';
		$i = 0;
		while ($distinct_pair->date_completed != $today) {
			$i++;
			$raw_data = self::getRawDataFromCurrentURL($url);
			if ($distinct_pair->quote_id == self::ID_OF_USDT) {
				self::savePriceWithUSDTInPresent($raw_data, $distinct_pair, $max, $today);
			} else {
				self::savePriceWithoutUSDTInPresent($raw_data, $distinct_pair, $max, $today);
			}
			$distinct_pair->date_completed = $today;
			$distinct_pair->save();
			if ($i == $limit && $distinct_pair->date_completed !== $today) {
				$i--;
			}
			if ($i == $limit && $distinct_pair->date_completed == $today) {
				break;
			}
		} 
		return $i;
	}

	public static function savePriceWithUSDTInPresent($raw_data, $distinct_pair, $max, $today)
	{
		$i = count($raw_data) - 1;
		while ($raw_data[$i][0] > $max) {
			$i--;
			$openning_hour = date("Y-m-d H:i:s", substr($raw_data[$i][0], 0, 10));
			$closing_hour = date("Y-m-d H:i:s", substr($raw_data[$i][6], 0, 10));
			$average_price = ($raw_data[$i][2] + $raw_data[$i][3]) / 2;
			self::updateOrCreate(
					[
				'distinct_pair_id' => $distinct_pair->id,
				'openning_date_in_unix' => $raw_data[$i][0],
				'openning_date' => $openning_hour
					], [
				'open' => $raw_data[$i][1],
				'high' => $raw_data[$i][2],
				'low' => $raw_data[$i][3],
				'close' => $raw_data[$i][4],
				'quote_open' => $raw_data[$i][1],
				'quote_high' => $raw_data[$i][2],
				'quote_low' => $raw_data[$i][3],
				'quote_close' => $raw_data[$i][4],
				'closing_date' => $closing_hour,
				'average' => $average_price,
					]
			);
		}
	}

	public static function savePriceWithoutUSDTInPresent($raw_data, $distinct_pair, $max, $today)
	{
		$id_of_distinct_pair_in_USDT = DistinctPair::where('base_id', $distinct_pair->quote_id)
						->where('quote_id', self::ID_OF_USDT)->first()->id;
		$i = count($raw_data) - 1;
		while ($raw_data[$i][0] > $max) {
			$i--;
			$openning_hour = date("Y-m-d H:i:s", substr($raw_data[$i][0], 0, 10));
			$closing_hour = date("Y-m-d H:i:s", substr($raw_data[$i][6], 0, 10));
			$distinct_pair_in_USDT = self::where('distinct_pair_id', $id_of_distinct_pair_in_USDT)
					->where('openning_date_in_unix', $raw_data[$i][0])
					->first();
			if ($distinct_pair_in_USDT !== null) {
				$open_USDT = $raw_data[$i][1] * $distinct_pair_in_USDT->open;
				$high_USDT = $raw_data[$i][2] * $distinct_pair_in_USDT->average;
				$low_USDT = $raw_data[$i][3] * $distinct_pair_in_USDT->average;
				$close_USDT = $raw_data[$i][4] * $distinct_pair_in_USDT->close;
				$average_price = ($high_USDT + $low_USDT) / 2;
			} else {
				$open_USDT = null;
				$high_USDT = null;
				$low_USDT = null;
				$close_USDT = null;
				$average_price = ($raw_data[$i][2] + $raw_data[$i][3]) / 2;
			}

			self::updateOrCreate([
				'distinct_pair_id' => $distinct_pair->id,
				'openning_date_in_unix' => $raw_data[$i][0],
				'openning_date' => $openning_hour
					], [
				'open' => $open_USDT,
				'high' => $high_USDT,
				'low' => $low_USDT,
				'close' => $close_USDT,
				'quote_open' => $raw_data[$i][1],
				'quote_high' => $raw_data[$i][2],
				'quote_low' => $raw_data[$i][3],
				'quote_close' => $raw_data[$i][4],
				'closing_date' => $closing_hour,
				'average' => $average_price,
					]
			);
		}
		$distinct_pair->date_completed = $today;
		$distinct_pair->save();
	}

	/**
	 * To get raw data  from the current URLs
	 *
	 * @param  string  $url
	 * @return array of all raw data
	 */
	public static function getRawDataFromCurrentURL($url)
	{
		try {
			$client = new Client();
			$respond = $client->request('GET', $url);
			$raw_data = json_decode($respond->getBody());
			usort($raw_data, function($a, $b) {
				return $a[0] <=> $b[0];
			});
			krsort($raw_data);
			return $raw_data;
		} catch (\Exception $e) {
			try {
				$client = new Client();
				$respond = $client->request('GET', $url);
				$raw_data = json_decode($respond->getBody());
				usort($raw_data, function($a, $b) {
					return $a[0] <=> $b[0];
				});
				krsort($raw_data);
				return $raw_data;
			} catch (\Exception $e) {
				throw \Exception($e->getMessage());
			}
		}
	}


}
