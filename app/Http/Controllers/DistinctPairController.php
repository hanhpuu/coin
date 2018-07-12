<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DistinctPair;

class DistinctPairController extends Controller {

    public function addDistinctPairs(Request $request) {
        try {
            DistinctPair::addPairByAPI($request);
        } catch (\Exception $e) {
            return response()->json([
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
            ]);
        }

        return response()->json([
                    'code' => 200,
                    'message' => " You're the best ",
        ]);
    }
}
