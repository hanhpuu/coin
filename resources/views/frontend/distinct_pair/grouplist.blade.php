@extends('layouts.template')

@section('css')
@parent
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
@endsection  

@section('content')
<div class="panel-heading" style="text-align:center"> <h2>List of coins of group {{$potential_group_id}} </h2></div>
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
				<th>Target Price in USDT</th>
				<th>Progress percentage</th>
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
				@if($pair['gain_in_percentage'] < 0)
				<td style="color: red;font-weight:bold">{{number_format((float)$pair['gain_in_percentage'], 2, '.', '') }} %</td>
				@else
				<td style="color: green; font-weight: bold">{{number_format((float)$pair['gain_in_percentage'], 2, '.', '') }} %</td>
				@endif
				<td>{{$pair['target_price']}}</td>
				@if($pair['progress_percentage'] < 0)
				<td style="color: red;font-weight:bold">{{number_format((float)$pair['progress_percentage'], 2, '.', '') }} %</td>
				@else
				<td style="color: green;font-weight:bold">{{number_format((float)$pair['progress_percentage'], 2, '.', '') }} %</td>
				@endif
			</tr>
			@endforeach
		</tbody>
	</table>
	<div class="panel-footer">
		<p>Note:</p> 

		To find <strong>Gain in percentage</strong>:<br>
		- First, work out the difference between the max price and price at the openning.<br> 
		- Then, divide the increase by the price at the openning and multiply the answer by 100<br><br>
		To find <strong>Progress percentage</strong>:<br>
		- Divide the latest price by the target price and multiply the answer by 100<br>
	</div>
</div>
@endsection

@section('js')
@parent
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script type="text/javascript">
    $(document).ready(function () {
        $('#sorting-data-table').DataTable({
            "order": [[1, "desc"]]
        });
    });
</script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
@endsection

