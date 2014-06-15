@extends('layouts.page')

@section('content')
	<div id="download-app">
		<h1><i class="fa {{ $titleIcon}}"></i> {{ $title }}</h1>
		{{ $content }}
	</div>
@stop

@section('add-js')
	@include('js.sendEvent')
@stop