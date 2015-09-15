<div class="col-md-3 col-sm-3 col-xs-2 animated fadeIn">
	<a class="hidden-sm hidden-xs badge" href="{{ URL::route('quotes.show', ['id' => $quote->id]) }}">#{{{ $quote->id }}}</a>

	<!-- Has comments -->
	@if ($quote->has_comments)
		<a href="{{ URL::route('quotes.show', ['id' => $quote->id]) }}" class="badge hidden-xs hidden-sm">{{{ Lang::choice('quotes.commentComments', $quote->total_comments, array('nb' => $quote->total_comments)) }}}</a>

		<a href="{{ URL::route('quotes.show', ['id' => $quote->id]) }}" class="badge hidden-md hidden-lg"><i class="fa fa-comment"></i> {{{ $quote->total_comments }}}</a>

	<!-- No comments -->
	@else
		<a href="{{ URL::route('quotes.show', ['id' => $quote->id]) }}" class="badge hidden-md hidden-lg"><i class="fa fa-comment"></i> {{{ $quote->total_comments }}}</a>
	@endif
</div>
