@extends('layouts.template')

@section('content')

<h1>Edit a currency pair</h1>
<hr />

<form action="{{ route('currency_pairs.update', $currency_pair->id) }}" method="POST" enctype="multipart/form-data">
	<input name="_token" type="hidden" value="{{ csrf_token() }}"/>

	<label for="base_id">Base ID:</label>
	<input type="number" name="base_id" id="base_id" class="form-control" value="{{$currency_pair->base_id}}"/>
	<label for="quote_id">Quote ID:</label>
	<input type="number" name="quote_id" id="quote_id" class="form-control" value="{{$currency_pair->quote_id}}"/>
	<label for="source_id">Source ID:</label>
	<input type="number" name="source_id" id="source_id" class="form-control" value="{{$currency_pair->source_id}}" />

	<br>
	{{ method_field('PUT') }}
	<input type="submit" class="btn btn-primary" value="Submit" />
</form>

@endsection