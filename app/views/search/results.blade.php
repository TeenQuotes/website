@extends('layouts.page')

@section('content')
<div id="search-page">
	<h1><i class="fa fa-search"></i> {{ Lang::get('search.resultForQuery', ['query' => $query]) }}</h1>
	<!-- SCROLLTO IF WE HAVE QUOTES AND USERS -->
	@if ($quotes->count() > 0 AND !is_null($users) AND $users->count() > 0)
		<div id="result-info" class="row">
			@foreach (['quotes', 'users'] as $element)
				<div class="col-xs-6 text-center">
					<div class="content" data-scroll="{{ $element }}">
						@if ($element == 'quotes')
							<i class="fa fa-comment"></i>
						@else
							<i class="fa fa-users"></i>
						@endif
						{{ Lang::get('search.'.$element.'Result') }}
						<span class="count">
							{{ $$element->count() }} {{ Lang::choice('search.result', $$element->count())}}
						</span>
					</div>
				</div>
			@endforeach
		</div>
	@endif

	<!-- DISPLAYING QUOTES AND USERS RESULTS -->
	@foreach (['quotes', 'users'] as $element)
		<?php $i = 0; ?>

		@if (!is_null($$element) AND $$element->count() > 0)
			<!-- Title -->
			<h2 id="{{ $element }}">
				@if ($element == 'quotes')
					<i class="fa fa-comment"></i>
				@else
					<i class="fa fa-users"></i>
				@endif
				{{ Lang::get('search.'.$element.'Result') }}<span class="count">{{ $$element->count() }} {{ Lang::choice('search.result', $$element->count())}}</span>
			</h2>

			<!-- Showing only top results -->
			@if ($$element->count() > $maxNbResultPerCategory)
				<div class="alert alert-info no-hide">
					{{ Lang::get('search.showingTopResults', ['nb' => $maxNbResultPerCategory]) }}
				</div>
			@endif

			<!-- Displaying results -->
			@foreach ($$element->take($maxNbResultPerCategory) as $el)
				@if ($element == 'quotes')
					@include('quotes.singleQuote', ['quote' => $el])
				@else
					@include('search.singleUser', ['user' => $el])
				@endif
				<?php $i++ ?>
			@endforeach
		@endif

		@if (($element == 'quotes' AND $quotes->count() > 0) OR ($element == 'users' AND $quotes->count() > 0) OR (!is_null($users) AND $users->count() > 0 AND $element == 'users'))
			<div class="margin-bottom"></div>
		@endif

	@endforeach
</div><!-- #searcĥ-page -->
@stop