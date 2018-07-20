@extends('layouts.template')

@section('content')

<h1>Enter the potential group</h1>
<hr />

<form action="/potential_group" method="POST">
	<input name="_token" type="hidden" value="{{ csrf_token() }}"/>

	<label for="potential_group_id">Potential group ID:</label>


	<select class="form-control" id="potential_group_id" name="potential_group_id">
		@foreach($potential_group_ids as $id)
		<option>{{$id['potential_group_id']}}</option>
		@endforeach
	</select>
	<br>

	<input type="submit" class="btn btn-primary" value="Submit" />
</form>

@endsection