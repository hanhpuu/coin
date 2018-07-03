<?php

namespace App\Http\Controllers;

use App\Price;

class PriceController extends Controller
{
    public function fetchAndSaveDataPerChunk() 
	{
//		Price::fetchAndSaveAllDataInPast();
		Price::fetchAndSaveAllDataInPresent();
	}

}
