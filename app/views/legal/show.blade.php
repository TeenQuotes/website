@extends('layouts.page')

@section('content')
	<div id="legal-page">
		<div id="ariane-line">
			@foreach ($arianeLineLinks as $key => $value)
				<a href="{{ URL::route('legal.show', $key) }}"><i class="fa fa-legal"></i>{{ $value }}</a>
			@endforeach
		</div>

		<h1><i class="fa fa-legal"></i> {{ $title }}</h1>
		{{ $content }}
	</div>
@stop