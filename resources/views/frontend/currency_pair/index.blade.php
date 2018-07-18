@extends('layouts.template')

@section('content')
<div class="panel-heading"> <h2>List of currency pairs</h2></div>
<div class="panel-body">
	@if (Session::has('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif
	<table class="table" style="text-align:center">
		<thead>
			<tr>
				<th>ID</th>
				<th>Base currency ID</th>
				<th>Quote currency ID</th>
				<th>Priority</th>
				<th>Date completed</th>
				<th>Source ID</th>
			</tr>
		</thead>
		<tbody>
			@foreach($currency_pairs as $currency_pair)
			<tr>
				<td>{{ $currency_pair->id }}</td>
				<td>{{ $currency_pair->base_id }}</td>
				<td>{{ $currency_pair->quote_id }}</td>
				<td>{{ $currency_pair->priority }}</td>
				<td>{{ $currency_pair->date_completed }}</td>
				<td>{{ $currency_pair->source_id }}</td>
				<td> <a href="{{ route('currency_pairs.edit',$currency_pair->id )}}" class="btn btn-success"> Edit </a></td>
			</tr>
			@endforeach
		</tbody>
	</table>
</div>
@endsection
