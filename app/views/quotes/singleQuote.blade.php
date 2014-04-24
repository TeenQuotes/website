<?php
$colorQuote = $colors[$i];
$darkColorQuote = Quote::adjustBrightness($colors[$i], -30);
if ($i % 2 == 1)
	$transition = 'fadeInRight';
else
	$transition = 'fadeInLeft';
?>
<div class="quote animated <?= $transition; ?>" style="background-color:<?= $colorQuote; ?>;border-bottom-color:<?= $darkColorQuote; ?>">
	{{{ $quote->content }}}
	
	<div class="row quotes-info">
		<!-- COMMENTS -->
		<div class="col-md-3 col-xs-2">
			<a class="hidden-sm hidden-xs" href="{{ URL::action('QuotesController@show', ['id' => $quote->id]) }}"><span class="badge transition" style="background:<?= $darkColorQuote; ?>">#{{{ $quote->id }}}</span></a>
			
			<!-- Has comments -->
			@if ($quote->has_comments)
				<a href="{{ URL::action('QuotesController@show', ['id' => $quote->id]) }}" class="badge hidden-xs hidden-sm" style="background:<?= $darkColorQuote ?>">{{{ Lang::choice('quotes.commentComments', $quote->total_comments, array('nb' => $quote->total_comments)) }}}</a>

				<a href="{{ URL::action('QuotesController@show', ['id' => $quote->id]) }}" class="badge hidden-md hidden-lg" style="background:<?= $darkColorQuote ?>"><i class="fa fa-comment"></i> {{{ $quote->total_comments }}}</a>
			
			<!-- No comments -->
			@else
				<a class="hidden-md hidden-lg" href="{{ URL::action('QuotesController@show', ['id' => $quote->id]) }}"><span class="badge transition" style="background:<?= $darkColorQuote; ?>">#{{{ $quote->id }}}</span></a>
			@endif
		</div>
		
		<!-- FAVORITE -->
		<div class="col-md-3 col-xs-2 favorite-links">
			@if (Auth::check())
				@if ($quote->is_favorite_for_current_user)
					<button data-url="{{URL::route('unfavorite', array($quote->id), true)}}" data-id="{{ $quote->id }}" data-type="unfavorite" class="badge transition favorite-action" style="background:<?= $darkColorQuote; ?>"><i class="fa fa-heart-o"></i></button>
				@else
					<button data-url="{{URL::route('favorite', array($quote->id), true)}}" data-id="{{ $quote->id }}" data-type="favorite" class="badge transition favorite-action" style="background:<?= $darkColorQuote; ?>"><i class="fa fa-heart"></i></button>
				@endif
			@endif
		</div>
		
		<!-- AUTHOR -->
		<div class="col-md-5 col-xs-5">
			<a href="{{ URL::action('UsersController@show', ['id' => $quote->user->login]) }}" class="transition link-author-profile">{{{ $quote->user->login }}}</a>
		</div>

		<!-- SOCIAL BUTTONS -->
		<div class="col-md-1 col-xs-3 social-buttons">
			<a href="https://www.facebook.com/sharer.php?u={{URL::route('quotes.show', array($quote->id), true)}}" class="transition" style="background:<?= $darkColorQuote ?>" target="_blank"><i class="fa fa-facebook"></i></a>
			<a href="https://twitter.com/home?status={{{ $quote->textTweet() }}}" class="transition" style="background:<?= $darkColorQuote ?>" target="_blank"><i class="fa fa-twitter"></i></a>
		</div>
	</div>
</div>