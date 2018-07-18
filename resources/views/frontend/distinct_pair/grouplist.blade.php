@extends('layouts.template')

@section('css')
@parent
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
@endsection  

@section('content')
<div class="panel-heading" style='text-align: center'> <h2>List of coins of group {{$potential_group_id}} </h2></div>
<div class="panel-body">
	<table class="table" id="sorting-data-table">
		<thead>
			<tr>
				<th>Currency Pair ID</th>
				<th>Base Currency Name</th>
				<th>Quote Currency Name</th>
				<th>Initial Price in USDT</th>
				<th>Latest Price in USDT</th>
				<th>Gain in percentage</th>
			</tr>
		</thead>
		<tbody>
			@foreach($data as $pair)
			<tr>
				<td>{{$pair['id'] }}</td>
				<td>{{$pair['base_name'] }}</td>
				<td>{{$pair['quote_name'] }}</td>
				<td>{{$pair['initial_price'] }}</td>
				<td>{{$pair['latest_price']}}</td>
				<td>{{$pair['gain_in_percentage'] }} %</td>
			</tr>
			@endforeach
		</tbody>
	</table>
</div>

@endsection

@section('js')
@parent
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script type="text/javascript">
    $(document).ready(function () {
        $('#sorting-data-table').DataTable({
            "order": [[5, "desc"]]
        });
    });
</script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
@endsection

