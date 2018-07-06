<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;
use App\CurrencyPair;

class Price extends Model
{

	public $timestamps = false;
	protected $fillable = ['currency_pair_id', 'openning_date_in_unix', 'openning_date', 'open', 'high', 'low', 'close', 'quote_open', 'quote_high', 'quote_low', 'quote_close', 'closing_date', 'average'];

	public function currency_pair()
	{
		return $this->belongsTo('App\CurrencyPair');
	}

	const LIMIT_API_CALL_PER_CHUNK = 9;
	const CORE_API_LINK = "https://www.binance.com/api/v1/klines?symbol=";
	const INTERVAL = "&interval=1h";
	const END_TIME = "&endTime=";
	const ID_OF_USDT = 1;

	public static function fetchAndSaveAllData()
	{
		$currency_pairs = CurrencyPair::where('cron_past_completed', 0)->orderBy('priority', 'asc')->get();
		$remaining_call = self::LIMIT_API_CALL_PER_CHUNK;
		foreach ($currency_pairs as $currency_pair) {
			$number_api_called = Price::fetchAndSaveCurrencyPairData($currency_pair, $remaining_call);
			$remaining_call = $remaining_call - $number_api_called;
			if ($remaining_call <= 0) {
				return;
			}
		}
	}

	/**
	 * To get price of a currency pair, loop til reach limit and get all data of currently loop one .
	 *
	 * @param  string  $nameOfCurrencyPair
	 * @return integer $i number of api 
	 */
	public static function fetchAndSaveCurrencyPairData($currency_pair, $limit)
	{
		$nameOfCurrencyPair = CurrencyPair::getPairName($currency_pair);
		$url = Price::getURL($nameOfCurrencyPair);
		$i = 0;
		do {
			$i++;
			$raw_data = self::getRawDataFromCurrentURL($url);
			if (count($raw_data) <= 1) {
				$currency_pair->cron_past_completed = 1;
				$currency_pair->save();
				break;
			}

			if ($currency_pair->quote_currency_id == self::ID_OF_USDT) {
				Price::savePriceWithUSDT($raw_data, $currency_pair);
			} else {
				Price::savePriceWithoutUSDT($raw_data, $currency_pair);
			}
			if ($i == $limit && $currency_pair->cron_past_completed == 0) {
				$i--;
			}
			if ($i == $limit && $currency_pair->cron_past_completed == 1) {
				break;
			}

			$url = self::CORE_API_LINK . $nameOfCurrencyPair . self::INTERVAL . self::END_TIME . $raw_data[0][0];
		} while (1);

		return $i;
	}

	/**
	 * To save price of currency pair with quote coin is USDT
	 *
	 * @param  array of all data  $raw_data
	 * @param  string  $currency_pair
	 */
	public static function savePriceWithUSDT($raw_data, $currency_pair)
	{
		foreach ($raw_data as $pricePerHour) {
			$openning_hour = date("Y-m-d H:i:s", substr($pricePerHour[0], 0, 10));
			$closing_hour = date("Y-m-d H:i:s", substr($pricePerHour[6], 0, 10));
			$average_price = ($pricePerHour[2] + $pricePerHour[3]) / 2;
			self::updateOrCreate(
					[
				'currency_pair_id' => $currency_pair->id,
				'openning_date_in_unix' => $pricePerHour[0],
				'openning_date' => $openning_hour
					], [
				'open' => $pricePerHour[1],
				'high' => $pricePerHour[2],
				'low' => $pricePerHour[3],
				'close' => $pricePerHour[4],
				'quote_open' => $pricePerHour[1],
				'quote_high' => $pricePerHour[2],
				'quote_low' => $pricePerHour[3],
				'quote_close' => $pricePerHour[4],
				'closing_date' => $closing_hour,
				'average' => $average_price,
					]
			);
		}
	}

	
	/**
	 * To save price of currency pair withOUT quote coin is USDT
	 *
	 * @param  array of all data  $raw_data
	 * @param  string  $currency_pair
	 */
	public static function savePriceWithoutUSDT($raw_data, $currency_pair)
	{
		$id_of_currency_pair_in_USDT = CurrencyPair::where('base_currency_id', $currency_pair->quote_currency_id)
						->where('quote_currency_id', self::ID_OF_USDT)->first()->id;

		foreach ($raw_data as $pricePerHour) {
			$openning_hour = date("Y-m-d H:i:s", substr($pricePerHour[0], 0, 10));
			$closing_hour = date("Y-m-d H:i:s", substr($pricePerHour[6], 0, 10));
			$currency_pair_in_USDT = Price::where('currency_pair_id', $id_of_currency_pair_in_USDT)
					->where('openning_date_in_unix', $pricePerHour[0])
					->first();
			if ($currency_pair_in_USDT !== null) {
				$open_USDT = $pricePerHour[1] * $currency_pair_in_USDT->open;
				$high_USDT = $pricePerHour[2] * $currency_pair_in_USDT->average;
				$low_USDT = $pricePerHour[3] * $currency_pair_in_USDT->average;
				$close_USDT = $pricePerHour[4] * $currency_pair_in_USDT->close;
				$average_price = ($high_USDT + $low_USDT) / 2;
			} else {
				$open_USDT = null;
				$high_USDT = null;
				$low_USDT = null;
				$close_USDT = null;
				$average_price = ($pricePerHour[2] + $pricePerHour[3]) / 2;
			}

			self::updateOrCreate([
				'currency_pair_id' => $currency_pair->id,
				'openning_date_in_unix' => $pricePerHour[0],
				'openning_date' => $openning_hour
					], [
				'open' => $open_USDT,
				'high' => $high_USDT,
				'low' => $low_USDT,
				'close' => $close_USDT,
				'quote_open' => $pricePerHour[1],
				'quote_high' => $pricePerHour[2],
				'quote_low' => $pricePerHour[3],
				'quote_close' => $pricePerHour[4],
				'closing_date' => $closing_hour,
				'average' => $average_price,
					]
			);
		}
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
			return $raw_data;
		} catch (\Exception $e) {
			try {
				$client = new Client();
				$respond = $client->request('GET', $url);
				$raw_data = json_decode($respond->getBody());
				return $raw_data;
			} catch (\Exception $e) {
				throw \Exception($e->getMessage());
			}
		}
	}

	public static function getURL($nameOfCurrencyPair)
	{
		$url = self::CORE_API_LINK . $nameOfCurrencyPair . self::INTERVAL;
		return $url;
	}

}
