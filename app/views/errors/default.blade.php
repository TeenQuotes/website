@extends('layouts.page')

@section('content')
	<div id="error-page">
		<h1><i class="fa fa-meh-o"></i>{{ $title }}</h1>
		{{ $content }}
	</div>
@stop

<!-- Send event to Google Analytics -->
@section('add-js')
	@include('js.sendEvent')
@stop