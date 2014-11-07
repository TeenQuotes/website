@extends('layouts.page')

@section('content')
	<div id="download-app">
		<div id="ariane-line" class="animated fadeInDown">
			@foreach ($devicesInfo as $deviceInfo)
				@if (strtolower($deviceInfo['name']) != $deviceType)
					<a href="{{ URL::route('apps.device', strtolower($deviceInfo['name'])) }}"><i class="fa {{ $deviceInfo['mini-icon'] }}"></i>{{ $deviceInfo['name'] }}</a>
				@endif
			@endforeach
		</div>
		<h2 class="animated fadeInUp"><i class="fa {{ $titleIcon}}"></i> {{ $title }}</h2>
		<div class="row animated fadeInUp">
			<div class="col-md-6 col-sm-6 col-xs-8">
				{{ $content }}
			</div>
			<div class="col-md-6 col-sm-6 col-xs-4">
				{{HTML::image('assets/images/icons/'.$devicesInfo[$deviceType]['large-icon'], $deviceType, ['class' => 'img-responsive']) }}				
			</div>
		</div>
	</div>
@stop

<!-- Send event to Google Analytics -->
@section('add-js')
	@include('js.sendEvent')
@stop