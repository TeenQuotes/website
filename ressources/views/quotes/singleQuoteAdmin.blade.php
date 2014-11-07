<?php
$colorQuote = $colors[$i];
$darkColorQuote = TeenQuotes\Quotes\Models\Quote::adjustBrightness($colors[$i], -30);
if ($i % 2 == 1)
	$transition = 'fadeInRight';
else
	$transition = 'fadeInLeft';
?>
<div class="quote animated <?= $transition; ?>" data-id="{{{ $quote->id }}}" style="background-color:<?= $colorQuote; ?>;border-bottom-color:<?= $darkColorQuote; ?>">
	{{{ $quote->content }}}

	<!-- Moderation buttons -->
	<div class="row quotes-info">
		@include ('quotes.partials.moderationButtons')
	</div>
</div>