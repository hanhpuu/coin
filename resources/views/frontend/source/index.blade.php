@extends('layouts.template')

@section('content')
<div class="panel-heading"> <h2>List of sources</h2></div>
<div class="panel-body">
	@if (Session::has('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif
	<table class="table">
		<thead>
			<tr>
				<th>ID</th>
				<th>Name</th>
				<th>Function</th>
			</tr>
		</thead>
		<tbody>
			@foreach($sources as $source)
			<tr>
				<td>{{ $source->id }}</td>
				<td>{{ $source->name }}</td>
				<td> <a href="{{ route('sources.edit',$source->id )}}" class="btn btn-success"> Edit </a></td>
			</tr>
			@endforeach
		</tbody>
	</table>
</div>
@endsection
