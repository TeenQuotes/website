@extends('layouts.page')

<?php $i = 0; ?>

@section('content')
	@foreach ($quotes as $quote)
		@include('quotes.singleQuote', compact("quote"))
		<?php $i++ ?>
		@if ($shouldDisplaySharePromotion AND $i == 5)
			@include('quotes.partials.promoteSharing')
		@endif
	@endforeach

	<!-- Display ads -->
	@include('layouts.ads.footer')

	<div id="paginator-quotes" class="text-center">
		{{ $paginator->links() }}
	</div>
@stop