@extends('layouts.template')

@section('content')

<h1>Create a new currency pair</h1>
<hr />

<form action="{{ route('distinct_pairs.store') }}" method="POST" enctype="multipart/form-data">
	<input name="_token" type="hidden" value="{{ csrf_token() }}"/>

	<label for="base_id" >Base ID:</label>
	<input type="number" name="base_id" id="base_id" class="form-control" />
	<label for="quote_id">Quote ID:</label>
	<input type="number" name="quote_id" id="quote_id" class="form-control" />
	<label for="initial_price">Initial Price:</label>
	<input type="number" name="initial_price" id="initial_price" class="form-control" step="0.001" />
	<label for="target_price">Target Price:</label>
	<input type="number" name="target_price" id="target_price" class="form-control" step="0.001" />
	<label for="source_id">Source ID:</label>
	<input type="number" name="source_id" id="source_id" class="form-control " />
	<label for="potential_group_id">Potential Group ID:</label>
	<input type="number" name="potential_group_id" id="potential_group_id" class="form-control " />


	
	<br>

	<input type="submit" class="btn btn-primary" value="Submit source" />
</form>

@endsection