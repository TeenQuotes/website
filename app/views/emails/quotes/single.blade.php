<div class="quote">
	{{{ $quote->content}}}
	<div class="info">
		@if ($quote->isPublished())
			<a href="{{ URL::route('quotes.show', array($quote->id)) }}">
		@endif
			#{{{ $quote->id }}}
		@if ($quote->isPublished())
			</a>
		@endif
		<div class="author">
			<a href="{{ URL::route('users.show', array($quote->user->login)) }}">{{{ $quote->user->login }}}</a>
		</div>
	</div>
</div>