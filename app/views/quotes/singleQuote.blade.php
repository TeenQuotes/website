<?php
$colorQuoteClass = $colors[$quote->id];
$transition = ($i % 2 == 1) ? 'fadeInRight' : 'fadeInLeft';
?>

<div class="quote wow animated <?= $transition.' '.$colorQuoteClass; ?>" data-id="{{ $quote->id }}">
	
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