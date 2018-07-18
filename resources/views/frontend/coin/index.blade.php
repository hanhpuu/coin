@extends('layouts.template')

@section('css')
@parent
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
@endsection  

@section('content')
<div class="panel-heading"> <h2>List of coins</h2></div>
<div class="panel-body">
	@if (Session::has('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif
	<table class="table" id='sorting-data-table'>
		<thead>
			<tr>
				<th>ID</th>
				<th>Name</th>
				<th>Function</th>
			</tr>
		</thead>
		<tbody>
			@foreach($coins as $coin)
			<tr>
				<td>{{ $coin->id }}</td>
				<td>{{ $coin->name }}</td>
				<td> <a href="{{ route('coins.edit',$coin->id )}}" class="btn btn-success"> Edit </a></td>
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
            "order": [[1, "desc"]]
        });
    });
</script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
@endsection

