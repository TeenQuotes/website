@extends('layouts.page')

@section('content')
	{{ $content }}
@stop

@section('add-js')
	@include('js.sendEvent')
@stop