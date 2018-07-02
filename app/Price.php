<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;
use App\CurrencyPair;

class Price extends Model
{

    public $timestamps = false;
    protected $fillable = ['currency_pair_id', 'name', 'openning_date_in_unix', 'openning_date', 'open', 'high', 'low', 'close', 'quote_open', 'quote_high', 'quote_low', 'quote_close', 'closing_date', 'average'];

    public function currency_pair()
    {
        return $this->belongsTo('App\CurrencyPair');
    }

    const CORE_API_LINK = "https://www.binance.com/api/v1/klines?symbol=";
    const INTERVAL = "&interval=1h";
    const END_TIME = "&endTime=";

    /**
     * To get average price from a number of URLs, loop til reach limit  or no URL left .
     *
     * @param  string  $nameOfCurrencyPair
     * @return 
     */
    public static function saveAveragePriceFromAPICall($base, $quote, $limit)
    {
        $nameOfCurrencyPair = $base . $quote;
        $currency_pair = CurrencyPair::where('name', $nameOfCurrencyPair)->firstOrFail();
        $url = Price::getURL($nameOfCurrencyPair);

        $i = 0;
        do {
            if ($quote == 'USDT') {
                $url = Price::saveAveragePriceWithQuoteUSDTFromCurrentURL($url, $currency_pair);
            } else {
                $url = Price::saveAveragePriceWithoutQuoteUSDTFromCurrentURL($url, $currency_pair, $quote);
            }
            $i++;
            if ($i == $limit) {
                break;
            }
        } while ($url !== FALSE);

        return $i;
    }

    /**
     * To get average price from the current URLs
     *
     * @param  string  $url
     * @param  string  $currency_pair
     * @return a new URL to continue the loop
     */
    public static function saveAveragePriceWithQuoteUSDTFromCurrentURL($url, $currency_pair)
    {
        $raw_data = self::getRawDataFromCurrentURL($url);
        if (count($raw_data) <= 1) {
            CurrencyPair::where('id', $currency_pair->id)->update(['cron_past_completed' => 1]);
            return FALSE;
        }

        foreach ($raw_data as $pricePerHour) {
            $openning_hour = date("Y-m-d H:i:s", substr($pricePerHour[0], 0, 10));
            $closing_hour = date("Y-m-d H:i:s", substr($pricePerHour[6], 0, 10));
            $average_price = ($pricePerHour[2] + $pricePerHour[3]) / 2;
            self::updateOrCreate(
                    [
                'currency_pair_id' => $currency_pair->id,
                'name' => $currency_pair->name,
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

        $new_url = self::CORE_API_LINK . $currency_pair->name . self::INTERVAL . self::END_TIME . $raw_data[0][0];

        return $new_url;
    }

    public static function saveAveragePriceWithoutQuoteUSDTFromCurrentURL($url, $currency_pair, $quote)
    {
        $raw_data = self::getRawDataFromCurrentURL($url);
        if (count($raw_data) <= 1) {
            CurrencyPair::where('id', $currency_pair->id)->update(['cron_past_completed' => 1]);
            return FALSE;
        }

        $currency_pair_in_USDT = $quote . 'USDT';

        foreach ($raw_data as $pricePerHour) {
            $openning_hour = date("Y-m-d H:i:s", substr($pricePerHour[0], 0, 10));
            $closing_hour = date("Y-m-d H:i:s", substr($pricePerHour[6], 0, 10));
            $currency_price = Price::where('name', '=', $currency_pair_in_USDT)
                    ->where('openning_date_in_unix', $pricePerHour[0])
                    ->first();
            if ($currency_price !== null) {
                $open_USDT = $pricePerHour[1] * $currency_price->open;
                $high_USDT = $pricePerHour[2] * $currency_price->average;
                $low_USDT = $pricePerHour[3] * $currency_price->average;
                $close_USDT = $pricePerHour[4] * $currency_price->close;
                $average_price = ($high_USDT + $low_USDT) / 2;
            } else {
                $open_USDT = null;
                $high_USDT = null;
                $low_USDT = null;
                $close_USDT = null;
                $average_price = ($pricePerHour[2] + $pricePerHour[3]) / 2;
            }

            self::updateOrCreate(
                    [
                'currency_pair_id' => $currency_pair->id,
                'name' => $currency_pair->name,
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

        $new_url = self::CORE_API_LINK . $currency_pair->name . self::INTERVAL . self::END_TIME . $raw_data[0][0];

        return $new_url;
    }

    /**
     * To get raw data  from the current URLs
     *
     * @param  string  $url
     * @return array of all raw data
     */
    public static function getRawDataFromCurrentURL($url)
    {
        $client = new Client();
        $respond = $client->request('GET', $url);
        $raw_data = json_decode($respond->getBody());

        return $raw_data;
    }

    public static function getURL($nameOfCurrencyPair)
    {
        $coinData = Price::where('name', $nameOfCurrencyPair)->first();

        if ($coinData !== null) {
            $openning_date_of_url_in_unix = Price::min('openning_date_in_unix');
            $url = self::CORE_API_LINK . $nameOfCurrencyPair . self::INTERVAL . self::END_TIME . $openning_date_of_url_in_unix;
        } else {
            $url = self::CORE_API_LINK . $nameOfCurrencyPair . self::INTERVAL;
        }

        return $url;
    }

}
