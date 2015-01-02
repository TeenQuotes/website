<?php $i = 0;
$count = 'nb'.ucfirst($element);
?>

@if ( ! is_null($$element) AND $$count > 0)
	<!-- Title -->
	<h2 id="{{ $element }}">
		@if ($element == 'quotes')
			<i class="fa fa-comment"></i>
		@else
			<i class="fa fa-users"></i>
		@endif
		{{ Lang::get('search.'.$element.'Result') }}<span class="count">{{ $$count }} {{ Lang::choice('search.result', $$count)}}</span>
	</h2>

	<!-- Showing only top results -->
	@include('search.partials.topResults')

	<!-- Displaying results -->
	@foreach ($$element as $el)
		@if ($element == 'quotes')
			@include('quotes.partials.singleQuote', ['quote' => $el])
		@else
			@include('search.partials.singleUser', ['user' => $el])
		@endif
		<?php $i++ ?>
	@endforeach
@endif

@if (($element == 'quotes' AND $nbQuotes > 0) OR ($element == 'users' AND $nbQuotes > 0) OR ( ! is_null($users) AND $nbUsers > 0 AND $element == 'users'))
	<div class="margin-bottom"></div>
@endif