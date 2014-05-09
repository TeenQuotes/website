@extends('layouts/page')
<?php
$i = 0;
?>
@section('content')
<div id="search-page">
	<!-- QUOTES -->
	@if ($quotes->count() > 0)
		<h2 id="quotes"><i class="fa fa-comment"></i> {{ Lang::get('search.quotesResult') }}<span class="count">{{ $quotes->count() }} {{ Lang::choice('search.result', $quotes->count())}}</span></h2>

		@if ($quotes->count() > $maxNbResultPerCategory)
			<div class="alert alert-info no-hide">
				{{ Lang::get('search.showingTopResults', ['nb' => $maxNbResultPerCategory]) }}
			</div>
		@endif

		@foreach ($quotes->take($maxNbResultPerCategory) as $quote)
			@include('quotes.singleQuote', compact($quote))
			<?php $i++ ?>
		@endforeach
	@endif

	<!-- USERS -->
	@if (!is_null($users) AND $users->count() > 0)
		<?php $i = 0; ?>
		<h2 id="users" <?php if ($quotes->count() > 0) echo 'class="margin"'; ?>><i class="fa fa-users"></i> {{ Lang::get('search.usersResult') }}<span class="count">{{ $users->count() }} {{ Lang::choice('search.result', $users->count())}}</span></h2>

		@if ($users->count() > $maxNbResultPerCategory)
			<div class="alert alert-info no-hide">
				{{ Lang::get('search.showingTopResults', ['nb' => $maxNbResultPerCategory]) }}
			</div>
		@endif

		@foreach ($users->take($maxNbResultPerCategory) as $user)
			@include('search.singleUser', compact($user))
			<?php $i++ ?>
		@endforeach
	@endif

</div>
@stop