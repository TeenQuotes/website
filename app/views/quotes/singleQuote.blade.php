<?php
$colorQuote = $colors[$i];
$colorBorderQuote = Quote::adjustBrightness($colors[$i], -30);
$colorBubbleComments = Quote::adjustBrightness($colorQuote, 100);
if ($i % 2 == 1)
	$transition = 'fadeInRight';
else
	$transition = 'fadeInLeft';
?>
<div class="quote animated <?= $transition; ?>" style="background-color:<?= $colorQuote; ?>;border-bottom-color:<?= $colorBorderQuote; ?>">
	{{{ $quote->content }}}
	
	<div class="row quotes-info">
		<!-- COMMENTS -->
		<div class="col-md-3 col-xs-3">
			<a class="hidden-sm hidden-xs" href="{{ URL::action('QuotesController@show', ['id' => $quote->id]) }}"><span class="badge transition" style="background:<?= $colorBorderQuote; ?>">#{{{ $quote->id }}}</span></a>
			
			@if ($quote->has_comments)
				<a href="{{ URL::action('QuotesController@show', ['id' => $quote->id]) }}" class="badge" style="background:<?= $colorBorderQuote ?>">{{{ Lang::choice('quotes.commentComments', $quote->total_comments, array('nb' => $quote->total_comments)) }}}</a>
			@else
				<a class="hidden-md hidden-lg" href="{{ URL::action('QuotesController@show', ['id' => $quote->id]) }}"><span class="badge transition" style="background:<?= $colorBorderQuote; ?>">#{{{ $quote->id }}}</span></a>
			@endif
		</div>
		
		<!-- FAVORITE -->
		<div class="col-md-3 col-xs-2">
			@if ($quote->is_favorite_for_current_user)
				<span class="badge">favorite</span>
			@else
				<span class="badge">no favorite</span>
			@endif
		</div>
		
		<!-- AUTHOR -->
		<div class="col-md-5 col-xs-5">
			<a href="{{ URL::action('UsersController@show', ['id' => $quote->user->login]) }}" class="transition link-author-profile">{{{ $quote->user->login }}}</a>
		</div>

		<!-- SOCIAL BUTTONS -->
		<div class="col-md-1 col-xs-2 social-buttons">
			<a href="https://www.facebook.com/sharer.php?u={{URL::route('quotes.show', array($quote->id), true)}}" class="transition" style="background:<?= $colorBorderQuote ?>"><i class="fa fa-facebook"></i></a>
			<a href="https://twitter.com/share?text={{{ $quote->textTweet() }}}" class="transition" style="background:<?= $colorBorderQuote ?>"><i class="fa fa-twitter"></i></a>
		</div>
	</div>
</div>