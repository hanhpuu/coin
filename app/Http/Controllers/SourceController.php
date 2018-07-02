<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Source;

class SourceController extends Controller
{
    public function addSourceName(Request $request) 
    {
        $validator  = Validator::make($request->all(), [
            'name'  => 'required|unique:sources|max:25',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 406,
                'message' => 'Source already exists!'
            ]);
        }

        $source = Source::create($request->all());
        return response()->json([
            'code' => 200,
            'message' => 'OK',
            'data' => $source
        ]);
    }
}
