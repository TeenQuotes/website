<?php $openedLink = false; ?>

<div class="col-md-5 col-sm-5 col-xs-5">
	@if ( ! (isset($hideAuthorQuote) AND $hideAuthorQuote))
		@if ( ! $quote->user->isHiddenProfile())
			<a href="{{ URL::route('users.show', ['id' => $quote->user->login]) }}" class="link-author-profile">
			<?php $openedLink = true; ?>
		@else
			<span class="link-author-profile">
		@endif

		{{{ $quote->user->login }}}

		@if ($openedLink)
			</a>
		@else
			</span>
		@endif
	@endif
</div>
