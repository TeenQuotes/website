@extends('layouts.page')

<?php $i = 0; ?>

@section('content')
	<h1 class="content__title">{{ $contentTitle }}</h1>

	<!-- Optional additional navigation -->
	@yield('navigation')

	@foreach ($quotes as $quote)
		@include('quotes.partials.singleQuote', compact("quote"))
		<?php $i++ ?>
		@if ($shouldDisplayPromotion AND $i == 5)
			@include('quotes.partials.promotion')
		@endif
	@endforeach

	<!-- Display ads -->
	@include('layouts.ads.footer')

	<div id="paginator-quotes" class="text-center">
		{{ $paginator->links() }}
	</div>
@stop
