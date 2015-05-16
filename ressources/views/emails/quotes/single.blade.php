@if (isset($colorGenerator))
	<?php
	$darkColorQuote = $colorGenerator->darken(20);
	$colorQuote = $colorGenerator->nextColor();
	?>
	<div class="quote" style="background-color:<?= $colorQuote; ?>;border-bottom-color:<?= $darkColorQuote; ?>">
@else
	<div class="quote">
@endif

	{{{ $quote->content}}}
	<div class="info">
		@if ($quote->isPublished())
			<a href="{{ URL::route('quotes.show', $quote->id) }}">
		@endif
			#{{{ $quote->id }}}
		@if ($quote->isPublished())
			</a>
		@endif
		<div class="author">
			<a href="{{ URL::route('users.show', $quote->user->login) }}">{{{ $quote->user->login }}}</a>
		</div>
	</div>
</div>
