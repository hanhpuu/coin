<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Source;
use Illuminate\Support\Facades\Session;

class SourceController extends Controller
{
    public function addSourceNameByAPI(Request $request) 
    {
        $validator = Source::checkSourceValidation($request);
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
	
	
	public function index()
	{
		$sources = Source::orderBy('id', 'asc')->get();
		return view('frontend.source.index')->with('sources', $sources);
	}

	public function create()
	{
		return view('frontend.source.create');
	}

	public function store(Request $request)
	{
		$validator = Source::checkSourceValidation($request);
		if ($validator->fails()) {
			$error = $validator->errors()->first();
			return view('frontend.error')->with('error',$error);
		}
		Source::create($request->all());
		$sources = Source::orderBy('id', 'asc')->get();
		Session::flash('success', 'The source was successfully saved');
		return view('frontend.source.index')->with('sources', $sources);
	}

	public function edit($id)
	{
		$source = Source::findOrFail($id);
		return view('frontend.source.edit')->withSource($source);
	}

	public function update(Request $request, $id)
	{
		$validator = Source::checkSourceValidation($request);
		if ($validator->fails()) {
			$error = $validator->errors()->first();
			return view('frontend.error')->with('error',$error);
		}
		$source = Source::findOrFail($id);
		$source->name = $request->name;
		$source->save();
		$sources = Source::orderBy('id', 'asc')->get();
		Session::flash('success', 'The source was successfully updated');
		return view('frontend.source.index')->with('sources', $sources);
		
	}

}
