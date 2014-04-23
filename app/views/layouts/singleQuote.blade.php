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
	{{ $quote->content}}
	
	<div class="row quotes-info">
		<!-- COMMENTS -->
		<div class="col-md-2 col-xs-3">
			<a class="hidden-sm hidden-xs" href="{{ URL::action('QuotesController@show', ['id' => $quote->id]) }}"><span class="badge transition" style="background:<?= $colorBorderQuote; ?>">#{{$quote->id}}</span></a>
			
			@if ($quote->has_comments)
				<a href="{{ URL::action('QuotesController@show', ['id' => $quote->id]) }}" class="comments-count"><i class="fa fa-comment nb-comments" style="color:<?= $colorBubbleComments ?>"></i>{{ $quote->total_comments }}</a>
			@else
				<a class="hidden-md hidden-lg" href="{{ URL::action('QuotesController@show', ['id' => $quote->id]) }}"><span class="badge transition" style="background:<?= $colorBorderQuote; ?>">#{{$quote->id}}</span></a>
			@endif
		</div>
		
		<!-- SOCIAL BUTTONS -->
		<div class="col-md-4 hidden-sm hidden-xs social-buttons">
			<div class="float-left">
				<div class="fb-like" data-href="{{URL::route('quotes.show', array($quote->id), true)}}" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>
			</div>
			<div class="float-left">
				<a href="https://twitter.com/share" class="twitter-share-button" data-lang="en" data-url="{{URL::route('quotes.show', array($quote->id), true)}}" data-count="none" data-text="{{$quote->textTweet()}}">Tweet</a>
			</div>
		</div>
		
		<!-- FAVORITE -->
		<div class="col-md-2 col-xs-2">
			@if ($quote->is_favorite_for_current_user)
				<span class="badge">favorite</span>
			@else
				<span class="badge">no favorite</span>
			@endif
		</div>
		
		<!-- AUTHOR -->
		<div class="col-md-4 col-xs-7">
			<a href="{{ URL::action('UsersController@show', ['id' => $quote->user->login]) }}" class="transition link-author-profile">{{$quote->user->login}}</a>
		</div>
	</div>
</div>