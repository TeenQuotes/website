<div class="col-md-1 col-sm-2 col-xs-3 social-buttons">
	<!-- Facebook -->
	<a href="https://www.facebook.com/sharer.php?u={{URL::route('quotes.show', array($quote->id), true)}}" target="_blank"><i class="fa fa-facebook"></i></a>
	
	<!-- Twitter -->
	<a href="https://twitter.com/home?status={{{ $quote->present()->textTweet }}}" target="_blank"><i class="fa fa-twitter"></i></a>
</div>