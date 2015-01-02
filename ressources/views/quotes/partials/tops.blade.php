@extends('quotes.partials.multiple')

@section('content')

	<!-- Link to other tops -->
	<div class="quotes__top-container">
		<div class="row">
			@foreach ($possibleTopTypes as $el)
				<div class="col-md-6 centered-column text-center">
					<i class="fa {{ ${'iconForTop'.ucfirst($el)} }}"></i>
					<a href="{{ URL::route('quotes.top.'.$el)}}">
						{{ trans('quotes.top'.ucfirst($el)) }}
					</a>
				</div>
			@endforeach
		</div>
	</div>

	@parent
@stop