@extends('layouts.template')

@section('content')


<h1>Create a new coin</h1>
<hr />

<form action="{{ route('coins.store') }}" method="POST" enctype="multipart/form-data">
	<input name="_token" type="hidden" value="{{ csrf_token() }}"/>

	<label for="name">Name:</label>
	<input type="text" name="name" id="name" class="form-control" />

	<br>

	<input type="submit" class="btn btn-primary" value="Submit post" />
</form>

@endsection