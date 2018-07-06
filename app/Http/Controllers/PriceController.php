<?php

namespace App\Http\Controllers;

use App\Price;
use App\CurrencyPair;
use Log;

class PriceController extends Controller
{
    public function fetchAndSaveDataPerChunk() 
	{
		try {
			Price::fetchAndSaveAllData();
		} catch (\Exception $e) {
			Log::info($e->getMessage());
		}
		
	}
	
	public function reset()
	{
		Price::query()->truncate();
		CurrencyPair::where('cron_past_completed',1)->update(['cron_past_completed' => 0]);
	}


}
