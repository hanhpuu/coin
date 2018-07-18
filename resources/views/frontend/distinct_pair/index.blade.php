@extends('layouts.template')

@section('content')
<div class="panel-heading"> <h2>List of distinct pairs</h2></div>
<div class="panel-body">
	@if (Session::has('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif
	<table class="table" style="text-align:center">
		<thead>
			<tr>
				<th>ID</th>
				<th>Base ID</th>
				<th>Quote ID</th>
				<th>Initial price</th>
				<th>Priority</th>
				<th>Date completed</th>
				<th>Source ID</th>
			</tr>
		</thead>
		<tbody>
			@foreach($distinct_pairs as $distinct_pair)
			<tr>
				<td>{{ $distinct_pair->id }}</td>
				<td>{{ $distinct_pair->base_id }}</td>
				<td>{{ $distinct_pair->quote_id }}</td>
				<td>{{ $distinct_pair->initial_price }}</td>
				<td>{{ $distinct_pair->priority }}</td>
				<td>{{ $distinct_pair->date_completed }}</td>
				<td>{{ $distinct_pair->source_id }}</td>
				<td> <a href="{{ route('distinct_pairs.edit',$distinct_pair->id )}}" class="btn btn-success"> Edit </a></td>
			</tr>
			@endforeach
		</tbody>
	</table>
</div>
@endsection
