@extends('layouts.template')

@section('content')

@if ($error)

    <div class="alert alert-warning">{!! $error !!}</div>
    @endif

@endsection