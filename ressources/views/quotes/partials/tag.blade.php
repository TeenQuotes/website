@extends('quotes.partials.multiple')

@section('content')

	<h2 class="quotes__tags-title">{{ trans('quotes.quotesForTag') }} <span class="quotes__tags-tag"><span class="quotes__tag-hashtag">#</span>{{ $tagName }}</span></h2>

	@parent
@stop