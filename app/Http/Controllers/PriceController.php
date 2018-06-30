<?php

namespace App\Http\Controllers;

use App\Price;

class PriceController extends Controller {
    
    public function getAveragePriceForEachCoin() 
    {
        
        Price::saveAveragePriceFromAPICall('KEY','BTC');
        echo 'Done';
    }

}
