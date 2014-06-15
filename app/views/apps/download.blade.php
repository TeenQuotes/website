@extends('layouts.page')

@section('content')
	<div id="download-app">
		<h2><i class="fa {{ $titleIcon}}"></i> {{ $title }}</h2>
		{{ $content }}
	</div>
@stop

@section('add-js')
	@include('js.sendEvent')
@stop