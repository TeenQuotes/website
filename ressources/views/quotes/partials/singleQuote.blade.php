<?php
$colorQuoteClass = $colors[$quote->id];
?>

<div class="quote {{ $colorQuoteClass }}" data-id="{{ $quote->id }}">

	<!-- Content -->
	{{{ $quote->content }}}

	<div class="row quotes-info">
		<!-- COMMENTS -->
		@include('quotes.partials.nbComments')

		<!-- FAVORITE -->
		@include('quotes.partials.isFavorite')

		<!-- AUTHOR -->
		@include('quotes.partials.author')

		<!-- SOCIAL BUTTONS -->
		@include('quotes.partials.social')
	</div>
</div>
