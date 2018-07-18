<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CurrencyPair;
use Illuminate\Support\Facades\Session;

class CurrencyPairController extends Controller {

    public function addPairNameByAPI(Request $request) {
        try {
            CurrencyPair::addPairByAPI($request);
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
	
	public function checkPriceFluctuation(Request $request)
	{
		try {
            $data = CurrencyPair::checkGainOfAllCurrencyCoin($request);
        } catch (\Exception $e) {
            return response()->json([
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
            ]);
        }

        return response()->json([
                    'code' => 200,
                    'message' => $data,
        ]);
	}

	public function index()
	{
		$currency_pairs = CurrencyPair::orderBy('id', 'asc')->get();
		return view('frontend.currency_pair.index')->with('currency_pairs', $currency_pairs);
	}

	public function create()
	{
		return view('frontend.currency_pair.create');
	}

	public function store(Request $request)
	{
		$validator = CurrencyPair::checkCurrencyPairValidation($request);
		if ($validator->fails()) {
			$error = $validator->errors()->first();
			return view('frontend.error')->with('error',$error);
		}
		CurrencyPair::insertNewCurrencyPair($request);
		//return a view after creating new currency pair
		$currency_pairs = CurrencyPair::orderBy('id', 'asc')->get();
		Session::flash('success', 'The currency pair was successfully saved');
		return view('frontend.currency_pair.index')->with('currency_pairs', $currency_pairs);
	}

	public function edit($id)
	{
		$currency_pair = CurrencyPair::findOrFail($id);
		return view('frontend.currency_pair.edit')->withCurrencyPair($currency_pair);
	}

	public function update(Request $request, $id)
	{
		$validator = CurrencyPair::checkCurrencyPairValidation($request);
		if ($validator->fails()) {
			$error = $validator->errors()->first();
			return view('frontend.error')->with('error',$error);
		}
		$currency_pair = CurrencyPair::findOrFail($id);
		$currency_pair->base_id = $request->base_id;
		$currency_pair->quote_id =$request->quote_id;
		if( $request->quote_id == 1) {
			$currency_pair->priority = 1;
		} else {
			$currency_pair->priority = 2;
		}
		$currency_pair->source_id = $request->source_id;
		$currency_pair->save();
		//return a view		
		$currency_pairs = CurrencyPair::orderBy('id', 'asc')->get();
		Session::flash('success', 'The currency pair was successfully updated');
		return view('frontend.currency_pair.index')->with('currency_pairs', $currency_pairs);
		
	}

}
