<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CurrencyPair;

class CurrencyPairController extends Controller {

    public function addPairName(Request $request) {
        try {
            $pair_name = CurrencyPair::addPairNameByAPI($request);
        } catch (\Exception $e) {
            return response()->json([
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
            ]);
        }

        return response()->json([
                    'code' => 200,
                    'message' => " You're the best ",
                    'data' => $pair_name
        ]);
    }

}
