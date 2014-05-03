<?php
$colorQuote = $colors[$i];
$darkColorQuote = Quote::adjustBrightness($colors[$i], -30);
if ($i % 2 == 1)
	$transition = 'fadeInRight';
else
	$transition = 'fadeInLeft';
?>
<div class="quote animated <?= $transition; ?>" data-id="{{{ $quote->id }}}" style="background-color:<?= $colorQuote; ?>;border-bottom-color:<?= $darkColorQuote; ?>">
	{{{ $quote->content }}}

	<div class="row quotes-info">
		<!-- Approve -->
		<div class="col-xs-1">
			<span class="badge quote-moderation" data-id="{{{ $quote->id }}}" data-url="{{{ URL::action('QuotesAdminController@postModerate', array($quote-> id, 'approve')) }}}" data-decision="approve"><i class="fa fa-thumbs-up"></i></span>
		</div>

		<!-- Unapprove -->
		<div class="col-xs-1">
			<span class="badge quote-moderation" data-id="{{{ $quote->id }}}" data-url="{{{ URL::action('QuotesAdminController@postModerate', array($quote-> id, 'unapprove')) }}}" data-decision="unapprove"><i class="fa fa-thumbs-down"></i></span>
		</div>

		<!-- Edit -->
		<div class="col-xs-1">
			<a href="{{ URL::route('admin.quotes.edit', array($quote->id)); }}" class="badge"><i class="fa fa-pencil-square-o"></i></a>
		</div>
	</div>
</div>