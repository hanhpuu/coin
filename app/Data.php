<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Goutte;
use DateTime;
use DB;

class Data extends Model {
    
    public $timestamps = false;

    public static function extractPriceDataFromWebsite($nameOfCoin) 
    {
        $link = 'https://coinmarketcap.com/currencies/'.$nameOfCoin.'/historical-data/?start=20130428&end=20180623';
        try {
            $crawler = Goutte::request('GET', $link);
        } catch (\Exception $e) {
            return;
        }
        
        $result = $crawler->filter('.table-responsive tbody tr')->each(function ($node) {
            return $node->children()->each(function ($node1, $i) {
                if ($i == 0) {
                    $date = DateTime::createFromFormat('M j, Y', $node1->text())->format('Y-m-d');
                    return $date;
                } else {
                    $number = floatval(str_replace(",", "", $node1->text()));
                    return $number;
                }
                return $node1->text();
            });
        });

        foreach ($result as $data) {
            DB::table('data')->insert(
                    ['Name' => $nameOfCoin, 'Date' => $data[0], 'Open' => $data[1], 'Low' => $data[2], 'High' => $data[3], 'Close' => $data[4], 'Volume' => $data[5], 'Market Cap' => $data[6],]
            );
        }
    }

    

}
