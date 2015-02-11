@extends('quotes.partials.multiple')

@section('content')

	<h2 class="quotes__tags-title">{{ trans('quotes.quotesForTag') }} <span class="quotes__tags-title-tag">{{ $tagName }}</span></h2>

	@parent
@stop