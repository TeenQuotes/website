<?php
$darkColorQuote = $colorGenerator->darken(20);
$colorQuote = $colorGenerator->nextColor();
if ($i % 2 == 1)
	$transition = 'fadeInRight';
else
	$transition = 'fadeInLeft';
?>
<div class="quote animated <?= $transition; ?>" data-id="{{{ $quote->id }}}" style="background-color:<?= $colorQuote; ?>;border-bottom-color:<?= $darkColorQuote; ?>">
	{{{ $quote->content }}}

	<!-- Moderation buttons -->
	<div class="row quotes-info">
		@include('quotes.partials.moderationButtons', compact('quote'))
	</div>
</div>
