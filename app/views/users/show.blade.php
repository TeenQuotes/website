@extends('layouts/page')
@section('content')
	<div id="user-profile">
		<!-- Alert if the profile is hidden -->
		@if ($user->isHiddenProfile() AND Auth::check() AND Auth::user()->login == $user->login)
			<div class="alert alert-info alert-dismissable no-hide">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<i class="fa fa-info"></i> {{ Lang::get('users.profileCurrentlyHidden') }}
			</div>
		@endif
		
		<!-- Profile basic info and counters -->
		@include('users.profile.info')

		<!-- Controls buttons -->
		@include('users.profile.controls')

		<!-- Content: quotes or comments -->
		@if (!empty($quotes))
			@if ($type == 'favorites')
				<h2><i class="fa fa-heart"></i> {{ Lang::get('users.favoriteQuotes') }}</h2>
			@elseif ($type == 'comments')	
				<h2><i class="fa fa-comments"></i> {{ Lang::get('users.comments') }}</h2>
			@else
				<h2><i class="fa fa-pencil"></i> {{ Lang::get('users.publishedQuotes') }}</h2>
			@endif

			<?php $i = 0; ?>
			@foreach ($quotes as $quote)
				@if ($type != 'comments')
					@include('quotes.singleQuote', compact("quote"))
				@else
					<?php $comment = $quote; ?>
					@include('comments.singleComment', compact("comment"))
					<div class="comment-quote-info">
						<a href="{{URL::route('quotes.show', $comment->quote->id)}}">#{{$comment->quote->id}}</a> - {{{ $comment->quote->content }}}
					</div>
				@endif
			<?php $i++ ?>
			@endforeach

			<div class="text-center">
				{{ $paginator->links() }}
			</div>
		@else
			<!-- If the user is new and is viewing its own profile, a small welcome tutorial -->
			@if (Auth::check() AND Auth::user()->login == $user->login)
				@include('users.welcome')
			@endif
		@endif
	</div>
@stop