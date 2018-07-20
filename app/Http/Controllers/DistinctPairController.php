<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DistinctPair;
use Illuminate\Support\Facades\Session;

class DistinctPairController extends Controller
{

	public function addDistinctPairsByAPI(Request $request)
	{
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

	public function checkGainOfPotentialGroupByAPI(Request $request)
	{
		try {
			$data = DistinctPair::checkGainOfPotentialGroup($request);
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

	public function enterPotentialGroupID()
	{
		$potential_group_ids = DistinctPair::distinct()->get(['potential_group_id'])->toArray();
		return view('frontend.distinct_pair.group',['potential_group_ids' => $potential_group_ids]);
	}

	public function checkPotentialGroupID(Request $request)
	{
		$data = DistinctPair::checkGainOfPotentialGroup($request);
		$potential_group_id = $request->potential_group_id;
		return view('frontend.distinct_pair.grouplist', ['data' => $data, 'potential_group_id' => $potential_group_id]);
	}

	public function index()
	{
		$distinct_pairs = DistinctPair::orderBy('id', 'asc')->get();
		return view('frontend.distinct_pair.index')->with('distinct_pairs', $distinct_pairs);
	}

	public function create()
	{
		return view('frontend.distinct_pair.create');
	}

	public function store(Request $request)
	{
		$validator = DistinctPair::checkDistinctPairValidation($request);
		if ($validator->fails()) {
			$error = $validator->errors()->first();
			return view('frontend.error')->with('error',$error);
		}
		DistinctPair::insertNewDistinctPair($request);
		//return a view after creating new currency pair
		$distinct_pairs = DistinctPair::orderBy('id', 'asc')->get();
		Session::flash('success', 'The currency pair was successfully saved');
		return view('frontend.distinct_pair.index')->with('distinct_pairs', $distinct_pairs);
	}

	public function edit($id)
	{
		$distinct_pair = DistinctPair::findOrFail($id);
		return view('frontend.distinct_pair.edit')->withDistinctPair($distinct_pair);
	}

	public function update(Request $request, $id)
	{
		$validator = DistinctPair::checkDistinctPairValidation($request);
		if ($validator->fails()) {
			$error = $validator->errors()->first();
			return view('frontend.error')->with('error',$error);
		}
		$distinct_pair = DistinctPair::findOrFail($id);
		$distinct_pair->base_id = $request->base_id;
		$distinct_pair->quote_id = $request->quote_id;
		if ($request->quote_id == 1) {
			$distinct_pair->priority = 1;
		} else {
			$distinct_pair->priority = 2;
		}
		$distinct_pair->initial_price = $request->initial_price;
		$distinct_pair->target_price = $request->target_price;
		$distinct_pair->source_id = $request->source_id;
		$distinct_pair->potential_group_id = $request->potential_group_id;
		$distinct_pair->save();
		//return a view		
		$distinct_pairs = DistinctPair::orderBy('id', 'asc')->get();
		Session::flash('success', 'The currency pair was successfully updated');
		return view('frontend.distinct_pair.index')->with('distinct_pairs', $distinct_pairs);
	}

	public function checkGainOfPotentialGroup(Request $request)
	{
		try {
			$data = DistinctPair::checkGainOfPotentialGroup($request);
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
	
	public function SaveLatestPrice()
	{
		DistinctPair::SaveLatestPrice();
	}
}
