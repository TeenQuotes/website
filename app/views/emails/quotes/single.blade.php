<div class="quote">
	{{{ $quote['content']}}}
	<div class="info">
		<a href="{{ URL::route('quotes.show', array($quote['id'])) }}">#{{{ $quote['id'] }}}</a>
		<div class="author">
			<a href="{{ URL::route('users.show', array($quote['user']['login'])) }}">{{{ $quote['user']['login'] }}}</a>
		</div>
	</div>
</div>