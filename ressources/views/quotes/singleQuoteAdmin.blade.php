<?php
$darkColorQuote = $colorGenerator->darken(20);
$colorQuote = $colorGenerator->nextColor();
?>
<div class="quote" data-id="{{{ $quote->id }}}" style="background-color:{{ $colorQuote }};border-bottom-color:{{ $darkColorQuote }}">
	{{{ $quote->content }}}

	<!-- Moderation buttons -->
	<div class="row quotes-info">
		@include('quotes.partials.moderationButtons', compact('quote'))
	</div>
</div>
