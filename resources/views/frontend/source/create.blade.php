@extends('layouts.template')

@section('content')

<h1>Create a new source</h1>
<hr />

<form action="{{ route('sources.store') }}" method="POST" enctype="multipart/form-data">
	<input name="_token" type="hidden" value="{{ csrf_token() }}"/>

	<label for="name">Name:</label>
	<input type="text" name="name" id="name" class="form-control box-inside-form" />

	<br>

	<input type="submit" class="btn btn-primary" value="Submit source" />
</form>

@endsection