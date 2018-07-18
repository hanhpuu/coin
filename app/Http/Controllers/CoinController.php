<?php

namespace App\Http\Controllers;

use App\Coin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CoinController extends Controller
{

	public function addCoinNameByAPI(Request $request)
	{
		$validator = Coin::checkCoinValidation($request);
		if ($validator->fails()) {
			return response()->json([
						'code' => 406,
						'message' => $validator->errors(),
			]);
		}
		$coin = Coin::create($request->all());
		return response()->json([
					'code' => 200,
					'message' => 'OK',
					'data' => $coin
		]);
	}

	public function index()
	{
		$coins = Coin::orderBy('id', 'asc')->get();
		return view('frontend.coin.index')->with('coins', $coins);
	}

	public function create()
	{
		return view('frontend.coin.create');
	}

	public function store(Request $request)
	{
		$validator = Coin::checkCoinValidation($request);
		if ($validator->fails()) {
			$error = $validator->errors()->first();
			return view('frontend.error')->with('error',$error);
		}
		Coin::create($request->all());
		$coins = Coin::orderBy('id', 'asc')->get();
		Session::flash('success', 'The coin was successfully saved');
		return view('frontend.coin.index')->with('coins', $coins);
	}

	public function edit($id)
	{
		$coin = Coin::findOrFail($id);
		return view('frontend.coin.edit')->withCoin($coin);
	}

	public function update(Request $request, $id)
	{
		$validator = Coin::checkCoinValidation($request);
		if ($validator->fails()) {
			$error = $validator->errors()->first();
			return view('frontend.error')->with('error',$error);
		}
		$coin = Coin::findOrFail($id);
		$coin->name = $request->name;
		$coin->save();
		$coins = Coin::orderBy('id', 'asc')->get();
		Session::flash('success', 'The coin was successfully updated');
		return view('frontend.coin.index')->with('coins', $coins);
		
	}

}
