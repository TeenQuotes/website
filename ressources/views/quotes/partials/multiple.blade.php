@extends('layouts.page')

<?php $i = 0; ?>

@section('content')
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