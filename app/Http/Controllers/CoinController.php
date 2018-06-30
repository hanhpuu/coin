<?php

namespace App\Http\Controllers;

use Validator;
use App\Coin;
use Illuminate\Http\Request;

class CoinController extends Controller {


    public function addCoinName(Request $request) 
    {
        $validator  = Validator::make($request->all(), [
            'name'  => 'required|unique:coins|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 406,
                'message' => 'Coin already exists!'
            ]);
        }

        $coin = Coin::create($request->all());
        return response()->json([
            'code' => 200,
            'message' => 'OK',
            'data' => $coin
        ]);
    }

}
