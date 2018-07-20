@extends('layouts.template')

@section('css')
@parent
<link rel="stylesheet" type="text/css" href="/datetimepicker/jquery.datetimepicker.css"/ >
	  @endsection

	  @section('content')

	  <h1>Enter the time duration</h1>
<hr />

<form action="/time" method="POST">
	<input name="_token" type="hidden" value="{{ csrf_token() }}"/>
	<label for="begin" >Begin:</label>
	<input id="datetimepicker-begin" type="text" name="begin" class="form-control" />
	<label for="end">End:</label>
	<input id="datetimepicker-end" type="text" name="end" class="form-control" />
	<label for="quote">Quote Currency:</label>
	<select class="form-control" id="quote" name="quote">
		<option value="USDT"> USDT </option>
		<option value="BTC"> BTC </option>
		<option value="Both"> Both </option>
	</select>

	<br>

	<input type="submit" class="btn btn-primary" value="Submit" />
</form>

@endsection

@section('js')
@parent
<script src="/datetimepicker/jquery.js"></script>
<script src="/datetimepicker/build/jquery.datetimepicker.full.min.js"></script>
<script>
jQuery('#datetimepicker-begin').datetimepicker({
  step:15,
  maxDate:'-1970/01/01'
});
</script>
<script>
jQuery('#datetimepicker-end').datetimepicker({
  step:15,
  maxDate:'-1970/01/01'
});
</script>
@endsection