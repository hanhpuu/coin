@extends('layouts.template')

@section('content')

<h1>Edit source name</h1>
<hr />

<form action="{{ route('sources.update', $source->id) }}" method="POST" enctype="multipart/form-data">
	<input name="_token" type="hidden" value="{{ csrf_token() }}"/>

	<label for="name">Name:</label>
	<input type="text" name="name" id="name" class="form-control" value="{{ $source->name}}" />

	<br>
	{{ method_field('PUT') }}
	<input type="submit" class="btn btn-primary" value="Submit" />
</form>

@endsection