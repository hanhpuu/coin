<?php

namespace App\Http\Controllers;

use App\Price;
use App\CurrencyPair;
use Log;

class PriceController extends Controller
{
    public function fetchAndSaveDataInPast() 
	{
		
		try {
			Price::fetchAndSaveAllData();
		} catch (\Exception $e) {
			Log::info($e->getMessage());
		}
		
	}

	public function fetchAndSaveAllDataInPresent() 
	{
		try {
			Price::fetchAndSaveDataInPresent();
		} catch (\Exception $e) {
			Log::info($e->getMessage());
		}
		
	}
	
	public function reset()
	{
		$today = date("Y-m-d");
		$yesterday = date("Y-m-d",strtotime("-1 days"));
		CurrencyPair::where('date_completed', $today)->update(['date_completed' => $yesterday]);
	}


}
