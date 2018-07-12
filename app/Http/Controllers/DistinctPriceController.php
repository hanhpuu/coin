<?php

namespace App\Http\Controllers;

use App\DistinctPrice;
use App\DistinctPair;
use Log;

class DistinctPriceController extends Controller
{
	public function fetchAndSaveDataInPresent() 
	{
		try {
			DistinctPrice::fetchAndSaveDataInPresent();
		} catch (\Exception $e) {
			echo '<pre>';
			print_r($e->getMessage());
			echo '</pre>';
			die;
			Log::info($e->getMessage());
		}
		
	}
	
	public function reset()
	{
		$today = date("Y-m-d");
		$yesterday = date("Y-m-d",strtotime("-1 days"));
		DistinctPair::where('date_completed', $today)->update(['date_completed' => $yesterday]);
	}


}
