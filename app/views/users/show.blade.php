@extends('layouts/page')
@section('content')
	<div id="user-profile">
		@if ($user->isHiddenProfile() AND Auth::check() AND Auth::user()->login == $user->login)
			<div class="alert alert-info alert-dismissable no-hide">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<i class="fa fa-info"></i> {{ Lang::get('users.profileCurrentlyHidden') }}
			</div>
		@endif
		@include('users.profile.info')

		@include('users.profile.controls')

		@if (!empty($quotes))
			@if ($type == 'favorites')
				<h2><i class="fa fa-heart"></i> {{ Lang::get('users.favoriteQuotes') }}</h2>
			@else
				<h2><i class="fa fa-pencil"></i> {{ Lang::get('users.publishedQuotes') }}</h2>
			@endif

			<?php $i = 0; ?>
			@foreach ($quotes as $quote)
				@include('quotes.singleQuote', compact("quote"))
			<?php $i++ ?>
			@endforeach

			<div class="text-center">
				{{ $paginator->links() }}
			</div>
		@endif
	</div>
@stop