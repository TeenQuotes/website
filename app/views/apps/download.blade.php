@extends('layouts.page')

@section('content')
	<div id="download-app">
		<div id="ariane-line">
			@foreach ($devicesInfo as $deviceInfo)
				@if (strtolower($deviceInfo['name']) != $deviceType)
					<a href="{{ URL::route('apps.device', strtolower($deviceInfo['name'])) }}"><i class="fa {{ $deviceInfo['icon'] }}"></i>{{ $deviceInfo['name'] }}</a>
				@endif
			@endforeach
		</div>
		<h2><i class="fa {{ $titleIcon}}"></i> {{ $title }}</h2>
		{{ $content }}
	</div>
@stop

@section('add-js')
	@include('js.sendEvent')
@stop