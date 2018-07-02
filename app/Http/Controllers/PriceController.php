<?php

namespace App\Http\Controllers;

use App\Price;
use App\CurrencyPair;

class PriceController extends Controller
{

    const LIMIT_API_CALL_PER_CHUNK = 10;

    public function getAveragePriceForEachCoin()
    {
        $currencyPairs = CurrencyPair::where('past', 0)->orderBy('priority', 'asc')->get();
        $remaining_call = self::LIMIT_API_CALL_PER_CHUNK;

        foreach ($currencyPairs as $currencyPair) {
            $base = $currencyPair->base_currency;
            $quote = $currencyPair->quote_currency;
            $number_api_called = Price::saveAveragePriceFromAPICall($base, $quote, $remaining_call);
            $remaining_call = $remaining_call - $number_api_called;

            if ($number_api_called <= 0) {
                return;
            }
        }
    }

}
