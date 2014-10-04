@extends('layouts.page')

<?php $i = 0; ?>

@section('content')
	<div id="admin-page">
		<h2><span id="nb-quotes-waiting">{{ count($quotes) }}</span> Waiting quotes</h2>
		<div class="alert alert-info no-hide">
			<span id="nb-quotes-pending">{{ $nbQuotesPending }}</span> <span id="text-quotes">{{ Lang::choice('quotes.quotesText', $nbQuotesPending) }} </span> waiting to be published (<span id="nb-quotes-per-day">{{ $nbDays }}</span> <span id="text-days">{{ Lang::choice('quotes.daysText', $nbDays) }}</span>).
		</div>

		@foreach ($quotes as $quote)
			@include('quotes.singleQuoteAdmin', compact("quote"))

			<?php $i = ($i == (count($colors) - 1)) ? 0 : $i + 1; ?>
		@endforeach
	</div>
@stop